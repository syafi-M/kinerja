<?php

namespace App\Http\Controllers\SPVW_Controller;

use App\Http\Controllers\Concerns\LocksRekapByDueDate;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\RekapDueDateSetting;
use App\Models\User;

class DataRekapController extends Controller
{
    use LocksRekapByDueDate;

    public function index()
    {
        $sessionKey = 'spvw.selected_client_id';
        $modeSessionKey = 'spvw.rekap_mode';
        $hasClientParam = request()->has('client_id');
        $isResetFilter = request()->boolean('reset_filter');
        $selectedClientId = $hasClientParam
            ? (int) request('client_id', 0)
            : (int) session($sessionKey, 0);

        $clients = Client::query()
            ->select(['id', 'name'])
            ->where('id', '!=', 1)
            ->when(in_array((int) (auth()->user()->jabatan_id ?? 0), [35, 20], true), function ($query) {
                $allowedJabatanIds = ((int) (auth()->user()->jabatan_id ?? 0) === 35)
                    ? [8, 11, 16, 17, 18]
                    : [9, 10, 34, 36];

                $query->whereHas('kerjasama', function ($kerjasamaQuery) use ($allowedJabatanIds) {
                    $kerjasamaQuery->whereIn('id', User::query()
                        ->select('kerjasama_id')
                        ->whereIn('jabatan_id', $allowedJabatanIds)
                        ->whereNotNull('kerjasama_id'));
                });
            })
            ->orderBy('name')
            ->get();

        if ($isResetFilter) {
            $selectedClientId = 0;
            session()->forget($sessionKey);
        }

        if ($selectedClientId <= 0 || !$clients->contains('id', $selectedClientId)) {
            $selectedClientId = 0;
            session()->forget($sessionKey);
        } else {
            session()->put($sessionKey, $selectedClientId);
        }

        $selectedMode = (string) request('mode', (string) session($modeSessionKey, 'pengajuan'));
        if (!in_array($selectedMode, ['pengajuan', 'riwayat'], true)) {
            $selectedMode = 'pengajuan';
        }
        session()->put($modeSessionKey, $selectedMode);

        $dueDate = RekapDueDateSetting::latest()->first();
        $isAfterDueDate = $this->isSubmissionLockedByDueDate();

        return view('spv_w_view.index', [
            'clients' => $clients,
            'selectedClientId' => $selectedClientId,
            'selectedMode' => $selectedMode,
            'dueDate' => $dueDate,
            'isAfterDueDate' => $isAfterDueDate,
            'isSubmissionLocked' => $isAfterDueDate,
        ]);
    }
}
