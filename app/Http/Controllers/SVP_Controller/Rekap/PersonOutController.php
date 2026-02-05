<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Models\PersonOut;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PersonOutController extends Controller
{
    public function index(Request $request, $kerjasama)
    {
        if (auth()->user()->kerjasama_id != 1) abort(403);

        $startDate = Carbon::now()->startOfMonth()->startOfDay();
        $endDate = Carbon::now()->startOfMonth()->addDays(24)->endOfDay();

        $personOut = PersonOut::with([
            'user' => function ($q) {
                $q->withTrashed()->with(['jabatan', 'kerjasama.client']);
            }
        ])->where('status', 'Di Ajukan')->whereHas('user', function ($q) use ($kerjasama) {
            $q->withTrashed()->where('kerjasama_id', $kerjasama);
        })->whereBetween('out_date', [$startDate, $endDate])
            ->when($request->month, function ($q) use ($request) {
                try {
                    $date = Carbon::createFromFormat('Y-m', $request->month);
                    $q->whereYear('out_date', $date->year)
                        ->whereMonth('out_date', $date->month);
                } catch (\Exception $e) {
                    throw $e;
                }
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $personOut,
            'message' => 'Data personil keluar berhasil diambil'
        ]);
    }
}
