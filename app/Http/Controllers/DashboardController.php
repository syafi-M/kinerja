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
        

        $news =  News::all();
        $now = Carbon::now()->format('Y-m-d');
        $now1 = Carbon::now()->subDay()->format('Y-m-d');
        $hitungNews = News::whereDate('tanggal_lihat', '<=', $now)->whereDate('tanggal_tutup', '>=', $now)->get();
        $lembur = Lembur::latest('jam_selesai')->get();
        $kerjasama = Kerjasama::all();
        $absen = Absensi::all();
        $warn = Absensi::where('user_id', Auth::user()->id)->where('absensi_type_pulang', 'Tidak Absen Pulang')->whereMonth('tanggal_absen', Carbon::now()->month)->get();
        $sholat = Absensi::where('user_id', Auth::user()->id)->firstWhere('absensi_type_pulang', null);
        $cekAbsen = Absensi::where('user_id', Auth::user()->id)->where('tanggal_absen', Carbon::now()->format('Y-m-d'))->where('absensi_type_pulang', null)->get();
        $rate = Rating::all();
        $user = Auth::user()->id;
        $point = Point::all();
        $shift = Shift::all();
        $izin = Izin::where('user_id', Auth::user()->id)->get();
        $jadwalUser = JadwalUser::all();
        $harLok = Lokasi::where('client_id', Auth::user()->kerjasama->client_id)->first();
        $isModal = Session::get('is_modal', false);
        return view('dashboard', [
            'absen' => $absen,
            'lembur' => $lembur,
            'kerjasama' => $kerjasama,
            'rate' => $rate,
            'user' => $user,
            'point' => $point,
            'harLok' => $harLok,
            'shift' => $shift,
            'izin' => $izin,
            'jadwalUser' => $jadwalUser,
            'sholat' => $sholat,
            'isModal' => $isModal,
            'news' => $news,
            'hitungNews' => $hitungNews,
            'cekAbsen' => $cekAbsen,
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
