<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Models\FinishedTraining;
use App\Models\Kerjasama;
use App\Models\Overtime;
use App\Models\KeteranganLanjutan;
use App\Models\PerformanceCuts;
use App\Models\PersonIn;
use App\Models\PersonOut;
use Carbon\Carbon;
use App\Http\Controllers\SVP_Controller\Rekap\Concerns\TransformOvertimes;
use Illuminate\Http\Request;

class AllRekapExportController extends RekapController
{
    use TransformOvertimes;

    public function getAllRekapData(Request $request, $kerjasama)
    {
        try {
            $month = $request->input('month', now()->format('Y-m'));
            $date = Carbon::createFromFormat('Y-m', $month);
            $startDate = $date->copy()->subMonth()->setDay(20)->startOfDay();
            $endDate = $date->copy()->setDay(25)->endOfDay();
            $includeAllStatus = false; // Debug parameter to include all status

            $kerjasamaData = Kerjasama::with('client:id,name')->findOrFail($kerjasama);
            $clientId = (int) $kerjasamaData->client_id;
            $clientName = $kerjasamaData->client?->name ?? 'Semua Mitra';

            if ($clientId <= 0) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'overtimes' => [],
                        'person_ins' => [],
                        'person_outs' => [],
                        'cuttings' => [],
                        'finished_trainings' => [],
                        'keterangan_lanjutan' => [],
                        'client' => ['name' => $clientName],
                        'period' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'),
                    ]
                ]);
            }

            // Build overtimes query
            $overtimesQuery = Overtime::with([
                'user:id,id,nama_lengkap,kerjasama_id,jabatan_id',
                'user.kerjasama:id,client_id',
                'user.kerjasama.client:id,name',
                'user.jabatan:id,name_jabatan'
            ])->whereHas('user', function ($q) use ($clientId) {
                $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                    ->whereIn('jabatan_id', $this->allowedSeeData());
            });
            if (!$includeAllStatus) $overtimesQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            $overtimesQuery = $overtimesQuery->join('users', 'overtimes.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $overtimesQuery->whereBetween('date_overtime', [$startDate, $endDate]);
            } else {
                $overtimesQuery->whereYear('date_overtime', $date->year)
                    ->whereMonth('date_overtime', $date->month);
            }
            $overtimes = $overtimesQuery->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('date_overtime')
                ->get();

            // Build person_ins query
            $personInsQuery = PersonIn::with(['jabatan:id,name_jabatan'])
                ->where('client_id', $clientId)
                ->whereIn('jabatan_id', $this->allowedSeeData());
            if (!$includeAllStatus) $personInsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            if ($startDate && $endDate) {
                $personInsQuery->whereBetween('date_in', [$startDate, $endDate]);
            } else {
                $personInsQuery->whereYear('date_in', $date->year)
                    ->whereMonth('date_in', $date->month);
            }
            $personIns = $personInsQuery->orderBy('date_in')->get();

            // Build person_outs query
            $personOutsQuery = PersonOut::with([
                'user' => fn($q) => $q->withTrashed(),
                'user.kerjasama.client',
                'user.jabatan:id,name_jabatan',
                'createdBy:id,nama_lengkap'
            ])->whereHas('user', function ($q) use ($clientId) {
                $q->withTrashed()
                    ->whereNotNull('nama_lengkap')
                    ->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                    ->whereIn('jabatan_id', $this->allowedSeeData());
            });
            if (!$includeAllStatus) $personOutsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            $personOutsQuery = $personOutsQuery->join('users', 'person_outs.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $personOutsQuery->whereBetween('out_date', [$startDate, $endDate]);
            } else {
                $personOutsQuery->whereYear('out_date', $date->year);
            }
            $personOuts = $personOutsQuery->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('out_date')
                ->get();

            // Build cuttings query
            $cuttingsQuery = PerformanceCuts::with(['user:id,nama_lengkap,kerjasama_id', 'user.kerjasama:id,client_id', 'user.jabatan:id,name_jabatan'])
                ->whereHas('user', function ($q) use ($clientId) {
                    $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                });
            if (!$includeAllStatus) $cuttingsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            $cuttingsQuery = $cuttingsQuery->join('users', 'performance_cuts.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $cuttingsQuery->whereBetween('date_cut', [$startDate, $endDate]);
            } else {
                $cuttingsQuery->whereYear('date_cut', $date->year)
                    ->whereMonth('date_cut', $date->month);
            }
            $cuttings = $cuttingsQuery->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('date_cut')
                ->get();

            // Build finished trainings query
            $finishedTrainingsQuery = FinishedTraining::with(['user:id,nama_lengkap,kerjasama_id', 'user.kerjasama:id,client_id', 'user.jabatan:id,name_jabatan'])
                ->whereHas('user', function ($q) use ($clientId) {
                    $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                });
            if (!$includeAllStatus) $finishedTrainingsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            $finishedTrainingsQuery = $finishedTrainingsQuery->join('users', 'finished_trainings.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $finishedTrainingsQuery->whereBetween('date_finish_train', [$startDate, $endDate]);
            } else {
                $finishedTrainingsQuery->whereYear('date_finish_train', $date->year)
                    ->whereMonth('date_finish_train', $date->month);
            }
            $finishedTrainings = $finishedTrainingsQuery->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('date_finish_train')
                ->get();

            // Build keterangan_lanjutan query
            $keteranganQuery = KeteranganLanjutan::with([
                'user:id,nama_lengkap,kerjasama_id,jabatan_id',
                'user.kerjasama:id,client_id',
                'user.kerjasama.client:id,name',
                'user.jabatan:id,name_jabatan',
                'createdBy:id,nama_lengkap'
            ])->whereHas('user', function ($q) use ($clientId) {
                $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                    ->whereIn('jabatan_id', $this->allowedSeeData());
            })->join('users', 'keterangan_lanjutans.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $keterangan = $keteranganQuery->whereBetween('keterangan_lanjutans.created_at', [$startDate, $endDate])
                    ->orderBy('users.kerjasama_id')
                    ->orderBy('users.jabatan_id')
                    ->orderBy('keterangan_lanjutans.created_at')
                    ->get();
            } else {
                $keterangan = $keteranganQuery->whereYear('keterangan_lanjutans.created_at', $date->year)
                    ->whereMonth('keterangan_lanjutans.created_at', $date->month)
                    ->orderBy('users.kerjasama_id')
                    ->orderBy('users.jabatan_id')
                    ->orderBy('keterangan_lanjutans.created_at')
                    ->get();
            }

            $data = [
                'overtimes' => $this->transformOvertimes($overtimes),
                'person_ins' => $personIns,
                'person_outs' => $personOuts,
                'cuttings' => $cuttings,
                'finished_trainings' => $finishedTrainings,
                'keterangan_lanjutan' => $keterangan,
                'client' => ['name' => $clientName],
                'period' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'),
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (
        Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getGlobalRekapData(Request $request)
    {
        try {
            $month = $request->input('month', now()->format('Y-m'));
            $date = Carbon::createFromFormat('Y-m', $month);
            $startDate = $date->copy()->subMonth()->setDay(20)->startOfDay();
            $endDate = $date->copy()->setDay(25)->endOfDay();
            $includeAllStatus = false; // Debug parameter to include all status

            $clients = Kerjasama::query()
                ->whereNotNull('client_id')
                ->distinct()
                ->pluck('client_id');

            if ($clients->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'overtimes' => [],
                        'person_ins' => [],
                        'person_outs' => [],
                        'cuttings' => [],
                        'finished_trainings' => [],
                        'keterangan_lanjutan' => [],
                        'client' => ['name' => 'Semua Mitra'],
                        'period' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'),
                    ],
                ]);
            }

            $overtimesQuery = Overtime::with([
                'user:id,id,nama_lengkap,kerjasama_id,jabatan_id',
                'user.kerjasama:id,client_id',
                'user.kerjasama.client:id,name',
                'user.jabatan:id,name_jabatan',
            ])
            ->whereHas('user', function ($q) use ($clients) {
                $q->whereHas('kerjasama', fn ($k) => $k->whereIn('client_id', $clients))
                ->whereIn('jabatan_id', $this->allowedSeeData());
                });
            if (!$includeAllStatus) {
                $overtimesQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            }
            $overtimesQuery = $overtimesQuery->join('users', 'overtimes.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $overtimesQuery->whereBetween('date_overtime', [$startDate, $endDate]);
            } else {
                $overtimesQuery->whereYear('date_overtime', $date->year)
                    ->whereMonth('date_overtime', $date->month);
            }
            $overtimes = $overtimesQuery
                ->orderBy('users.kerjasama_id')
                ->get();

            $personInsQuery = PersonIn::with(['jabatan:id,name_jabatan', 'client:id,name'])
                ->whereIn('client_id', $clients)
                ->whereIn('jabatan_id', $this->allowedSeeData());
            if (!$includeAllStatus) {
                $personInsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            }
            if ($startDate && $endDate) {
                $personInsQuery->whereBetween('date_in', [$startDate, $endDate]);
            } else {
                $personInsQuery->whereYear('date_in', $date->year)
                    ->whereMonth('date_in', $date->month);
            }
            $personIns = $personInsQuery->orderBy('date_in')->get();

            $personOutsQuery = PersonOut::with([
                'user' => fn ($q) => $q->withTrashed(),
                'user.kerjasama.client',
                'user.jabatan:id,name_jabatan',
                'createdBy:id,nama_lengkap',
            ])
                ->whereHas('user', function ($q) use ($clients) {
                    $q->withTrashed()
                        ->whereNotNull('nama_lengkap')
                        ->whereHas('kerjasama', fn ($k) => $k->whereIn('client_id', $clients))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                });
            if (!$includeAllStatus) {
                $personOutsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            }
            $personOutsQuery = $personOutsQuery->join('users', 'person_outs.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $personOutsQuery->whereBetween('out_date', [$startDate, $endDate]);
            } else {
                $personOutsQuery->whereYear('out_date', $date->year);
            }
            $personOuts = $personOutsQuery->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('out_date')
                ->get();

            $cuttingsQuery = PerformanceCuts::with([
                'user:id,nama_lengkap,kerjasama_id',
                'user.kerjasama:id,client_id',
                'user.jabatan:id,name_jabatan',
                'user.kerjasama.client:id,name',
            ])
                ->whereHas('user', function ($q) use ($clients) {
                    $q->whereHas('kerjasama', fn ($k) => $k->whereIn('client_id', $clients))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                });
            if (!$includeAllStatus) {
                $cuttingsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            }
            $cuttingsQuery = $cuttingsQuery->join('users', 'performance_cuts.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $cuttingsQuery->whereBetween('date_cut', [$startDate, $endDate]);
            } else {
                $cuttingsQuery->whereYear('date_cut', $date->year)
                    ->whereMonth('date_cut', $date->month);
            }
            $cuttings = $cuttingsQuery->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('date_cut')
                ->get();

            $finishedTrainingsQuery = FinishedTraining::with([
                'user:id,nama_lengkap,kerjasama_id',
                'user.kerjasama:id,client_id',
                'user.jabatan:id,name_jabatan',
                'user.kerjasama.client:id,name',
            ])
                ->whereHas('user', function ($q) use ($clients) {
                    $q->whereHas('kerjasama', fn ($k) => $k->whereIn('client_id', $clients))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                });
            if (!$includeAllStatus) {
                $finishedTrainingsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            }
            $finishedTrainingsQuery = $finishedTrainingsQuery->join('users', 'finished_trainings.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $finishedTrainingsQuery->whereBetween('date_finish_train', [$startDate, $endDate]);
            } else {
                $finishedTrainingsQuery->whereYear('date_finish_train', $date->year)
                    ->whereMonth('date_finish_train', $date->month);
            }
            $finishedTrainings = $finishedTrainingsQuery->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('date_finish_train')
                ->get();

            $keteranganQuery = KeteranganLanjutan::with([
                'user:id,nama_lengkap,kerjasama_id,jabatan_id',
                'user.kerjasama:id,client_id',
                'user.kerjasama.client:id,name',
                'user.jabatan:id,name_jabatan',
                'createdBy:id,nama_lengkap',
            ])
                ->whereHas('user', function ($q) use ($clients) {
                    $q->whereHas('kerjasama', fn ($k) => $k->whereIn('client_id', $clients))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                })
                ->join('users', 'keterangan_lanjutans.user_id', '=', 'users.id');
            if ($startDate && $endDate) {
                $keterangan = $keteranganQuery->whereBetween('keterangan_lanjutans.created_at', [$startDate, $endDate])
                    ->orderBy('users.kerjasama_id')
                    ->orderBy('users.jabatan_id')
                    ->orderBy('keterangan_lanjutans.created_at')
                    ->get();
            } else {
                $keterangan = $keteranganQuery->whereYear('keterangan_lanjutans.created_at', $date->year)
                    ->whereMonth('keterangan_lanjutans.created_at', $date->month)
                    ->orderBy('users.kerjasama_id')
                    ->orderBy('users.jabatan_id')
                    ->orderBy('keterangan_lanjutans.created_at')
                    ->get();
            }

            $data = [
                'overtimes' => $this->transformOvertimes($overtimes),
                'person_ins' => $personIns,
                'person_outs' => $personOuts,
                'cuttings' => $cuttings,
                'finished_trainings' => $finishedTrainings,
                'keterangan_lanjutan' => $keterangan,
                'client' => ['name' => 'Semua Mitra'],
                'period' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'),
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
