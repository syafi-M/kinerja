<x-app-layout>

    <x-absen-status :absenP="$absenP" :luweh1Dino="$luweh1Dino" :rillSholat="$rillSholat" :izin="$izin" :statusClass="$statusClass"
        :statusMessage="$statusMessage" />


    @if (Auth::user()->kerjasama_id == 1 && session()->has('point'))
        <div class="flex items-center justify-end mx-5 mb-5">
            <div class="flex flex-row px-4 py-1 text-xs text-white rounded-md shadow-sm gap-x-2 sm:w-fit"
                style="background-color: #0C642F">
                <i class="ri-checkbox-circle-line"></i>
                <span>{{ session('point') }}</span>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-center">
        @if ($rillSholat)
            <div style="margin-top: 6pt; margin-bottom: 6pt; font-size: 10pt;"
                class="inset-0 px-4 py-2 mx-10 font-semibold text-center capitalize rounded-tr-lg rounded-bl-lg shadow-md sm:w-fit bg-slate-100">
                <p>Sedang memasuki waktu {{ ucfirst($sholatSaatIni) }}</p>
                @if (Auth::user()->kerjasama_id == 1)
                    <form action="{{ route('update' . ucfirst($sholatSaatIni), $sholat->id) }}" method="POST"
                        class="flex items-center justify-center">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center justify-center">
                                <input id="lat" name="lat_user" value="" class="hidden lat" />
                                <input id="long" name="long_user" value="" class="hidden long" />
                                <button type="submit"
                                    class="flex items-center justify-center px-3 py-1 mr-0 text-white capitalize transition duration-100 ease-out bg-yellow-600 rounded-md shadow-md hover:bg-yellow-700 hover:shadow-none all sm:mr-2"
                                    style="margin-top: 4pt; font-size: 12pt;">
                                    <i class="ri-sun-foggy-line"></i><span class="font-bold">Oke Siap</span>
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <a href="{{ route(Auth::user()->divisi->jabatan->code_jabatan == 'CO-CS' ? 'leader-absenSholat' : 'danru-absenSholat') }}"
                        class="flex items-center justify-center">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-col justify-center">
                            <div class="flex items-center justify-center">
                                <input id="lat" name="lat_user" value="" class="hidden lat" />
                                <input id="long" name="long_user" value="" class="hidden long" />
                                <button type="submit"
                                    class="flex items-center justify-center px-3 py-1 mr-0 text-white uppercase transition duration-100 ease-out bg-yellow-600 rounded-md shadow-md hover:bg-yellow-700 hover:shadow-none all sm:mr-2"
                                    style="margin-top: 4pt; font-size: 12pt;">
                                    <i class="ri-sun-foggy-line"></i><span class="font-bold">Oke</span>
                                </button>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        @endif
    </div>
    @include('partials.dashboard-menu')
    @if ($warn && count($warn) >= 3)
        <div class="flex justify-center pt-10 mx-10 sm:justify-start">
            <div
                class="inset-0 flex flex-col justify-start px-4 py-2 mb-5 font-semibold text-white bg-red-500 rounded-lg shadow-md w-fit text-md sm:text-xl">
                <p class="p-1 px-2 text-xs bg-yellow-500 rounded-full w-fit">Warning</p>
                <p style="padding-left: 3px;">Kamu Sudah Tidak Absen Pulang {{ count($warn) }}x</p>
            </div>
        </div>
    @endif
    </div>

    @include('partials.script-dashboard')
</x-app-layout>
