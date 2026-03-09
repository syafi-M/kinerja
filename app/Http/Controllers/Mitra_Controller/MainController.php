<?php

namespace App\Http\Controllers\Mitra_Controller;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Lokasi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MainController extends Controller
{
    public function indexKehadiran(Request $request)
    {
        $kerjasamaId = auth()->user()->kerjasama_id;
        $periode = $request->string('periode')->toString();
        $periode = in_array($periode, ['today', '30days'], true) ? $periode : 'today';

        $query = Absensi::with(['user', 'shift'])
            ->where('kerjasama_id', $kerjasamaId);

        // Backward compatibility: dukung filter bulan lama (?search=YYYY-MM).
        if ($request->filled('search') && preg_match('/^\d{4}-\d{2}$/', $request->search)) {
            $periode = 'custom';
            $query->where('tanggal_absen', 'like', $request->search . '%');
            try {
                $periodeLabel = 'Bulan ' . Carbon::createFromFormat('Y-m', $request->search)->translatedFormat('F Y');
            } catch (\Throwable $th) {
                $periodeLabel = 'Periode Bulanan';
            }
        } elseif ($periode === '30days') {
            $query->whereDate('created_at', '>=', now()->subDays(29)->toDateString());
            $periodeLabel = '30 Hari Terakhir';
        } else {
            $query->whereDate('created_at', now()->toDateString());
            $periodeLabel = 'Hari Ini';
        }

        $latestAbsensi = (clone $query)->latest('id')->first(['id', 'created_at', 'updated_at']);
        $dataSignature = $latestAbsensi
            ? $latestAbsensi->id . '|' . optional($latestAbsensi->created_at)->timestamp . '|' . optional($latestAbsensi->updated_at)->timestamp
            : 'empty';

        if ($request->boolean('check_updates') && $request->ajax()) {
            return response()->json([
                'signature' => $dataSignature,
            ]);
        }

        $absensiList = $query->latest()->get();
        $jumlahKehadiran = $absensiList
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->count();
        $jumlahAbsensiHariIni = Absensi::where('kerjasama_id', $kerjasamaId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return view('mitra_view.kehadiran.index', compact(
            'absensiList',
            'jumlahKehadiran',
            'jumlahAbsensiHariIni',
            'periode',
            'periodeLabel',
            'dataSignature',
        ));
    }

    public function showLocation(Request $request, $id)
    {
        $tgl = $request->tgl;
        $us = $request->user;

        if ($request->tgl) {
            $absen = Absensi::with('kerjasama')->where('user_id', $request->user)->firstWhere('tanggal_absen', $request->tgl);
            $lokMitra = Lokasi::firstWhere('client_id', $absen?->kerjasama->client_id);
        } else {
            $absen = Absensi::with('kerjasama')->findOrFail($id);
            $lokMitra = Lokasi::firstWhere('client_id', $absen?->kerjasama->client_id);
        }

        return view('mitra_view.kehadiran.maps', compact('absen', 'lokMitra', 'tgl', 'us'));
    }

    public function indexKaryawan()
    {
        $search = request('search');
        $kerjasamaId = auth()->user()->kerjasama_id;

        $query = User::with(['divisi.jabatan', 'kerjasama.client'])
            ->where('kerjasama_id', $kerjasamaId)
            ->whereNotIn('nama_lengkap', ['admin', 'user', 'MITRA SAC']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->orderBy('nama_lengkap', 'asc')->paginate(50)->appends(request()->query());
        $totalKaryawan = (clone $query)->count();

        return view('mitra_view.karyawan.index', compact('users', 'search', 'totalKaryawan'));
    }

    public function indexLembur()
    {
        return view('mitra_view.lembur.index');
    }

    public function indexIzin()
    {
        return view('mitra_view.izin.index');
    }

    public function indexRekap()
    {
        return view('mitra_view.rekap.index');
    }
}
