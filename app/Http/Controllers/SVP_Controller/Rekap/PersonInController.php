<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Models\PersonIn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PersonInController extends Controller
{
    public function index(Request $request, $kerjasama)
    {
        $kerjasamaModel = \App\Models\Kerjasama::findOrFail($kerjasama);

        $personIn = PersonIn::with('jabatan')
            ->where('client_id', $kerjasamaModel->client_id)
            ->where('status', 'Di Ajukan')
            ->when($request->month, function ($q) use ($request) {
                $date = Carbon::createFromFormat('Y-m', $request->month);
                $q->whereYear('date_in', $date->year)
                    ->whereMonth('date_in', $date->month);
            })
            ->orderByDesc('date_in')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $personIn,
            'message' => 'Data personil masuk berhasil diambil'
        ]);
    }
}
