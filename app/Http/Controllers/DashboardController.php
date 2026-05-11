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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Simpan waktu sekarang dan user dalam variabel
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $user = Auth::user()->loadMissing([
            'jabatan',
            'divisi.jabatan',
            'kerjasama.client',
        ]);

        // Dashboard MITRA: hindari query dashboard umum yang tidak dipakai.
        if ($user->jabatan?->code_jabatan === "MITRA") {
            $todayKey = $now->format('Ymd');
            $jumlahKaryawan = Cache::remember(
                "dashboard:mitra:karyawan:{$user->kerjasama_id}",
                now()->addMinutes(10),
                fn() => User::where('kerjasama_id', $user->kerjasama_id)->count()
            );
            $jumlahAbsensiHariIni = Cache::remember(
                "dashboard:mitra:absensi:{$user->kerjasama_id}:{$todayKey}",
                now()->addMinutes(2),
                function () use ($user, $today) {
                    return Absensi::whereHas('user', function ($query) use ($user) {
                        $query->where('kerjasama_id', $user->kerjasama_id);
                    })->whereDate('created_at', $today)->count();
                }
            );
            $jumlahIzinHariIni = Cache::remember(
                "dashboard:mitra:izin:{$user->kerjasama_id}:{$todayKey}",
                now()->addMinutes(2),
                function () use ($user, $today) {
                    return Izin::whereHas('user', function ($query) use ($user) {
                        $query->where('kerjasama_id', $user->kerjasama_id);
                    })->whereDate('updated_at', $today)->count();
                }
            );
            $jumlahLemburHariIni = Cache::remember(
                "dashboard:mitra:lembur:{$user->kerjasama_id}:{$todayKey}",
                now()->addMinutes(2),
                function () use ($user, $today) {
                    return Lembur::whereHas('user', function ($query) use ($user) {
                        $query->where('kerjasama_id', $user->kerjasama_id);
                    })->whereDate('created_at', $today)->count();
                }
            );

            return view('mitra_view.index', compact(
                'jumlahKaryawan',
                'jumlahAbsensiHariIni',
                'jumlahIzinHariIni',
                'jumlahLemburHariIni'
            ));
        }

        // Hitung news yang masih berlaku berdasarkan tanggal
        $hitungNews = Cache::remember(
            "dashboard:news:active:{$today}",
            now()->addMinutes(10),
            function () use ($today) {
                return News::whereRaw('DATE(tanggal_lihat) <= ?', [$today])
                    ->whereRaw('DATE(tanggal_tutup) >= ?', [$today])
                    ->get();
            }
        );


        // Ambil data lembur dengan sorting berdasarkan 'jam_selesai'
        // $lembur = Lembur::latest('jam_selesai')->get();

        // Buat dasar query absensi untuk user
        $absenQueryBase = Absensi::query()->where('user_id', $user->id);

        $tempAbsen = (clone $absenQueryBase)
            ->with(['shift:id,jam_end,is_overnight'])
            ->whereBetween('created_at', [Carbon::yesterday()->startOfDay(), Carbon::now()])
            ->latest()
            ->first();

        if ($tempAbsen && $tempAbsen->shift) {
            // Ambil jam_end dari shift, set ke tanggal hari ini, lalu tambah 2 jam
            $limitPulang = Carbon::today()->setTimeFromTimeString($tempAbsen->shift->jam_end)->addHours(1)->addMinutes(30);

            // Cek jika ini shift overnight (absen masuk kemarin, pulang pagi ini)
            // Jika sekarang masih sebelum jam pulang yang ditambah 2 jam, maka batasnya valid
        } else {
            // Default jika data shift tidak ditemukan
            $limitPulang = Carbon::today()->setTime(12, 0, 0);
        }

        // Ambil data absensi pada rentang waktu dari kemarin hingga hari ini
        $absenP = (clone $absenQueryBase)
            ->with(['shift:id,jam_start,jam_end,is_overnight', 'kerjasama:id,client_id'])
            ->whereBetween('created_at', [
                $limitPulang->copy()->subDay()->subHour(),
                $limitPulang
            ])
            ->latest()
            ->first();

        $lokasiMitra = collect();
        $activeClientId = $absenP?->kerjasama?->client_id ?? $user?->kerjasama?->client_id;
        $needsRadiusCheck = $absenP && $absenP->absensi_type_pulang === null && (int) $user->kerjasama_id !== 1;

        if ($needsRadiusCheck && $activeClientId) {
            $lokasiMitra = Cache::remember(
                "dashboard:lokasi-mitra:client:{$activeClientId}",
                now()->addMinutes(30),
                function () use ($activeClientId) {
                    return Lokasi::query()
                        ->select(['id', 'client_id', 'latitude', 'longtitude', 'radius'])
                        ->where('client_id', $activeClientId)
                        ->get();
                }
            );
        }

        // Filter absensi untuk mengambil yang memiliki tipe "Tidak Absen Pulang"
        // dan terjadi di bulan berjalan. Jika memungkinkan, filter ini juga bisa dilakukan di query.
        $warnCount = (clone $absenQueryBase)
            ->whereYear('tanggal_absen', $now->year)
            ->whereMonth('tanggal_absen', $now->month)
            ->where('absensi_type_pulang', '')
            ->count();

        // Ambil absensi hari ini untuk kondisi sholat
        $cekAbsen = (clone $absenQueryBase)
            ->whereDate('tanggal_absen', $today)
            ->whereNull('absensi_type_pulang')
            ->get(['id', 'user_id', 'tanggal_absen', 'subuh', 'dzuhur', 'asar', 'magrib', 'isya']);
        $sholat = $cekAbsen->first();

        // Ambil data izin untuk user terkait
        $izin = Izin::query()
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

        $cex = Cache::remember(
            "dashboard:cp:rencana:user:{$user->id}:week:{$awalMinggu->format('Ymd')}-{$akhirMinggu->format('Ymd')}",
            now()->addMinutes(3),
            function () use ($user, $awalMinggu, $akhirMinggu) {
                return CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
                    ->where('user_id', $user->id)
                    ->where('type_check', 'rencana')
                    ->latest()
                    ->first();
            }
        );

        // Ambil CheckPoint tipe 'dikerjakan' untuk bulan berjalan
        $startOfMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $totcex = Cache::remember(
            "dashboard:cp:total-dikerjakan:month:{$startOfMonth->format('Ym')}",
            now()->addMinutes(10),
            function () use ($startOfMonth, $endOfMonth) {
                return CheckPoint::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->where('type_check', 'dikerjakan')
                    ->count();
            }
        );

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

            if ($user->kerjasama_id == 1) {
                foreach ($waktuSholat as $namaSholat => $waktu) {
                    if (
                        $waktu['status'] === "0" &&
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
                        $waktu['status'] === "0" &&
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
            $user->kerjasama_id == 1 ||
            $user->divisi->jabatan->code_jabatan == "CO-CS" ||
            $user->divisi->jabatan->code_jabatan == "CO-SCR"
        );

        $luweh1Dino = false;
        if ($absenP) {
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
        return view('dashboard', compact(
            'absenP',
            'user',
            'izin',
            'sholat',
            'hitungNews',
            'cekAbsen',
            'cex',
            'totcex',
            'lokasiMitra',
            'warnCount',
            'sholatSaatIni',
            'rillSholat',
            'luweh1Dino',
            'statusClass',
            'statusMessage'
        ));

    }

    public function sendTestEmail()
    {
        return;
    }

}
