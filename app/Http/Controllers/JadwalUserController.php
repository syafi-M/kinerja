<?php

namespace App\Http\Controllers;

use App\Http\Requests\JadwalUserRequest;
use App\Models\Area;
use App\Models\JadwalUser;
use App\Models\Kerjasama;
use App\Models\Divisi;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\JadwalImport;

class JadwalUserController extends Controller
{

    public function __construct(Request $request)
    {
        $this->str = $request->input('str1');
        $this->ended = $request->input('end1');
        $this->divisi = $request->input('divisi');
    }

    public function index()
    {
        $kerj = Kerjasama::all();
        $divisi = Divisi::all();
        if(Auth::user()->divisi->jabatan->code_jabatan == "CO-CS"){
            $codeJabatan = ['OCS', 'CO-CS'];
            $user = User::where('kerjasama_id', Auth::user()->kerjasama_id)->whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                $query->whereIn('code_jabatan', $codeJabatan);
            })->paginate(50);
        }else if(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR") {
            $codeJabatan = ['SCR', 'CO-SCR'];
            $user = User::where('kerjasama_id', Auth::user()->kerjasama_id)->whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                $query->whereIn('code_jabatan', $codeJabatan);
            })->paginate(50);
        }elseif(Auth::user()->divisi->jabatan->code_jabatan == "MITRA"){
             $user = User::where('kerjasama_id', Auth::user()->kerjasama_id)->paginate(50);
        }
        else{
            $user = User::paginate(50);
        }
        $shift = Shift::all();
        $area = Area::all();
        $jadwalUser = JadwalUser::latest()->paginate(50);
        return view('admin.jadwalUser.index', compact('jadwalUser', 'kerj', 'divisi', 'user', 'shift', 'area'));   
    }

    public function create(Request $request)
    {
        if (Auth::user()->divisi->jabatan->code_jabatan == "MITRA" && Auth::user()->divisi->jabatan->code_jabatan == "LEADER") {
            $user = User::with('Kerjasama')->where('kerjasama_id', Auth::user()->kerjasama_id)->get();
        } else {
            $kerj = Kerjasama::all();
            $filter = $request->filter;
            $user = User::with('Kerjasama')->where('kerjasama_id', $filter)->get();
        }
        $shift = Shift::all();
        return view('admin.jadwalUser.create', compact('user', 'shift', 'kerj'));
    }

    public function processDate(Request $request)
    {
        $area = Area::with('Kerjasama')->where('kerjasama_id', Auth::user()->kerjasama_id)->get();
        
        $str1 = $this->str;
        $end1 = $this->ended;
        $divisi = $this->divisi;
        
        $kerj = Kerjasama::with('Devisi');
        $totalHari =  Carbon::parse($this->ended)->diffInDays(Carbon::parse($this->str));
        if($request->has(['str1', 'end1', 'divisi'])){
            if (Auth::user()->divisi->jabatan->code_jabatan == "MITRA" || Auth::user()->divisi->jabatan->code_jabatan == "LEADER") {
                $user = User::with('Kerjasama')->where('kerjasama_id', Auth::user()->kerjasama_id)->where('devisi_id', $divisi)->get();
                $filter = Auth::user()->kerjasama_id;
            } else {
                $kerj = Kerjasama::all();
                $filter = $request->filter;
                $user = User::with('Kerjasama')->where('kerjasama_id', $filter)->where('devisi_id', $divisi)->get();
            }
            $jadwal = JadwalUser::all();
            $shift = Shift::all();
            return view('admin.jadwalUser.create', compact('user', 'shift', 'totalHari', 'area', 'jadwal', 'str1', 'end1', 'kerj', 'filter'));
        }else{
            toastr()->error('Mohon Masukkan Taggal', 'Error');
            return redirect()->back();
        }


        // return redirect()->to(route('leader-jadwal.create')->with('totalHari', $totalHari));
    }

    public function store(JadwalUserRequest $request)
    {
        $jadwal = new JadwalUser();
        $user = User::all();
        
        $jadwal = [
            'user_id' => $request->user_id,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'area_id' => $request->area,
            'status' => $request->status
        ];
        
        JadwalUser::create($jadwal);
        return response()->noContent();
    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {
        
    }
 
    public function destroy($id)
    {
        $jadwalId = JadwalUser::findOrFail($id);
        $jadwalId->delete();
        toastr()->warning('Jadwal Telah Dihapus', 'warning');
        return redirect()->back();

    }

    public function exportJadwal(Request $request)
    {
        $tanggalSekarang = Carbon::now();
        $currentMonth = Carbon::parse($this->ended)->month;
        $currentYear = Carbon::parse($this->str)->year;
        $str1 = $this->str;
        $end1 = $this->ended;
        $filter = $request->filter;
        $kerj = Kerjasama::all();
        $kerjabagus = Kerjasama::where('id', $filter)->first();
        
        $totalHari =  Carbon::parse($this->ended)->diffInDays(Carbon::parse($this->str));
        
        if($request->has(['end1', 'str1'])) {
            
        $expPDF = User::with(['jadwalUser' => function ($query) use ($str1, $end1) {
            return $query->whereBetween('created_at', [$str1, $end1]);
        }])->when($filter, function($query) use ($filter) {
            return $query->where('kerjasama_id', $filter);
        })->get();
        
        $path = 'logo/sac.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $pdf = new Dompdf($options);
        $html = view('admin.jadwalUser.export', compact('expPDF', 'base64', 'totalHari','currentYear', 'currentMonth','str1', 'end1', 'filter', 'kerj', 'kerjabagus'))->render();
        $pdf->loadHtml($html);

        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $output = $pdf->output();
        $filename = 'jadwal.pdf';

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
    
    public function storeJadwal(JadwalUserRequest $request)
    {
        $jadwal = new JadwalUser();
        $user = User::all();
        
        $jadwal = [
            'user_id' => $request->user_id,
            'shift_id' => $request->shift_id,
            'tanggal' => $request->tanggal,
            'area_id' => $request->area,
            'status' => $request->status
        ];
        
        
        JadwalUser::create($jadwal);
        return response()->noContent();
    }
    
    public function getJadwal(Request $request, $id)
    {
        $jadwal = JadwalUser::where('user_id', $id)->latest()->get();
        return response()->json($jadwal);
    }
    
    public function import(Request $request)
    {
        Excel::import(new JadwalImport,  $request->file);
        toastr('Data Success To Add', 'success', 'Success !');
        return redirect()->back();
        
    }
}
