<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\SVP_Controller\Rekap\Concerns\HasAllowedSeeData;
use App\Models\PersonOut;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PersonOutController extends RekapController
{
    use HasAllowedSeeData;
    public function index(Request $request, $kerjasama)
    {
        $startDate = Carbon::now()->startOfMonth()->startOfDay();
        $endDate = Carbon::now()->startOfMonth()->addDays(24)->endOfDay();

        $personOut = PersonOut::with([
            'user' => function ($q) {
                $q->withTrashed()->with(['jabatan', 'kerjasama.client']);
            }
        ])->whereIn('status', ['Di Ajukan', 'Di Setujui', 'Di Tolak'])->whereHas('user', function ($q) use ($kerjasama) {
            $q->withTrashed()->where('kerjasama_id', $kerjasama)
                ->whereIn('jabatan_id', $this->allowedSeeData());
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
    public function updateStatus(Request $request, $id)
    {
        try {
            $personOut = PersonOut::findOrFail($id);
            $status = $request->input('status');
            if (!in_array($status, ['Di Setujui', 'Di Tolak'])) {
                return response()->json(['success' => false, 'message' => 'Status tidak valid'], 422);
            }
            $personOut->update(['status' => $status]);
            return response()->json(['success' => true, 'message' => 'Status berhasil diupdate']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
