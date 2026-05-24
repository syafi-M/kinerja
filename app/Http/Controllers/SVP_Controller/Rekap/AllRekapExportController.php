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
            $kerjasamaModel = Kerjasama::with('client')->findOrFail($kerjasama);
            $clientId = $kerjasamaModel->client_id;
            $month = $request->input('month', now()->format('Y-m'));
            $includeAllStatus = false; // Debug parameter

            $data = [
                'overtimes' => $this->getOvertimesPerMitra($clientId, $month, $includeAllStatus),
                'person_ins' => $this->getPersonInsPerMitra($clientId, $month, $includeAllStatus),
                'person_outs' => $this->getPersonOutsPerMitra($clientId, $month, $includeAllStatus),
                'cuttings' => $this->getCuttingsPerMitra($clientId, $month, $includeAllStatus),
                'finished_trainings' => $this->getFinishedTrainingsPerMitra($clientId, $month, $includeAllStatus),
                'keterangan_lanjutan' => $this->getKeteranganLanjutansPerMitra($clientId, $month),
                'client' => $kerjasamaModel->client,
                'period' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getOvertimesPerMitra($clientId, $month, $includeAllStatus = false)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        $query = Overtime::with([
            'user:id,id,nama_lengkap,kerjasama_id,jabatan_id',
            'user.kerjasama:id,client_id',
            'user.kerjasama.client:id,name',
            'user.jabatan:id,name_jabatan',
            'createdBy:id,nama_lengkap'
        ])
            ->whereHas('user', function ($q) use ($clientId) {
                $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                    ->whereIn('jabatan_id', $this->allowedSeeData());
            });

        if (!$includeAllStatus) $query->whereNotIn('status', ['Di Tolak', 'Pending']);

        $overtimes = $query->join('users', 'overtimes.user_id', '=', 'users.id')
            ->whereYear('date_overtime', $date->year)
            ->whereMonth('date_overtime', $date->month)
            ->orderBy('users.kerjasama_id')
            ->orderBy('users.jabatan_id')
            ->orderBy('user_id')
            ->orderBy('date_overtime')
            ->get();

        return $this->transformOvertimes($overtimes);
    }

    private function getPersonInsPerMitra($clientId, $month, $includeAllStatus = false)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        $query = PersonIn::with(['jabatan:id,name_jabatan', 'createdBy:id,nama_lengkap'])
            ->where('client_id', $clientId)
            ->whereIn('jabatan_id', $this->allowedSeeData());

        if (!$includeAllStatus) $query->whereNotIn('status', ['Di Tolak', 'Pending']);

        return $query->whereYear('date_in', $date->year)
            ->whereMonth('date_in', $date->month)
            ->orderBy('client_id')
            ->orderBy('jabatan_id')
            ->orderBy('date_in')
            ->get();
    }

    private function getPersonOutsPerMitra($clientId, $month, $includeAllStatus = false)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        $query = PersonOut::with([
            'user' => fn($q) => $q->withTrashed(),
            'user.kerjasama.client',
            'createdBy:id,nama_lengkap'
        ])
            ->whereHas('user', function ($q) use ($clientId) {
                $q->withTrashed()
                    ->whereNotNull('nama_lengkap')
                    ->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                    ->whereIn('jabatan_id', $this->allowedSeeData());
            });

        if (!$includeAllStatus) $query->whereNotIn('status', ['Di Tolak', 'Pending']);

        return $query->join('users', 'person_outs.user_id', '=', 'users.id')
            ->whereYear('out_date', $date->year)
            ->orderBy('users.kerjasama_id')
            ->orderBy('users.jabatan_id')
            ->orderBy('user_id')
            ->orderBy('out_date')
            ->get();
    }

    private function getCuttingsPerMitra($clientId, $month, $includeAllStatus = false)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        $query = PerformanceCuts::with(['user:id,nama_lengkap,kerjasama_id', 'user.kerjasama:id,client_id', 'createdBy:id,nama_lengkap'])
            ->whereHas('user', function ($q) use ($clientId) {
                $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                    ->whereIn('jabatan_id', $this->allowedSeeData());
            });

        if (!$includeAllStatus) $query->whereNotIn('status', ['Di Tolak', 'Pending']);

        return $query->join('users', 'performance_cuts.user_id', '=', 'users.id')
            ->whereYear('date_cut', $date->year)
            ->whereMonth('date_cut', $date->month)
            ->orderBy('users.kerjasama_id')
            ->orderBy('users.jabatan_id')
            ->orderBy('user_id')
            ->orderBy('date_cut')
            ->get();
    }

    private function getFinishedTrainingsPerMitra($clientId, $month, $includeAllStatus = false)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        $query = FinishedTraining::with(['user:id,nama_lengkap,kerjasama_id', 'user.kerjasama:id,client_id', 'createdBy:id,nama_lengkap'])
            ->whereHas('user', function ($q) use ($clientId) {
                $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                    ->whereIn('jabatan_id', $this->allowedSeeData());
            });

        if (!$includeAllStatus) $query->whereNotIn('status', ['Di Tolak', 'Pending']);

        return $query->join('users', 'finished_trainings.user_id', '=', 'users.id')
            ->whereYear('date_finish_train', $date->year)
            ->whereMonth('date_finish_train', $date->month)
            ->orderBy('users.kerjasama_id')
            ->orderBy('users.jabatan_id')
            ->orderBy('user_id')
            ->orderBy('date_finish_train')
            ->get();
    }

    private function getKeteranganLanjutansPerMitra($clientId, $month)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        return KeteranganLanjutan::with([
            'user:id,nama_lengkap,kerjasama_id,jabatan_id',
            'user.kerjasama:id,client_id',
            'user.kerjasama.client:id,name',
            'user.jabatan:id,name_jabatan'
        ])
            ->whereHas('user', function ($q) use ($clientId) {
                $q->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId))
                    ->whereIn('jabatan_id', $this->allowedSeeData());
            })
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->orderBy('created_at')
            ->get();
    }

    public function getGlobalRekapData(Request $request)
    {
        try {
            $month = $request->input('month', now()->format('Y-m'));
            $date = Carbon::createFromFormat('Y-m', $month);
            $includeAllStatus = false; // Debug parameter to include all status

            // Get all unique clients from kerjasama table
            $clients = Kerjasama::distinct()->pluck('client_id');

            // If no clients, return empty success response
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
                        'period' => $date->format('F Y'),
                    ]
                ]);
            }

            // Build overtimes query
            $overtimesQuery = Overtime::with([
                'user:id,id,nama_lengkap,kerjasama_id,jabatan_id',
                'user.kerjasama:id,client_id',
                'user.kerjasama.client:id,name',
                'user.jabatan:id,name_jabatan'
            ])
                ->whereHas('user', function ($q) use ($clients) {
                    $q->whereHas('kerjasama', fn($k) => $k->whereIn('client_id', $clients))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                });
            if (!$includeAllStatus) $overtimesQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            $overtimesQuery->join('users', 'overtimes.user_id', '=', 'users.id')
                ->whereYear('date_overtime', $date->year)
                ->whereMonth('date_overtime', $date->month)
                ->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('date_overtime');

            // Build person_ins query
            $personInsQuery = PersonIn::with(['jabatan:id,name_jabatan'])
                ->whereIn('client_id', $clients)
                ->whereIn('jabatan_id', $this->allowedSeeData());
            if (!$includeAllStatus) $personInsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            $personInsQuery->whereYear('date_in', $date->year)
                ->whereMonth('date_in', $date->month)
                ->orderBy('date_in');

            // Build person_outs query
            $personOutsQuery = PersonOut::with([
                'user' => fn($q) => $q->withTrashed(),
                'user.kerjasama.client',
                'user.jabatan:id,name_jabatan',
                'createdBy:id,nama_lengkap'
            ])
                ->whereHas('user', function ($q) use ($clients) {
                    $q->withTrashed()
                        ->whereNotNull('nama_lengkap')
                        ->whereHas('kerjasama', fn($k) => $k->whereIn('client_id', $clients))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                });

            if (! $includeAllStatus) {
                $personOutsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            }

            $personOutsQuery->join('users', 'person_outs.user_id', '=', 'users.id')
                ->whereYear('out_date', $date->year)
                ->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('out_date');
            // Build cuttings query
            $cuttingsQuery = PerformanceCuts::with(['user:id,nama_lengkap,kerjasama_id', 'user.kerjasama:id,client_id', 'user.jabatan:id,name_jabatan'])
                ->whereHas('user', function ($q) use ($clients) {
                    $q->whereHas('kerjasama', fn($k) => $k->whereIn('client_id', $clients))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                });
            if (!$includeAllStatus) $cuttingsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            $cuttingsQuery->join('users', 'performance_cuts.user_id', '=', 'users.id')
                ->whereYear('date_cut', $date->year)
                ->whereMonth('date_cut', $date->month)
                ->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('date_cut');

            // Build finished trainings query
            $finishedTrainingsQuery = FinishedTraining::with(['user:id,nama_lengkap,kerjasama_id', 'user.kerjasama:id,client_id', 'user.jabatan:id,name_jabatan'])
                ->whereHas('user', function ($q) use ($clients) {
                    $q->whereHas('kerjasama', fn($k) => $k->whereIn('client_id', $clients))
                        ->whereIn('jabatan_id', $this->allowedSeeData());
                });
            if (!$includeAllStatus) $finishedTrainingsQuery->whereNotIn('status', ['Di Tolak', 'Pending']);
            $finishedTrainingsQuery->join('users', 'finished_trainings.user_id', '=', 'users.id')
                ->whereYear('date_finish_train', $date->year)
                ->whereMonth('date_finish_train', $date->month)
                ->orderBy('users.kerjasama_id')
                ->orderBy('users.jabatan_id')
                ->orderBy('user_id')
                ->orderBy('date_finish_train');

            $data = [
                'overtimes' => $this->transformOvertimes($overtimesQuery->get()),
                'person_ins' => $personInsQuery->get(),
                'person_outs' => $personOutsQuery->get(),
                'cuttings' => $cuttingsQuery->get(),
                'finished_trainings' => $finishedTrainingsQuery->get(),
                'keterangan_lanjutan' => KeteranganLanjutan::with([
                    'user:id,nama_lengkap,kerjasama_id,jabatan_id',
                    'user.kerjasama:id,client_id',
                    'user.kerjasama.client:id,name',
                    'user.jabatan:id,name_jabatan'
                ])
                    ->whereHas('user', function ($q) use ($clients) {
                        $q->whereHas('kerjasama', fn($k) => $k->whereIn('client_id', $clients))
                            ->whereIn('jabatan_id', $this->allowedSeeData());
                    })
                    ->join('users', 'keterangan_lanjutans.user_id', '=', 'users.id')
                    ->whereYear('keterangan_lanjutans.created_at', $date->year)
                    ->whereMonth('keterangan_lanjutans.created_at', $date->month)
                    ->orderBy('users.kerjasama_id')
                    ->orderBy('users.jabatan_id')
                    ->orderBy('keterangan_lanjutans.created_at')
                    ->get(),
                'client' => ['name' => 'Semua Mitra'],
                'period' => $date->format('F Y'),
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
