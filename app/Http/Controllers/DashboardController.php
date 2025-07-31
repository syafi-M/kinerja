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
use App\Models\SlipGaji;
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
        // Simpan waktu sekarang dan user dalam variabel
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $user  = Auth::user();
        
        // Ambil semua data news (pertimbangkan pagination jika data sangat banyak)
        $news = News::all();
        
        // Hitung news yang masih berlaku berdasarkan tanggal
        $hitungNews = News::whereDate('tanggal_lihat', '<=', $today)
            ->whereDate('tanggal_tutup', '>=', $today)
            ->get();
        
        // Ambil data lembur dengan sorting berdasarkan 'jam_selesai'
        $lembur = Lembur::latest('jam_selesai')->get();
        
        // Buat dasar query absensi untuk user
        $absenQuery = Absensi::with(['user', 'shift', 'kerjasama', 'tipeAbsensi'])
            ->where('user_id', $user->id);
        
        // Ambil data absensi yang belum melakukan absensi pulang
        $absen = (clone $absenQuery)
            ->whereNull('absensi_type_pulang')
            ->get();
        
        // Ambil absensi pada rentang waktu dari kemarin hingga hari ini
        $absenP = (clone $absenQuery)
            ->whereBetween('created_at', [
                Carbon::yesterday()->startOfDay(), 
                Carbon::today()->endOfDay()
            ])
            ->latest()
            ->first();
        
        // Jika absensiP ada, ambil lokasi berdasarkan client_id dari kerjasama
        $lok = ($absenP && $absenP->kerjasama)
            ? Lokasi::with('client')->firstWhere('client_id', $absenP->kerjasama->client_id)
            : null;
        
        // Filter absensi untuk mengambil yang memiliki tipe "Tidak Absen Pulang" 
        // dan terjadi di bulan berjalan. Jika memungkinkan, filter ini juga bisa dilakukan di query.
        $currentMonth = $now->month;
        $warn = $absen->filter(function ($item) use ($currentMonth) {
            return $item->absensi_type_pulang === ''
                && $item->tanggal_absen->month === $currentMonth;
        });
        
        // Ambil absensi hari ini untuk kondisi sholat (misal belum melakukan absensi pulang)
        $sholat = (clone $absenQuery)
            ->where('tanggal_absen', $today)
            ->whereNull('absensi_type_pulang')
            ->first();
        
        // Atau jika ingin menggunakan koleksi yang sudah diambil sebelumnya:
        $cekAbsen = $absen->where('tanggal_absen', $today);
        
        // Ambil data izin untuk user terkait
        $izin = Izin::with('user')
            ->where('user_id', $user->id)
            ->where('updated_at', Carbon::now()->format('Y-m-d'))
            ->latest()
            ->first();
        if($izin) {
            $status = $izin->approve_status;
        } else {
            $status = '';
        }
        $statusClass = $status == 'process' ? 'bg-yellow-500' : ($status == 'accept' ? 'bg-green-500' : 'bg-red-500');
        $statusMessage = $status == 'process' ? 'Izin Masih Dalam Proses !!' : ($status == 'accept' ? 'Izin Sudah Disetujui !!' : 'Izin Anda Ditolak !!');
        
        // Tentukan rentang tanggal untuk pengecekan CheckPoint
        $awalMinggu = $now->copy()->subWeek()->startOfWeek()->addDays(5);
        $akhirMinggu = $now->copy()->endOfWeek()->subDays(2);
        
        // Ambil CheckPoint dengan tipe 'rencana'
        $cex = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
            ->where('user_id', $user->id)
            ->where('type_check', 'rencana')
            ->latest()
            ->first();
        
        // Ambil CheckPoint dengan tipe 'dikerjakan'
        $cex2 = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
            ->where('user_id', $user->id)
            ->where('type_check', 'dikerjakan')
            ->latest()
            ->first();
        
        // Ambil CheckPoint tipe 'dikerjakan' untuk bulan berjalan
        $startOfMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();
        $totcex = CheckPoint::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('type_check', 'dikerjakan')
            ->latest()
            ->get();
            
        $sholatSaatIni = null;
        
        if ($sholat) {
            $waktuSekarang = Carbon::now()->format('H:i');
    
            $waktuSholat = [
                'subuh' => ['start' => '03:30', 'end' => '04:00', 'status' => $sholat->subuh],
                'dzuhur' => ['start' => '11:20', 'end' => '14:00', 'status' => $sholat->dzuhur],
                'asar' => ['start' => '15:00', 'end' => '17:00', 'status' => $sholat->asar],
                'magrib' => ['start' => '17:20', 'end' => '18:30', 'status' => $sholat->magrib],
                'isya' => ['start' => '18:30', 'end' => '21:00', 'status' => $sholat->isya],
            ];
    
            $waktuSholat2 = [
                'dzuhur' => ['start' => '11:20', 'end' => '14:00', 'status' => $sholat->dzuhur],
                'magrib' => ['start' => '17:20', 'end' => '18:30', 'status' => $sholat->magrib],
            ];
    
            if (Auth::user()->kerjasama_id == 1) {
                foreach ($waktuSholat as $namaSholat => $waktu) {
                    if (
                        $waktu['status'] === 0 &&
                        $waktuSekarang >= $waktu['start'] &&
                        $waktuSekarang <= $waktu['end']
                    ) {
                        $sholatSaatIni = $namaSholat;
                        break;
                    }
                }
            } else {
                foreach ($waktuSholat2 as $namaSholat => $waktu) {
                    if (
                        $waktu['status'] === 0 &&
                        $waktuSekarang >= $waktu['start'] &&
                        $waktuSekarang <= $waktu['end']
                    ) {
                        $sholatSaatIni = $namaSholat;
                        break;
                    }
                }
            }
        }
        $rillSholat = $sholatSaatIni && (
            Auth::user()->kerjasama_id == 1 ||
            Auth::user()->divisi->jabatan->code_jabatan == "CO-CS" ||
            Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR"
        );
    
        $luweh1Dino = false;
        if ($absenP) {
            $luweh1Dino = Carbon::createFromFormat('Y-m-d, H:i:s', $absenP->created_at->format('Y-m-d, H:i:s'))
                ->diffInHours(Carbon::now()) <= 20;
        }
            
        return view('dashboard', compact(
            'absen',
            'absenP',
            'lembur',
            'user',
            'izin',
            'sholat',
            'news',
            'hitungNews',
            'cekAbsen',
            'cex',
            'cex2',
            'totcex',
            'lok',
            'warn',
            'sholatSaatIni',
            'rillSholat',
            'luweh1Dino',
            'statusClass',
            'statusMessage'
        ));
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
