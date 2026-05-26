<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SVP_Controller\Rekap\Concerns\HasAllowedSeeData;
use App\Http\Controllers\SVP_Controller\Rekap\Concerns\TransformOvertimes;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\Kerjasama;
use App\Models\Overtime;
use App\Models\User;
use Carbon\Carbon;

class OvertimesController extends Controller
{
    use HasAllowedSeeData, TransformOvertimes;

    public function index($kerjasama)
    {
        if (!Kerjasama::where('id', $kerjasama)->exists()) {
            abort(404);
        }

        try {
            $overtimes = Overtime::with([
                'user',
                'user.jabatan',
                'user.kerjasama.client',
                'createdBy'
            ])
                ->whereIn('status', ['Di Ajukan', 'Di Setujui', 'Di Tolak'])
                ->whereHas('user', function ($q) use ($kerjasama) {
                    $q->where('kerjasama_id', $kerjasama)
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                })
                ->latest()
                ->get();

            $employee = User::where('kerjasama_id', $kerjasama)->count();

            return response()->json([
                'success' => true,
                'data' => $this->transformOvertimes($overtimes),
                'users_count' => $employee,
                'message' => 'Data lembur berhasil diambil'
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Gagal mengambil data lembur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $overtime = Overtime::with('user')
                ->where('id', $id)
                ->firstOrFail();

            return $this->success(
                $overtime,
                'Detail lembur berhasil diambil'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error(
                'Data lembur tidak ditemukan',
                404
            );
        }
    }
    public function updateStatus(Request $request, $id)
    {
        try {
            $overtime = Overtime::findOrFail($id);
            $status = $request->input('status');
            if (!in_array($status, ['Di Setujui', 'Di Tolak'])) {
                return response()->json(['success' => false, 'message' => 'Status tidak valid'], 422);
            }
            $overtime->update(['status' => $status]);
            return response()->json(['success' => true, 'message' => 'Status berhasil diupdate']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
