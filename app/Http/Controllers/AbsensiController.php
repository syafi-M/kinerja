<?php

namespace App\Http\Controllers;
use App\Http\Requests\AbsensiRequest;
use App\Models\Absensi;
use App\Models\Client;
use GuzzleHttp\Client as HTTP;
use App\Models\Divisi;
use App\Models\JadwalUser;
use App\Models\Lokasi;
use App\Models\Point;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
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

class AbsensiController extends Controller
{


    public function index(Request $request)
    {
        $user = Auth::user()->id;
        $dev = Divisi::with(['Jabatan', 'Perlengkapan'])->get();
        $client = Client::all();
        
        $shiftFirst = Shift::orderBy('jam_start', 'asc');
        
        $shift1 = $shiftFirst->where('jam_start', '>=', '04:00')->where('client_id', Auth::user()->kerjasama->client_id)->where('jabatan_id', Auth::user()->divisi->jabatan_id)->get();
	    $shift2 = $shiftFirst->where('jam_start', '>=', '11:00')->where('client_id', Auth::user()->kerjasama->client_id)->where('jabatan_id', Auth::user()->divisi->jabatan_id)->get();
	    $shift3 = $shiftFirst->where('jam_start', '>=', '16:00')->where('client_id', Auth::user()->kerjasama->client_id)->where('jabatan_id', Auth::user()->divisi->jabatan_id)->get();
	    
	    if(Carbon::now()->format('H:i') >= '04:00' && Carbon::now()->format('H:i') < '11:00'){
	        $shift = $shift1;
	    }else if(Carbon::now()->format('H:i') >= '11:00' && Carbon::now()->format('H:i') < '24:00'){
	        $shift = $shift2;
	    }else{
	        $shift = $shift3;
	    }
	    
        $dirShift = Shift::firstWhere('shift_name', 'DIREKSI');
        $aID = Absensi::where('tanggal_absen', Carbon::now()->format('Y-m-d'))->pluck('user_id')->toArray();
        $userL = User::where('kerjasama_id', Auth::user()->kerjasama_id)->whereNotIn('id', $aID)->get();
	    
	    
        $jadwal = JadwalUser::where('user_id', $user)->latest()->get();
        $absensi = Absensi::with(['User', 'Kerjasama', 'Shift'])->where('user_id',$user)->latest()->get();
        $cekAbsen = Absensi::with(['User'])->where('user_id', $user)->where('tanggal_absen', Carbon::now()->format('Y-m-d'))->get();
        $cekPulang = Absensi::with(['User'])->where('user_id', $user)->where('tanggal_absen', Carbon::now()->format('Y-m-d'))->latest()->first();
        $cekTukar = Absensi::with(['User'])->where('tukar_id', $user)->where('tanggal_absen', Carbon::now()->format('Y-m-d'))->first();
        $pengganti = User::where('kerjasama_id', Auth::user()->kerjasama_id)->where('devisi_id', Auth::user()->devisi_id)->where('id', '!=', Auth::user()->id)->whereNotIn('id', function($query) {
            $query->select('tukar_id')
                  ->from('absensis')
                  ->where('tanggal_absen', Carbon::now()->format('Y-m-d'))
                  ->whereNotNull('tukar_id');
        })->get();
        
        $tesLib = '';
        $afaLib = false;
        
        if(Auth::user()->kerjasama_id == 1){
            $hLib = Carbon::now()->isoFormat('dddd');
            $dayInMonth = Carbon::now()->daysInMonth;
            $bBulan = Carbon::now()->month;
            
            $loginResponse = httped::timeout(30)->get("https://raw.githubusercontent.com/guangrei/APIHariLibur_V2/main/holidays.json");
            $holidayDates = collect($loginResponse->json())->keys()->toArray();
            $liburNasional = false;
            foreach($holidayDates as $hLiday){
                if($hLiday == Carbon::now()->format('Y-m-d')){
                    $liburNasional = true;
                }else{
                    $liburNasional = false;
                }
                $tesLib = $hLiday;
            }
            
            if($hLib == 'Minggu' || $hLib == 'Sabtu' || $liburNasional){
                $afaLib = true;
                // dd($afaLib);
            }else{
                $afaLib = false;
                // dd($afaLib);
            }
        }
        
        $harLok = Lokasi::where('client_id', Auth::user()->kerjasama->client_id)->first();
        return view('absensi.index', compact('userL','cekPulang', 'tesLib','afaLib','shift', 'client', 'dev', 'absensi', 'harLok', 'jadwal', 'cekAbsen', 'pengganti', 'cekTukar', 'dirShift')); 
    }
    
