<?php

namespace App\Http\Controllers\LEADER_Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\OvertimeStoreRequest;
use App\Models\Overtime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OvertimeApplicationController extends Controller
{
    public function create()
    {

        $users = User::where('kerjasama_id', auth()->user()->kerjasama_id)->whereHas('Jabatan', function ($q) {
            $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
        })->get();

        return view('leader_view.data_rekap.lembur.create', [
            'users' => $users,
        ]);
    }

    public function store(OvertimeStoreRequest $request)
    {
        try {
            $data = $request->validated();
            Overtime::create($data);
            toastr()->success('Lembur Berhasil Disimpan!', 'success');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show(Request $request, $id)
    {
        // dd($request->all());
        $startDate = Carbon::now()->startOfMonth()->startOfDay();
        $endDate = Carbon::now()->startOfMonth()->addDays(24)->endOfDay();

        $overtimes = Overtime::whereHas('user', function ($q) {
            $q->where('kerjasama_id', auth()->user()->kerjasama_id);
        })
            ->whereBetween('date_overtime', [$startDate, $endDate])
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->month, function ($q) use ($request) {
                $q->whereMonth('date_overtime', $request->month);
            })
            ->paginate(15)
            ->withQueryString();

        return view('leader_view.data_rekap.lembur.show', [
            'overtimes' => $overtimes
        ]);
    }

    public function edit($id)
    {
        $users = User::where('kerjasama_id', auth()->user()->kerjasama_id)->whereHas('Jabatan', function ($q) {
            $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
        })->get();
        $overtime = Overtime::findOrFail($id);
        return view('leader_view.data_rekap.lembur.edit', [
            'overtime' => $overtime,
            'users' => $users
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only([
            'user_id',
            'date_overtime',
            'desc',
            'type_overtime',
            'type_overtime_manual'
        ]);
        Overtime::findOrFail($id)->update($data);
        toastr()->success('Lembur Berhasil Diupdate!', 'success');
        return to_route('overtime-application.show', 1);
    }

    public function destroy($id)
    {
        Overtime::findOrFail($id)->delete();
        toastr()->warning('Lembur Berhasil Dihapus!', 'warning');
        return redirect()->back();
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
        Overtime::where("id", $id)->update(["status" => "Di Ajukan"]);
        toastr()->success('Lembur Berhasil Di Ajukan!', 'success');
        return redirect()->back();
    }

    public function bulkStatus()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        Overtime::whereHas('user', function ($q) {
            $q->where('kerjasama_id', auth()->user()->kerjasama_id);
        })
            ->whereBetween('date_overtime', [$startDate, $endDate])
            ->where('status', null)->orWhere('status', 'pending')->update(["status" => "Di Ajukan"]);
        // Overtime::where("id", $key->id)->where('status', '!=', 'rejected')->update(["status" => "Di Ajukan"]);
        toastr()->success('Berhasil mengajukan semua lembur!', 'success');
        return redirect()->back();
    }
}
