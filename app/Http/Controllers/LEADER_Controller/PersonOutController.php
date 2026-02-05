<?php

namespace App\Http\Controllers\LEADER_Controller;

use App\Http\Controllers\Controller;
use App\Models\PersonOut;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PersonOutController extends Controller
{
    public function create()
    {
        $users = User::where('id', '!=', auth()->user()->id)->where('kerjasama_id', auth()->user()->kerjasama_id)->whereHas('Jabatan', function ($q) {
            $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
        })->get();
        return view('leader_view.data_rekap.person_out.create', [
            'users' => $users
        ]);
    }

    public function show(Request $request, $id)
    {
        $startDate = Carbon::now()->startOfMonth()->startOfDay();
        $endDate = Carbon::now()->startOfMonth()->addDays(24)->endOfDay();
        $personOut = PersonOut::with([
            'user' => function ($q) {
                $q->withTrashed();
            }
        ])->whereHas('user', function ($q) {
            $q->withTrashed()->where('kerjasama_id', auth()->user()->kerjasama_id);
        })->whereBetween('out_date', [$startDate, $endDate])
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->month, function ($q) use ($request) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $request->month);

                    $q->whereYear('out_date', $date->year)
                        ->whereMonth('out_date', $date->month);
                } catch (\Exception $e) {
                    throw $e;
                }
            })


            ->paginate(15)
            ->withQueryString();
        // dd($request->month, $personOut[0]);

        return view('leader_view.data_rekap.person_out.show', [
            'personOut' => $personOut
        ]);
    }

    public function store(Request $request)
    {

        try {
            $validated = $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'total_mk' => ['required'],
                'reason' => ['required', 'string', 'max:255'],
                'reason_manual' => ['nullable', 'string', 'max:255'],
                'out_date' => ['required', 'date'],
                'img' => ['required', 'image', 'max:2048'],
            ]);

            if ($request->hasFile('img')) {
                $validated['img'] = UploadImageV2($request, 'img');
            }

            PersonOut::create($validated);

            toastr()->success('Berhasil mengajukan data!', 'success');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($id)
    {
        $personOut = PersonOut::findOrFail($id);
        $users = User::where('id', '!=', auth()->user()->id)->where('kerjasama_id', auth()->user()->kerjasama_id)->whereHas('Jabatan', function ($q) {
            $q->where('type_jabatan', auth()->user()->jabatan->type_jabatan);
        })->get();
        return view('leader_view.data_rekap.person_out.edit', [
            'users' => $users,
            'personOut' => $personOut
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'total_mk' => ['required'],
                'reason' => ['required', 'string', 'max:255'],
                'reason_manual' => ['nullable', 'string', 'max:255'],
                'out_date' => ['required', 'date'],
                'img' => ['nullable', 'image', 'max:2048'],
            ]);

            DB::transaction(function () use ($validated, $request, $id) {

                $personOut = PersonOut::findOrFail($id);

                // prevent user_id mutation if you decided it must be immutable
                if ($personOut->user_id !== (int) $validated['user_id']) {
                    throw new \Exception('user_id cannot be changed');
                }

                // handle image replacement
                if ($request->hasFile('img')) {

                    // delete old image if exists
                    if ($personOut->img) {
                        $oldPath = 'public/images/' . $personOut->img;
                        if (Storage::exists($oldPath)) {
                            Storage::delete($oldPath);
                        }
                    }

                    // upload new image
                    $validated['img'] = UploadImageV2($request, 'img');
                } else {
                    // keep old image
                    unset($validated['img']);
                }

                $personOut->update($validated);
            });

            toastr()->success('Berhasil update data!', 'success');
            return redirect()->back();
        } catch (\Throwable $th) {
            report($th);
            toastr()->error('Gagal update data', 'error');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(PersonOut $personOut)
    {
        $personOut->delete();

        toastr()->success('Personil berhasil direcover', 'success');
        return redirect()->back();
    }

    public function changeStatus($id)
    {
        PersonOut::where("id", $id)->update(["status" => "Di Ajukan"]);
        toastr()->success('Personil Keluar Berhasil Di Ajukan!', 'success');
        return redirect()->back();
    }

    public function bulkStatus()
    {
        PersonOut::whereHas('user', function ($q) {
            $q->withTrashed()->where('kerjasama_id', auth()->user()->kerjasama_id);
        })
            ->where('status', null)->orWhere('status', 'pending')->update(["status" => "Di Ajukan"]);
        // Overtime::where("id", $key->id)->where('status', '!=', 'rejected')->update(["status" => "Di Ajukan"]);
        toastr()->success('Berhasil mengajukan semua personil keluar!', 'success');
        return redirect()->back();
    }

    public function fetchApi($id)
    {
        $personOut = PersonOut::with([
            'user' => function ($q) {
                $q->withTrashed();
            }
        ])->findOrFail($id);
        return response()->json([
            'message' => 'Get data by id',
            'data' => $personOut,
            'error' => ''
        ]);
    }
}
