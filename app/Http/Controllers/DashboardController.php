<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Lembur;
use App\Models\Lokasi;
use App\Models\Izin;
use App\Models\CheckPoint;
use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Simpan waktu sekarang dan user dalam variabel
        $now = Carbon::now();
        $today = $now->toDateString();
        $user = Auth::user();

        // Hitung news yang masih berlaku berdasarkan tanggal
        $hitungNews = News::query()
            ->whereDate('tanggal_lihat', '<=', $today)
            ->whereDate('tanggal_tutup', '>=', $today)
            ->get();


        // Ambil data lembur dengan sorting berdasarkan 'jam_selesai'
        // $lembur = Lembur::latest('jam_selesai')->get();

        // Buat dasar query absensi untuk user
        $absenQueryBase = Absensi::with(['user', 'shift', 'kerjasama', 'tipeAbsensi'])
            ->where('user_id', $user->id);

        $tempAbsen = (clone $absenQueryBase)
            ->whereBetween('created_at', [$now->copy()->subDay()->startOfDay(), $now])
            ->latest()
            ->first();

        if ($tempAbsen && $tempAbsen->shift) {
            // Ambil jam_end dari shift, set ke tanggal hari ini, lalu tambah 2 jam
            $limitPulang = $now->copy()->startOfDay()->setTimeFromTimeString($tempAbsen->shift->jam_end)->addHours(1)->addMinutes(30);

            // Cek jika ini shift overnight (absen masuk kemarin, pulang pagi ini)
            // Jika sekarang masih sebelum jam pulang yang ditambah 2 jam, maka batasnya valid
        } else {
            // Default jika data shift tidak ditemukan
            $limitPulang = $now->copy()->startOfDay()->setTime(12, 0, 0);
        }

        // Ambil data absensi yang belum melakukan absensi pulang
        $absen = (clone $absenQueryBase)
            ->whereNull('absensi_type_pulang')
            ->get();

        // Ambil data absensi pada rentang waktu dari kemarin hingga hari ini
        // Fix: ambil absen terakhir yang belum pulang dalam 24 jam terakhir
        // Untuk shift overnight, tetap bisa kebaca di esok hari
        $absenP = (clone $absenQueryBase)
            ->whereNull('absensi_type_pulang')
            ->where('created_at', '>=', $now->copy()->subHours(24))
            ->latest()
            ->first();
        
        // Filter lagi: jika shift overnight dan sudah lewat batas pulang + 2 jam, anggap expired
        if ($absenP && $absenP->shift && $absenP->shift->is_overnight) {
            // Shift overnight tetap valid sampai jam_end + 2 jam di hari berikutnya
        }

        $shouldTrackPulang = $absenP &&
            $absenP->user_id == $user->id &&
            is_null($absenP->absensi_type_pulang);

        $clientIdPulang = $absenP?->kerjasama?->client_id;

        $lokasiMitra = $shouldTrackPulang && $clientIdPulang
            ? Lokasi::query()
                ->where('client_id', $clientIdPulang)
                ->get(['id', 'client_id', 'latitude', 'longtitude', 'radius'])
            : collect();

        // Filter absensi untuk mengambil yang memiliki tipe "Tidak Absen Pulang"
        // dan terjadi di bulan berjalan. Jika memungkinkan, filter ini juga bisa dilakukan di query.
        $currentMonth = $now->month;
        $warn = $absen->filter(function ($item) use ($currentMonth) {
            return $item->absensi_type_pulang === ''
                && $item->tanggal_absen->month === $currentMonth;
        });

        // Atau jika ingin menggunakan koleksi yang sudah diambil sebelumnya:
        $cekAbsen = $absen->where('tanggal_absen', $today);

        // Ambil data izin untuk user terkait
        $izin = Izin::with('user')
            ->where('user_id', $user->id)
            ->whereDate('updated_at', $today)
            ->latest()
            ->first();
        if ($izin) {
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
        $cpQuery = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
            ->where('user_id', $user->id);

        $cex = (clone $cpQuery)->where('type_check', 'rencana')
            ->latest()
            ->first();

        // Ambil CheckPoint dengan tipe 'dikerjakan'
        $cex2 = (clone $cpQuery)->where('type_check', 'dikerjakan')
            ->latest()
            ->first();

        // Ambil CheckPoint tipe 'dikerjakan' untuk bulan berjalan
        $startOfMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $totcex = CheckPoint::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('type_check', 'dikerjakan')
            ->count();

        $luweh1Dino = false;
        if ($absenP && $absenP->shift) {
            if ($absenP->shift->is_overnight) {
                // Untuk shift lintas hari, hitung dari jam selesai shift kemarin
                $shiftEndTime = Carbon::createFromFormat('Y-m-d H:i', $absenP->created_at->format('Y-m-d') . ' ' . $absenP->shift->jam_end)
                    ->addDay(); // Tambah satu hari karena shiftnya lintas hari
                $luweh1Dino = $shiftEndTime->diffInHours(Carbon::now()) <= 4;
            } else {
                // Untuk shift reguler, hitung dari waktu absen dibuat
                $luweh1Dino = Carbon::createFromFormat('Y-m-d, H:i:s', $absenP->created_at->format('Y-m-d, H:i:s'))
                    ->diffInHours(Carbon::now()) <= 20;
            }
        }

        // data untuk dashboard mitra
        $jumlahKaryawan = User::where('kerjasama_id', $user->kerjasama_id)->count();
        $jumlahAbsensiHariIni = Absensi::whereHas('user', function ($query) use ($user) {
            $query->where('kerjasama_id', $user->kerjasama_id);
        })->whereDate('created_at', $today)->count();
        $jumlahIzinHariIni = Izin::whereHas('user', function ($query) use ($user) {
            $query->where('kerjasama_id', $user->kerjasama_id);
        })->whereDate('updated_at', $today)->count();
        $jumlahLemburHariIni = Lembur::whereHas('user', function ($query) use ($user) {
            $query->where('kerjasama_id', $user->kerjasama_id);
        })->whereDate('created_at', $today)->count();
        
        if(Auth::user()->jabatan->code_jabatan == "MITRA") {
            return view('mitra_view.index', compact(
                'jumlahKaryawan',
                'jumlahAbsensiHariIni',
                'jumlahIzinHariIni',
                'jumlahLemburHariIni'
            ));
        } else {
            return view('dashboard', compact(
                'absen',
                'absenP',
                'user',
                'izin',
                'hitungNews',
                'cekAbsen',
                'cex',
                'cex2',
                'totcex',
                'lokasiMitra',
                'warn',
                'luweh1Dino',
                'shouldTrackPulang',
                'statusClass',
                'statusMessage'
            ));
        }

    }

    public function sendTestEmail()
    {
        return;
    }

    public function waktuSholat(Request $request)
    {
        $lat = $request->input('lat');
        $lon = $request->input('lng');

        $user = Auth::user();
        $now = Carbon::now();
        $absenQueryBase = Absensi::with(['user', 'shift', 'kerjasama', 'tipeAbsensi'])
            ->where('user_id', $user->id)
            ->whereNull('absensi_type_pulang')
            ->where('created_at', '>=', $now->copy()->subHours(24))
            ->latest()
            ->first();
        $statusSholat = $absenQueryBase->status_sholat;

        $date = Carbon::now()->format('d-m-Y');

        $response = Http::withHeaders([
            'Accept-Encoding' => ''
        ])->get("https://api.aladhan.com/v1/timings/{$date}", [
            'latitude'                 => $lat,
            'longitude'                => $lon,
            'method'                   => '3',
            'shafaq'                   => 'general',
            'tune'                     => '5,3,5,7,9,-1,0,8,-6', // %2C otomatis di-encode oleh Laravel menjadi koma
            'school'                   => '0',
            'midnightMode'             => '0',
            'timezonestring'           => '', // Kosongkan agar API mendeteksi timezone otomatis berdasarkan koordinat GPS user
            'latitudeAdjustmentMethod' => '1',
            'calendarMethod'           => 'UAQ',
            'iso8601'                  => 'false'
        ]);
        
        if ($response->successful()) {
            // 1. Ambil data timings dan timezone dari response API
            $timings = $response->json('data.timings');
            $timezone = $response->json('data.meta.timezone');

            // 2. Ambil waktu sekarang berdasarkan timezone lokasi user
            $now = Carbon::now($timezone);

            // 3. Konversi string jam sholat menjadi objek Carbon penuh
            $subuh   = Carbon::createFromFormat('H:i', $timings['Fajr'], $timezone);
            $zuhur   = Carbon::createFromFormat('H:i', $timings['Dhuhr'], $timezone);
            $ashar   = Carbon::createFromFormat('H:i', $timings['Asr'], $timezone);
            $maghrib = Carbon::createFromFormat('H:i', $timings['Maghrib'], $timezone);
            $isya    = Carbon::createFromFormat('H:i', $timings['Isha'], $timezone);

            // 4. Logika penentuan nama waktu sholat dengan gap 15 menit sebelum waktu berikutnya
            $subuhMulai   = $subuh->copy()->subMinutes(10);
            $subuhSelesai = $subuh->copy()->addMinutes(90);

            $zuhurMulai   = $zuhur->copy()->subMinutes(10);
            $zuhurSelesai = $zuhur->copy()->addMinutes(90);

            $asharMulai   = $ashar->copy()->subMinutes(10);
            $asharSelesai = $ashar->copy()->addMinutes(90);

            $maghribMulai   = $maghrib->copy()->subMinutes(10);
            $maghribSelesai = $maghrib->copy()->addMinutes(30);

            $isyaMulai   = $isya->copy()->subMinutes(10);
            $isyaSelesai = $isya->copy()->addMinutes(120);

            $sholatSekarang = null;

            if ($now->between($subuhMulai, $subuhSelesai) && $statusSholat->Subuh == 0) {
                $sholatSekarang = 'Subuh';
            } elseif ($now->between($zuhurMulai, $zuhurSelesai) && $statusSholat->Zuhur == 0) {
                $sholatSekarang = 'Zuhur';
            } elseif ($now->between($asharMulai, $asharSelesai) && $statusSholat->Ashar == 0) {
                $sholatSekarang = 'Ashar';
            } elseif ($now->between($maghribMulai, $maghribSelesai) && $statusSholat->Maghrib == 0) {
                $sholatSekarang = 'Maghrib';
            } elseif ($now->between($isyaMulai, $isyaSelesai) && $statusSholat->Isya == 0) {
                $sholatSekarang = 'Isya';
            }

            // 5. Hanya mengembalikan nama waktu sholat saja
            return response()->json([
                'sholat_sekarang' => $sholatSekarang ?? 'kosong'
            ]);
        }

        return response()->json($response->json(), $response->status());   
    }

}
