<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class ProfileController extends Controller
{
    
    public function index()
    {
        $dataUser = User::findOrFail(Auth::user()->id);
        return view('profile.index', compact('dataUser'));
    }
    
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, $id)
    {
        $datas = Status::all();
        $user = User::find($id);
        $dataUser = User::findOrFail($id);
        if ($dataUser != null) {
            return view('profile.edit', compact('dataUser', 'datas'));
        }
        toastr()->error('Data tidak tidak ditemukan', 'error');
        return redirect()->back();
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request, $id)
    {
        $user = [
            'nama_lengkap' => $request->nama_lengkap,
            'email'     => $request->email,
            'image'     => $request->image,
            'nik'       => Crypt::encryptString($request->nik),
            'no_hp'     => $request->no_hp
        ];
        

        if($request->hasFile('image'))
        {
            if($request->oldimage)
            {
                Storage::disk('public')->delete('images/' . $request->oldimage);
            }

            $user['image'] = UploadImage($request, 'image');
        }else{
            $user['image'] = $request->oldimage;
        }
        User::findOrFail($id)->update($user);
    
        toastr()->success('Data Berhasil diupdate', 'success');
        return to_route('profile.index');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'], 
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
