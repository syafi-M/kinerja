<?php

namespace App\Http\Controllers\SPVW_Controller;

use App\Http\Controllers\Concerns\UsesToastRedirects;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SPVW_Controller\Concerns\HasAllowedSeeData;
use App\Models\KeteranganLanjutan;
use App\Models\User;
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
        $users = User::select(['id', 'nama_lengkap', 'kerjasama_id'])
            ->where('role_id', '!=', 2)
            ->when($this->selectedClientId() > 0, fn($q) => $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $this->selectedClientId())))
            ->whereHas('jabatan', function ($jabatanQuery) {
                $jabatanQuery->whereIn('id', $this->allowedSeeData());
            })
            ->orderBy('nama_lengkap')
            ->get();

        $keteranganLanjutans = KeteranganLanjutan::with([
            'user:id,nama_lengkap,kerjasama_id',
            'createdBy:id,nama_lengkap',
        ])
            ->where('created_by_user_id', auth()->user()->id)
            ->when(request()->filled('user_id'), function ($q) {
                $q->where('user_id', request('user_id'));
            })
            ->when(request()->filled('periode'), function ($q) {
                $periode = trim((string) request('periode'));
                $q->whereRaw('LOWER(keterangan) LIKE ?', ['%' . mb_strtolower($periode) . '%']);
            })
            ->when(request()->filled('search'), function ($q) {
                $keyword = trim((string) request('search'));

                $q->where(function ($subQuery) use ($keyword) {
                    $lowerKeyword = '%' . mb_strtolower($keyword) . '%';

                    $subQuery->whereHas('user', function ($userQuery) use ($lowerKeyword) {
                        $userQuery->whereRaw('LOWER(nama_lengkap) LIKE ?', [$lowerKeyword]);
                    })->orWhereRaw('LOWER(keterangan) LIKE ?', [$lowerKeyword]);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
            
        return view('spv_w_view.keterangan_lanjutan.history', [
            'keteranganLanjutans' => $keteranganLanjutans,
            'users' => $users,
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
            'created_by_user_id' => auth()->id(),
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
