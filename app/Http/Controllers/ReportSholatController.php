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
        $absenQuery = Absensi::with(['User', 'Shift', 'Kerjasama', 'TipeAbsensi'])
            ->orderBy('tanggal_absen', 'desc')
            ->orderBy('created_at', 'desc');
        
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
        $currentMonth = Carbon::parse($this->ended)->month;
        $currentYear = Carbon::parse($this->str)->year;
        
        $strYear = Carbon::parse($this->str)->year;
        $endYear = Carbon::parse($this->ended)->year;
        
        $loginResponse = Http::get('https://kalenderindonesia.com/api/login');
        $dailyData = [];
        
        $cMonth = Carbon::now()->month;
        $getDateLib = Http::get("https://dayoffapi.vercel.app/api");
        if($getDateLib->successful()){
            if($strYear == $endYear){
                $kalenderData = $getDateLib->json();
                foreach($kalenderData as $dailys) {
                    $dailyData[] = $dailys['tanggal'];
                    // dd($dailyData);
                }
            }else{
                $kalenderResponse = Http::get("https://dayoffapi.vercel.app/api?year={$strYear}");
                $kalenderResponse2 = Http::get("https://dayoffapi.vercel.app/api?year={$endYear}");
                if ($kalenderResponse->successful()) {
                    $kalenderData = $kalenderResponse->json();
                    foreach($kalenderData as $dailys) {
                        $dailyData[] = $dailys['tanggal'];
                        // dd($dailyData);
                    }
                }
                if ($kalenderResponse2->successful()) {
                    $kalenderData = $kalenderResponse2->json();
                    foreach($kalenderData as $dailys) {
                        $dailyData[] = $dailys['tanggal'];
                        // dd($dailyData);
                    }
                }
            }
        }
        
        $tanggalSekarang = Carbon::now();
        
        $dataUser = User::all();
        $divisi = Divisi::all();
        $user = Absensi::all();
        $mit = Kerjasama::all();
        $str1 = $this->str;
        $end1 = $this->ended;
        
        $mitra = $request->input('kerjasama_id');
        $divisiId = $request->input('divisi_id');
        $jdwl = $request->input('jadwal'); 

        // dd($izin);
        
        $totalHari =  Carbon::parse($this->ended)->diffInDays(Carbon::parse($this->str));
        
        if($request->has(['end1', 'str1'])) {
            
         $expPDF = User::with(['absensi' => function ($query) use ($str1, $end1) {
            return $query->whereBetween('tanggal_absen', [$str1, $end1]);
        }, 'jadwalUser' => function ($query) use ($str1, $end1) {
            return $query->whereBetween('created_at', [$str1, $end1]);
        }])->when($mitra, function($query) use ($mitra) {
            return $query->where('kerjasama_id', $mitra);
        })->when($divisiId, function($query) use ($divisiId) {
            return $query->where('devisi_id', $divisiId);
        })->orderBy('nama_lengkap', 'asc')->get();
        
        $point = Point::all();

        $path = 'logo/sac.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $pdf = new Dompdf($options);
        $html = view('admin.reportSholat.export', compact('point', 'dailyData', 'expPDF','jdwl', 'base64', 'totalHari', 'user', 'dataUser', 'currentYear', 'currentMonth', 'divisi', 'str1', 'end1', 'mit', 'mitra'))->render();
        $pdf->loadHtml($html);

        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $output = $pdf->output();
        $filename = 'absensi.pdf';

        if ($request->input('action') == 'download') {
            return response()->download($output, $filename);
        }

        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
                    
        }else{
            toastr()->error('Mohon Masukkan Filter Export', 'error');
            return redirect()->back();
        }
    }
}
