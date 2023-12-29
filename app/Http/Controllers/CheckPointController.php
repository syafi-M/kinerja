<?php

namespace App\Http\Controllers;

use App\Models\CheckPoint;
use App\Models\User;
use App\Models\PekerjaanCp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;
use Intervention\Image\Facades\Image;

class CheckPointController extends Controller
{
   public function index(Request $request)
   {
    $filter = $request->search;
    $filter2 = Carbon::parse($filter);
    $pcp = PekerjaanCp::where('kerjasama_id', Auth::user()->kerjasama_id)->get();
    
    if($filter){
        $cek = CheckPoint::where('user_id', Auth::user()->id)->whereMonth('created_at', $filter2->month)->paginate(90);
        $currentMonth = Carbon::now()->month;
    }else{
        $currentMonth = Carbon::now()->month;
        $cek = CheckPoint::where('user_id', Auth::user()->id)->whereMonth('created_at', $currentMonth)->paginate(90);
    }
       
    return view('check.index', compact('cek'));

   }

   public function create()
   {
    $user = Auth::user()->id;
    $c = CheckPoint::where('user_id', $user)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();
    $pcp = PekerjaanCp::where('user_id', Auth::user()->id)->get();
    $pch = Checkpoint::where('user_id', Auth::user()->id)->where('type_check', 'harian')->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();
    
    $che = CheckPoint::query()->where('user_id', Auth::user()->id)->where('type_check', 'harian')->whereDate('created_at', Carbon::now()->format('Y-m-d'));
    $cheli = $che->selectRaw('pekerjaan_cp_id')->get();
    // dd($cheli);
    return view('check.create', compact('pcp', 'c', 'cheli', 'pch'));

   }

   public function store(Request $request)
   {
        $cek = new CheckPoint();

        $cek = [
            'user_id' => $request->user_id,
            'divisi_id' => $request->divisi_id,
            'pekerjaan_cp_id' => $request->pekerjaan_id,
            'type_check' => $request->type_check,
            'img' => $request->img,
            'deskripsi' => $request->deskripsi,
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude
        ];
        
        if($request->hasFile('img'))
        {
            $cek['img'] = UploadImageV2($request, 'img');
        }else{
            toastr()->error('Foto harus ditambahkan', 'error');
            return redirect()->back();
        }

        CheckPoint::create($cek);
        toastr()->success('Data Berhasil Ditambahkan', 'succes');
        return to_route('checkpoint-user.index');

        
        
   }

   public function edit($id)
   {
    $pcp = PekerjaanCp::all();
    $cex = CheckPoint::findOrFail($id);
    return view('check.edit', compact('cex', 'pcp'));

   }

   public function update(Request $request, $id)
   {

    $cek = [
        'user_id' => $request->user_id,
        'divisi_id' => $request->divisi_id,
        'pekerjaan_cp_id' => $request->pekerjaan_id,
        'type_check' => $request->type_check,
        'img' => $request->img,
        'deskripsi' => $request->deskripsi,
        'latitude' => $request->latitude,
        'longtitude' => $request->longtitude
    ];

    if($request->hasFile('img'))
        {
            if($request->oldimage)
            {
                Storage::disk('public')->delete('images/' . $request->oldimage);
            }

            $cek['img'] = UploadImageV2($request, 'img');
        }else{
            $cek['img'] = $request->oldimage;
        }
         try {
            CheckPoint::findOrFail($id)->update($cek);
            toastr()->success('Data berhasil diedit', 'success');
            return redirect()->to(route('checkpoint-user.index'));

        } catch(\Illuminate\Database\QueryException $e){
           toastr()->error('Data Tidak Ada', 'error');
           return redirect()->back();
        }
   }

   public function destroy($id)
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
   
    public function exportWith(Request $request)
    {
        
        $currentMonth = $request->this_month;
        $user_id = $request->user_id;
        
        $user = User::firstWhere('id', $user_id);
        $nowMonth = Carbon::createFromFormat('Y-m', $currentMonth)->translatedFormat('F Y');

        // dd($request->all());
        
        if($request->has(['this_month', 'user_id'])) {
            
        $cp = CheckPoint::whereMonth('created_at', Carbon::createFromFormat('Y-m', $currentMonth))->where('user_id', $user_id)->get();
        // dd($cp, $currentMonth);
        $path = 'logo/sac.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $pdf = new Dompdf($options);
        $html = view('admin.check.export', compact('base64', 'cp', 'user', 'nowMonth'))->render();
        $pdf->loadHtml($html);

        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        $output = $pdf->output();
        $filename = 'check_point.pdf';

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
    
    public function show($id)
    {
        $cex = CheckPoint::findOrFail($id);
        return view('admin.check.maps', compact('cex'));
    }

}
