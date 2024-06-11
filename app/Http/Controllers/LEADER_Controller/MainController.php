<?php

namespace App\Http\Controllers\LEADER_Controller;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Laporan;
use App\Models\Lembur;
use App\Models\User;
use App\Models\Divisi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    
    public function indexAbsen(Request $request)
    {
        
        
        $filter = $request->search;
        $filter2 = Carbon::parse($filter);
        
        $tanggalIki = Carbon::now()->format('Y-m-d') == '2024-05-24' && Auth::user()->devisi_id == 18;
        // dd($tanggalIki);
        
        if ($filter) {
            $kerjasama = Auth::user()->kerjasama_id;
            $absen = Absensi::latest()->where('kerjasama_id', $kerjasama)->whereMonth('tanggal_absen', $filter2->month)->paginate(1000);
        }else{
            $mon = Carbon::now()->month;
            $kerjasama = Auth::user()->kerjasama_id;
            if(Auth::user()->divisi->jabatan->code_jabatan == "CO-CS"){
                $codeJabatan = ['OCS', 'CO-CS'];
                $absen = Absensi::latest()->where('kerjasama_id', $kerjasama)->whereMonth('tanggal_absen', $mon)->whereHas('user.divisi.jabatan', function ($query) use ($codeJabatan) {
                            $query->whereIn('code_jabatan', $codeJabatan);
                        })->paginate(31);
            }else if(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR"){
                $codeJabatan = ['SCR', 'CO-SCR'];
                $absen = Absensi::latest()->where('kerjasama_id', $kerjasama)->whereMonth('tanggal_absen', $mon)->whereHas('user.divisi.jabatan', function ($query) use ($codeJabatan) {
                            $query->whereIn('code_jabatan', $codeJabatan);
                        })->paginate(31);
            }
            else{
                $codeJabatan = ['SCR', 'CO-SCR'];
                $absen = Absensi::latest()->where('kerjasama_id', $kerjasama)->whereMonth('tanggal_absen', $mon)->paginate(31);
            }
        }
        
        if(!$tanggalIki){
            return view('leader_view/absen/index', compact('absen'));
        } else {
            abort(500);
            return view('leader_view/absen/index');
        }
    }

    public function indexLaporan()
    {
        $kerjasama = Auth::user()->kerjasama->client_id;
        $laporan = Laporan::where('client_id', $kerjasama)->paginate(30);
        return view('leader_view/laporan/index', compact('laporan'));
    }
    public function showLaporan($id)
    {
        $kerjasama = Auth::user()->kerjasama->client_id;
        $laporan = Laporan::findOrfail($id);
        return view('leader_view/laporan/show', compact('laporan'));
    }

    public function indexUser(Request $request)
    {
        $filterDivisi = $request->input('divisi');
        
        $divisi = Divisi::all();
        $kerjasama = Auth::user()->kerjasama_id;
        
        if($filterDivisi){
            $user = User::where('kerjasama_id', $kerjasama)->where('devisi_id', $filterDivisi)->paginate(90);
        }else{
            if(Auth::user()->role_id == 2 || Auth::user()->divisi->jabatan->code_jabatan == 'DIREKSI'){
                $user = User::paginate(9999999);
            }else{
                if(Auth::user()->divisi->jabatan->code_jabatan == "CO-CS"){
                   $codeJabatan = ['OCS', 'CO-CS'];
                    $user = User::where('kerjasama_id', $kerjasama)->whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                        $query->whereIn('code_jabatan', $codeJabatan);
                    })->orderBy('nama_lengkap', 'asc')->paginate(30);
                }else if(Auth::user()->divisi->jabatan->code_jabatan == "MITRA") {
                    $user = User::where('kerjasama_id', $kerjasama)->orderBy('nama_lengkap', 'asc')->paginate(30);
                }else{
                    $codeJabatan = ['SCR', 'CO-SCR'];
                    $user = User::where('kerjasama_id', $kerjasama)->whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                        $query->whereIn('code_jabatan', $codeJabatan);
                    })->orderBy('nama_lengkap', 'asc')->paginate(30);
                }
            }
        }
        
        return view('leader_view/user/index', compact('user', 'divisi'));
    }

    public function indexLembur()
    {
        $kerjasama = Auth::user()->kerjasama_id;
        if(Auth::user()->divisi->jabatan->code_jabatan == "CO-CS"){
                $codeJabatan = ['OCS', 'CO-CS'];
                $lembur = Lembur::latest()->where('kerjasama_id', $kerjasama)->whereHas('user.divisi.jabatan', function ($query) use ($codeJabatan) {
                            $query->whereIn('code_jabatan', $codeJabatan);
                        })->paginate(31);
        }else if(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR"){
                $codeJabatan = ['SCR', 'CO-SCR'];
                $lembur = Lembur::latest()->where('kerjasama_id', $kerjasama)->whereHas('user.divisi.jabatan', function ($query) use ($codeJabatan) {
                            $query->whereIn('code_jabatan', $codeJabatan);
                        })->paginate(31);
            
        }else{
            $lembur = Lembur::where('kerjasama_id', $kerjasama)->paginate(30);
        }
        return view('leader_view/lembur/index', compact('lembur'));
    }
}
