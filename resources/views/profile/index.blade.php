<x-app-layout>
    <x-main-div>
        <style>
            .profile-image-wrapper {
                border-radius: 50%;
                overflow: hidden;
                width: 100px;
                /* w-24 = 6rem = 96px */
                height: 100px;
                /* h-24 = 6rem = 96px */
                padding: 6px;
                display: flex;
                justify-content: center;
                align-items: flex-center;
                /* top-aligned */
            }

            .profile-image {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                object-fit: cover;
                object-position: center;
                display: block;
            }
        </style>
        <div>
            <p class="text-center text-2xl font-bold pt-5 uppercase grid justify-center items-center">Profile</p>
            <div class="bg-slate-100 mx-5 my-5 rounded-md shadow">
                <div>
                    <span class="flex justify-end mt-5 mx-5">
                        <a href="{{ route('profile.edit', Auth::user()->id) }}"
                            class="bg-amber-400 py-1.5 px-2 text-xs rounded-full">
                            <span class="flex">
                                <p class="font-semibold">Edit</p>
                                <i class="ri-edit-2-line"></i>
                            </span>
                        </a>
                    </span>
                    <div class="flex items-center py-5 justify-center">
                        <div
                            class="mx-2 my-2 overflow-hidden flex items-center justify-center bg-slate-200 rounded-full shadow-md hover:shadow-none transition-all duration-200 ease-in-out w-24 h-24 profile-image-wrapper">
                            @if (Auth::user()->image == 'no-image.jpg')
                                <img class="profile-image" src="{{ URL::asset('/logo/person.png') }}"
                                    alt="profile-logo.png">
                            @elseif(Storage::disk('public')->exists('images/' . Auth::user()->image))
                                <img class="profile-image" src="{{ asset('storage/images/' . Auth::user()->image) }}"
                                    alt="profile-logo.png">
                            @elseif(Storage::disk('public')->exists('user/' . Auth::user()->image))
                                <img class="profile-image" src="{{ asset('storage/user/' . Auth::user()->image) }}"
                                    alt="profile-logo.png">
                            @else
                                <img class="profile-image" src="{{ URL::asset('/logo/person.png') }}"
                                    alt="profile-logo.png">
                            @endif
                        </div>
                    </div>


                    <div class="bg-slate-300 mx-2 my-4 rounded-md p-2 py-5 font-semibold text-sm">
                        <div class="text-slate-800 space-y-2">
                            <table>
                                <thead class="uppercase" style="font-size: 10px;">
                                    <tr>
                                        <td>UserName</td>
                                        <td style="padding-left: 8px;">: {{ Auth::user()->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top;">
                                            Fullname
                                        </td>
                                        <td style="padding-left: 8px;" class="break-words whitespace-pre-wrap">:
                                            {{ ucwords(strtolower(Auth::user()->nama_lengkap)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td style="padding-left: 8px;" class="break-words whitespace-pre-line">:
                                            {{ Auth::user()->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>NIK</td>
                                        <td style="padding-left: 8px;" class="break-words whitespace-pre-line">:
                                            {{ Auth::user()->nik ? \Illuminate\Support\Facades\Crypt::decryptString(Auth::user()->nik) : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>No. Hp</td>
                                        <td style="padding-left: 8px;" class="break-words whitespace-pre-line">:
                                            {{ Auth::user()->no_hp ? Auth::user()->no_hp : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Jabatan</td>
                                        @if (!Auth::user()->divisi->jabatan)
                                            <td style="padding-left: 8px;">: Kosong</td>
                                        @else
                                            <td style="padding-left: 8px;">:
                                                {{ Auth::user()->divisi->jabatan->name_jabatan }}</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top;">Bermitra</td>
                                        @if (!Auth::user()->kerjasama)
                                            <td style="padding-left: 8px;">: Kosong</td>
                                        @else
                                            <td style="padding-left: 8px;">:
                                                {{ Auth::user()->kerjasama->client->name }}</td>
                                        @endif
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($kontrak)
                    @if ($kontrak->send_to_atasan == 0 && $kontrak->ttd == null)
                        <div class="min-w-full flex justify-center items-center">
                            <a href="{{ route('form-kontrak-index', ['id' => $kontrak?->id]) }}"
                                class="mx-5 mb-2 bg-yellow-500 rounded-md p-2 px-5 font-semibold">Form Kontrak</a>
                        </div>
                    @elseif($kontrak->send_to_atasan == 1)
                        <div class="min-w-full flex justify-center items-center">
                            <a href="{{ route('form-kontrak-preview', ['id' => $kontrak?->id]) }}"
                                onclick="window.open(this.href, '_blank'); window.location.reload(); return false;"
                                class="mx-5 mb-2 bg-yellow-500 rounded-md p-2 px-5 font-semibold">Lihat Kontrak Saya
                                (Proses)</a>
                        </div>
                    @elseif($kontrak->send_to_atasan == 0 && $kontrak->ttd && $kontrak->ttd_atasan)
                        <div class="min-w-full flex justify-center items-center">
                            <a href="{{ route('form-kontrak-preview', ['id' => $kontrak?->id]) }}"
                                onclick="window.open(this.href, '_blank'); window.location.reload(); return false;"
                                class="mx-5 mb-2 bg-yellow-500 rounded-md p-2 px-5 font-semibold">Lihat Kontrak Saya
                                (Berlaku)</a>
                        </div>
                    @endif
                @endif
            </div>
            <div class="flex justify-center sm:justify-end mt-2 mb-5">
                <a href="{{ route('dashboard.index') }}" class="btn btn-error mx-2 sm:mx-10">Kembali</a>
            </div>
        </div>
    </x-main-div>
</x-app-layout>
