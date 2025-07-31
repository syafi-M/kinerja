<?php

namespace App\Http\Controllers;

use App\Models\SlipGaji;
use App\Models\User;
use App\Models\Kerjasama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class SlipGajiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bulan = $request->bulan;
        
        $slip = SlipGaji::on('mysql2')->where('user_id', Auth::user()->id)->where('bulan_tahun', $bulan)->latest()->first();
        
        return view('slip.index', compact('slip', 'bulan'));
    }

    /**
     * Show the form for creating a new resource.
     */
     
    public function exportWith(Request $request)
    {
        $path = 'logo/sac.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $options = new Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $id = $request->slip_id;
        
        $slip = SlipGaji::on('mysql2')->find($id);
        $formatedMonth = Carbon::createFromFormat('Y-m', $slip->bulan_tahun)->isoFormat('M Y');
        // dd($slip);

        $pdf = new Dompdf($options);
        $html = view('slip.export', compact('slip'))->render();
        $pdf->loadHtml($html);

        $customPaper = array(0.0, 0.0, 2481 / 96 * 72, 2883 / 96 * 72);
        $pdf->setPaper($customPaper, 'portrait');
        $pdf->render();

        $output = $pdf->output();
        $filename = 'Slip Gaji Saya Bulan '.$formatedMonth.'.pdf';

        if ($request->input('action') == 'download') {
            return response()->download($output, $filename);
        }

        return response($output, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
    }
    
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SlipGaji $slipGaji)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SlipGaji $slipGaji)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SlipGaji $slipGaji)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SlipGaji $slipGaji)
    {
        //
    }
    
    
    public function leaderIndex(Request $request)
    {
        $bulan = $request->bulan;
        $mitra = Kerjasama::all();
        
        $penempatan = $request->penempatan;
        
        if(Auth::user()->divisi->jabatan_id == 10){
            $user = User::where('kerjasama_id', Auth::user()->kerjasama_id)->where('jabatan_id', [9, 10])->pluck('id');
        }else if(Auth::user()->divisi->jabatan_id == 11){
            $user = User::where('kerjasama_id', Auth::user()->kerjasama_id)->where('jabatan_id', [8, 11])->pluck('id');
        }else if(Auth::user()->devisi_id == 26){
            $user = User::where('kerjasama_id', '!=', 1)->when($penempatan, function ($query, $penempatan) { return $query->where('kerjasama_id', $penempatan); })->whereNotIn('devisi_id', [6, 11, 23, 13])->pluck('id');
        }else if(Auth::user()->id == 175) {
            $user = User::where('jabatan_id', 8)->pluck('id');
        }else {
            $user = User::where('kerjasama_id', '!=', 1)->orderBy('kerjasama_id', 'asc')->when($penempatan, function ($query, $penempatan) { return $query->where('kerjasama_id', $penempatan); })->whereNotIn('id', [289, 387])->pluck('id');
        }
        $slip = SlipGaji::with('User')->whereIn('user_id', $user)->where('bulan_tahun', $bulan ? $bulan : Carbon::now()->subMonth()->format('Y-m'))->get();
        // dd($slip[0]->user);
        
        return view('leader_view.slip.index', compact('slip', 'bulan', 'mitra', 'penempatan'));
    }
}
