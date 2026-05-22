<?php

namespace App\Http\Controllers\SPVW_Controller;

use App\Http\Controllers\Concerns\UsesToastRedirects;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SPVW_Controller\Concerns\HasAllowedSeeData;
use App\Models\KeteranganLanjutan;
use Illuminate\Http\Request;

class KeteranganLanjutanController extends Controller
{
    use UsesToastRedirects, HasAllowedSeeData;

    public function index()
    {
        return view('spv_w_view.keterangan_lanjutan.index');
    }

    public function history()
    {
        $keteranganLanjutans = KeteranganLanjutan::with('user:id,nama_lengkap,kerjasama_id')
            ->whereHas('user', function ($q) {
                $q->whereHas('jabatan', function ($jabatanQuery) {
                    $jabatanQuery->whereIn('id', $this->allowedSeeData());
                })->when($this->selectedClientId() > 0, fn($userQuery) => $userQuery->whereHas('kerjasama', fn($k) => $k->where('client_id', $this->selectedClientId())));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('spv_w_view.keterangan_lanjutan.history', [
            'keteranganLanjutans' => $keteranganLanjutans,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.periode' => ['required', 'string', 'max:255'],
            'entries.*.judul' => ['required', 'string', 'max:255'],
            'entries.*.keterangan' => ['required', 'string'],
        ]);

        $keterangan = collect($validated['entries'])
            ->map(fn($entry) => [
                'periode' => trim((string) ($entry['periode'] ?? '')),
                'judul' => trim((string) ($entry['judul'] ?? '')),
                'keterangan' => trim((string) ($entry['keterangan'] ?? '')),
            ])
            ->filter(fn($entry) => $entry['periode'] !== '' && $entry['judul'] !== '' && $entry['keterangan'] !== '')
            ->values()
            ->all();

        if (count($keterangan) === 0) {
            return $this->redirectBackWithToast('warning', 'Minimal isi 1 periode, judul, dan keterangan.');
        }

        KeteranganLanjutan::create([
            'user_id' => auth()->id(),
            'keterangan' => $keterangan,
        ]);

        return $this->redirectBackWithToast('success', 'Keterangan lanjutan berhasil disimpan.');
    }

    private function selectedClientId(): int
    {
        $sessionKey = 'spvw.selected_client_id';

        if (request()->has('client_id')) {
            $clientId = max((int) request('client_id', 0), 0);

            if ($clientId > 0) {
                session([$sessionKey => $clientId]);
            } else {
                session()->forget($sessionKey);
            }

            return $clientId;
        }

        return max((int) session($sessionKey, 0), 0);
    }
}
