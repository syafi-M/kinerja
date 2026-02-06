<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\Kerjasama;
use App\Models\Overtime;
use App\Models\User;
use Carbon\Carbon;

class OvertimesController extends Controller
{
    public function index($kerjasama)
    {
        if (auth()->user()->kerjasama_id != 1) abort(403);
        if (! Kerjasama::where('id', $kerjasama)->exists()) {
            abort(404);
        }

        try {
            $startDate = Carbon::now()->startOfMonth()->startOfDay();
            $endDate = Carbon::now()->startOfMonth()->addDays(24)->endOfDay();

            $overtimes = Overtime::with(['user', 'user.jabatan', 'user.kerjasama.client'])
                ->whereBetween('date_overtime', [$startDate, $endDate])
                ->where('status', 'Di Ajukan')
                ->whereHas(
                    'user',
                    fn($q) =>
                    $q->where('kerjasama_id', $kerjasama)
                        ->whereHas('jabatan', function ($j) {
                            $j->where('type_jabatan', auth()->user()->jabatan_id == 14 ? 'CLEANING SERVICE' : 'SECURITY');
                        })
                )
                ->get();

            $employe = User::where('kerjasama_id', $kerjasama)->whereHas('Jabatan', function ($j) {
                $j->where('type_jabatan', auth()->user()->jabatan_id == 14 ? 'CLEANING SERVICE' : 'SECURITY');
            })->count();

            // Group by user_id AND type_overtime
            $groupedOvertimes = $overtimes->groupBy(function ($item) {
                return $item->user_id . '_' . strtolower($item->type_overtime);
            })->map(function ($group) {
                $first = $group->first();
                $typeOvertime = strtolower($first->type_overtime);

                if (in_array($typeOvertime, ['jam', 'lainnya'])) {
                    $totalJam = 0;
                    $totalRupiah = 0;

                    foreach ($group as $overtime) {
                        $value = $this->parseOvertimeValue($overtime->type_overtime_manual);
                        // dd($overtime->type_overtime_manual);
                        if ($value['type'] == 'jam') {
                            $totalJam += $value['value'];
                        } elseif ($value['type'] == 'rupiah') {
                            $totalRupiah += $value['value'];
                        }
                    }

                    $formattedOvertime = '';
                    if ($totalJam > 0 && $totalRupiah > 0) {
                        $formattedOvertime = $totalJam . ' Jam + Rp ' . number_format($totalRupiah, 0, ',', '.');
                    } elseif ($totalJam > 0) {
                        $formattedOvertime = $totalJam . ' Jam';
                    } elseif ($totalRupiah > 0) {
                        $formattedOvertime = 'Rp ' . number_format($totalRupiah, 0, ',', '.');
                    }

                    return [
                        'id' => $first->id,
                        'user' => $first->user,
                        'date_overtime' => $first->date_overtime,
                        'type_overtime' => $first->type_overtime,
                        'type_overtime_manual' => $formattedOvertime,
                        'total_jam' => $totalJam,
                        'total_rupiah' => $totalRupiah,
                        'status' => $first->status,
                        'desc' => $first->desc,
                        'count' => $group->count()
                    ];
                } else {
                    return [
                        'id' => $first->id,
                        'user' => $first->user,
                        'date_overtime' => $first->date_overtime,
                        'type_overtime' => $first->type_overtime,
                        'type_overtime_manual' => $first->type_overtime_manual,
                        'status' => $first->status,
                        'desc' => $first->desc,
                        'count' => $group->count()
                    ];
                }
            })->values();

            return response()->json([
                'success' => true,
                'data' => $groupedOvertimes,
                'users_count' => $employe,
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

    private function parseOvertimeValue($text)
    {
        if (!$text || !is_string($text)) {
            return ['type' => 'none', 'value' => 0];
        }

        $clean = preg_replace('/[^0-9]/', '', $text);

        if ($clean === '') {
            return ['type' => 'none', 'value' => 0];
        }

        $value = intval($clean);

        // 10–500 dianggap JAM
        if ($value >= 1 && $value <= 500) {
            return ['type' => 'jam', 'value' => $value];
        }

        // >= 1000 dianggap RUPIAH
        if ($value >= 1000) {
            return ['type' => 'rupiah', 'value' => $value];
        }

        return ['type' => 'none', 'value' => 0];
    }

    public function show(string $id)
    {
        if (auth()->user()->kerjasama_id != 1) abort(403);
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
}
