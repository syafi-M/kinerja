<?php

namespace App\Http\Controllers\SPVW_Controller;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\FinishedTraining;
use App\Models\Kerjasama;
use App\Models\Overtime;
use App\Models\PerformanceCuts;
use App\Models\PersonIn;
use App\Models\PersonOut;
use App\Models\RekapDueDateSetting;
use App\Models\RekapPenaltyExemption;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DataRekapController extends Controller
{
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
        $isAfterDueDate = $dueDate ? Carbon::today()->gt(Carbon::parse($dueDate->due_date)) : false;

        $isExempted = RekapPenaltyExemption::where('user_id', auth()->id())
            ->where('is_active', true)
            ->exists();

        $isRekapEmpty = $this->isCurrentMonthRekapEmpty();

        return view('spv_w_view.index', [
            'clients' => $clients,
            'selectedClientId' => $selectedClientId,
            'selectedMode' => $selectedMode,
            'dueDate' => $dueDate,
            'isAfterDueDate' => $isAfterDueDate,
            'isExempted' => $isExempted,
            'isRekapEmpty' => $isRekapEmpty,
        ]);
    }

    public function exemptSelf()
    {
        $code = strtoupper((string) auth()->user()->jabatan->code_jabatan);
        if (!in_array($code, ['CO-SCR'], true)) {
            abort(403);
        }

        $dueDate = RekapDueDateSetting::latest()->first();
        if (!$dueDate) {
            toastr()->error('Due date rekap belum diatur oleh admin.', 'error');
            return back();
        }

        if (Carbon::today()->gt(Carbon::parse($dueDate->due_date))) {
            toastr()->error('Masa aktivasi pengecualian sudah lewat due date.', 'error');
            return back();
        }

        if (!$this->isCurrentMonthRekapEmpty()) {
            toastr()->error('Pengecualian hanya bisa diaktifkan jika data rekap kosong.', 'error');
            return back();
        }

        RekapPenaltyExemption::updateOrCreate(
            ['user_id' => auth()->id()],
            ['is_active' => true, 'source' => 'leader_self']
        );

        toastr()->success('Pengecualian penalty berhasil diaktifkan.', 'success');
        return back();
    }

    private function isCurrentMonthRekapEmpty(): bool
    {
        $user = auth()->user();
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $overtimeExists = Overtime::whereBetween('date_overtime', [$start, $end])
            ->whereHas('user', function ($q) use ($user) {
                $q->where('kerjasama_id', $user->kerjasama_id)
                    ->whereHas('jabatan', function ($jabatanQ) use ($user) {
                        $jabatanQ->where('type_jabatan', $user->jabatan->type_jabatan);
                    });
            })
            ->exists();

        $personOutExists = PersonOut::whereBetween('out_date', [$start, $end])
            ->whereHas('user', function ($q) use ($user) {
                $q->where('kerjasama_id', $user->kerjasama_id)
                    ->whereHas('jabatan', function ($jabatanQ) use ($user) {
                        $jabatanQ->where('type_jabatan', $user->jabatan->type_jabatan);
                    });
            })
            ->exists();

        $personInExists = PersonIn::whereBetween('date_in', [$start, $end])
            ->where('client_id', $user->kerjasama->client_id)
            ->whereHas('jabatan', function ($q) use ($user) {
                $q->where('type_jabatan', $user->jabatan->type_jabatan);
            })
            ->exists();

        $cuttingExists = PerformanceCuts::whereBetween('date_cut', [$start, $end])
            ->whereHas('user', function ($q) use ($user) {
                $q->where('kerjasama_id', $user->kerjasama_id)
                    ->whereHas('jabatan', function ($jabatanQ) use ($user) {
                        $jabatanQ->where('type_jabatan', $user->jabatan->type_jabatan);
                    });
            })
            ->exists();

        $finishedTrainingExists = FinishedTraining::whereBetween('date_finish_train', [$start, $end])
            ->whereHas('user', function ($q) use ($user) {
                $q->where('kerjasama_id', $user->kerjasama_id)
                    ->whereHas('jabatan', function ($jabatanQ) use ($user) {
                        $jabatanQ->where('type_jabatan', $user->jabatan->type_jabatan);
                    });
            })
            ->exists();

        return !($overtimeExists || $personOutExists || $personInExists || $cuttingExists || $finishedTrainingExists);
    }
}
