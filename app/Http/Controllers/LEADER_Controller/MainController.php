<?php

namespace App\Http\Controllers\LEADER_Controller;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Laporan;
use App\Models\Lembur;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Kerjasama;
use App\Models\UploadImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{

    public function indexAbsen(Request $request)
    {
        // --- 1. Early Exit for Specific Condition ---
        $isSpecialDate = Carbon::now()->format('Y-m-d') == '2024-05-24' && Auth::user()->devisi_id == 18;
        if ($isSpecialDate) {
            abort(500);
        }

        // --- 2. Initialize Core Variables ---
        $user = Auth::user();
        $filter = $request->search; // This can be a full date or a month string
        $filterMitra = $request->mitra;
        $isDiv18 = $user->devisi_id == 18;

        // Data for the view
        $mitra = Kerjasama::with('client')->get();

        // --- 3. Start Building the Query ---
        $query = Absensi::with('user.divisi.jabatan')->latest();

        // --- 4. Apply Date/Month Filter (THIS IS THE UPDATED SECTION) ---
        if ($isDiv18) {
            // For users in devisi_id 18:
            // - If a date is provided, filter by that exact date.
            // - If no date is provided, default to filtering by the current month.
            $query->when(
                $filter,
                fn($q) => $q->whereDate('tanggal_absen', $filter),
                fn($q) => $q->whereMonth('tanggal_absen', Carbon::now()->month)
            );
        } else {
            // For all other users:
            // - The filter is treated as a month selector.
            // - Default to the current month if no filter is provided.
            $targetMonth = $filter ? Carbon::parse($filter)->month : Carbon::now()->month;
            $query->whereMonth('tanggal_absen', $targetMonth);
        }

        // --- 5. Apply Role-Based and User-Specific Filters ---
        $userRole = $user->divisi->jabatan->code_jabatan ?? null;
        $isSPV = $user->jabatan->code_jabatan == 'SPV-W';

        $jabatanCodes = [
            'CO-CS' => ['OCS', 'CO-CS'],
            'CO-SCR' => ['SCR', 'CO-SCR'],
        ];

        if ($isDiv18) {
            $query->when($filterMitra, fn($q) => $q->where('kerjasama_id', $filterMitra));
        } elseif (isset($jabatanCodes[$userRole]) || $isSPV) {
            $codesToFilter = $jabatanCodes[$userRole] ?? $jabatanCodes['CO-SCR'];

            $query->whereHas('user.divisi.jabatan', fn($q) => $q->whereIn('code_jabatan', $codesToFilter));

            if ($isSPV) {
                if ($filterMitra) {
                    $query->where('kerjasama_id', $filterMitra);
                }else {
                    $query;
                }
            } else {
                $query->where('kerjasama_id', $user->kerjasama_id);
            }
        } else {
            $query->where('kerjasama_id', $user->kerjasama_id);
        }

        // --- 6. Apply Ordering and Pagination ---
        if ($isSPV && !$filterMitra) {
            $query->orderBy('kerjasama_id', 'asc')->latest();
        } elseif ($isDiv18) {
            $query->orderBy('tanggal_absen', 'desc')->orderBy('kerjasama_id', 'desc');
        } else {
            $query->latest();
        }

        $perPage = 31;
        if ($isSPV && $filterMitra) {
            $perPage = 20;
        } elseif ($filter) { // If any filter is applied, show more results
            $perPage = 50;
        }

        $absen = $query->paginate($perPage)->appends($request->except('page'));

        // dd($absen);

        // --- 7. Return the View ---
        return view('leader_view/absen/index', compact('absen', 'mitra', 'filterMitra', 'filter'));
    }

    public function indexLaporan()
    {
        $kerjasama = Auth::user()->kerjasama->client_id;
        $laporan = UploadImage::where('clients_id', $kerjasama)->paginate(30);
        return view('leader_view/laporan/index', compact('laporan'));
    }
    public function showLaporan($id)
    {
        $kerjasama = Auth::user()->kerjasama->client_id;
        $laporan = Laporan::findOrfail($id);
        return view('leader_view/laporan/show', compact('laporan'));
    }

    public function indexUser(Request $request)
    {
        $filterDivisi = $request->input('divisi');
        $filterMitra = $request->input('mitra');

        $divisi = Divisi::all();
        $mitra = Kerjasama::with('client')->get();
        $kerjasama = Auth::user()->kerjasama_id;

        if($filterDivisi){
            $user = User::where('kerjasama_id', $kerjasama)->where('devisi_id', $filterDivisi)->paginate(90);
        }else{
            if(Auth::user()->role_id == 2 || Auth::user()->divisi->jabatan->code_jabatan == 'DIREKSI'){
                $user = User::paginate(9999999);
            }else{
                if(Auth::user()->divisi->jabatan->code_jabatan == "CO-CS"){
                   $codeJabatan = ['OCS', 'CO-CS'];
                    $user = User::where('kerjasama_id', $kerjasama)->whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                        $query->whereIn('code_jabatan', $codeJabatan);
                    })->orderBy('nama_lengkap', 'asc')->paginate(30);
                }else if(Auth::user()->divisi->jabatan->code_jabatan == "MITRA") {
                    $user = User::where('kerjasama_id', $kerjasama)->orderBy('nama_lengkap', 'asc')->paginate(30);
                }else if(Auth::user()->devisi_id == 26) {
                    if(Auth::user()->id == 175){
                        $user = User::where('kerjasama_id', '!=', 1)->orderBy('nama_lengkap', 'asc')->whereHas('divisi.jabatan', function ($query) {
                                    $query->whereNotIn('code_jabatan', ['OCS', 'CO-CS', 'TMN']);
                                })->paginate(30);
                    }else{
                        $user = User::where('kerjasama_id', '!=', 1)->orderBy('nama_lengkap', 'asc')->whereHas('divisi.jabatan', function ($query) {
                                    $query->whereNotIn('code_jabatan', ['SCR', 'CO-SCR', 'JM']);
                                })->paginate(30);
                    }
                }else{
                    $codeJabatan = ['SCR', 'CO-SCR'];
                    if(auth()->user()->id == 175){
                        if ($filterMitra) {
                            $user = User::where('kerjasama_id', $filterMitra)->whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                                $query->whereIn('code_jabatan', $codeJabatan);
                            })->orderBy('kerjasama_id', 'asc')->orderBy('nama_lengkap', 'asc')->paginate(50);
                        }else {
                            $user = User::whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                                $query->whereIn('code_jabatan', $codeJabatan);
                            })->orderBy('kerjasama_id', 'asc')->orderBy('nama_lengkap', 'asc')->paginate(50);
                        }
                    }
                    else{
                        $user = User::where('kerjasama_id', $kerjasama)->whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                            $query->whereIn('code_jabatan', $codeJabatan);
                        })->orderBy('nama_lengkap', 'asc')->paginate(30);
                    }
                }
            }
        }

        // dd($user, Auth::user());

        return view('leader_view/user/index', compact('user', 'divisi', 'mitra', 'filterMitra'));
    }

    public function indexLembur()
    {
        $kerjasama = Auth::user()->kerjasama_id;
        if(Auth::user()->divisi->jabatan->code_jabatan == "CO-CS"){
                $codeJabatan = ['OCS', 'CO-CS'];
                $lembur = Lembur::latest()->where('kerjasama_id', $kerjasama)->whereHas('user.divisi.jabatan', function ($query) use ($codeJabatan) {
                            $query->whereIn('code_jabatan', $codeJabatan);
                        })->paginate(31);
        }else if(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR"){
                $codeJabatan = ['SCR', 'CO-SCR'];
                $lembur = Lembur::latest()->where('kerjasama_id', $kerjasama)->whereHas('user.divisi.jabatan', function ($query) use ($codeJabatan) {
                            $query->whereIn('code_jabatan', $codeJabatan);
                        })->paginate(31);

        }else{
            $lembur = Lembur::where('kerjasama_id', $kerjasama)->paginate(30);
        }
        return view('leader_view/lembur/index', compact('lembur'));
    }

    public function indexAbsenSholat()
    {
        $kerjasama = Auth::user()->kerjasama_id;
        if(Auth::user()->divisi->jabatan->code_jabatan == "CO-CS"){
                $codeJabatan = ['OCS', 'CO-CS'];
                $user = User::where('kerjasama_id', $kerjasama)->whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                            $query->whereIn('code_jabatan', $codeJabatan);
                        })->get();
        }else if(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR"){
                $codeJabatan = ['SCR', 'CO-SCR'];
                $user = User::where('kerjasama_id', $kerjasama)->whereHas('divisi.jabatan', function ($query) use ($codeJabatan) {
                            $query->whereIn('code_jabatan', $codeJabatan);
                        })->get();
        }
        $absen = Absensi::where('kerjasama_id', $kerjasama)->where('tanggal_absen', Carbon::now()->format('Y-m-d'))->get();
        return view('leader_view/absenSholat/index', compact('user', 'absen'));
    }

    public function storeAbsenSholat(Request $request)
    {
        $absenRecords = Absensi::whereIn('user_id', $request->user)->where('tanggal_absen', Carbon::now()->format('Y-m-d'))->orderBy('user_id', 'asc')->get();
        foreach ($absenRecords as $absen) {
            if(Carbon::now()->format('H:i:s') >= '11:20:00' && Carbon::now()->format('H:i:s') <= '14:10:00'){
                $img = $request->fotoSholat;

                $folderPath = "public/images/";
                $image_parts = explode(";base64,", $img);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $formatName = uniqid() . '-data';
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = $formatName . '.png';
                $file = $folderPath . $fileName;
                Storage::put($file, $image_base64);

                $absen->fotoDzuhur = $fileName;

                $absen->dzuhur = 1;
            }else if(Carbon::now()->format('H:i:s') >= '17:20:00' && Carbon::now()->format('H:i:s') <= '18:45:00'){
                $img = $request->fotoSholat;

                $folderPath = "public/images/";
                $image_parts = explode(";base64,", $img);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $formatName = uniqid() . '-data';
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = $formatName . '.png';
                $file = $folderPath . $fileName;
                Storage::put($file, $image_base64);

                $absen->fotoMagrib = $fileName;

                $absen->magrib = 1;
            }
            // dd($request->all(), $absen);
            $absen->save();
        }
        // dd($request->all(), $absenRecords);
        toastr()->success('Berhasil Absen Sholat', 'sukses');
        return to_route('dashboard.index');
    }
}
