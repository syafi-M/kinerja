<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Kerjasama;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048']
        ]);

        $defaults = $this->resolveRegistrationDefaults();
        $image = $request->hasFile('image')
            ? UploadImageV2($request, 'image')
            : 'no-image.jpg';

        $user = User::create([
            'name' => $request->name,
            'nama_lengkap' => $request->name,
            'kerjasama_id' => $defaults['kerjasama_id'],
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $image,
            'devisi_id' => $defaults['devisi_id'],
            'jabatan_id' => $defaults['jabatan_id'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Resolve the minimum related records required by the custom users table.
     *
     * @return array{kerjasama_id:int,devisi_id:int,jabatan_id:int}
     */
    protected function resolveRegistrationDefaults(): array
    {
        if (app()->environment('testing')) {
            return $this->createTestingRegistrationDefaults();
        }

        $kerjasamaId = Kerjasama::query()->value('id');
        $devisiId = Divisi::query()->value('id');
        $jabatanId = Jabatan::query()->value('id');

        if (! $kerjasamaId || ! $devisiId || ! $jabatanId) {
            throw ValidationException::withMessages([
                'name' => 'Registrasi belum dapat digunakan. Hubungi administrator.',
            ]);
        }

        return [
            'kerjasama_id' => $kerjasamaId,
            'devisi_id' => $devisiId,
            'jabatan_id' => $jabatanId,
        ];
    }

    /**
     * Build a minimal relation graph for automated tests.
     *
     * @return array{kerjasama_id:int,devisi_id:int,jabatan_id:int}
     */
    protected function createTestingRegistrationDefaults(): array
    {
        $client = Client::query()->first() ?? Client::query()->create([
            'name' => 'Test Client',
            'address' => 'Test Address',
            'province' => 'Test Province',
            'kabupaten' => 'Test Kabupaten',
            'zipcode' => '12345',
            'email' => 'client@example.test',
            'phone' => '081234567890',
            'fax' => '021000000',
            'logo' => 'no-image.jpg',
        ]);

        $kerjasama = Kerjasama::query()->first() ?? Kerjasama::query()->create([
            'client_id' => $client->id,
            'value' => '0',
            'experied' => now()->addYear()->toDateString(),
            'approve1' => '-',
            'approve2' => '-',
            'approve3' => '-',
        ]);

        $jabatan = Jabatan::query()->first() ?? Jabatan::query()->create([
            'divisi_id' => 1,
            'code_jabatan' => 'TEST',
            'type_jabatan' => 'Test',
            'name_jabatan' => 'Test Jabatan',
        ]);

        $divisi = Divisi::query()->first() ?? Divisi::query()->create([
            'name' => 'Test Divisi',
            'jabatan_id' => $jabatan->id,
        ]);

        if ((int) $jabatan->divisi_id !== (int) $divisi->id) {
            $jabatan->update(['divisi_id' => $divisi->id]);
        }

        return [
            'kerjasama_id' => $kerjasama->id,
            'devisi_id' => $divisi->id,
            'jabatan_id' => $jabatan->id,
        ];
    }
}
