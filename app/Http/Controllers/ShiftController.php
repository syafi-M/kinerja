<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShiftRequest;
use App\Models\Client;
use App\Models\Jabatan;
use App\Models\Shift;

class ShiftController extends Controller
{
    public function index()
    {
        $shift = Shift::orderBy('client_id', 'asc')->paginate(99999);

        return view('admin.shift.index', compact('shift'));
    }

    public function create()
    {
        $jabatan = Jabatan::all();
        $client = Client::all();

        return view('admin.shift.create', compact('jabatan', 'client'));
    }

    public function store(ShiftRequest $request)
    {
        // dd($request->all());
        $isOvernight = $request->boolean('is_overnight');
        $shift = new Shift;
        $shift = [
            'jabatan_id' => $request->jabatan_id,
            'client_id' => $request->client_id,
            'shift_name' => $request->shift_name,
            'jam_start' => $request->jam_start,
            'jam_end' => $request->jam_end,
            'is_overnight' => $isOvernight,
            'hari' => json_encode($request->hari),
        ];

        Shift::create($shift);
        toastr()->success('Shift berhasil ditambahkan', 'success');

        return to_route('shift.index');
    }

    public function show($id)
    {
        $shift = Shift::find($id);
        if ($shift != null) {
            return view('admin.shift.show', compact('shift'));
        }

        toastr()->error('Data Tidak Ditemukan', 'error');

        return redirect()->back();
    }

    public function edit($id)
    {
        $jabatan = Jabatan::all();
        $client = Client::all();
        $shift = Shift::find($id);
        $selectedDays = json_decode($shift->hari, true) ?? [];
        if ($shift != null) {
            return view('admin.shift.edit', compact('shift', 'jabatan', 'client', 'selectedDays'));
        }
        toastr()->error('Data tidak ditemukan', 'error');

        return redirect()->back();
    }

    public function update(ShiftRequest $request, $id)
    {
        $isOvernight = $request->boolean('is_overnight');
        $shift = [
            'jabatan_id' => $request->jabatan_id,
            'client_id' => $request->client_id,
            'shift_name' => $request->shift_name,
            'jam_start' => $request->jam_start,
            'jam_end' => $request->jam_end,
            'is_overnight' => $isOvernight,
            'hari' => json_encode($request->hari),
        ];

        Shift::findOrFail($id)->update($shift);
        toastr()->success('Shift berhasil diupdate', 'success');

        return to_route('shift.index');
    }

    public function destroy($id)
    {
        $shift = Shift::find($id);
        if ($shift != null) {
            $shift->delete();
            toastr()->warning('Data Shift Terhapus', 'warning');

            return redirect()->back();
        }
        toastr()->error('Data Tidak Ditemukan', 'error');

        return redirect()->back();
    }
}
