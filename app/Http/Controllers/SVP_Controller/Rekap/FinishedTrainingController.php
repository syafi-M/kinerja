<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Models\FinishedTraining;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinishedTrainingController extends Controller
{
    public function index(Request $request, $kerjasama)
    {
        \App\Models\Kerjasama::findOrFail($kerjasama);

        $items = FinishedTraining::with(['user' => function ($q) {
            $q->with(['jabatan', 'kerjasama.client']);
        }])
            ->where('status', 'Di Ajukan')
            ->whereHas('user', function ($q) use ($kerjasama) {
                $q->where('kerjasama_id', $kerjasama);
            })
            ->when($request->month, function ($q) use ($request) {
                $date = Carbon::createFromFormat('Y-m', $request->month);
                $q->whereYear('date_finish_train', $date->year)
                    ->whereMonth('date_finish_train', $date->month);
            })
            ->orderByDesc('date_finish_train')
            ->get()
            ->map(function ($item) {
                $item->masa_training_hari = Carbon::parse($item->date_finish_train)->day;
                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => $items,
            'message' => 'Data lepas training berhasil diambil'
        ]);
    }
}
