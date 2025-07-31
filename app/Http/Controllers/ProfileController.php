<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Status;
use App\Models\Kontrak;
use App\Models\Employe;
use App\Models\Kerjasama;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;

class ProfileController extends Controller
{
    
    public function index()
    {
        $dataUser = User::findOrFail(Auth::user()->id);
        $kontrak = Kontrak::latest()->where('nama_pk_kda', Auth::user()->nama_lengkap)->where('tgl_selesai_kontrak', '>=', Carbon::now()->format('Y-m-d'))->first();
        
        // if(Auth::user()->id == 11){
            // dd(Kontrak::latest()->where('nama_pk_kda', Auth::user()->nama_lengkap)->where('tgl_selesai_kontrak', '>=', Carbon::now()->format('Y-m-d'))->first());
        // }
        
        return view('profile.index', compact('dataUser', 'kontrak'));
    }
    
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, $id)
    {
        $datas = Status::all();
        $user = User::find($id);
        $dataUser = User::findOrFail($id);
        if ($dataUser != null) {
            return view('profile.edit', compact('dataUser', 'datas'));
        }
        toastr()->error('Data tidak tidak ditemukan', 'error');
        return redirect()->back();
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request, $id)
    {
        $user = [
            'nama_lengkap' => $request->nama_lengkap,
            'email'     => $request->email,
            'image'     => $request->image,
            'nik'       => Crypt::encryptString($request->nik),
            'no_hp'     => $request->no_hp
        ];
        

        if($request->hasFile('image'))
        {
            if($request->oldimage)
            {
                Storage::disk('public')->delete('images/' . $request->oldimage);
            }

            $user['image'] = UploadImageV2($request, 'image');
        }else{
            $user['image'] = $request->oldimage;
        }
        User::findOrFail($id)->update($user);
    
        toastr()->success('Data Berhasil diupdate', 'success');
        return to_route('profile.index');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'], 
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    
    public function indexKontrak(Request $request)
    {
        $kontrak = Kontrak::firstWhere('id', $request->id);
        // if (!$request->session()->has('seen_kontrak')) {
        //     $request->session()->put('seen_kontrak', false);
        // }

        $request->session()->regenerate();
        
        $sesi = $request->session();
        // dd($request->session());
        return view('profile.kontrak', compact('kontrak', 'sesi'));
    }
    
    public function requestKontrak()
    {
        // $kontrak = Kontrak::firstWhere('no_srt', $request->no);

        // $request->session()->regenerate();
        
        // $sesi = $request->session();
        // dd($request->session());
        return view('profile.pengajuanKontrak');
    }
    
    public function kirimRequest(Request $request)
    {
        $lastNo = Kontrak::latest()->pluck('no_srt')->first(); // e.g., "007/SAC/I/2025"
        $firstThree = substr($lastNo, 0, 3); // "007"
        $noLastKontrak = str_pad((intval($firstThree) + 1), 3, '0', STR_PAD_LEFT);
        
        $emp = Employe::where('client_id', Auth::user()->kerjasama->client_id)->orderBy('numbers', 'desc')->select('numbers', 'initials')->first();
        $nextNumber = str_pad(intval($emp->numbers) + 1, 4, '0', STR_PAD_LEFT);
        // dd($emp, $nextNumber);
        
        $monthNumber = Carbon::now()->month;

        $romanMonths = [
            1  => 'I',
            2  => 'II',
            3  => 'III',
            4  => 'IV',
            5  => 'V',
            6  => 'VI',
            7  => 'VII',
            8  => 'VIII',
            9  => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];
        
        $romanMonth = $romanMonths[$monthNumber];
        
        $employe = [
            'name' => $request->name,
            'ttl' => $request->tempat_lhr . ', ' . $request->tgl_lhr,
            'nik' => $request->nik,
            'no_kk' => Crypt::encryptString($request->no_kk),
            'no_ktp' => Crypt::encryptString($request->nik),
            'client_id' => $request->client_id,
            'numbers' => $nextNumber,
            'initials' => $emp->initials,
            'date_real' => '2999-01',
        ];
        $kontrak = [
            'no_srt' => $noLastKontrak . '/SAC/' . $romanMonth . '/' . Carbon::now()->year,
            'send_to_operator' => 0,
            'send_to_atasan' => 0,
            'nama_pk_kda' => $request->name,
            'tempat_lahir_pk_kda' => $request->tempat_lhr,
            'tgl_lahir_pk_kda' => $request->tgl_lhr,
            'nik_pk_kda' => $request->nik,
            'alamat_pk_kda' => $request->alamat_pk_kda,
            'jabatan_pk_kda' => Auth::user()->divisi->jabatan->name_jabatan,
            'unit_pk_kda' => Auth::user()->kerjasama->client->name,
            'status_pk_kda' => 'Pengajuan',
            'tgl_mulai_kontrak' => '2999-01-01',
            'tgl_selesai_kontrak' => '2999-12-01',
        ];
        // dd($employe, $kontrak);
        
        Kontrak::create($kontrak);
    
        toastr()->success('Form Pengajuan berhasil dikirim', 'success');
        return to_route('profile.index');
    }
    
    public function previewKontrak(Request $request)
    {
        if(Auth::user()->role_id == 2 || Auth::user()->jabatan_id == 24){
            $kontrak = Kontrak::where('id', $request->id)->first();
        }else{
            $kontrak = Kontrak::where('id', $request->id)->where('nama_pk_kda', Auth::user()->nama_lengkap)->first();
        }
        
        // if ($request->session()->has('seen_kontrak')) {
        //     $request->session()->put('seen_kontrak', true);
        // }
        $logoPath = public_path('logo/Stampel.png');
        $logoData = base64_encode(file_get_contents($logoPath));
        $Stampel = 'data:image/png;base64,' . $logoData;
        
        $logoPath2 = public_path('logo/tapak.png');
        $logoData2 = base64_encode(file_get_contents($logoPath2));
        $Tapak = 'data:image/png;base64,' . $logoData2;
        
        $logoPath3 = public_path('logo/headerSurat.png');
        $logoData3 = base64_encode(file_get_contents($logoPath3));
        $Header = 'data:image/png;base64,' . $logoData3;

        $request->session()->regenerate();
        // dd($Stampel, $Tapak, $Header);
        
        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Times New Roman');

        $pdf = new Dompdf($options);
        
        if($kontrak != null) {
            $html = view('profile.previewKontrak', compact('kontrak', 'Stampel', 'Tapak', 'Header'))->render();
            $pdf->loadHtml($html);
    
            $pdf->setPaper('Letter', 'portrait');
            $pdf->render();
            
            $output = $pdf->output();
            $filename = strtoupper('Kontrak ' . $kontrak->nama_pk_kda . ' Dibuat Pada ' . Carbon::createFromFormat('Y-m-d', $kontrak->tgl_dibuat)->translatedFormat('j F Y'));
            return response($output, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
        }else {
            toastr()->error('Data tidak valid', 'error');
            return to_route('profile.index');
        }
        // return $pdf->stream('profile.previewKontrak');
    }
    
    public function updateKontrak(Request $request, $id)
    {
        $svgData = $request->input('signature_svg');

        // Clean the base64 SVG
        $svgData = preg_replace('/^data:image\/svg\+xml;base64,/', '', $svgData);
        $svgDecoded = base64_decode($svgData);

        // $filename = 'signature_' . time() . '.svg';
        
        $kontrak = [
            'ttd' => $svgDecoded,
            'send_to_atasan' => 1
        ];
        
        // dd($kontrak, Kontrak::findOrFail($id));
        
        Kontrak::findOrFail($id)->update($kontrak);
    
        toastr()->success('Form kontrak berhasil dikirim', 'success');
        return to_route('profile.index');
    }
    
    // direksi
    public function cekKontrak(Request $request)
    {
        $filterMitra = $request->mitra ?? 3;
        $user = User::where('kerjasama_id', $filterMitra)->pluck('nama_lengkap');
        $kontrak = Kontrak::latest()->whereIn('nama_pk_kda', $user)->get();
        $mitra = Kerjasama::all();
        // dd($kontrak, $user);
        return view('leader_view.kontrak.check', compact('filterMitra', 'kontrak', 'mitra'));
    }
    public function accKontrak(Request $request)
    {
        // dd($request->all());
        $ids = $request->input('kontrak_ids', []);
        $acc = $request->boolean('acc');
        
        foreach ($ids as $id) {
            $kontrak = Kontrak::find($id);
            if ($kontrak) {
                $kontrak->ttd_atasan = $acc ? 1 : 0;
                $kontrak->send_to_atasan = 0;
                $kontrak->save();
            }
        }
        
        toastr()->success('Kontrak Berhasil di ' . ($acc ? 'Acc' : 'Tolak'), 'success');
        return redirect()->back();
    }
}
