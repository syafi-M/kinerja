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
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Images;

class CheckPointController extends Controller
{
   public function index(Request $request)
   {
    $filter = $request->search;
    $type = $request->type;
    $filter2 = Carbon::parse($filter);

    // Menghitung tanggal awal dua minggu yang lalu
    $tanggalAwal = Carbon::now()->startOfWeek()->subWeeks(1);

    // Menghitung tanggal akhir minggu sekarang
    $tanggalAkhir = Carbon::now()->endOfWeek();

    if ($type) {
        $cek = CheckPoint::where('user_id', Auth::user()->id)->where('type_check', $type)->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])->paginate(90)->first();
    } else {
        $cek = CheckPoint::where('user_id', Auth::user()->id)->paginate(90)->first();
    }
    
    $pcp = PekerjaanCp::where('kerjasama_id', Auth::user()->kerjasama_id)->get();
    
    $currentMonth = Carbon::now()->month;
     
       
    return view('check.index', compact('cek', 'pcp', 'type'));

   }

   public function create()
   {
    $id = null;
    $user = Auth::user()->id;
    $c = CheckPoint::where('user_id', $user)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();
    $pcp = PekerjaanCp::where('user_id', Auth::user()->id)->get();
    $pch = Checkpoint::where('user_id', Auth::user()->id)->where('type_check', 'harian')->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();
    
    $che = CheckPoint::query()->where('user_id', Auth::user()->id)->where('type_check', 'harian')->whereDate('created_at', Carbon::now()->format('Y-m-d'));
    $cheli = $che->selectRaw('pekerjaan_cp_id')->get();
    // dd($cheli);
    return view('check.create', compact('pcp', 'c', 'cheli', 'pch', 'id'));

   }

   public function store(Request $request)
   {
        $cek = new CheckPoint();

        $data  = [
            'user_id' => $request->user_id,
            'divisi_id' => $request->divisi_id,
            'pekerjaan_cp_id' => $request->pekerjaan_id,
            'img' => $request->img,
            'deskripsi' => $request->deskripsi,
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude,
            // 'approve_status' => $request->approve_status,
            'type_check' => 'rencana'
        ];
        // dd($data);
        
        $imagePaths = [];
        
        if($request->hasFile('img'))
        {
             foreach ($request->file('img') as $image) {
                
                $file = $request->file('img');
                if ($file != null && $file->isValid()) {
                    
                $img = Image::make($file);
                $imageSize = $img->filesize();
                
                $image = Images::make($file);
                $extensions = $file->getClientOriginalExtension();
                $randomNumber = mt_rand(1, 999999999999);
                $rename = 'data' . $randomNumber . '.' . $extensions;
                
                $path = public_path('storage/images/' . $rename);
                $img = Images::make($file->getRealPath());
                $img->save($path, 13);
            
                $imagePaths[] = $rename; 
                }
                $data['img'] = implode(',', $imagePaths); 
             }
            
        }

        DB::beginTransaction();

        try {
            $cek->create($data);
            DB::commit();
            toastr()->success('Data Berhasil Ditambahkan', 'success');
            return to_route('checkpoint-user.index');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            toastr()->error('Error in storing data', 'error');
            return redirect()->back();
        }
            
   }

   public function edit($id)
   {
    // $user = Auth::user()->id;
    $c = CheckPoint::where('id', $id)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();
    // $pch = Checkpoint::where('user_id', Auth::user()->id)->where('type_check', 'harian')->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();
    $awalMinggu = Carbon::now()->startOfWeek();
    $akhirMinggu = Carbon::now()->endOfWeek()->subDays(2); 
    $cex = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
    ->where('id', $id)
    ->latest()
    ->first();
    $pcp = PekerjaanCp::where('user_id', $cex->user_id)->get();

    $che = CheckPoint::query()->where('user_id', Auth::user()->id)->where('type_check', 'harian')->whereDate('created_at', Carbon::now()->format('Y-m-d'));
    $cheli = $che->selectRaw('pekerjaan_cp_id')->get();
    // dd($cheli);
    return view('check.create', compact('pcp', 'c','cex', 'cheli', 'id'));

   }

   public function update(Request $request, $id)
   {
    $awalMinggu = Carbon::now()->startOfWeek();
    $akhirMinggu = Carbon::now()->endOfWeek()->subDays(2); 
    $cex2 = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
        ->where('id', $id)
        ->latest()
        ->first();

        // $cex2->user_id = $request->user_id;
        // $cex2->divisi_id = $request->divisi_id;
        // $cex2->latitude = $request->latitude;
        // $cex2->longtitude = $request->longtitude;
        // $cex2->approve_status = $request->approve_status;
        // $cex2->type_check = 'rencana';

       // Get the existing pekerjaan_cp_id array from the CheckPoint model or initialize it as an empty array if it doesn't exist
        $pekerjaanCpId = $cex2->pekerjaan_cp_id ?? [];

        // Get the new pekerjaan_id values from the request and filter out those that already exist in pekerjaan_cp_id
        $newPekerjaanIds = collect($request->pekerjaan_id ?? [])
            ->reject(function ($value) use ($pekerjaanCpId) {
                return in_array($value, $pekerjaanCpId);
            })
            ->toArray();

        // Filter out the values in pekerjaan_cp_id that are not present in $request->pekerjaan_id
        $pekerjaanCpId = array_filter($pekerjaanCpId, function ($value) use ($request) {
            return in_array($value, $request->pekerjaan_id ?? []);
        });

        // Merge the filtered new pekerjaan_id values with the existing pekerjaan_cp_id array
        $pekerjaanCpId = array_merge($pekerjaanCpId, $newPekerjaanIds);

        // Update the pekerjaan_cp_id attribute of the CheckPoint model
        $cex2->pekerjaan_cp_id = $pekerjaanCpId;

        // dd($request->all(),$cex2);

        try {
            
            $cex2->save();
            toastr()->success('Data berhasil diedit', 'success');
            return redirect()->back();
            // return to_route('checkpoint-user.index', 'type=dikerjakan');

        } catch(\Illuminate\Database\QueryException $e){
            dd($e);
            toastr()->error('Data Tidak Ada', 'error');
            return redirect()->back();
        }
   }
   
   public function editBukti(Request $request)
   {

    $cId = $request->cpId;

    $pcp = PekerjaanCp::all();
    // $cex = CheckPoint::latest()->first();
    $awalMinggu = Carbon::now()->startOfWeek();
    $akhirMinggu = Carbon::now()->endOfWeek()->subDays(2); // Mengurangi 2 hari untuk mendapatkan hari Jumat sebagai akhir minggu
    if ($cId) {
        $cex = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
            ->where('type_check', 'rencana')
            ->where('id', $cId)
            ->latest()
            ->first();
    } else {
        $cex = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
            ->where('user_id', Auth::user()->id)
            ->where('type_check', 'rencana')
            ->latest()
            ->first();
    }
    
    // dd($cex);
    return view('check.editBukti', compact('cex', 'pcp', 'cId'));
   }
   
   public function uploadBukti(Request $request)
{
    $awalMinggu = Carbon::now()->startOfWeek();
    $akhirMinggu = Carbon::now()->endOfWeek()->subDays(2); // Mengurangi 2 hari untuk mendapatkan hari Jumat sebagai akhir minggu
    $cex = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
        ->where('user_id', Auth::user()->id)
        ->where('type_check', 'rencana')
        ->latest()
        ->first();
    $cex2 = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
        ->where('user_id', Auth::user()->id)
        ->where('type_check', 'dikerjakan')
        ->latest()
        ->first();

    if (!$cex2) {
        $cek = [
            'user_id' => $request->user_id,
            'divisi_id' => $request->divisi_id,
            'pekerjaan_cp_id' => $request->pekerjaan_cp_id,
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude,
            'approve_status' => $request->approve_status,
            'note' => $request->note,
            'type_check' => 'dikerjakan'
        ];
    } else {
        $cex2->user_id = $request->user_id;
        $cex2->divisi_id = $request->divisi_id;
        $cex2->latitude = $request->latitude;
        $cex2->longtitude = $request->longtitude;
        $cex2->approve_status = $request->approve_status;
        $cex2->type_check = 'dikerjakan';

        // Append new pekerjaan_cp_id values
        $pekerjaanCpId = $cex2->pekerjaan_cp_id ?? [];
        $pekerjaanCpId = array_merge($pekerjaanCpId, $request->pekerjaan_cp_id ?? []);
        $cex2->pekerjaan_cp_id = $pekerjaanCpId;

        // Append new approve_status values
        $approve_status = $cex2->approve_status ?? [];
        $approve_status = array_merge($approve_status, $request->approve_status ?? []);
        $cex2->approve_status = $approve_status;
        // $cex2->pekerjaan_cp_id = array_unique($pekerjaanCpId);
    }
    
    $imagePaths = [];
    if ($request->hasFile('img')) {
        foreach ($request->file('img') as $image) {
            if ($image != null) {
                $imag = Images::make($image);
                $extensions = $image->getClientOriginalExtension();
                $randomNumber = mt_rand(1, 999999999999);
                $rename = 'data' . $randomNumber . '.' . $extensions;
                $path = public_path('storage/images/' . $rename);
                $imag->save($path, 13);
                // Add the image path to the array
                $imagePaths[] = $rename; 
            }
        }
        // Append new image paths
        if (!$cex2) {
            $cek['img'] = $imagePaths;
        } else {
            $cex2->img = array_merge($cex2->img ?? [], $imagePaths);
            $cex2->approve_status = array_merge($cex2->approve_status, $request->approve_status);
        }
        
    }

    // Filter out null values from deskripsi array
    if ($request->has('deskripsi')) {
        $deskripsi = array_filter($request->deskripsi, function ($value) {
            return $value !== null;
        });
        $notes = array_filter($request->note, function ($value) {
            return $value !== null;
        });
        // Append new deskripsi values
        if (!$cex2) {
            $cek['deskripsi'] = $deskripsi;
            $cek['note'] = $notes;
        } else {
            $cex2->deskripsi = array_merge($cex2->deskripsi ?? [], $deskripsi);
            $cex2->note = array_merge($cex2->note ?? [], $notes);
        }
        
    }


    // dd($request->all(),$cex2);
    
    try {
        if (!$cex2) {
            CheckPoint::create($cek);
        } else {
            $cex2->save();
        }
        
        toastr()->success('Data berhasil diedit', 'success');
        return to_route('checkpoint-user.index', 'type=dikerjakan');

    } catch(\Illuminate\Database\QueryException $e){
        dd($e);
        toastr()->error('Data Tidak Ada', 'error');
        return redirect()->back();
    }
}
   public function uploadNilai(Request $request, $id)
{
    $awalMinggu = Carbon::now()->startOfWeek();
    $akhirMinggu = Carbon::now()->endOfWeek()->subDays(2); 
    $cex2 = CheckPoint::whereBetween('created_at', [$awalMinggu, $akhirMinggu])
        ->where('id', $id)
        ->latest()
        ->first();

        $arrKe = $request->arrKe;

        // $cex2->approve_status = array_merge($cex2->approve_status ?? [], $request->approve_status);
        // $cex2->note = array_merge($cex2->note ?? [], $request->note);
        // Retrieve the existing approve_status and note arrays
    $approveStatus = $cex2->approve_status ?? [];
    $note = $cex2->note ?? [];

    // Update the values at the specified index
    if (isset($request->approve_status)) {
        $approveStatus[$arrKe] = $request->approve_status[0];
    }

    if (isset($request->note)) {
        $note[$arrKe] = $request->note[0];
    }

    // Assign the modified arrays back to the properties
    $cex2->approve_status = $approveStatus;
    $cex2->note = $note;
        
        // dd($request->all(),$cex2);

        try {
            
            $cex2->save();
            toastr()->success('Data berhasil diedit', 'success');
            return redirect()->back();
            // return to_route('checkpoint-user.index', 'type=dikerjakan');

        } catch(\Illuminate\Database\QueryException $e){
            dd($e);
            toastr()->error('Data Tidak Ada', 'error');
            return redirect()->back();
        }
}


   

   public function deleteRencana(Request $request, $id)
   {
       $cek = CheckPoint::findOrFail($id);
       try {
        // Check if arrKe exists in the request
        if ($request->has('arrKe')) {
            $arrKe = $request->arrKe;

            // Check if arrKe is a valid index in the array
            if (isset($cek->pekerjaan_cp_id[$arrKe])) {
                // Create a copy of the arrays
                $pekerjaan_cp_id = $cek->pekerjaan_cp_id;

                // Unset the item at the specified index
                unset($pekerjaan_cp_id[$arrKe]);

                // Reset array keys to maintain continuity
                $pekerjaan_cp_id = array_values($pekerjaan_cp_id);

                // Assign modified arrays back to the object properties
                $cek->pekerjaan_cp_id = $pekerjaan_cp_id;

                // dd($cek);
                // Save the changes to the model
                $cek->save();

                toastr()->warning('Data Telah Dihapus', 'warning');
                return redirect()->back();
            } else {
                toastr()->error('Index Tidak Valid', 'error');
                return redirect()->back();
            }
        } else {
            toastr()->error('Parameter arrKe Tidak Ditemukan', 'error');
            return redirect()->back();
        }
    } catch (\Illuminate\Database\QueryException $e) {
        toastr()->error('Data Tidak Ditemukan', 'error');
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
