<?php

namespace App\Http\Controllers\LEADER_Controller;

use App\Http\Controllers\Controller;
use App\Models\FinishedTraining;
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
        $dueDate = RekapDueDateSetting::latest()->first();
        $isAfterDueDate = $dueDate ? Carbon::today()->gt(Carbon::parse($dueDate->due_date)) : false;

        $isExempted = RekapPenaltyExemption::where('user_id', auth()->id())
            ->where('is_active', true)
            ->exists();

        $isRekapEmpty = $this->isCurrentMonthRekapEmpty();

        return view('leader_view.data_rekap.index', [
            'dueDate' => $dueDate,
            'isAfterDueDate' => $isAfterDueDate,
            'isExempted' => $isExempted,
            'isRekapEmpty' => $isRekapEmpty,
        ]);
    }

    public function exemptSelf()
    {
        $code = strtoupper((string) auth()->user()->jabatan->code_jabatan);
        if (!in_array($code, ['CO-CS', 'CO-SCR'], true)) {
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
