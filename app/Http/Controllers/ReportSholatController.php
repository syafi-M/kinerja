<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\CheckPoint;  
use App\Models\PekerjaanCp;
use App\Models\Client;
use App\Models\Divisi;
use App\Models\Kerjasama;
use App\Models\Point;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportSholatController extends Controller
{
    
     public function __construct(Request $request)
    {
        $this->str = $request->input('str1');
        $this->ended = $request->input('end1');
    }
    
    public function index(Request $request)
    {
        // Retrieve filter values from the request
        $filter = $request->filterKerjasama;
        $filterDivisi = $request->filterDevisi;
        
        // Build the initial query
        $absenQuery = Absensi::with(['user', 'shift', 'kerjasama', 'tipeAbsensi'])
            ->where(function ($query) {
                foreach (['subuh', 'dzuhur', 'asar', 'maghrib', 'isya'] as $field) {
                    $query->orWhere($field, "1");
                }
            })
            ->orderBy('tanggal_absen', 'desc')
            ->orderBy('created_at', 'desc');
        // how to add where subuh, dzuhur, ashar, is 0
        
        // Apply filters if provided
        if ($filter && $filterDivisi) {
            $absenQuery = $absenQuery->where('kerjasama_id', $filter)
                ->whereHas('user', function ($query) use ($filterDivisi) {
                    $query->where('devisi_id', $filterDivisi);
                });
        } elseif ($filterDivisi != null) {
            $absenQuery = $absenQuery->whereHas('user', function ($query) use ($filterDivisi) {
                $query->where('devisi_id', $filterDivisi);
            });
        } elseif ($filter) {
            $absenQuery = $absenQuery->where('kerjasama_id', $filter);
        }
        
        // Paginate and include the filter values in the pagination links
        $absen = $absenQuery->paginate(50);
        $absen->appends(['filterKerjasama' => $filter, 'filterDevisi' => $filterDivisi]);
        
        // Other data retrieval
        $absenSi = Kerjasama::all();
        $point = Point::all();
        $divisi = Divisi::all();
        
        $min1 = Absensi::orderBy('tanggal_absen', 'asc')->first();
        $min2 = $min1->created_at->format('Y-m-d');
        
        $max1 = Absensi::orderBy('tanggal_absen', 'desc')->first();
        $max2 = $max1->created_at->subMonth(3)->format('Y-m-d');
        
        return view('admin.reportSholat.index' ,['min' => $min2,'max' => $max2,'absen' => $absen, 'filterDivisi' => $filterDivisi, 'absenSi' => $absenSi, 'point' => $point, 'divisi' => $divisi, 'filter' => $filter]);
    }
    
    public function download(Request $request)
    {
        $validated = $request->validate([
            'str1' => ['required', 'date'],
            'end1' => ['required', 'date', 'after_or_equal:str1'],
            'kerjasama_id' => ['nullable', 'integer'],
            'divisi_id' => ['nullable', 'integer'],
        ]);

        $str1 = $validated['str1'];
        $end1 = $validated['end1'];

        $strYear = Carbon::parse($str1)->year;
        $endYear = Carbon::parse($end1)->year;

        $dailyData = [];
        $getDateLib = Http::get("https://dayoffapi.vercel.app/api");

        if ($getDateLib->successful()) {
            if ($strYear == $endYear) {
                foreach ($getDateLib->json() as $dailys) {
                    $dailyData[] = $dailys['tanggal'];
                }
            } else {
                $kalenderResponse = Http::get("https://dayoffapi.vercel.app/api?year={$strYear}");
                $kalenderResponse2 = Http::get("https://dayoffapi.vercel.app/api?year={$endYear}");

                if ($kalenderResponse->successful()) {
                    foreach ($kalenderResponse->json() as $dailys) {
                        $dailyData[] = $dailys['tanggal'];
                    }
                }

                if ($kalenderResponse2->successful()) {
                    foreach ($kalenderResponse2->json() as $dailys) {
                        $dailyData[] = $dailys['tanggal'];
                    }
                }
            }
        }

        $currentMonth = Carbon::parse($end1)->month;
        $currentYear = Carbon::parse($str1)->year;
        $totalHari = Carbon::parse($str1)->diffInDays(Carbon::parse($end1));

        $divisi = Divisi::all();
        $mit = Kerjasama::all();

        $mitra = $request->input('kerjasama_id');
        $divisiId = $request->input('divisi_id');
        $jdwl = $request->input('jadwal');

        $expPDF = User::with([
            'absensi' => function ($query) use ($str1, $end1) {
                $query->whereBetween('tanggal_absen', [$str1, $end1]);
            },
            'jadwalUser' => function ($query) use ($str1, $end1) {
                $query->whereBetween('created_at', [$str1, $end1]);
            }
        ])
        ->when($mitra, function ($query) use ($mitra) {
            return $query->where('kerjasama_id', $mitra);
        })
        ->when($divisiId, function ($query) use ($divisiId) {
            return $query->where('devisi_id', $divisiId);
        })
        ->orderBy('nama_lengkap', 'asc')
        ->get();


        $path = public_path('logo/sac.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $pdf = new Dompdf($options);
        $html = view('admin.reportSholat.export', compact(
            'dailyData',
            'expPDF',
            'jdwl',
            'base64',
            'totalHari',
            'currentYear',
            'currentMonth',
            'divisi',
            'str1',
            'end1',
            'mit',
            'mitra'
        ))->render();

        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $output = $pdf->output();
        $filename = 'absensi-sholat-' . $str1 . '_sampai_' . $end1 . '.pdf';

        return response($output)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="'.$filename.'"'
            );
    }

    public function detail($id)
    {
        $absen = Absensi::with(['user', 'shift', 'kerjasama.client'])->findOrFail($id);

        $toUrl = function ($fileName) {
            return $fileName
                ? asset('storage/' . 'sholat/' . $fileName)
                : null;
        };

        return response()->json([
            'id' => $absen->id,
            'nama' => $absen->user?->nama_lengkap,
            'tanggal' => $absen->tanggal_absen,
            'client' => $absen->kerjasama?->client?->name,
            'shift' => $absen->shift?->shift_name,

            'subuh' => [
                'status' => (int) $absen->subuh,
                'lat' => $absen->subuh_lat,
                'lng' => $absen->subuh_long,
                'foto' => $toUrl($absen->fotoSubuh),
            ],
            'zuhur' => [
                'status' => (int) $absen->dzuhur,
                'lat' => $absen->dzuhur_lat,
                'lng' => $absen->dzuhur_long,
                'foto' => $toUrl($absen->fotoDzuhur),
            ],
            'ashar' => [
                'status' => (int) $absen->asar,
                'lat' => $absen->asar_lat,
                'lng' => $absen->asar_long,
                'foto' => $toUrl($absen->fotoAsar),
            ],
            'maghrib' => [
                'status' => (int) $absen->maghrib,
                'lat' => $absen->maghrib_lat,
                'lng' => $absen->maghrib_long,
                'foto' => $toUrl($absen->fotoMaghrib),
            ],
            'isya' => [
                'status' => (int) $absen->isya,
                'lat' => $absen->isya_lat,
                'lng' => $absen->isya_long,
                'foto' => $toUrl($absen->fotoIsya),
            ],
        ]);
    }
}
