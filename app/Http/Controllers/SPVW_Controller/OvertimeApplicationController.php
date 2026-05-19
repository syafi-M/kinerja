<?php

namespace App\Http\Controllers\SPVW_Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\OvertimeStoreRequest;
use App\Models\Overtime;
use App\Models\RekapDueDateSetting;
use App\Models\User;
use App\Notifications\OvertimeSubmitted;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class OvertimeApplicationController extends Controller
{
    public function create()
    {
        $users = User::select(['id', 'name', 'nama_lengkap'])
            ->where('role_id', '!=', 2)
            ->where('kerjasama_id', '!=', 1)
            ->when($this->selectedClientId() > 0, fn($q) => $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $this->selectedClientId())))
            ->orderBy('nama_lengkap')
            ->get();
        
        return view('spv_w_view.lembur.create', [
            'users' => $users,
        ]);
    }

    public function store(OvertimeStoreRequest $request)
    {
        try {
            $data = $request->validated();
            Overtime::create($data);
            return redirect()->back()->with('toast', [
                'type' => 'success',
                'message' => 'Lembur berhasil disimpan!',
            ]);
        } catch (\Throwable $th) {
            report($th);
            return redirect()->back()->withInput()->with('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data lembur. Silakan coba lagi.',
            ]);
        }
    }

    public function history(Request $request)
    {
        $isSubmissionLocked = $this->isSubmissionLockedByDueDate();

        $overtimes = Overtime::with(['user:id,name,nama_lengkap'])
            ->select(['id', 'user_id', 'date_overtime', 'desc', 'type_overtime', 'type_overtime_manual', 'status', 'created_at'])
            ->whereHas('user', function ($q) {
                $q->where('role_id', '!=', 2)
                    ->where('kerjasama_id', '!=', 1)
                    ->when($this->selectedClientId() > 0, fn($userQuery) => $userQuery->whereHas('kerjasama', fn($k) => $k->where('client_id', $this->selectedClientId())));
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->month, function ($q) use ($request) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $request->month);
                    $q->whereYear('date_overtime', $date->year)
                        ->whereMonth('date_overtime', $date->month);
                } catch (\Throwable $th) {
                    // Ignore invalid month and keep base query
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('spv_w_view.lembur.show', [
            'overtimes' => $overtimes,
            'isSubmissionLocked' => $isSubmissionLocked,
            'canBulkSubmit' => !$isSubmissionLocked && $this->hasBulkSubmittableOvertime(),
        ]);
    }

    public function show(Request $request, $id)
    {
        return $this->history($request);
    }

    public function edit($id)
    {
        $users = User::select(['id', 'name', 'nama_lengkap'])
            ->where('role_id', '!=', 2)
            ->where('kerjasama_id', '!=', 1)
            ->when($this->selectedClientId() > 0, fn($q) => $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $this->selectedClientId())))
            ->orderBy('nama_lengkap')
            ->get();
        $overtime = Overtime::findOrFail($id);
        return view('spv_w_view.lembur.edit', [
            'overtime' => $overtime,
            'users' => $users
        ]);
    }

    public function update(OvertimeStoreRequest $request, $id)
    {
        $data = $request->validated();
        Overtime::findOrFail($id)->update($data);
        return to_route('spvw.overtime-application.history', array_filter([
            'client_id' => $this->selectedClientId(),
        ]))->with('toast', [
            'type' => 'success',
            'message' => 'Lembur berhasil diupdate!',
        ]);
    }

    public function destroy($id)
    {
        Overtime::findOrFail($id)->delete();
        return redirect()->back()->with('toast', [
            'type' => 'warning',
            'message' => 'Lembur berhasil dihapus!',
        ]);
    }

    public function fetchApi($id)
    {
        $overtime = Overtime::with('user')->findOrFail($id);
        return response()->json([
            'message' => 'Get data by id',
            'data' => $overtime,
            'error' => ''
        ]);
    }

    public function changeStatus($id)
    {
        if ($this->isSubmissionLockedByDueDate()) {
            return redirect()->back()->with('toast', [
                'type' => 'info',
                'message' => 'Masa pengajuan rekap bulan ini sudah ditutup. Silakan tunggu bulan berikutnya.',
            ]);
        }

        $overtime = Overtime::findOrFail($id);

        $overtime->update([
            'status' => 'Di Ajukan'
        ]);


        $targetCode = auth()->user()->jabatan->code_jabatan == 'CO-CS'
            ? 'SPV'
            : (auth()->user()->jabatan->code_jabatan == 'CO-SCR'
                ? 'MARKETING'
                : null);

        if ($targetCode) {
            $users = User::whereHas(
                'jabatan',
                fn($q) =>
                $q->where('code_jabatan', $targetCode)
            )->get();

            Notification::send(
                $users,
                new OvertimeSubmitted($overtime)
            );
        }
        return redirect()->back()->with('toast', [
            'type' => 'success',
            'message' => 'Lembur berhasil diajukan!',
        ]);
    }

    public function bulkStatus()
    {
        if ($this->isSubmissionLockedByDueDate()) {
            return back()->with('toast', [
                'type' => 'info',
                'message' => 'Masa pengajuan rekap bulan ini sudah ditutup. Silakan tunggu bulan berikutnya.',
            ]);
        }

        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        $targetCode = auth()->user()->jabatan->code_jabatan == 'CO-CS'
            ? 'SPV'
            : (auth()->user()->jabatan->code_jabatan == 'CO-SCR'
                ? 'MARKETING'
                : null);

        $overtimes = Overtime::whereHas('user', function ($q) {
            $q->where('role_id', '!=', 2)
                ->where('kerjasama_id', '!=', 1)
                ->when($this->selectedClientId() > 0, fn($userQuery) => $userQuery->whereHas('kerjasama', fn($k) => $k->where('client_id', $this->selectedClientId())));
        })
            ->whereBetween('date_overtime', [$startDate, $endDate])
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereRaw('LOWER(status) = ?', ['pending']);
            })
            ->get();

        if ($overtimes->isEmpty()) {
            return back()->with('toast', [
                'type' => 'info',
                'message' => 'Tidak ada data lembur untuk diajukan.',
            ]);
        }

        Overtime::whereIn('id', $overtimes->pluck('id'))
            ->update(['status' => 'Di Ajukan']);

        // ambil approver
        $approvers = User::whereHas(
            'jabatan',
            fn($q) =>
            $q->where('code_jabatan', $targetCode)
        )->get();

        foreach ($overtimes as $overtime) {
            Notification::send(
                $approvers,
                new OvertimeSubmitted($overtime)
            );
        }


        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Berhasil mengajukan semua lembur!',
        ]);
    }

    private function isSubmissionLockedByDueDate(): bool
    {
        $dueDate = RekapDueDateSetting::latest()->first();

        return $dueDate !== null
            && Carbon::today()->gt(Carbon::parse($dueDate->due_date)->endOfDay());
    }

    private function hasBulkSubmittableOvertime(): bool
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        return Overtime::whereHas('user', function ($q) {
            $q->where('role_id', '!=', 2)
                ->where('kerjasama_id', '!=', 1)
                ->when($this->selectedClientId() > 0, fn($userQuery) => $userQuery->whereHas('kerjasama', fn($k) => $k->where('client_id', $this->selectedClientId())));
        })
            ->whereBetween('date_overtime', [$startDate, $endDate])
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereRaw('LOWER(status) = ?', ['pending']);
            })
            ->exists();
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
