<?php

namespace App\Http\Controllers;

use App\Models\SlipGaji;
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
        
        $slip = SlipGaji::on('mysql2')->where('user_id', Auth::user()->id)->where('bulan_tahun', $bulan)->first();
        
        return view('slip.index', compact('slip'));
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
        $filename = 'Slip Gaji Bulan '.$formatedMonth.'.pdf';

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
}