    public function getShift($cli, $jab)
    {
        $dev = Divisi::with(['Jabatan', 'Perlengkapan'])->get();
        $shiftFirst = Shift::with(['jabatan'])->orderBy('jam_start', 'asc');
        $shift1 = $shiftFirst->where('jam_start', '>=', '04:00')->where('client_id', $cli)->where('jabatan_id', $jab)->get();
	    $shift2 = $shiftFirst->where('jam_start', '>=', '11:00')->where('client_id', $cli)->where('jabatan_id', $jab)->get();
	    $shift3 = $shiftFirst->where('jam_start', '>=', '16:00')->where('client_id', $cli)->where('jabatan_id', $jab)->get();
	    
	    if(Carbon::now()->format('H:i') >= '04:00' && Carbon::now()->format('H:i') < '11:00'){
	        $shift = $shift1;
	    }else if(Carbon::now()->format('H:i') >= '11:00' && Carbon::now()->format('H:i') < '24:00'){
	        $shift = $shift2;
	    }else{
	        $shift = $shift3;
	    }
	    
	    return response()->json(['shift' => $shift, 'dev' => $dev]);
    }

    public function store(Request $request)
    {
           $rules = [
                    'user_id'       => 'required',
                    'kerjasama_id'  => 'required',
                    'shift_id'      => 'required',
                    'perlengkapan'  => 'required',
                    'keterangan'    => 'required',
                    'absensi_type_masuk'  => 'required',
                    'absensi_type_pulang'  => 'nullable',
                    'image'       => Auth::user()->kerjasama_id != 1 ? 'required' : 'nullable',
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
        
            // Melakukan validasi
            $validator = Validator::make($request->all(), $rules, $customMessages);
        
            if ($validator->fails()) {
                toastr()->error('Formulir tidak lengkap. Mohon isi semua kolom.', 'error');
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $user = Auth::user()->id; 
            $absensi = Absensi::latest()->where('user_id', $user)->whereNotNull('absensi_type_pulang')->whereDate('created_at', Carbon::now()->format('Y-m-d'))->get();
            if(Auth::user()->kerjasama_id != 1){
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
            }else{
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
        $latMitra = $harLok->latitude;
        $longMitra = $harLok->longtitude;
        $jarak = $this->distance($latMitra, $longMitra, $latUser, $longUser);
        $radius = round($jarak['meters']);
       
        // dd($radius);
        $panjangLat = strlen($latUser);
        $panjangLong = strlen($longUser);
        
        $agent = $this->detectDevice($request->header('User-Agent'));
        $ukuran = $panjangLat + $panjangLong;
        
        if($agent == 'android' || $agent == 'unknow')
        {
            $sebuahPengukur = 24;
            
        }elseif($agent == 'iphone')
        {
            $sebuahPengukur = 35;
        }
        
        if($ukuran <= $sebuahPengukur){
            if ($radius <= $harLok->radius) {
                try {
                    if($absensi)    
                    {
                        if(count($absensi) <= 2)
                        {
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
                            toastr()->success('Berhasil Absen Hari Ini', 'succes');
                            
                            $users = Auth::user();
                            
                            return redirect()->to(route('dashboard.index'));
                        }else
                        {
                            toastr()->error('Tidak Dapat Absensi Lebih 2x', 'Error');
                            return redirect()->back();  
                        }
                            
                    }else{
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
                        toastr()->success('Berhasil Absen Hari Ini', 'succes');
                        
                        $users = Auth::user();
                        
                        return redirect()->to(route('dashboard.index'));
                    }
                } catch (\Exception $e) {
                    // dd($request->all(), $e);
                    \Log::error('Error storing data Absensi: ' . $e->getMessage());
                     toastr()->error('Gagal Absen Cek Signal Dan Coba Lagi', 'error');
                     return redirect()->back();
                }
                
                
            }else {
                toastr()->error('Kamu Diluar Radius', 'Error');
                return redirect()->back();  
            }
        }else{
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
            if(Auth::user()->kerjasama_id != 1){
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
            }else{
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
            if($absensi)    
            {
                if(Carbon::now()->format('Y-m-d') == $absensi->tanggal_absen)
                {
    
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
                }else
                {
                    toastr()->error('Tidak Dapat Absensi 2x', 'Error');
                    return redirect()->back();  
                }
                    
            }else{
    
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
        }else {
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
        $absensi = Absensi::where('user_id',$user)->latest()->get();
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
            if($absensi)    
            {
                if(Carbon::now()->format('Y-m-d') != $absensi->tanggal_absen)
                {
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
                }else
                {
                    toastr()->error('Tidak Dapat Absensi 2x', 'Error');
                    return redirect()->back();  
                }
                    
            }else{
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
        
        if($absensi->tipe_id == 1)
        {
            // Get Data kerjasama 1 and update point
            if(Auth::user()->kerjasama_id == 1 && $absensi->absensi_type_masuk != null)
            {
                    if(Auth::user()->name == 'DIREKSI')
                    {
                        $absensi->point_id = 1;
                        $absensi->plg_lat = $latUser;
                        $absensi->plg_long = $longUser;
                        $point = "Point Di Klaim !";
                    }else{
                        
                        if($latUser != null && $longUser != null)
                        {
                            $panjangLat = strlen($latUser);
                            $panjangLong = strlen($longUser);
                             $agent = $this->detectDevice($request->header('User-Agent'));
                            $ukuran = $panjangLat + $panjangLong;
                            
                            if($agent == 'android' || $agent == 'unknow')
                            {
                                $sebuahPengukur = 24;
                                
                            }elseif($agent == 'iphone')
                            {
                                $sebuahPengukur = 35;
                            }
                            
                            
                        }else{
                            toastr()->error('Gagal Absen Pulang !! Nyalakan GPS !!', 'error');
                            return redirect()->back();
                        }
                        
                        if($ukuran <= $sebuahPengukur){
                            if ($absensi->keterangan == "telat" && $selisihWaktu <= 15) {
                                $absensi->point_id = 2;
                                $absensi->plg_lat = $latUser;
                                $absensi->plg_long = $longUser;
                                $point = "Point Di Klaim !";
                            }elseif($absensi->keterangan == "telat" && $selisihWaktu > 15 ) {
                                $absensi->point_id = null;
                                $absensi->plg_lat = $latUser;
                                $absensi->plg_long = $longUser;
                                $point = "Point Tidak Dapat Klaim !";
                            }else{
                                $absensi->point_id = 1;
                                $absensi->plg_lat = $latUser;
                                $absensi->plg_long = $longUser;
                                $point = "Point Di Klaim !";
                            }
                        }else{
                            toastr()->error('Error GPS Mati !!, Nyalakan GPS Untuk Absen Pulang !!', 'errorr');
                            return redirect()->back();
                        }
                    }
                
                $absensi->absensi_type_pulang = Carbon::now()->format('H:i:s');
                $absensi->save();
            
                toastr()->success('Berhasil Absen Pulang Hari Ini', 'succes');
                return redirect()->to(route('dashboard.index'))->with(['point' => $point]);
                
            //This diferent but same action to update data   
            }elseif($absensi && Auth::user()->kerjasama_id != 1){
                if($latUser != null && $longUser != null)
                {
                    $panjangLat = strlen($latUser);
                    $panjangLong = strlen($longUser);
                    
                     $agent = $this->detectDevice($request->header('User-Agent'));
                    $ukuran = $panjangLat + $panjangLong;
                    
                    if($agent == 'android' || $agent == 'unknow')
                    {
                        $sebuahPengukur = 24;
                        
                    }elseif($agent == 'iphone')
                    {
                        $sebuahPengukur = 35;
                    }
                    
                }else{
                    toastr()->error('Gagal Absen Pulang !! Nyalakan GPS !!', 'error');
                    return redirect()->back();
                }
                        
                    if($ukuran <= $sebuahPengukur)
                    {
                        $absensi->absensi_type_pulang = Carbon::now()->format('H:i:s');
                        $absensi->plg_lat = $latUser;
                        $absensi->plg_long = $longUser;
                        $absensi->save();
                
                        toastr()->success('Berhasil Absen Pulang Hari Ini', 'succes');
                        return redirect()->to(route('dashboard.index'));
                    }else{
                        toastr()->error('Error GPS Mati !!, Nyalakan GPS Untuk Absen Pulang !!', 'errorr');
                        return redirect()->back();
                    }
            }else {
                toastr()->error('Gagal Absen Pulang', 'errorr');
                return redirect()->back();
            }
        }else{
            // Get Data kerjasama 1 and update point
            if(Auth::user()->kerjasama_id == 1 && $absensi->absensi_type_masuk != null)
            {
                if(Auth::user()->name == 'DIREKSI')
                    {
                        $absensi->point_id = 1;
                        $absensi->plg_lat = $latUser;
                        $absensi->plg_long = $longUser;
                        $point = "Point Di Klaim !";
                    }else{
                        
                        $latitud = $request->lat_user;
                        $long = $request->long_user;
                        
                        if($latitud != null && $long != null)
                        {
                            $panjangLat = strlen($latUser);
                            $panjangLong = strlen($longUser);
                            
                             $agent = $this->detectDevice($request->header('User-Agent'));
                            $ukuran = $panjangLat + $panjangLong;
                            
                            if($agent == 'android' || $agent == 'unknow')
                            {
                                $sebuahPengukur = 24;
                                
                            }elseif($agent == 'iphone')
                            {
                                $sebuahPengukur = 35;
                            }
                            
                        }else{
                            toastr()->error('Gagal Absen Pulang !! Nyalakan GPS !!', 'error');
                            return redirect()->back();
                        }
                        
                        if($ukuran <= $sebuahPengukur){
                            if ($absensi->keterangan == "telat" && $selisihWaktu <= 15) {
                                $absensi->point_id = 2;
                                $absensi->plg_lat = $latUser;
                                $absensi->plg_long = $longUser;
                                $point = "Point Di Klaim !";
                            }elseif($absensi->keterangan == "telat" && $selisihWaktu > 15 ) {
                                $absensi->point_id = null;
                                $absensi->plg_lat = $latUser;
                                $absensi->plg_long = $longUser;
                                $point = "Point Tidak Dapat Klaim !";
                            }else{
                                $absensi->point_id = 1;
                                $absensi->plg_lat = $latUser;
                                $absensi->plg_long = $longUser;
                                $point = "Point Di Klaim !";
                            }
                        }else{
                            toastr()->error('Error GPS Mati !!, Nyalakan GPS Untuk Absen Pulang !!', 'errorr');
                            return redirect()->back();
                        }
                    }
                
                $absensi->absensi_type_pulang = Carbon::now()->format('H:i:s');
                $absensi->save();
            
                toastr()->success('Berhasil Absen Pulang Hari Ini', 'success');
                return redirect()->to(route('dashboard.index'))->with(['point' => $point]);
                
            //This diferent but same action to update data   
            }elseif($absensi && Auth::user()->kerjasama_id != 1){
                
                $latitud = $request->lat_user;
                $long = $request->long_user;
                
                if($latitud != null && $long != null)
                {
                    $panjangLat = strlen($latUser);
                    $panjangLong = strlen($longUser);
                    
                    $agent = $this->detectDevice($request->header('User-Agent'));
                    $ukuran = $panjangLat + $panjangLong;
                    
                    if($agent == 'android' || $agent == 'unknow')
                    {
                        $sebuahPengukur = 24;
                        
                    }elseif($agent == 'iphone')
                    {
                        $sebuahPengukur = 35;
                    }
                    
                }else{
                    toastr()->error('Gagal Absen Pulang !! Nyalakan GPS !!', 'error');
                    return redirect()->back();
                }
                
                $absensi->absensi_type_pulang = Carbon::now()->format('H:i:s');
                $absensi->save();
        
                toastr()->success('Berhasil Absen Pulang Hari Ini', 'succes');
                return redirect()->to(route('dashboard.index'));
                
                
            }else {
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
            toastr()->success('Berhasil Absen Siang Jam : ' .$clock, 'succes');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Error Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }
        
    }
    
    // Subuh
     public function updateSubuh($id)
    {
        try {
            $absensi = Absensi::find($id);
            $clock = Carbon::now()->format('H:i:s');
            $absensi->subuh = 1;
            $absensi->save();
            toastr()->success('Berhasil Absen Shalat Jam : ' .$clock, 'succes');
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
        
        if($latitud != null && $long != null)
        {
            $panjangLat = strlen($latitud);
            $panjangLong = strlen($long);
            
            $agent = $this->detectDevice($request->header('User-Agent'));
            $ukuran = $panjangLat + $panjangLong;
            
            if($agent == 'android' || $agent == 'unknow')
            {
                $sebuahPengukur = 24;
                
            }elseif($agent == 'iphone')
            {
                $sebuahPengukur = 35;
            }
                if($ukuran <= $sebuahPengukur){
                    try {
                        $absensi = Absensi::find($id);
                        $clock = Carbon::now()->format('H:i:s');
                        $absensi->dzuhur = 1;
                        $absensi->sig_lat = $request->lat_user;
                        $absensi->sig_long = $request->long_user;
                        
                        $absensi->save();
                        toastr()->success('Berhasil Absen Shalat Jam : ' .$clock, 'succes');
                        return redirect()->back();
                    } catch (\Throwable $th) {
                        toastr()->error('Error Data Tidak Ditemukan', 'error');
                        return redirect()->back();
                    }
                }else{
                    toastr()->error('Gagal Absen Siang !! Matikan Extension Fake GPS !!', 'error');
                    return redirect()->back();
                }
        }else{
            toastr()->error('Gagal Absen Siang !! Nyalakan GPS !!', 'error');
            return redirect()->back();
        }
            
        
            
        
    }
    
    // asar
     public function updateAsar($id)
    {
        try {
            $absensi = Absensi::find($id);
            $clock = Carbon::now()->format('H:i:s');
            $absensi->asar = 1;
            $absensi->save();
            toastr()->success('Berhasil Absen Shalat Jam : ' .$clock, 'succes');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Error Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }
        
    }
    
    // maghrib
     public function updateMaghrib($id)
    {
        try {
            $absensi = Absensi::find($id);
            $clock = Carbon::now()->format('H:i:s');
            $absensi->maghrib = 1;
            $absensi->save();
            toastr()->success('Berhasil Absen Shalat Jam : ' .$clock, 'succes');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Error Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }
        
    }
    
    // isya
     public function updateIsya($id)
    {
        try {
            $absensi = Absensi::find($id);
            $clock = Carbon::now()->format('H:i:s');
            $absensi->isya = 1;
            $absensi->save();
            toastr()->success('Berhasil Absen Shalat Jam : ' .$clock, 'succes');
            return redirect()->back();
        } catch (\Throwable $th) {
            toastr()->error('Error Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }
        
    }

    public function historyAbsensi(Request $request)
    {
        
        $filter = $request->search;
        $filter2 = Carbon::parse($filter);
        
        if ($filter) {
            $user = Auth::user()->id;
            $abs = Absensi::all();
            $pointId = Point::all();
            $point = Absensi::whereNotNull('point_id')->where('user_id', $user)->whereMonth('created_at', $filter2->month)->get();
            $absen = Absensi::where('user_id', $user)->whereMonth('created_at', $filter2->month)->paginate(50);
            $absenTiga = Absensi::where('user_id', $user)->whereMonth('created_at', $filter2->month)->whereNotNull('absensi_type_masuk')->whereNotNull('dzuhur')->whereNotNull('absensi_type_pulang')->paginate(50);
            $telat = Absensi::where('user_id', $user)->whereMonth('created_at', $filter2->month)->where('keterangan', 'telat')->paginate(50);
            
        } else {
            $mon = Carbon::now()->month;
            $user = Auth::user()->id;
            $abs = Absensi::all();
            $pointId = Point::all();
            $point = Absensi::whereNotNull('point_id')->where('user_id', $user)->whereMonth('created_at', $mon)->get();
            $absen = Absensi::where('user_id', $user)->whereMonth('created_at', $mon)->paginate(50);
            $absenTiga = Absensi::where('user_id', $user)->whereMonth('created_at', $mon)->whereNotNull('absensi_type_masuk')->whereNotNull('dzuhur')->whereNotNull('absensi_type_pulang')->paginate(50);
            $telat = Absensi::where('user_id', $user)->whereMonth('created_at', $mon)->where('keterangan', 'telat')->paginate(50);
        }
        
        $countTe = count($telat);        
        $contAb = count($absenTiga);
        $mon = Carbon::now()->month;

        $client = new HTTP();
        // Specify the API endpoint you want to fetch data from
        $apiEndpoint = 'https://api-harilibur.vercel.app/api?month='. $mon;
        // Make an HTTP GET request to the API
        $response = $client->get($apiEndpoint);
        // Decode the JSON response
        $data = json_decode($response->getBody(), true);
        $nationalHolidays = array_filter($data, function ($holiday) {
            return $holiday["is_national_holiday"] === true;
        });
        $date = Carbon::now();
        
        if($filter){
            $totalDays = $filter2->daysInMonth;
        }else{
            $totalDays = $date->daysInMonth;
        }
        $saturday = 0;
        $sundays = 0;

        for ($i = 1; $i <= $totalDays; $i++) {
            $currentDate = $date->copy()->day($i);

            if ($currentDate->isWeekend()) {
                if ($currentDate->isSaturday()) {
                    $saturday++;
                }elseif ($currentDate->isSunday()) {
                    $sundays++;
                }
            }
        }
        
        
        $total = $sundays + $sundays;
        $liburNat = count($nationalHolidays);
        $hariMasuk = ($totalDays - $total) - $liburNat;
        

        $cekPer = $hariMasuk != 0 ? (100 / $hariMasuk) : $hariMasuk;
        $cekM = $contAb * $cekPer;
        // $tes = $sundays + $sundays;
        // dd($cekPer, $tes, $totalDays);
        // dd($totalDays, $sundays, $liburNat, $contAb, $cekPer, "Total", ($totalDays - $total) - $liburNat);
        
        if ($cekM >= 80) {
            $status = "BAIK";
        }elseif($cekM >= 60){
            $status = "CUKUP";
        }else{
            $status = "KURANG";
        }
        
        return view('absensi.history', [
            'telat' => $countTe,
            'persentase' => $cekM,
            'status' => $status,
            'absen' => $absen,
            'abs' => $abs,
            'point' => $point,
            'pointId' => $pointId
        ]);
    }
    
    public function historyAbsenFilter(Request $request)
    {
        $user = Auth::user()->id;
        $abs = Absensi::all();
        $pointId = Point::all();
        $point = Absensi::whereNotNull('point_id')->where('user_id', $user)->whereMonth('created_at', $parse->month)->get();
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

    function distance($lat1, $lon1, $lat2, $lon2)
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
    public function showLocation($id){
        $absen = Absensi::findOrFail($id);
        return view('leader_view.absen.maps', compact('absen'));
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
