<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Models\PerformanceCuts;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CuttingController extends Controller
{
    public function index(Request $request, $kerjasama)
    {
        \App\Models\Kerjasama::findOrFail($kerjasama);

        $cuttings = PerformanceCuts::with(['user' => function ($q) {
            $q->with(['jabatan', 'kerjasama.client']);
        }])
            ->where('status', 'Di Ajukan')
            ->whereHas('user', function ($q) use ($kerjasama) {
                $q->where('kerjasama_id', $kerjasama);
            })
            ->when($request->month, function ($q) use ($request) {
                $date = Carbon::createFromFormat('Y-m', $request->month);
                $q->whereYear('date_cut', $date->year)
                    ->whereMonth('date_cut', $date->month);
            })
            ->orderByDesc('date_cut')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cuttings,
            'message' => 'Data cutting berhasil diambil'
        ]);
    }
}
