<?php

namespace App\Http\Controllers;
use App\Http\Requests\AbsensiRequest;
use App\Models\Absensi;
use App\Models\Client;
use App\Models\Kerjasama;
use GuzzleHttp\Client as HTTP;
use Illuminate\Support\Facades\DB;
use App\Models\Divisi;
use App\Models\JadwalUser;
use App\Models\Lokasi;
use App\Models\Point;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http as httped;
use App\Mail\AbsensiNotification;
// use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AbsensiController extends Controller
{


    public function index(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        // Eager load only necessary fields
        $dev = Divisi::with([
            'Perlengkapan:id,name'
        ])->where('id', $user->devisi_id)->get(['id']);

        // $client = Client::all();

        // Determine shift if not SPV-W
        if ($user->divisi->jabatan->code_jabatan != 'SPV-W') {
            $startTime = match (true) {
                $now->between(Carbon::createFromTime(4, 0), Carbon::createFromTime(11, 0)) => '04:00',
                $now->between(Carbon::createFromTime(11, 0), Carbon::createFromTime(16, 0)) => '11:00',
                default => '16:00',
            };

            $shift = Shift::orderBy('jam_start', 'asc')
                ->where('jam_start', '>=', $startTime)
                ->where('client_id', $user->kerjasama->client_id)
                ->where('jabatan_id', $user->divisi->jabatan_id)
                ->get();
        } else {
            $shift = $now->format('H:i');
        }

        // Absensi dan jadwal user
        $absensi = Absensi::with(['User', 'Kerjasama', 'Shift'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get(['absensi_type_pulang', 'tanggal_absen', 'masuk', 'terus', 'tukar']);

        // $jadwal = JadwalUser::where('user_id', $user->id)
        //     ->latest()
        //     ->get();

        // Shift direksi
        $dirShift = Shift::firstWhere('shift_name', 'DIREKSI');

        // Users belum absen hari ini
        $aID = Absensi::where('tanggal_absen', $today)
            ->pluck('user_id');

        $userL = User::where('kerjasama_id', $user->kerjasama_id)
            ->whereNotIn('id', $aID)
            ->get();

        // Cek pulang terakhir
        $cekPulang = Absensi::where('user_id', $user->id)
            ->where('tanggal_absen', $today)
            ->latest()
            ->first();

        // Cek jika ada tukar shift
        $cekTukar = Absensi::where('tukar_id', $user->id)
            ->where('tanggal_absen', $today)
            ->first();

        // Cek hari libur (khusus kerjasama_id == 1)
        $tesLib = '';
        $afaLib = false;

        if ($user->kerjasama_id == 1) {
            $hLib = $now->isoFormat('dddd');
            $liburNasional = false;

            try {
                $loginResponse = httped::timeout(30)->get(
                    "https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/holidays.json"
                );

                $holidayDates = collect($loginResponse->json())->keys();
                $liburNasional = $holidayDates->contains($today);
                $tesLib = $liburNasional ? $today : '';
            } catch (\Exception $e) {
                // Handle error or log it if necessary
            }

            $afaLib = in_array($hLib, ['Minggu', 'Sabtu']) || ($liburNasional && $user->devisi_id != 26);
        }

        // Lokasi terkait
        $harLok = Lokasi::where('client_id', $user->kerjasama->client_id)->first();
        $lokLok = Lokasi::with('Client')->get();
        $penempatan = Kerjasama::with('Client')->get();

        return view('absensi.index', compact(
            'penempatan',
            'lokLok',
            'userL',
            'tesLib',
            'afaLib',
            'shift',
            'dev',
            'absensi',
            'harLok',
            'dirShift'
        ));
    }


    public function getShift($cli, $jab)
    {
        $dev = Divisi::with(['Jabatan', 'Perlengkapan'])->get();
        $shiftFirst = Shift::with(['jabatan'])->orderBy('jam_start', 'asc');
        $shift1 = $shiftFirst->where('jam_start', '>=', '04:00')->where('client_id', $cli)->where('jabatan_id', $jab)->get();
        $shift2 = $shiftFirst->where('jam_start', '>=', '11:00')->where('client_id', $cli)->where('jabatan_id', $jab)->get();
        $shift3 = $shiftFirst->where('jam_start', '>=', '16:00')->where('client_id', $cli)->where('jabatan_id', $jab)->get();

        if (Carbon::now()->format('H:i') >= '04:00' && Carbon::now()->format('H:i') < '11:00') {
            $shift = $shift1;
        } else if (Carbon::now()->format('H:i') >= '11:00' && Carbon::now()->format('H:i') < '24:00') {
            $shift = $shift2;
        } else {
            $shift = $shift3;
        }

        return response()->json(['shift' => $shift, 'dev' => $dev]);
    }

    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'kerjasama_id' => 'required',
            'shift_id' => 'required',
            'perlengkapan' => 'required',
            'keterangan' => 'required',
            'absensi_type_masuk' => 'required',
            'absensi_type_pulang' => 'nullable',
            'image' => Auth::user()->kerjasama_id != 1 || !in_array(Auth::user()->devisi_id, [2, 3, 7, 8, 12, 14, 18]) ? 'required' : 'nullable',
            'deskripsi' => 'nullable',
            'point_id' => 'nullable',
            'subuh' => 'nullable',
            'dzuhur' => 'nullable',
            'asar' => 'nullable',
            'magrib' => 'nullable',
            'isya' => 'nullable',
            'msk_lat' => 'nullable|max:11',
            'msk_long' => 'nullable|max:11',
            'sig_lat' => 'nullable|max:11',
            'sig_long' => 'nullable|max:11',
            'plg_lat' => 'nullable|max:11',
            'plg_long' => 'nullable|max:11',
            'masuk' => 'nullable',
            'tukar' => 'nullable',
            'lembur' => 'nullable',
            'terus' => 'nullable',
            'tukar_id' => 'nullable'
        ];

        $customMessages = [
            'required' => 'Kolom :attribute tidak boleh kosong.',
        ];

        if (Auth::user()->id == 506) {
            // dd($request->all());
        }

        // Melakukan validasi
        $validator = Validator::make($request->all(), $rules, $customMessages);

        // dd($request->all());
        if ($validator->fails()) {
            toastr()->error('Formulir tidak lengkap. Mohon isi semua kolom.', 'error');
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = Auth::user()->id;
        $absensi = Absensi::latest()->where('user_id', $user)->whereNotNull('absensi_type_pulang')->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();
        if (Auth::user()->kerjasama_id != 1 || !in_array(Auth::user()->devisi_id, [2, 3, 7, 8, 12, 14, 18])) {
            // Get Data Image With base64
            $img = $request->image;

            $folderPath = "public/images/";
            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $formatName = uniqid() . '-data';
            $image_base64 = base64_decode($image_parts[1]);

            // Simpan sementara untuk pemeriksaan
            $temporaryFile = tempnam(sys_get_temp_dir(), 'img');
            file_put_contents($temporaryFile, $image_base64);

            // Buat gambar dari file sementara
            $image = Image::make($temporaryFile);

            // Hitung brightness rata-rata
            $totalBrightness = 0;
            $pixelCount = 0;

            for ($x = 0; $x < $image->width(); $x++) {
                for ($y = 0; $y < $image->height(); $y++) {
                    $pixel = $image->pickColor($x, $y);
                    $brightness = ($pixel[0] + $pixel[1] + $pixel[2]) / 3;
                    $totalBrightness += $brightness;
                    $pixelCount++;
                }
            }

            $averageBrightness = $totalBrightness / $pixelCount;
            $normalizedBrightness = ($averageBrightness / 255) * 200 - 100;

            // Periksa apakah brightness kurang dari -75
            if ($normalizedBrightness < -50) {
                return back()->with('error', 'Foto terlalu gelap. Brightness minimal harus -50.');
            }

            $fileName = $formatName . '.png';
            $file = $folderPath . $fileName;
            Storage::put($file, $image_base64);
            // Hapus file sementara
            unlink($temporaryFile);
            // End Get Data Image With base64
        } else {
            $fileName = 'no-image.jpg';
        }
        $latUser = $request->lat_user;
        $longUser = $request->long_user; // 125950.0
        // $latUser = -7.864453554822072;
        // $longUser = 111.49581153034036; //13316.0

        // Sementara
        $user_id = $request->user_id;
        $kerjasama_id = $request->kerjasama_id;
        $shift_id = $request->shift_id;
        $perlengkapan = json_encode($request->perlengkapan);
        $keterangan = $request->keterangan;
        $absensi_type_masuk = $request->absensi_type_masuk;
        $deskripsi = $request->deskripsi;
        $masuk = $request->masuk;
        $tukar = $request->tukar;
        $lembur = $request->lembur;
        $terus = $request->terus;
        $tukar_id = $request->pengganti;

        // end Sementara

        $harLok = Lokasi::where('client_id', Auth::user()->kerjasama->client_id)->first();
        // dd($harLok);
        $latMitra = (float) $request->lat_mitra;
        $longMitra = (float) $request->long_mitra;
        $jarak = $this->distance($latMitra, $longMitra, $latUser, $longUser);
        $radius = round($jarak['meters']);

        // dd($jarak, $latMitra, $longMitra, $latUser, $longUser, $request->all());
        $panjangLat = strlen($latUser);
        $panjangLong = strlen($longUser);

        $agent = $this->detectDevice($request->header('User-Agent'));
        $ukuran = $panjangLat + $panjangLong;


        if ($agent == 'android' || $agent == 'unknow') {
            $sebuahPengukur = 22;

        } elseif ($agent == 'iphone') {
            $sebuahPengukur = 36;
        }


        if ($ukuran <= $sebuahPengukur) {
            if ($radius <= $request->radius_mitra) {
                try {
                    DB::beginTransaction();
                    if ($absensi) {
                        if (count($absensi) <= 2) {
                            $absensi = new Absensi();

                            $absensiData = [
                                'user_id' => $user_id,
                                'kerjasama_id' => $kerjasama_id,
                                'shift_id' => $shift_id,
                                'perlengkapan' => $perlengkapan,
                                'keterangan' => $keterangan,
                                'absensi_type_masuk' => Carbon::now()->format('H:i:s'),
                                'tanggal_absen' => Carbon::now()->format('Y-m-d'),
                                'image' => $fileName,
                                'deskripsi' => $deskripsi,
                                'tipe_id' => '1',
                                'msk_lat' => $latUser,
                                'msk_long' => $longUser,
                                'masuk' => $masuk,
                                'tukar' => $tukar,
                                'lembur' => $lembur,
                                'terus' => $terus,
                                'tukar_id' => $tukar_id,
                            ];
                            $absensi->create($absensiData);
                            DB::commit();
                            toastr()->success('Berhasil Absen Hari Ini', 'succes');

                            $users = Auth::user();

                            return redirect()->to(route('dashboard.index'));
                        } else {
                            toastr()->error('Tidak Dapat Absensi Lebih 2x', 'Error');
                            return redirect()->back();
                        }

                    } else {
                        $absensi = new Absensi();

                        $absensiData = [
                            'user_id' => $user_id,
                            'kerjasama_id' => $kerjasama_id,
                            'shift_id' => $shift_id,
                            'perlengkapan' => $perlengkapan,
                            'keterangan' => $keterangan,
                            'absensi_type_masuk' => Carbon::now()->format('H:i:s'),
                            'tanggal_absen' => Carbon::now()->format('Y-m-d'),
                            'image' => $fileName,
                            'deskripsi' => $deskripsi,
                            'tipe_id' => '1',
                            'msk_lat' => $latUser,
                            'msk_long' => $longUser,
                            'masuk' => $masuk,
                            'tukar' => $tukar,
                            'lembur' => $lembur,
                            'terus' => $terus,
                            'tukar_id' => $tukar_id
                        ];

                        $absensi->create($absensiData);
                        DB::commit();
                        toastr()->success('Berhasil Absen Hari Ini', 'succes');

                        $users = Auth::user();

                        return redirect()->to(route('dashboard.index'));
                    }
                } catch (\Exception $e) {
                    // dd($request->all(), $e);
                    DB::rollBack();
                    \Log::error('Error storing data Absensi: ' . $e->getMessage());
                    toastr()->error('Gagal Absen Cek Signal Dan Coba Lagi', 'error');
                    return redirect()->back();
                }


            } else {
                // dd($radius <= $request->radius_mitra, $jarak, $radius, $request->radius_mitra, $request->all());
                toastr()->error('Kamu Diluar Radius', 'Error');
                return redirect()->back();
            }
        } else {
            toastr()->error('Harap Matikan Extension Fake GPS !', 'Error');
            return redirect()->back();
        }

    }

    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        $cekAbsen = Absensi::where('user_id', Auth::user()->id)->where('tanggal_absen', Carbon::now()->format('Y-m-d'))->get();
        $user = Auth::user()->id;
        $dev = Divisi::all();
        $client = Client::all();
        $shift = Shift::orderBy('jam_start', 'asc')->get();
        $jadwal = JadwalUser::where('user_id', $user)->latest()->get();
        $cekAbsen = Absensi::where('user_id', $user)->where('tanggal_absen', Carbon::now()->format('Y-m-d'))->get();
        // dd(count($cekAbsen));
        $harLok = Lokasi::where('client_id', Auth::user()->kerjasama->client_id)->first();
        if ($absensi != null) {
            return view('absensi.updateAbsen', compact('absensi', 'cekAbsen', 'user', 'dev', 'client', 'shift', 'jadwal', 'harLok'));
        }
        toastr()->error('Data Tidak Ditemukan', 'error');
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {

        $user = Auth::user()->id;
        $absensi = Absensi::latest()->where('user_id', $user)->first();
        if (Auth::user()->kerjasama_id != 1) {
            // Get Data Image With base64
            $img = $request->image;

            $folderPath = "public/images/";
            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $formatName = uniqid() . '-data';
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $formatName . '.png';
            $file = $folderPath . $fileName;
            Storage::put($file, $image_base64);
            // End Get Data Image With base64
        } else {
            $fileName = 'no-image.jpg';
        }
        $latUser = $request->lat_user;
        $longUser = $request->long_user; // 125950.0
        // $latUser = -7.864453554822072;
        // $longUser = 111.49581153034036; //13316.0

        // Sementara
        $user_id = $request->user_id;
        $kerjasama_id = $request->kerjasama_id;
        $shift_id = $request->shift_id;
        $perlengkapan = json_encode($request->perlengkapan);
        $keterangan = $request->keterangan;
        $absensi_type_masuk = $request->absensi_type_masuk;
        $deskripsi = $request->deskripsi;
        // end Sementara

        $harLok = Lokasi::where('client_id', Auth::user()->kerjasama->client_id)->first();
        // dd($harLok);
        $latMitra = $harLok->latitude;
        $longMitra = $harLok->longtitude;
        $jarak = $this->distance($latMitra, $longMitra, $latUser, $longUser);
        $radius = round($jarak['meters']);

        // dd($request->all());
        // dd($radius);

        if ($radius <= $harLok->radius) {
            if ($absensi) {
                if (Carbon::now()->format('Y-m-d') == $absensi->tanggal_absen) {

                    $absensi = [
                        'user_id' => $user_id,
                        'kerjasama_id' => $kerjasama_id,
                        'shift_id' => $shift_id,
                        'perlengkapan' => $perlengkapan,
                        'keterangan' => $keterangan,
                        'tanggal_absen' => Carbon::now()->format('Y-m-d'),
                        'image' => $fileName,
                        'deskripsi' => $deskripsi,
                        'tipe_id' => '1',
                        'absensi_type_pulang' => null,
                    ];
                    // dd($absensi);


                    Absensi::findOrFail($id)->update($absensi);
                    toastr()->success('Berhasil Update Absen Hari Ini', 'success');

                    $users = Auth::user();

                    return redirect()->route('dashboard.index');
                } else {
                    toastr()->error('Tidak Dapat Absensi 2x', 'Error');
                    return redirect()->back();
                }

            } else {

                $absensi = [
                    'user_id' => $user_id,
                    'kerjasama_id' => $kerjasama_id,
                    'shift_id' => $shift_id,
                    'perlengkapan' => $perlengkapan,
                    'keterangan' => $keterangan,
                    'tanggal_absen' => Carbon::now()->format('Y-m-d'),
                    'image' => $fileName,
                    'deskripsi' => $deskripsi,
                    'tipe_id' => '1',
                    'absensi_type_pulang' => null,
                ];


                Absensi::findOrFail($id)->update($absensi);
                toastr()->success('Berhasil Absen Hari Ini', 'succes');

                $users = Auth::user();

                return redirect()->to(route('dashboard.index'));
            }
        } else {
            toastr()->error('Kamu Diluar Radius', 'Error');
            return redirect()->back();
        }

    }

    public function indexPrivate(Request $request)
    {
        $user = Auth::user()->id;
        $dev = Divisi::all();
        $client = Client::all();
        $shift = Shift::orderBy('jam_start', 'asc')->get();
        $jadwal = JadwalUser::where('user_id', $user)->latest()->get();
        $absensi = Absensi::where('user_id', $user)->latest()->get();
        // dd($absensi);
        $harLok = Lokasi::where('client_id', Auth::user()->kerjasama->client_id)->first();
        return view('absensi.absenPrivate', compact('shift', 'client', 'dev', 'absensi', 'harLok', 'jadwal'));
    }

    public function storePrivate(AbsensiRequest $request)
    {

        $user = Auth::user()->id;
        $absensi = Absensi::latest()->where('user_id', $user)->first();
        // Get Data Image With base64
        $img = $request->image;

        $folderPath = "public/images/";
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $formatName = uniqid() . '-data';
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . '.png';
        $file = $folderPath . $fileName;
        Storage::put($file, $image_base64);
        // End Get Data Image With base64


        // Sementara
        $user_id = $request->user_id;
        $kerjasama_id = $request->kerjasama_id;
        $shift_id = $request->shift_id;
        $perlengkapan = json_encode($request->perlengkapan);
        $keterangan = $request->keterangan;
        $absensi_type_masuk = $request->absensi_type_masuk;
        $deskripsi = $request->deskripsi;
        $masuk = $request->masuk;
        $tukar = $request->tukar;
        $lembur = $request->lembur;
        // end Sementara



        // dd($radius);
        if ($absensi) {
            if (Carbon::now()->format('Y-m-d') != $absensi->tanggal_absen) {
                $absensi = new Absensi();

                $absensi = [
                    'user_id' => $user_id,
                    'kerjasama_id' => $kerjasama_id,
                    'shift_id' => $shift_id,
                    'perlengkapan' => $perlengkapan,
                    'keterangan' => $keterangan,
                    'absensi_type_masuk' => Carbon::now()->format('H:i:s'),
                    'tanggal_absen' => Carbon::now()->format('Y-m-d'),
                    'image' => $fileName,
                    'deskripsi' => $deskripsi,
                    'tipe_id' => '2',
                    'masuk' => $masuk,
                    'tukar' => $tukar,
                    'lembur' => $lembur,
                ];

                Absensi::create($absensi);
                toastr()->success('Berhasil Absen Hari Ini', 'succes');

                $users = Auth::user();

                return redirect()->to(route('dashboard.index'));
            } else {
                toastr()->error('Tidak Dapat Absensi 2x', 'Error');
                return redirect()->back();
            }

        } else {
            $absensi = new Absensi();

            $absensi = [
                'user_id' => $user_id,
                'kerjasama_id' => $kerjasama_id,
                'shift_id' => $shift_id,
                'perlengkapan' => $perlengkapan,
                'keterangan' => $keterangan,
                'absensi_type_masuk' => Carbon::now()->format('H:i:s'),
                'tanggal_absen' => Carbon::now()->format('Y-m-d'),
                'image' => $fileName,
                'deskripsi' => $deskripsi,
                'tipe_id' => '2',
                'masuk' => $masuk,
                'tukar' => $tukar,
                'lembur' => $lembur,
            ];

            Absensi::create($absensi);
            toastr()->success('Berhasil Absen Hari Ini', 'succes');

            $users = Auth::user();

            return redirect()->to(route('dashboard.index'));
        }

    }

    public function updatePulang(Request $request, $id)
    {
        $absensi = Absensi::find($id);
        $waktuMasuk = Carbon::parse($absensi->absensi_type_masuk);

        $selisihWaktu = $waktuMasuk->diffInMinutes(Carbon::now());
        $absenMasuk = $waktuMasuk->toTimeString();
        $waktuPoint = "08:15:00";

        $formatPoint = strtotime($waktuPoint);
        $formatMasuk = strtotime($absenMasuk);

        $absenmasuk = Carbon::createFromFormat('H:i:s', $absenMasuk);

        // Mengonversi waktu point menjadi objek Carbon
        $waktuPointObj = Carbon::createFromFormat('H:i:s', $waktuPoint);

        // Menghitung selisih waktu antara waktu masuk dan waktu point dalam menit
        $selisihWaktu = $absenmasuk->diffInMinutes($waktuPointObj);

        $latUser = $request->lat_user;
        $longUser = $request->long_user;

        $harLok = Lokasi::where('client_id', Auth::user()->kerjasama->client_id)->first();
        // dd($harLok);
        $latMitra = $harLok->latitude;
        $longMitra = $harLok->longtitude;
        $jarak = $this->distance($latMitra, $longMitra, $latUser, $longUser);
        $radius = round($jarak['meters']);

        // dd($request->all());

        if ($absensi->tipe_id == 1) {
            // Get Data kerjasama 1 and update point
            if (Auth::user()->kerjasama_id == 1 && $absensi->absensi_type_masuk != null) {
                if (Auth::user()->name == 'DIREKSI') {
                    $absensi->point_id = 1;
                    $absensi->plg_lat = $latUser;
                    $absensi->plg_long = $longUser;
                    $point = "Point Di Klaim !";
                } else {

                    if ($latUser != null && $longUser != null) {
                        $panjangLat = strlen($latUser);
                        $panjangLong = strlen($longUser);
                        $agent = $this->detectDevice($request->header('User-Agent'));
                        $ukuran = $panjangLat + $panjangLong;

                        if ($agent == 'android' || $agent == 'unknow') {
                            $sebuahPengukur = 24;

                        } elseif ($agent == 'iphone') {
                            $sebuahPengukur = 35;
                        }


                    } else {
                        toastr()->error('Gagal Absen Pulang !! Nyalakan GPS !!', 'error');
                        return redirect()->back();
                    }

                    if ($ukuran <= $sebuahPengukur) {
                        if ($absensi->keterangan == "telat" && $selisihWaktu <= 15) {
                            $absensi->point_id = 2;
                            $absensi->plg_lat = $latUser;
                            $absensi->plg_long = $longUser;
                            $point = "Point Di Klaim !";
                        } elseif ($absensi->keterangan == "telat" && $selisihWaktu > 15) {
                            $absensi->point_id = null;
                            $absensi->plg_lat = $latUser;
                            $absensi->plg_long = $longUser;
                            $point = "Point Tidak Dapat Klaim !";
                        } else {
                            $absensi->point_id = 1;
                            $absensi->plg_lat = $latUser;
                            $absensi->plg_long = $longUser;
                            $point = "Point Di Klaim !";
                        }
                    } else {
                        toastr()->error('Error GPS Mati !!, Nyalakan GPS Untuk Absen Pulang !!', 'errorr');
                        return redirect()->back();
                    }
                }

                $absensi->absensi_type_pulang = Carbon::now()->format('H:i:s');
                $absensi->save();

                toastr()->success('Berhasil Absen Pulang Hari Ini', 'succes');
                return redirect()->to(route('dashboard.index'))->with(['point' => $point]);

                //This diferent but same action to update data
            } elseif ($absensi && Auth::user()->kerjasama_id != 1) {
                if ($latUser != null && $longUser != null) {
                    $panjangLat = strlen($latUser);
                    $panjangLong = strlen($longUser);

                    $agent = $this->detectDevice($request->header('User-Agent'));
                    $ukuran = $panjangLat + $panjangLong;

                    if ($agent == 'android' || $agent == 'unknow') {
                        $sebuahPengukur = 24;

                    } elseif ($agent == 'iphone') {
                        $sebuahPengukur = 35;
                    }

                } else {
                    toastr()->error('Gagal Absen Pulang !! Nyalakan GPS !!', 'error');
                    return redirect()->back();
                }

                if ($ukuran <= $sebuahPengukur) {
                    $absensi->absensi_type_pulang = Carbon::now()->format('H:i:s');
                    $absensi->plg_lat = $latUser;
                    $absensi->plg_long = $longUser;
                    $absensi->save();

                    toastr()->success('Berhasil Absen Pulang Hari Ini', 'succes');
                    return redirect()->to(route('dashboard.index'));
                } else {
                    toastr()->error('Error GPS Mati !!, Nyalakan GPS Untuk Absen Pulang !!', 'errorr');
                    return redirect()->back();
                }
            } else {
                toastr()->error('Gagal Absen Pulang', 'errorr');
                return redirect()->back();
            }
        } else {
            // Get Data kerjasama 1 and update point
            if (Auth::user()->kerjasama_id == 1 && $absensi->absensi_type_masuk != null) {
                if (Auth::user()->name == 'DIREKSI') {
                    $absensi->point_id = 1;
                    $absensi->plg_lat = $latUser;
                    $absensi->plg_long = $longUser;
                    $point = "Point Di Klaim !";
                } else {

                    $latitud = $request->lat_user;
                    $long = $request->long_user;

                    if ($latitud != null && $long != null) {
                        $panjangLat = strlen($latUser);
                        $panjangLong = strlen($longUser);

                        $agent = $this->detectDevice($request->header('User-Agent'));
                        $ukuran = $panjangLat + $panjangLong;

                        if ($agent == 'android' || $agent == 'unknow') {
                            $sebuahPengukur = 24;

                        } elseif ($agent == 'iphone') {
                            $sebuahPengukur = 35;
                        }

                    } else {
                        toastr()->error('Gagal Absen Pulang !! Nyalakan GPS !!', 'error');
                        return redirect()->back();
                    }

                    if ($ukuran <= $sebuahPengukur) {
                        if ($absensi->keterangan == "telat" && $selisihWaktu <= 15) {
                            $absensi->point_id = 2;
                            $absensi->plg_lat = $latUser;
                            $absensi->plg_long = $longUser;
                            $point = "Point Di Klaim !";
                        } elseif ($absensi->keterangan == "telat" && $selisihWaktu > 15) {
                            $absensi->point_id = null;
                            $absensi->plg_lat = $latUser;
                            $absensi->plg_long = $longUser;
                            $point = "Point Tidak Dapat Klaim !";
                        } else {
                            $absensi->point_id = 1;
                            $absensi->plg_lat = $latUser;
                            $absensi->plg_long = $longUser;
                            $point = "Point Di Klaim !";
                        }
                    } else {
                        toastr()->error('Error GPS Mati !!, Nyalakan GPS Untuk Absen Pulang !!', 'errorr');
                        return redirect()->back();
                    }
                }

                $absensi->absensi_type_pulang = Carbon::now()->format('H:i:s');
                $absensi->save();

                toastr()->success('Berhasil Absen Pulang Hari Ini', 'success');
                return redirect()->to(route('dashboard.index'))->with(['point' => $point]);

                //This diferent but same action to update data
            } elseif ($absensi && Auth::user()->kerjasama_id != 1) {

                $latitud = $request->lat_user;
                $long = $request->long_user;

                if ($latitud != null && $long != null) {
                    $panjangLat = strlen($latUser);
                    $panjangLong = strlen($longUser);

                    $agent = $this->detectDevice($request->header('User-Agent'));
                    $ukuran = $panjangLat + $panjangLong;

                    if ($agent == 'android' || $agent == 'unknow') {
                        $sebuahPengukur = 24;

                    } elseif ($agent == 'iphone') {
                        $sebuahPengukur = 35;
                    }

                } else {
                    toastr()->error('Gagal Absen Pulang !! Nyalakan GPS !!', 'error');
                    return redirect()->back();
                }

                $absensi->absensi_type_pulang = Carbon::now()->format('H:i:s');
                $absensi->save();

                toastr()->success('Berhasil Absen Pulang Hari Ini', 'succes');
                return redirect()->to(route('dashboard.index'));


            } else {
                toastr()->error('Gagal Absen Pulang', 'errorr');
                return redirect()->back();
            }
        }


    }

    public function updateAbsenPulang($id)
    {
        $currentTime = Carbon::now()->format('H:i:s');
        $timeLimit = Carbon::parse('11:26:00'); // Waktu batas absen pulang
        $absensi = Absensi::findOrFail($id);
        $absensi->whereNull('absensi_type_pulang')->update(['absensi_type_pulang' => 'belum Absen Pulang']);
        $absensi->save();
        return response()->json(['success' => true]);

        // if ($currentTime > $timeLimit) {
        //     $absensi = Absensi::whereNull('absensi_type_pulang')->update(['absensi_type_pulang' => 'Tanpa Absen Pulang']);
        //     $absensi->save();
        // }
    }
    public function updateSiang($id)
    {
        try {
            $absensi = Absensi::find($id);
            $clock = Carbon::now()->format('H:i:s');
            $absensi->absensi_type_siang = $clock;
            $absensi->save();
            toastr()->success('Berhasil Absen Siang Jam : ' . $clock, 'succes');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Error Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }

    }

    // Subuh
    public function updateSubuh(Request $request, $id)
    {
        try {
            $absensi = Absensi::find($id);
            $clock = Carbon::now()->format('H:i:s');

            $absensi->subuh_lat = $request->lat_user;
            $absensi->subuh_long = $request->long_user;

            $absensi->subuh = 1;
            $absensi->save();
            toastr()->success('Berhasil Absen Shalat Jam : ' . $clock, 'succes');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Error Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }

    }

    // dzuhur
    public function updateDzuhur(Request $request, $id)
    {

        $latitud = $request->lat_user;
        $long = $request->long_user;

        // dd($request->lat_user, $request->long_user);

        if ($latitud != null && $long != null) {
            $panjangLat = strlen($latitud);
            $panjangLong = strlen($long);

            $agent = $this->detectDevice($request->header('User-Agent'));
            $ukuran = $panjangLat + $panjangLong;

            if ($agent == 'android' || $agent == 'unknow') {
                $sebuahPengukur = 24;

            } elseif ($agent == 'iphone') {
                $sebuahPengukur = 35;
            }
            if ($ukuran <= $sebuahPengukur) {
                try {
                    $absensi = Absensi::find($id);
                    $clock = Carbon::now()->format('H:i:s');
                    $absensi->dzuhur = 1;
                    $absensi->sig_lat = $request->lat_user;
                    $absensi->sig_long = $request->long_user;

                    $absensi->save();
                    toastr()->success('Berhasil Absen Shalat Jam : ' . $clock, 'succes');
                    return redirect()->back();
                } catch (\Throwable $th) {
                    toastr()->error('Error Data Tidak Ditemukan', 'error');
                    return redirect()->back();
                }
            } else {
                toastr()->error('Gagal Absen Siang !! Matikan Extension Fake GPS !!', 'error');
                return redirect()->back();
            }
        } else {
            toastr()->error('Gagal Absen Siang !! Nyalakan GPS !!', 'error');
            return redirect()->back();
        }




    }

    // asar
    public function updateAsar(Request $request, $id)
    {
        try {
            $absensi = Absensi::find($id);
            $clock = Carbon::now()->format('H:i:s');

            $absensi->asar_lat = $request->lat_user;
            $absensi->asar_long = $request->long_user;

            $absensi->asar = 1;
            $absensi->save();
            toastr()->success('Berhasil Absen Shalat Jam : ' . $clock, 'succes');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Error Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }

    }

    // maghrib
    public function updateMaghrib(Request $request, $id)
    {
        try {
            $absensi = Absensi::find($id);
            $clock = Carbon::now()->format('H:i:s');

            $absensi->maghrib_lat = $request->lat_user;
            $absensi->maghrib_long = $request->long_user;

            $absensi->maghrib = 1;
            $absensi->save();
            toastr()->success('Berhasil Absen Shalat Jam : ' . $clock, 'succes');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Error Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }

    }

    // isya
    public function updateIsya(Request $request, $id)
    {
        try {
            $absensi = Absensi::find($id);
            $clock = Carbon::now()->format('H:i:s');

            $absensi->isya_lat = $request->lat_user;
            $absensi->isya_long = $request->long_user;

            $absensi->isya = 1;
            $absensi->save();
            toastr()->success('Berhasil Absen Shalat Jam : ' . $clock, 'succes');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Error Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }

    }

    public function historyAbsensi(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $kerjasamaId = $user->kerjasama_id;

        $isFiltered = $request->filled('search');
        $filterDate = $isFiltered ? Carbon::parse($request->search) : Carbon::now();
        $month = $filterDate->month;
        $year = $filterDate->year;

        $pointId = Point::all();

        // Base query
        $baseQuery = Absensi::with(['point', 'shift', 'user'])->where('user_id', $userId);

        // Use 30-day range when no filter is given
        if (!$isFiltered) {
            $startDate = Carbon::now()->subDays(31)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $absen = $baseQuery->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('tanggal_absen', 'desc')->paginate(50);
            $point = $baseQuery->clone()->whereNotNull('point_id')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
            $absenTiga = $baseQuery->clone()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('absensi_type_masuk')
                ->whereNotNull('absensi_type_pulang')
                ->get();
            $telat = $baseQuery->clone()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('keterangan', 'telat')
                ->paginate(50);

            // Calculate effective working days for 30 days
            $hariEfektif = 0;
            $period = CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $d) {
                if ($kerjasamaId == 1 && !$d->isWeekend()) {
                    $hariEfektif++;
                } elseif ($kerjasamaId != 1) {
                    $hariEfektif++;
                }
            }
        } else {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->startOfWeek();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfWeek();

            $absen = $baseQuery
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('tanggal_absen', 'desc')
                ->paginate(50);
            $point = $baseQuery->clone()->whereNotNull('point_id')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->get();
            $absenTiga = $baseQuery->clone()
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->whereNotNull('absensi_type_masuk')
                ->whereNotNull('absensi_type_pulang')
                ->get();
            $telat = $baseQuery->clone()
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('keterangan', 'telat')
                ->paginate(50);

            // Effective days for filtered month
            $date = Carbon::createFromDate($year, $month, 1);
            $endOfMonth = $date->copy()->endOfMonth();
            $weeks = ceil(($date->startOfMonth()->dayOfWeek + $endOfMonth->day) / 7);

            $hariEfektif = 0;
            $period = CarbonPeriod::create($date->startOfMonth(), $endOfMonth);
            foreach ($period as $d) {
                if ($kerjasamaId == 1 && !$d->isWeekend()) {
                    $hariEfektif++;
                } elseif ($kerjasamaId != 1) {
                    $hariEfektif++;
                }
            }
            if ($kerjasamaId != 1) {
                $hariEfektif -= $weeks;
            }
        }

        $date = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $startOfMonth = $date->copy()->startOfMonth()->dayOfWeek;

        // API data
        $client = new HTTP();
        $apiEndpoint = 'https://dayoffapi.vercel.app/api?year=' . $year;
        $apiEndpointMonth = 'https://dayoffapi.vercel.app/api?month=' . $month . '&year=' . $year;

        $data = json_decode($client->get($apiEndpoint)->getBody(), true);
        $dataMonth = json_decode($client->get($apiEndpointMonth)->getBody(), true);

        // Percent calculation
        $countTelat = $telat->total();
        $countAbsenLengkap = $absenTiga->count();

        $persentaseHariMasuk = $hariEfektif ? (100 / $hariEfektif) : 0;
        $persentaseKehadiran = ($countAbsenLengkap - $countTelat) * $persentaseHariMasuk;

        $status = match (true) {
            $persentaseKehadiran >= 80 => "BAIK",
            $persentaseKehadiran >= 60 => "CUKUP",
            default => "KURANG"
        };

        return view('absensi.history', [
            'telat' => $countTelat,
            'persentase' => $persentaseKehadiran,
            'status' => $status,
            'absen' => $absen,
            'point' => $point,
            'pointId' => $pointId,
            'filter' => $request->search,
            'year' => $year,
            'month' => $month,
            'daysInMonth' => $daysInMonth,
            'startOfMonth' => $startOfMonth,
            'harLib' => collect($data)
        ]);
    }


    public function historyAbsenFilter(Request $request)
    {
        $user = Auth::user()->id;
        $abs = Absensi::all();
        $pointId = Point::all();
        $point = Absensi::whereNotNull('point_id')->where('user_id', $user)->whereMonth('created_at', $request->month)->get();
        $absen = Absensi::query();

        return view('absensi.history', [
            'absen' => $absen,
            'abs' => $abs,
            'point' => $point,
            'pointId' => $pointId
        ]);
    }

    public function claimPoint(Request $request, $id)
    {
        $absen = [
            'point_id' => $request->point_id,
        ];
        $absensiId = Absensi::findOrFail($id);
        $absensiId->update($absen);
        toastr()->success('Point Diclaim', 'success');
        return redirect()->back();
    }

    public function distance($lat1, $lon1, $lat2, $lon2)
    {
        // Konversi ke radian
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        // Haversine formula
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $earthRadius = 6371000; // Radian bumi dalam meter
        $meters = $earthRadius * $c;

        return compact('meters');
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

        return view('leader_view.absen.maps', compact('absen', 'lokMitra', 'tgl', 'us'));
    }

    public function detectDevice($userAgent)
    {
        // Convert the user agent to lowercase
        $agent = Str::lower($userAgent);

        // Check for Android
        if (Str::contains($agent, 'android')) {
            return "android"; // or return $agent; if you want to return the value
        }
        // Check for iPhone, iPad, or iPod
        elseif (Str::contains($agent, ['iphone', 'ipad', 'ipod'])) {
            return "iphone"; // or return $agent; if you want to return the value
        }
        // For other cases
        else {
            return "unknow"; // or return $agent; if you want to return the value
        }
    }
}
