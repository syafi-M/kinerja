<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\CheckPoint;  
use App\Models\Client;
use App\Models\Divisi;
use App\Models\Kerjasama;
use App\Models\Point;
use App\Models\Shift;
use App\Models\User;
use App\Models\Izin;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    
    public function __construct(Request $request)
    {
        $this->str = $request->input('str1');
        $this->ended = $request->input('end1');
    }

    public function index(Request $request)
    {
        $user = User::count();
        $client = Client::count();
        $izin = Izin::where('approve_status', 'process')->whereMonth('created_at', Carbon::now()->month)->count();
        
        


        return view('admin.index',
        [
            'user' => $user,
            'client' => $client,
            'izin' => $izin,
    ]);
    }
    public function getUptime()
    {
        $startTime = Cache::get('app_start_time');
        $currentTime = now();
        $uptime = $currentTime->diffInSeconds($startTime);
        
        // Menambahkan waktu yang telah berlalu sejak app_start_time ke waktu sekarang
        $uptime += $currentTime->day * 24 * 3600;
        
        $days = intdiv($uptime, 86400); // 86400 detik dalam satu hari
        $uptime %= 86400; // Sisa detik setelah menghitung hari
        
        $hours = intdiv($uptime, 3600);
        $minutes = intdiv(($uptime % 3600), 60);
        $seconds = $uptime % 60;
        
        // Menampilkan "hari" jika jumlah hari adalah 1, dan "hari" jika lebih dari 1
        $daysLabel = $days == 1 ? 'Hari' : 'Hari';
        
        $formattedUptime = sprintf('%d %s %02d:%02d:%02d', $days, $daysLabel, $hours, $minutes, $seconds);
        
        return response()->json(['uptime' => $formattedUptime]);

    }
    
    public function checkPoint(Request $request)
    {
        $filter = $request->filterKerjasama;
        
        $kerjasama = Kerjasama::all();
        
        if($filter)
        {
            $user = User::orderBy('kerjasama_id', 'asc')->where('kerjasama_id', $filter)->get();   
            $cek = CheckPoint::paginate(800000);
        }else{
            $user = User::orderBy('kerjasama_id', 'asc')->get();
            $cek = CheckPoint::paginate(800000);
        }
        
        return view('admin.check.index', compact('cek', 'user', 'kerjasama', 'filter')); 
    }
    public function lihatCheck($id)
    {
        $inMonth = Carbon::now()->month;
        $user = User::findOrFail($id);
        $cek = CheckPoint::orderBy('created_at', 'asc')->where('user_id', $id)->whereMonth('created_at', $inMonth)->paginate(15);
        return view('admin.check.lihatCP', compact('user', 'cek'));
    }
    
    public function approveCheck(Request $request, $id)
    {
        $appCheck = [
            'approve_status' => $request->approve_status,
        ];
        // dd($appCheck);
        CheckPoint::findOrFail($id)->update($appCheck);
        toastr()->success('Check Point Has Approve', 'success');
        return redirect()->back();
    }
    
    public function deniedCheck(Request $request, $id)
    {
        $appCheck = [
            'approve_status' => $request->approve_status,
        ];
        // dd($appCheck);
        CheckPoint::findOrFail($id)->update($appCheck);
        toastr()->warning('Check Point Has Denied', 'success');
        return redirect()->back();
    }

    public function destroyCheck($id)
    {
 
     try {
         $cek = CheckPoint::findOrFail($id);
         if ($cek->img != null) {
 
             Storage::disk('public')->delete('images/'.$cek->img);
 
             $cek->delete();
             toastr()->warning('Data Telah Dihapus', 'warning');
             return redirect()->back();
         }else{
             toastr()->error('Foto Tidak Ditemukan', 'error');
         }
     } catch (\Illuminate\Database\QueryException $e) {
         toastr()->error('Data Tidak Ditemukan', 'error');
         return redirect()->back();
     }
 
    }

    public function absen(Request $request)
    {
        
        $filter = $request->filterKerjasama;
        $filterDivisi = $request->filterDevisi;
        
        $absenSi = Kerjasama::all();
        $point = Point::all();
        $absen = Absensi::orderBy('tanggal_absen', 'desc')->orderBy('created_at', 'desc')->paginate(50);      
        $divisi = Divisi::all();
        
        if($filter && $filterDivisi)
        {
            $absen = Absensi::with(['User', 'Shift', 'Kerjasama', 'TipeAbsensi'])->where('kerjasama_id', $filter)->whereHas('user', function ($query) use ($filterDivisi) {
                $query->where('devisi_id', $filterDivisi);
            })->orderBy('tanggal_absen', 'desc')->paginate(999999999);
        }
        elseif($filterDivisi != null)
        {
            $absen = Absensi::with(['User', 'Shift', 'Kerjasama', 'TipeAbsensi'])->whereHas('user', function ($query) use ($filterDivisi) {
                $query->where('devisi_id', $filterDivisi);
            })->orderBy('tanggal_absen', 'desc')->paginate(999999999);
        }
        elseif($filter) {
            $absen = Absensi::with(['User', 'Shift', 'Kerjasama'])->where('kerjasama_id', $filter)->orderBy('tanggal_absen', 'desc')->paginate(999999999);

        }
        
        return view('admin.absen.index',['absen' => $absen, 'filterDivisi' => $filterDivisi, 'absenSi' => $absenSi, 'point' => $point, 'divisi' => $divisi, 'filter' => $filter]);
    }

    public function izin()
    {
        $izin = Absensi::where('keterangan', 'izin')->paginate(5);
        return view('admin.absen.izin', ['izin' => $izin]);
    }

    public function export(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $tanggalSekarang = Carbon::now();
        $totalHari = $tanggalSekarang->daysInMonth;
        $tanggalFormat = Carbon::now()->format('Y-m-d');
        
        $libur = $request->input('libur');

        if($request->has(['libur']) && $libur != null) {
        $dataAbsen = User::with(['absensi' => function ($query) use ($currentMonth, $currentYear) {
            $query->whereMonth('tanggal_absen', $currentMonth)->whereYear('tanggal_absen', $currentYear);
        }])->get();
        $dataUser = User::all();
        $all = Absensi::all();
        $user = Absensi::all();
        $aku = Absensi::where('keterangan', 'masuk')->get();
        $absen = Absensi::orderBy('absensi_type_masuk', 'asc')->where('keterangan', 'masuk')->get();

        $path = 'logo/sac.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $pdf = new Dompdf($options);
        $html = view('admin.absen.export', compact('absen','all','base64', 'user', 'dataUser', 'totalHari', 'dataAbsen', 'currentMonth' , 'currentYear', 'libur'))->render();
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
            toastr()->error('Mohon Masukkan Hari Libur', 'error');
            return redirect()->back();
        }
    }

    public function exportWith(Request $request)
    {
        $tanggalSekarang = Carbon::now();
        $currentMonth = Carbon::parse($this->ended)->month;
        $currentYear = Carbon::parse($this->str)->year;
        $dataUser = User::all();
        $divisi = Divisi::all();
        $user = Absensi::all();
        $mit = Kerjasama::all();
        $str1 = $this->str;
        $end1 = $this->ended;
        
        $izin = Izin::whereBetween('created_at', [$str1, $end1])->get();
        $hitungIzin = Izin::whereBetween('created_at', [$str1, $end1])->get();
        
        $mitra = $request->input('kerjasama_id');
        $divisiId = $request->input('divisi_id');
        $libur = $request->input('libur');
        $jdwl = $request->input('jadwal'); 

        // dd($izin);
        
        $totalHari =  Carbon::parse($this->ended)->diffInDays(Carbon::parse($this->str));
        
        if($request->has(['libur', 'end1', 'str1'])) {
            
         $expPDF = User::with(['absensi' => function ($query) use ($str1, $end1) {
            return $query->whereBetween('created_at', [$str1, $end1]);
        }, 'jadwalUser' => function ($query) use ($str1, $end1) {
            return $query->whereBetween('created_at', [$str1, $end1]);
        }])->when($mitra, function($query) use ($mitra) {
            return $query->where('kerjasama_id', $mitra);
        })->when($divisiId, function($query) use ($divisiId) {
            return $query->where('devisi_id', $divisiId);
        })->orderBy('nama_lengkap', 'asc')->get();
        


        $path = 'logo/sac.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $pdf = new Dompdf($options);
        $html = view('admin.absen.exportV2', compact('expPDF','izin','hitungIzin','jdwl', 'base64', 'totalHari', 'user', 'dataUser', 'currentYear', 'currentMonth', 'divisi', 'libur', 'str1', 'end1', 'mit', 'mitra'))->render();
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

    public function exp(Request $request)
    {
        $all = Absensi::all();
        $kerjasamaId = $request->kerjasama_id;
        $kerjasama = Kerjasama::firstWhere('id', $kerjasamaId);
        $izin = Izin::orderBy('updated_at', 'desc')->where('kerjasama_id', $kerjasamaId)->where('approve_status', 'accept')->get();
        // dd($izin);

        $path = 'logo/sac.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $pdf = new Dompdf($options);
        $html = view('admin.absen.export-izin', compact('izin','all','base64', 'kerjasama'))->render();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $output = $pdf->output();
        $filename = 'absensi-izin.pdf';

        if ($request->input('action') == 'download') {
            return response()->download($output, $filename);
        }

        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }
    
    public function exportCheck()
    {
        // dd($check);
        $arr = User::all();
        return view('admin.user.test', compact('arr'));
    }
    
    public function prosesExport(Request $request)
    {
        $check = $request->input('check');
        $checkAll = $request->input('check_all');
        if ($check != null || $checkAll != null)
        {
            $data = [];
            foreach($check as $id)
            {
                $arr = User::where('id', $id)->get();
                $data[] = $arr;
            }
        
            $options = new Options();
            $options->setIsHtml5ParserEnabled(true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
    
            $pdf = new Dompdf($options);
            $html = view('admin.user.export-user', compact('data'))->render();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();
    
            $output = $pdf->output();
            $filename = 'user.pdf';
    
            if ($request->input('action') == 'download') {
                return response()->download($output, $filename);
            }
    
            return response($output, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
        }
        
    }
    
    
    
    
    
    
    
    
}
