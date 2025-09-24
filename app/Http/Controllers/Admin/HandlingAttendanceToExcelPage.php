<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Izin;
use App\Models\Kerjasama;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HandlingAttendanceToExcelPage extends Controller
{
    public function __construct(Request $request)
    {
        $this->str = $request->input('str1');
        $this->ended = $request->input('end1');
    }

    public function index(Request $request)
    {
        ini_set('max_execution_time', 1800); // 30 minutes
        ini_set('memory_limit', '1024M');    // 1GB memory
        set_time_limit(1800);                // Also 30 minutes

        $startDate = Carbon::parse($this->str);
        $endDate = Carbon::parse($this->ended);

        $currentMonth = $endDate->month;
        $currentYear = $startDate->year;
        $strYear = $startDate->year;
        $endYear = $endDate->year;

        $dailyData = Cache::remember("libur_{$strYear}_{$endYear}", now()->addHours(12), function () use ($strYear, $endYear) {
            $allDates = [];

            $years = $strYear == $endYear ? [$strYear] : [$strYear, $endYear];

            foreach ($years as $year) {
                $res = Http::get("https://dayoffapi.vercel.app/api?year={$year}");
                if ($res->successful()) {
                    foreach ($res->json() as $d) {
                        $allDates[] = $d['tanggal'];
                    }
                }
            }

            return $allDates;
        });
        // dd($dailyData);

        $tanggalSekarang = Carbon::now();

        $user = Absensi::select('created_at')->get();
        $str1 = $this->str;
        $end1 = $this->ended;
        $starte = Carbon::createFromFormat('Y-m-d', $str1);
        $ende = Carbon::createFromFormat('Y-m-d', $end1);

        $izin = Izin::whereBetween('created_at', [$str1, $end1])->get();

        $mitra = $request->input('kerjasama_id');
        $divisiId = $request->input('divisi_id');
        $libur = $request->input('libur');
        $jdwl = $request->input('jadwal');

        $totalHari = Carbon::parse($this->ended)->diffInDays(Carbon::parse($this->str));

        if ($request->has(['libur', 'end1', 'str1'])) {
            $expPDF = User::query()
                ->with([
                    'absensi' => function ($query) use ($str1, $end1) {
                        $query->whereBetween('tanggal_absen', [$str1, $end1]);
                    },
                    'jadwalUser' => function ($query) use ($str1, $end1) {
                        $query->whereBetween('created_at', [$str1, $end1]);
                    },
                    'jabatan'
                ])
                ->whereHas('absensi', function ($query) use ($str1, $end1) {
                    $query->whereBetween('tanggal_absen', [$str1, $end1]);
                })
                ->when($mitra, fn($q) => $q->where('kerjasama_id', $mitra))
                ->when($divisiId, fn($q) => $q->where('devisi_id', $divisiId))
                ->orderBy('nama_lengkap', 'asc')
                ->get();


            $kerjasama = Kerjasama::find($mitra);
            $kantor = optional($kerjasama)->id === 1;
            $liburCount = 0;
            $hae = 0;
            $calendarHeaders = [];

            $startDate = Carbon::parse($str1);
            $endDate = Carbon::parse($end1);
            $current = $startDate->copy();

            while ($current->lte($endDate)) {
                $isHoliday = in_array($current->format('Y-m-j'), $dailyData);

                if ($current->isWeekend() || $isHoliday) {
                    $liburCount++;
                }

                if (!$current->isWeekend() && !$isHoliday) {
                    $hae++;
                }

                $calendarHeaders[] = [
                    'day' => $current->format('d'),
                    'isHoliday' => in_array($current->format('Y-m-j'), $dailyData),
                    'isWeekend' => $current->isWeekend(),
                ];

                $current->addDay();
            }
            // dd($expPDF);

            $processedUsers = [];
            $dummy = [];

            foreach ($expPDF as $user) {
                if (in_array(strtolower($user->nama_lengkap), ['admin', 'user', 'subhan santosa'])) {
                    continue;
                }

                $uid = $user->id;

                // Preload user's absensi and izin
                $userAbsensi = $user->absensi->groupBy(fn($absen) => $absen->created_at->format('Y-m-d'));
                $userIzin = $izin->where('user_id', $uid)->keyBy(fn($izin) => $izin->created_at->format('Y-m-d'));

                $rows = [];
                $totalMasuk = 0;
                $totalMasukTidakPulang = 0;
                $totalTelat = 0;
                $totalIzin = 0;
                $totalTerus = 0;
                $totalMS = 0;
                $totalST = 0;

                $current = $starte->copy();
                while ($current->lte($ende)) {
                    $dateKey = $current->format('Y-m-d');
                    $isHoliday = in_array($current->format('Y-m-j'), $dailyData);
                    $isWeekend = $current->isWeekend();

                    $absensiList = $userAbsensi->get($dateKey) ?? collect();
                    $izinItem = $userIzin->get($dateKey);


                    // Default status
                    $symbol = '-';
                    $alterSymbol = '-';
                    $hasTerus = false;
                    $mainMarked = false;

                    foreach ($absensiList as $absen) {
                        if (!$mainMarked) {
                            if ($absen->keterangan === 'masuk' && $absen->terus == null) {
                                $symbol = 'M';
                                $totalMasuk++;
                                $mainMarked = true;
                            } elseif ($absen->keterangan === 'masuk' && $absen->absensi_type_pulang == null) {
                                $symbol = 'TP';
                                $totalMasukTidakPulang++;
                                $mainMarked = true;
                            } elseif ($absen->keterangan === 'telat' && $absen->terus == null) {
                                $symbol = 'T';
                                $totalTelat++;
                                $mainMarked = true;
                            } elseif ($absen->tukar) {
                                $symbol = 'MS';
                                $totalMS++;
                                $mainMarked = true;
                            } elseif ($absen->tukar_id === $uid) {
                                $symbol = 'ST';
                                $totalST++;
                                $mainMarked = true;
                            }
                        }

                        if ($absen->terus) {
                            if ($absen->keterangan === 'masuk') {
                                $alterSymbol = 'N';
                            } elseif ($absen->keterangan === 'telat') {
                                $alterSymbol = 'NT';
                            }
                            $hasTerus = true;
                        } elseif ($isHoliday || $isWeekend) {
                            $alterSymbol = '//';
                        }
                    }

                    if (!$mainMarked && $izinItem && $izinItem->approve_status === 'accept') {
                        $symbol = 'I';
                        $totalIzin++;
                        $mainMarked = true;
                    }

                    if (!$mainMarked && ($isHoliday || $isWeekend)) {
                        $symbol = '//';
                        $alterSymbol = '//';
                    }

                    if ($hasTerus)
                        $totalTerus++;

                    $rows[] = [
                        'date' => $dateKey,
                        'symbol' => $symbol,
                        'alterSymbol' => $alterSymbol,
                        'isHoliday' => $isHoliday,
                        'isWeekend' => $isWeekend,
                    ];

                    $current->addDay();
                    // $dummy[] = $absen;
                }


                $totalHariKerja = $kantor ? $hae - $libur : $totalHari - $libur + 1;

                $totalMasukAdjusted = $kantor ? $totalMasuk : $totalMasuk + $totalTerus;
                $tesPer = $totalHariKerja > 0 ? round(($totalMasukAdjusted + $totalTelat + $totalMasukTidakPulang) / $totalHariKerja * 100) : 0;

                // $totalMasukAdjusted = $kantor ? $totalMasuk : $totalMasuk + $totalTerus;
                // $totalMinusAdjusted = $totalHariKerja > 0 ? ((($totalTelat + $totalMasukTidakPulang) / $totalHariKerja * 100) / 2) : 0;
                // $tesPer = $totalHariKerja > 0 ? round(($totalMasukAdjusted / $totalHariKerja * 100) + $totalMinusAdjusted) : 0;

                $tesPer = min($tesPer, 100);

                $processedUsers[] = [
                    'user' => $user,
                    'rows' => $rows,
                    'm' => $totalMasuk,
                    'mt' => $totalMasukTidakPulang,
                    't' => $totalTelat,
                    'z' => $totalIzin,
                    'terus' => $totalTerus,
                    'ms' => $totalMS,
                    'st' => $totalST,
                    'percentage' => $tesPer,
                    'totalHariKerja' => $totalHariKerja,
                    'totalPoints' => $user->absensi->whereNotNull('point_id')->sum(fn($p) => (int) optional($p->point)->sac_point),
                ];

                // dd($processedUsers);
            }
            // dd($dummy);
            // return view('admin.absen.report', compact('absen', 'all', 'base64', 'user', 'dataUser', 'totalHari', 'dataAbsen', 'currentMonth', 'currentYear', 'libur'));

            return view('admin.absen.report', [
                'processedUsers' => $processedUsers,
                'calendarHeaders' => $calendarHeaders,
                'currentMonth' => $currentMonth,
                'currentYear' => $currentYear,
                'totalHari' => $totalHari,
            ]);


            $logoPath = public_path('logo/sac.png');
            $base64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
        } else {
            toastr()->error('Mohon Masukkan Filter Export', 'error');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $userId = $request->input('user_id');
        $column = $request->input('column');
        $value = $request->input('value');
        $valueParsed = '';

        // contoh: jika column adalah tanggal, simpan ke absensi
        if (is_numeric($column)) {
            // misalnya column = 25 artinya tanggal 25
            $date = Carbon::createFromFormat('Y-m-d', $request->input('row')['date']);
            Absensi::updateOrCreate(
                ['user_id' => $userId, 'tanggal_absen' => $date],
                ['keterangan' => $value] // M / I / T / //
            );
        } else {
            // kalau kolom rekap (M, I, T, L, Persentase)
            // terserah apakah mau langsung update atau dihitung ulang
        }

        return response()->json(['success' => true]);
    }
}
