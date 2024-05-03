<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaporanRequest;
use App\Models\Laporan;
use App\Models\Ruangan;
use App\Models\Kerjasama;
use App\Models\User;
use App\Models\ListPekerjaan;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{

    public function __construct(Request $request)
    {
        $this->str = $request->input('str1');
        $this->ended = $request->input('end1');
    }
    
    public function getcode($ruanganId, $kerjasamaId)
    {
        // echo $ruanganId, $kerjasamaId;
        return $this->create($ruanganId, $kerjasamaId);
    }

    public function index()
    {
        try {
            if (Auth::user()->role_id == 1) {
                $laporan = Laporan::orderBy('created_at', 'desc')->where('client_id', Auth::user()->kerjasama->client_id)->paginate(90);
                return view('laporan.index', ['laporan' => $laporan]);
            }elseif(Auth::user()->role_id == 2)
            {
                $mitra = Kerjasama::all();
                $ruangan = Ruangan::all();
                $laporan = Laporan::paginate(25);
                return view('laporan.index', ['laporan' => $laporan, 'mitra' => $mitra, 'ruangan' => $ruangan]);
            }elseif(Auth::user()->divisi->jabatan->code_jabatan == 'MITRA'){
                $ker = Auth::user()->kerjasama->client_id;
                $laporan = Laporan::where('client_id', $ker)->paginate(25);
                return view('laporan.index', ['laporan' => $laporan]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }

    }
    public function create($ruanganId, $kerjasamaId)
    {
        $kerjasama = Kerjasama::where('id', $kerjasamaId)->first();
        $ruangan = Ruangan::where('kerjasama_id', $kerjasamaId)->where('id', $ruanganId)->first();
        $listPekerjaan = ListPekerjaan::where('ruangan_id', $ruanganId)->get();
        return view('laporan.create', ['ruangan' => $ruangan, 'listPekerjaan' => $listPekerjaan, 'kerjasama' => $kerjasama]);
    }

    public function store(LaporanRequest $request)
    {
        $laporan = new Laporan();

        $laporan = [
            'user_id' => $request->user_id,
            'client_id' => $request->client_id,
            'ruangan_id' => $request->ruangan_id,
            'image1' => $request->image1,
            'image2' => $request->image2,
            'image3' => $request->image3,
            'image4' => $request->image4,
            'image5' => $request->image5,
            'keterangan' => $request->keterangan,
            'pekerjaan' => json_encode($request->pekerjaan),
            'nilai' => $request->nilai
        ];
        
        // dd($laporan);
            $allowedImages = ['image1', 'image2', 'image3', 'image4', 'image5'];

        try {
            
            foreach ($allowedImages as $imageField) {
                if ($request->hasFile($imageField)) {
                    $laporan[$imageField] = UploadImage($request, $imageField);
                }
            }
            
            Laporan::create($laporan);
            toastr()->success('Laporan Berhasil Disimpan', 'success');
            return to_route('laporan.index');
        } catch (\Throwable $th) {
            // dd($th);
            toastr()->error('Image Must Be Insert Or Large Image', 'error');
            return redirect()->back();
        }

    }

    public function destroy($id) // Admin
    {

        try {
            $laporan = Laporan::findOrFail($id);
            $imageFields = ['image1', 'image2', 'image3', 'image4', 'image5'];

            foreach ($imageFields as $imageField) {
                if ($laporan->$imageField) {
                    Storage::disk('public')->delete('images/' . $laporan->$imageField);
                }
            }
            
            $laporan->delete();
            toastr()->warning('Laporan Berhasil Dihapus', 'warning');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Laporan Tidak Ditemukan', 'error');
        }
    }
    public function exportWith(Request $request)
    {
        $tanggalSekarang = Carbon::now();
        $currentMonth = Carbon::parse($this->ended)->month;
        $currentYear = Carbon::parse($this->str)->year;
        $str1 = $this->str;
        $end1 = $this->ended;
        
        $mitra = $request->input('client_id');
        $ruangan = $request->input('ruangan_id');
        
        $kerjasama = Kerjasama::firstWhere('id', $mitra);
        
        // dd($request->all());
        
        $totalHari =  Carbon::parse($this->ended)->diffInDays(Carbon::parse($this->str));
        
        if($request->has(['client_id', 'end1', 'str1'])) {
            
        //  $expPDF = User::with(['laporan' => function ($query) use ($str1, $end1) {
        //     return $query->whereBetween('created_at', [$str1, $end1]);
        // }])->when($mitra, function($query) use ($mitra) {
        //      $query->whereHas('kerjasama', function ($subQuery) use ($mitra) {
        //         $subQuery->where('client_id', $mitra);
        //     });
        // })->get();
        
        if($str1 != $end1 && !$request->has('ruangan_id')){
            // $expPDF = Laporan::with(['User'])->whereBetween('created_at', [$str1, $end1])->where('client_id', $mitra)->where('nilai', '!=', 'baik')->get();
            $expPDF = Laporan::with(['User'])->whereBetween('created_at', [$str1, $end1])->where('client_id', $mitra)->get();
        }else if($str1 != $end1 && $request->has('ruangan_id')){
            $expPDF = Laporan::with(['User'])->whereBetween('created_at', [$str1, $end1])->where('client_id', $mitra)->where('ruangan_id', $ruangan)->get();
        }else{
            $expPDF = Laporan::with(['User'])->whereDate('created_at', $str1)->where('client_id', $mitra)->get();
        }
        
        // dd($expPDF);

        $path = 'logo/sac.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('compression', true);
        // $options->set('imageQuality', 1);

        $pdf = new Dompdf($options);
        $html = view('laporan.export', compact('expPDF', 'base64', 'totalHari', 'currentYear', 'currentMonth', 'str1', 'end1', 'mitra', 'kerjasama'))->render();
        $pdf->loadHtml($html);

        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $output = $pdf->output();
        $filename = 'laporan.pdf';

        if ($request->input('action') == 'download') {
            return response()->stream(function () use ($output) {
                echo $output;
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                
            ]);
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
