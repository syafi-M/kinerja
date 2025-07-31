<?php

namespace App\Http\Controllers;

use App\Http\Requests\IzinRequest;
use App\Models\Izin;
use App\Models\Shift;
use App\Models\Kerjasama;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    public function index()
    {
        $user = Auth::user()->id;
        $izin = Izin::where('user_id', $user)->paginate(30);

        return view('absensi.izin.index', ['izin' => $izin]);
    }

    public function indexLead()
    {
        $user = Auth::user()->kerjasama_id;
        if(auth()->user()->id == 175) {
            
        $izin = Izin::latest()->orderBy('kerjasama_id', 'asc')->paginate(40);
        }else {
            
        $izin = Izin::where('kerjasama_id', $user)->paginate(30);
        }

        return view('leader_view.absen.izin', ['izin' => $izin]);
    }

    public function indexAdmin()
    {
        $izin = Izin::latest()->paginate(50);
        $kerja = Kerjasama::all();
        return view('admin.absen.izin', compact('izin', 'kerja'));
    }

    public function show($id)
    {
        $izinId = Izin::findOrFail($id);
        return view('absensi.izin.detail', compact('izinId'));
    }

    public function create()
    {
        $shift = Shift::all();

        return view('absensi.izin.create', compact('shift'));
    }

    public function store(IzinRequest $request)
    {
        $izin = new Izin();

        $izin = [
            'user_id' => $request->user_id,
            'kerjasama_id' => $request->kerjasama_id,
            'shift_id' => $request->shift_id,
            'alasan_izin' => $request->alasan_izin,
            'img' => $request->img,
        ];

        if ($request->hasFile('img')) {
            $izin['img'] = UploadImage($request, 'img');
        }else{
            toastr()->error('Image harus ditambahkan', 'error');
        }
            Izin::create($izin);
            toastr()->success('Data izin Berhasil Disimpan', 'success');
            return redirect()->to(route('izin.index'));
    }

    public function updateSuccess($id)
    {
        $izinId = Izin::findOrFail($id);
        $izinId->approve_status = 'accept';
        $izinId->save();
        return redirect()->back()->with('message', 'Berhasil Approve');

    }

    public function updateDenied($id)
    {
        $izinId = Izin::findOrFail($id);
        $izinId->approve_status = "denied";
        $izinId->save();
        return redirect()->back()->with('msgError', 'Berhasil Menolak Izin');
    }

    public function deleteAdmin($id)
    {
        $izinId = Izin::findOrFail($id);
        $izinId->delete();
        toastr()->warning('Data Izin Dihapus', 'warning');
        return redirect()->back();
    }
}
