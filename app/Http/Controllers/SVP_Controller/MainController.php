<?php

namespace App\Http\Controllers\SVP_Controller;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Laporan;
use App\Models\Lembur;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Kerjasama;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    public function indexAbsen(Request $request)
    {
        $filter = $request->search;
        $filterMitra = $request->mitra;
        $filter2 = Carbon::parse($filter);
        
        $mitra = Kerjasama::with('client')->get();
        
        $tanggalIki = Carbon::now()->format('Y-m-d') == '2024-05-24' && Auth::user()->devisi_id == 18;
        
        $kerjasama = Auth::user()->kerjasama_id;
        $absenQue = Absensi::latest();
        
        if ($filter) {
            $absenQ = $absenQue->whereMonth('tanggal_absen', $filter2->month);
            $absen = $absenQ->paginate(50)->appends($request->except('page'));;
        }else{
            $mon = Carbon::now()->month;
            $absen = Absensi::orderBy('tanggal_absen', 'desc')->orderBy('kerjasama_id', 'desc')->whereMonth('tanggal_absen', $mon)->latest()->paginate(31);
        }
        
        return view('spv_view/absen/index', compact('absen', 'mitra', 'filterMitra', 'filter'));
    }

    public function indexLaporan()
    {
        $laporan = Laporan::paginate(15);
        return view('spv_view/laporan/index', compact('laporan'));
    }

    public function indexUser()
    {
        $kerjasama = Auth::user()->kerjasama_id;
        $user = User::where('kerjasama_id', $kerjasama)->paginate(15);
        return view('spv_view/laporan/index', compact('user'));
    }

    public function indexLembur()
    {
        $kerjasama = Auth::user()->kerjasama_id;
        $lembur = Lembur::where('kerjasama_id', $kerjasama)->paginate(15);
        return view('leader_view.lembur.index', compact('lembur'));
    }
}
