<?php

namespace App\Http\Controllers\SVP_Controller\Rekap;

use App\Http\Controllers\Controller;
use App\Models\FinishedTraining;
use App\Models\Kerjasama;
use App\Models\Overtime;
use App\Models\PerformanceCuts;
use App\Models\PersonIn;
use App\Models\PersonOut;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AllRekapExportController extends Controller
{
    public function getAllRekapData(Request $request, $kerjasama)
    {
        try {
            $kerjasamaModel = Kerjasama::with('client')->findOrFail($kerjasama);
            $clientId = $kerjasamaModel->client_id;
            $month = $request->input('month', now()->format('Y-m'));

            $data = [
                'overtimes' => $this->getOvertimes($clientId, $month),
                'person_ins' => $this->getPersonIns($clientId, $month),
                'person_outs' => $this->getPersonOuts($clientId, $month),
                'cuttings' => $this->getCuttings($clientId, $month),
                'finished_trainings' => $this->getFinishedTrainings($clientId, $month),
                'client' => $kerjasamaModel->client,
                'period' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getOvertimes($clientId, $month)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        return Overtime::with([
            'user:id,nama_lengkap,kerjasama_id,jabatan_id',
            'user.kerjasama:id,client_id',
            'user.kerjasama.client:id,name',
            'user.jabatan:id,name_jabatan',
            'createdBy:id,nama_lengkap'
        ])
            ->whereHas('user.kerjasama', fn($k) => $k->where('client_id', $clientId))
            ->where('status', 'Di Setujui')
            ->whereYear('date_overtime', $date->year)
            ->whereMonth('date_overtime', $date->month)
            ->orderBy('date_overtime')
            ->get();
    }

    private function getPersonIns($clientId, $month)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        return PersonIn::with(['jabatan:id,name_jabatan', 'createdBy:id,nama_lengkap'])
            ->where('client_id', $clientId)
            ->where('status', 'Di Setujui')
            ->whereYear('date_in', $date->year)
            ->whereMonth('date_in', $date->month)
            ->orderBy('date_in')
            ->get();
    }

    private function getPersonOuts($clientId, $month)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        return PersonOut::with(['user:id,nama_lengkap', 'createdBy:id,nama_lengkap'])
            ->whereHas('user', function ($q) use ($clientId) {
                $q->withTrashed()->whereHas('kerjasama', fn($k) => $k->where('client_id', $clientId));
            })
            ->where('status', 'Di Setujui')
            ->whereYear('out_date', $date->year)
            ->whereMonth('out_date', $date->month)
            ->orderBy('out_date')
            ->get();
    }

    private function getCuttings($clientId, $month)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        return PerformanceCuts::with(['user:id,nama_lengkap', 'createdBy:id,nama_lengkap'])
            ->whereHas('user.kerjasama', fn($k) => $k->where('client_id', $clientId))
            ->where('status', 'Di Setujui')
            ->whereYear('date_cut', $date->year)
            ->whereMonth('date_cut', $date->month)
            ->orderBy('date_cut')
            ->get();
    }

    private function getFinishedTrainings($clientId, $month)
    {
        $date = Carbon::createFromFormat('Y-m', $month);

        return FinishedTraining::with(['user:id,nama_lengkap', 'createdBy:id,nama_lengkap'])
            ->whereHas('user.kerjasama', fn($k) => $k->where('client_id', $clientId))
            ->where('status', 'Di Setujui')
            ->whereYear('date_finish_train', $date->year)
            ->whereMonth('date_finish_train', $date->month)
            ->orderBy('date_finish_train')
            ->get();
    }
}

