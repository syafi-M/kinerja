<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kerjasama;
use App\Models\Lembur;
use App\Models\Rating;
use App\Models\Point;
use App\Models\Lokasi;
use App\Models\Shift;
use App\Models\Izin;
use App\Models\JadwalUser;
use App\Models\CheckPoint;
use App\Models\News;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\AbsensiNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        
    
        $now = Carbon::now()->format('Y-m-d');
        $news =  News::all();
        $rate = Rating::all();
        $shift = Shift::all();
        $user = Auth::user();
        $hitungNews = News::whereDate('tanggal_lihat', '<=', $now)->whereDate('tanggal_tutup', '>=', $now)->get();
        $lembur = Lembur::latest('jam_selesai')->get();
        
        $absen = Absensi::with(['user', 'shift', 'kerjasama', 'tipeAbsensi'])
            ->where('user_id', $user->id)
            ->where('absensi_type_pulang', null)
            ->get();
            
        $absenP = Absensi::with(['user', 'shift', 'kerjasama', 'tipeAbsensi'])
            ->where('user_id', $user->id)
            ->latest()->first();
            // dd($absenP);
        
        
        $warn = $absen->filter(function ($item) {
            return $item->absensi_type_pulang == 'Tidak Absen Pulang'
                && $item->tanggal_absen->month == Carbon::now()->month;
        });
        
        $sholat = $absen->where('tanggal_absen', Carbon::now()->format('Y-m-d'))->firstWhere('absensi_type_pulang', null);
        // dd($sholat);
        
        $cekAbsen = $absen->where('absensi_type_pulang', null)->where('tanggal_absen', Carbon::now()->format('Y-m-d'));
        
        // if(Auth::user()->id == 11){
        //     dd($absenP);
        // }
        
        $jadwalUser = JadwalUser::all();
        
        $izin = Izin::where('user_id', $user->id)->get();
        $harLok = Lokasi::where('client_id', $user->kerjasama->client_id)->first();
        $isModal = Session::pull('is_modal', false);
        $awalMinggu = Carbon::now()->startOfWeek();
        $akhirMinggu = Carbon::now()->endOfWeek()->subDays(1); // Mengurangi 2 hari untuk mendapatkan hari Jumat sebagai akhir minggu
        $cex = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
            ->where('user_id', Auth::user()->id)
            ->where('type_check', 'rencana')
            ->latest()
            ->first();
        $cex2 = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
            ->where('user_id', Auth::user()->id)
            ->where('type_check', 'dikerjakan')
            ->latest()
            ->first();
        $totcex = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
            ->where('type_check', 'dikerjakan')
            ->latest()
            ->get();
        // dd($cex, $cex2);
        return view('dashboard', [
            'absen' => $absen,
            'absenP' => $absenP,
            'lembur' => $lembur,
            'rate' => $rate,
            'user' => $user,
            'harLok' => $harLok,
            'shift' => $shift,
            'izin' => $izin,
            'jadwalUser' => $jadwalUser,
            'sholat' => $sholat,
            'isModal' => $isModal,
            'news' => $news,
            'hitungNews' => $hitungNews,
            'cekAbsen' => $cekAbsen,
            'cex' => $cex,
            'cex2' => $cex2,
            'totcex' => $totcex,
            'warn' => $warn
        ]);
    }
    
    public function sendTestEmail()
    {
        
        $user = Auth::user()->id; 
        $absensi = Absensi::latest()->where('user_id', $user)->first();
        if($absensi)
        {
            dd($absensi);
        }else{
            return "KOSONG BANG";
        }

    }

}
