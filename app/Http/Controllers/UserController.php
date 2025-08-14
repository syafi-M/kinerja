<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Client;
use App\Models\Absensi;
use App\Models\Divisi;
use App\Models\Kerjasama;
use App\Models\User;
use App\Models\TempUser;
use App\Models\Jabatan;
use App\Notifications\OtpNotif;
use App\Notifications\RegSukses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use SadiqSalau\LaravelOtp\Facades\Otp;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $kerjasama = Kerjasama::all();
        $client = Client::all();
        $dev = Divisi::all();
        $user = User::with('Kerjasama')->orderBy('name', 'desc');
        $user->when($request->filterKerjasama, function ($query) use ($request) {
            return $query->where('kerjasama_id', '=', $request->filterKerjasama . '%');
        });

        // dd(User::whereRaw("name REGEXP '^[a-zA-Z]{3}[0-9]{3}$'")->latest()->first());

        return view('admin.user.index', ['user' => $user->paginate(5000), 'kerjasama' => $kerjasama, 'client' => $client, 'dev' => $dev, 'filterKerjasama' => $request->filterKerjasama]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dev = Divisi::all();
        $data = Kerjasama::all();
        $jabatan = Jabatan::all();

        $excludedUserIDs = [9, 7, 55, 261, 3, 109, 292, 11, 58, 146, 8, 1, 6, 60];
        $lastUser = User::whereNotIn('id', $excludedUserIDs)->latest()->where('name', 'REGEXP', '[0-9]')->first();

        return view('admin.user.create', compact('data', 'dev', 'lastUser', 'jabatan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $user = new User();
        $user = [
            'kerjasama_id' => $request->kerjasama_id,
            'devisi_id' => $request->devisi_id,
            'jabatan_id' => $request->jabatan_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $request->image,
            'nama_lengkap' => $request->nama_lengkap,
            'nik' => Crypt::encryptString($request->nik),
            'no_hp' => normalizePhone($request->no_hp)
        ];

        if ($request->hasFile('image')) {
            $user['image'] = UploadImageV2($request, 'image');
        }
        try {
            User::create($user);
        } catch (\Illuminate\Database\QueryException $e) {
            // dd($e);
            toastr()->error('Data Sudah Ada', 'error');
            return redirect()->back();
        }
        toastr()->success('User Berhasil Ditambahkan', 'succes');
        return redirect()->back();

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dev = Divisi::all();
        $kerjasama = Kerjasama::all();
        $user = User::find($id);
        $dataUser = User::findOrFail($id);
        $jabatan = Jabatan::all();

        if ($user != null) {
            return view('admin.user.edit', compact('user', 'kerjasama', 'dev', 'dataUser', 'jabatan'));
        }
        toastr()->error('Data tidak tidak ditemukan', 'error');
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = [
            'kerjasama_id' => $request->kerjasama_id,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'devisi_id' => $request->devisi_id,
            'jabatan_id' => $request->jabatan_id,
            'email' => $request->email,
            'image' => $request->image,
            'nama_lengkap' => $request->nama_lengkap,
            'nik' => Crypt::encryptString($request->nik),
            'no_hp' => normalizePhone($request->no_hp)
        ];

        if ($request->hasFile('image')) {
            if ($request->oldimage) {
                Storage::disk('public')->delete('images/' . $request->oldimage);
            }

            $user['image'] = UploadImageV2($request, 'image');
        } else {
            $user['image'] = $request->oldimage;
        }
        try {
            User::findOrFail($id)->update($user);
        } catch (\Illuminate\Database\QueryException $e) {
            // dd($e);
            toastr()->error($e, 'error');
            return redirect()->back();
        }


        toastr()->success('Data Berhasil diupdate', 'success');
        return to_route('users.index');
        // return redirect()->back();
    }

    public function massUpdate(Request $request)
    {
        $kerjasama = $request->input('kerjasama');
        $devisi = $request->input('devisi');
        $field = $request->input('field');
        $oldValue = $request->input('old_value');
        $newValue = $request->input('new_value');

        // dd($request->all());

        // Perform the mass update
        if ($kerjasama) {
            User::where($field, $oldValue)->where('devisi_id', $devisi)->where('kerjasama_id', $kerjasama)->update([$field => $newValue]);
        } else {
            User::where($field, $oldValue)->where('devisi_id', $devisi)->update([$field => $newValue]);
        }

        toastr()->success('Data ' . $field . ' Berhasil diupdate', 'success');
        return redirect()->back();
    }

    public function show($id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $absen = Absensi::firstWhere('user_id', $id);
        // dd($user, $id);

        if ($user != null) {
            if ($user->image == null) {
                toastr()->error('Image Tidak Ditemukan', 'error');
            }
            if ($user->image) {
                Storage::disk('public')->delete('images/' . $user->image);
            }
            if ($absen != null) {
                if ($absen->image) {
                    Storage::disk('public')->delete('images/' . $absen->image);
                }
                $absen->delete();
            }

            $user->delete();
            toastr()->warning('Data User Telah Dihapus', 'warning');
            return to_route('users.index');
        } else {
            toastr()->error('Data Tidak Ditemukan', 'error');
            return redirect()->back();
        }

    }

    public function addKaryawanIndex()
    {
        $kerjasama = Kerjasama::all();
        $devisi = Divisi::whereNotIn('id', [2, 3, 4, 7, 8, 9, 10, 11, 12, 14, 18, 20, 24, 26])->get();
        // sendOtpReg('syafimq00@gmail.com');
        return view('admin.user.addKaryawan.index', compact('kerjasama', 'devisi'));
    }

    public function addKaryawanStore(Request $request)
    {
        $image = $request->file('image');
        $image2 = $request->file('ktp');

        // dd($request->all(), Kerjasama::where('client_id', $request->client_id)->value('id'), Divisi::find($request->devisi_id)->value('jabatan_id'));

        // Ambil angka terbesar dari username dengan format 'SACXXX'
        $lastNumber = User::where('name', 'LIKE', 'SAC%')
            ->select(DB::raw('MAX(CAST(SUBSTRING(name, 4) AS UNSIGNED)) AS max_number'))
            ->value('max_number');

        // Jika belum ada, mulai dari 1
        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        // Format jadi 3 digit, misalnya SAC001, SAC099
        $newUsername = 'SAC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // absen
        $userAbsen = new User();
        $userAbsen = [
            'kerjasama_id' => Kerjasama::where('client_id', $request->client_id)->value('id'),
            'devisi_id' => $request->devisi_id,
            'jabatan_id' => Divisi::find($request->devisi_id)->value('jabatan_id'),
            'name' => $newUsername,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $image,
            'nama_lengkap' => $request->nama_lengkap,
            'nik' => Crypt::encryptString($request->nik),
            'no_hp' => normalizePhone($request->no_hp),
            'status_id' => '6'
        ];

        if ($request->hasFile('image')) {
            $userAbsen['image'] = UploadImageUser($request, 'image');
        }

        // data center
        $user = [
            'username' => $newUsername,
            'nama_lengkap' => $request->nama_lengkap,
            'password' => Hash::make($request->password),
            'pw' => $request->password,
            'email' => $request->email,
            'image' => $image,
            'img_ktp_dpn' => $image2,
            'ttl' => $request->tpt . ', ' . $request->tgl,
            'nik' => Crypt::encryptString($request->nik),
            'no_kk' => Crypt::encryptString($request->kk),
            'no_hp' => normalizePhone($request->no_hp),
            'client_id' => $request->client_id,
            'devisi_id' => $request->devisi_id,
        ];

        if ($request->hasFile('image')) {
            $user['image'] = UploadImageUser($request, 'image');
        }

        if ($request->hasFile('ktp')) {
            $user['img_ktp_dpn'] = UploadImageUser($request, 'ktp');
        }


        $tUser = new TempUser();
        $tUser = [
            'data' => json_encode($user),
            'status' => 0,
        ];

        try {
            User::create($userAbsen);
            TempUser::create($tUser);

            Notification::route('mail', $request->email)
                ->notify(new RegSukses($newUsername, $request->password));
            Cache::forget("otp_{$request->email}");

            // dd($request->all(), $user, $tUser);
            return view('admin.user.addKaryawan.wait');
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e);
            toastr()->error('Ada Kesalahan Input Data', 'error');
            return redirect()->back();
        }

    }

    public function addKaryawanAdminIndex()
    {
        $kerjasama = Kerjasama::all();
        $devisi = Divisi::whereNotIn('id', [2, 3, 4, 7, 8, 9, 10, 11, 12, 14, 18, 20, 24, 26])->get();
        return view('admin.user.addKaryawan.adminIndex', compact('kerjasama', 'devisi'));
    }

    public function addKaryawanStatus(Request $request)
    {
        $kerjasama = Kerjasama::all();
        $devisi = Divisi::whereNotIn('id', [2, 3, 4, 7, 8, 9, 10, 11, 12, 14, 18, 20, 24, 26])->get();
        return view('admin.user.addKaryawan.adminIndex', compact('kerjasama', 'devisi'));
    }
}
