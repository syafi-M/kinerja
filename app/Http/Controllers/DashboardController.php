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

class DashboardController extends Controller
{
    public function index()
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
        $absenP = (clone $absenQueryBase)
            ->whereBetween('created_at', [
                $limitPulang->copy()->subDay()->subHour(),
                $limitPulang
            ])
            ->latest()
            ->first();

        $shouldTrackPulang = $absenP &&
            $absenP->user_id === $user->id &&
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

        // Ambil absensi hari ini untuk kondisi sholat
        $sholat = (clone $absenQueryBase)
            ->where('tanggal_absen', $today)
            ->whereNull('absensi_type_pulang')
            ->first();

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
                'sholat',
                'hitungNews',
                'cekAbsen',
                'cex',
                'cex2',
                'totcex',
                'lokasiMitra',
                'warn',
                'sholatSaatIni',
                'rillSholat',
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

}
