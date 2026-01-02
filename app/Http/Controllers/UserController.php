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
use Illuminate\Support\Facades\Log;
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

        return view('admin.user.index', ['user' => $user->paginate(100), 'kerjasama' => $kerjasama, 'client' => $client, 'dev' => $dev, 'filterKerjasama' => $request->filterKerjasama]);
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
            'image' => $request->image,
            'nama_lengkap' => $request->nama_lengkap,
            'password' => Hash::make($request->password),
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

        if ($request->password == null) {
            unset($user['password']);
        }

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

    public function show($id) {}

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
        $kerjasama = Kerjasama::whereNotIn('id', [1])->get();
        $devisi = Divisi::whereNotIn('id', [2, 3, 4, 7, 8, 9, 10, 11, 12, 14, 18, 20, 24, 26])->get();
        // sendOtpReg('syafimq00@gmail.com');
        return view('admin.user.addKaryawan.index', compact('kerjasama', 'devisi'));
    }

    public function addKaryawanStore(Request $request)
    {
        $lock = Cache::lock('generate-username-sac', 10);

        try {
            $lock->block(5);

            $startingNumber = 100;

            // 1. Get all existing numeric parts of SAC usernames from the 'users' table.
            // Note: I'm assuming the column is 'name' based on your code's context.
            $userNumbers = User::where('name', 'LIKE', 'SAC%')
                ->pluck('name')
                ->map(function ($username) {
                    return (int) substr($username, 3);
                });

            // 2. Get all existing numeric parts of SAC usernames from the 'temp_users' table.
            // We use Laravel's JSON query syntax to access the 'username' inside the 'data' column.
            $tempUserNumbers = TempUser::where('data->username', 'LIKE', 'SAC%')
                ->get() // Get a collection of TempUser model instances
                ->map(function ($tempUser) {
                    $dataString = $tempUser->data;

                    $dataObject = json_decode($dataString);

                    if (!$dataObject || !isset($dataObject->username)) {
                        return null; // or handle the error as needed
                    }

                    $username = $dataObject->username;

                    return (int) substr($username, 3);
                })
                ->filter()
                ->values();

            $allTakenNumbers = $userNumbers->merge($tempUserNumbers)
                ->unique()
                ->sort()
                ->values()
                ->all();

            // 2. Create a quick lookup map for efficient checking.
            // This turns [100, 101, 103] into [100 => 0, 101 => 1, 103 => 2]
            $numberLookup = array_flip($allTakenNumbers);
            // 3. Find the first available number starting from our $startingNumber.
            $nextNumber = $startingNumber; // Default to the starting number if no users exist.
            for ($i = $startingNumber; $i < $startingNumber + 10000; $i++) {
                // If the number 'i' is NOT in our lookup map, it's available.
                if (!isset($numberLookup[$i])) {
                    $nextNumber = $i;
                    break; // Found the first available number, exit the loop.
                }
            }

            // 4. Generate the new, unique username.
            $newUsername = 'SAC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Username generation timed out due to high traffic: ' . $th->getMessage());
            toastr()->error('System is busy, please try again in a moment.', 'Error');
            return redirect()->back();
        } finally {
            optional($lock)->release();
        }

        try {
            $imagePath = $request->hasFile('image') ? UploadImageUser($request, 'image') : null;
            $ktpPath = $request->hasFile('ktp') ? UploadImageUser($request, 'ktp') : null;

            $userData = [
                'username'      => $newUsername,
                'nama_lengkap'  => $request->nama_lengkap,
                'password'      => Hash::make($request->password),
                'pw'            => $request->password,
                'email'         => $request->email,
                'alamat'        => $request->alamat,
                'image'         => $imagePath,
                'img_ktp_dpn'   => $ktpPath,
                'ttl'           => $request->tpt . ', ' . $request->tgl,
                'nik'           => Crypt::encryptString($request->nik),
                'no_kk'         => Crypt::encryptString($request->kk),
                'no_hp'         => normalizePhone($request->no_hp),
                'client_id'     => $request->client_id,
                'devisi_id'     => $request->devisi_id,
                'jabatan_id'    => Divisi::find($request->devisi_id)->jabatan_id,
            ];

            TempUser::create([
                'data'   => json_encode($userData),
                'status' => 0,
            ]);

            Notification::route('mail', $request->email)
                ->notify(new RegSukses($newUsername, $request->password));

            Cache::forget("otp_{$request->email}");
            return view('admin.user.addKaryawan.wait');
        } catch (\Throwable $th) {
            Log::error('Failed to create temp user: ' . $th->getMessage());
            toastr()->error('An error occurred while saving your data. Please try again.', 'Error');
            return redirect()->back();
        }

        // $image = $request->file('image');
        // $image2 = $request->file('ktp');

        // // dd($request->all(), Kerjasama::where('client_id', $request->client_id)->value('id'), Divisi::find($request->devisi_id)->value('jabatan_id'));

        // $lastNumber = User::where('name', 'LIKE', 'SAC%')
        //     ->select(DB::raw('MAX(CAST(SUBSTRING(name, 4) AS UNSIGNED)) AS max_number'))
        //     ->value('max_number');

        // $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        // $newUsername = 'SAC' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // // data center
        // $user = [
        //     'username' => $newUsername,
        //     'nama_lengkap' => $request->nama_lengkap,
        //     'password' => Hash::make($request->password),
        //     'pw' => $request->password,
        //     'email' => $request->email,
        //     'alamat' => $request->alamat,
        //     'image' => $image,
        //     'img_ktp_dpn' => $image2,
        //     'ttl' => $request->tpt . ', ' . $request->tgl,
        //     'nik' => Crypt::encryptString($request->nik),
        //     'no_kk' => Crypt::encryptString($request->kk),
        //     'no_hp' => normalizePhone($request->no_hp),
        //     'client_id' => $request->client_id,
        //     'devisi_id' => $request->devisi_id,
        //     'jabatan_id' => Divisi::find($request->devisi_id)->jabatan_id,
        // ];

        // if ($request->hasFile('image')) {
        //     $user['image'] = UploadImageUser($request, 'image');
        // }

        // if ($request->hasFile('ktp')) {
        //     $user['img_ktp_dpn'] = UploadImageUser($request, 'ktp');
        // }


        // $tUser = new TempUser();
        // $tUser = [
        //     'data' => json_encode($user),
        //     'status' => 0,
        // ];

        // try {
        //     TempUser::create($tUser);

        //     Notification::route('mail', $request->email)
        //         ->notify(new RegSukses($newUsername, $request->password));
        //     Cache::forget("otp_{$request->email}");

        //     return view('admin.user.addKaryawan.wait');
        // } catch (\Illuminate\Database\QueryException $e) {
        //     Log::error($e);
        //     toastr()->error('Ada Kesalahan Input Data', 'error');
        //     return redirect()->back();
        // }
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
