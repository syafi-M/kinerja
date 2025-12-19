<x-app-layout>
    <x-main-div>
        <div>
            <p class="pt-5 text-2xl font-bold text-center uppercase">Detail Izin</p>
            <div class="mx-5 my-5 rounded-md shadow bg-slate-100">
                <div>
                    <div class="flex items-center justify-center pt-10">
                        <div class=" mx-2 my-2 overflow-hidden flex items-center sm:w-[40%] justify-center bg-slate-200  shadow-md  hover:shadow-none transition-all .2s  ease-in-out">
                            @if ($izinId->img == 'no-image.jpg')
                                <img class="w-20 rounded-full " src="{{ URL::asset('/logo/person.png') }}" alt="profile-logo.png"
                                srcset="{{ URL::asset('/logo/person.png') }}">
                            @else
                                <img class="m-2 " src="{{ asset('storage/images/'.  $izinId->img) }}" alt="profile-logo2.png" srcset="{{ asset('storage/images/'.  $izinId->img) }}">
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-center pb-5">
                        @if ($izinId->approve_status == 'process')
                            <div class="w-full px-4 py-2 mx-10 overflow-hidden text-xs font-semibold text-center capitalize shadow-md rounded-tr-md rounded-bl-md bg-amber-500 sm:text-base">
                                <span >{{ $izinId->approve_status }}</span>
                            </div>
                        @elseif($izinId->approve_status == 'accept')
                            <div class="w-full px-4 py-2 mx-10 overflow-hidden text-xs text-center text-white capitalize shadow-md rounded-tr-md rounded-bl-md bg-emerald-700 sm:text-base">
                                <span >{{ $izinId->approve_status }}</span>
                            </div>
                        @else
                            <div class="w-full px-4 py-2 mx-10 overflow-hidden text-xs font-semibold text-center text-white capitalize bg-red-500 shadow-md rounded-tr-md rounded-bl-md sm:text-base">
                                <span >{{ $izinId->approve_status }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-2 py-5 mx-4 my-4 text-sm font-semibold rounded-md bg-slate-300">
                        <div class="space-y-2 text-xs text-slate-800 sm:text-sm">
                            <div class="flex flex-col w-full sm:flex-row ">
                                <span>Nama Lengkap: </span>
                                <span class="indent-2">{{ $izinId->user->nama_lengkap }}</span>
                            </div>
                            <div class="flex flex-col w-full sm:flex-row ">
                                <span>Bermitra Dengan: </span>
                                <span class="indent-2">{{ $izinId->kerjasama->client->name }}</span>
                            </div>
                            <div class="flex flex-col w-full sm:flex-row ">
                                <span>Shift :</span>
                                <span class="indent-2">{{ $izinId->shift->shift_name }} | {{ $izinId->shift->jam_start }} - {{ $izinId->shift->jam_end }}</span>
                            </div>
                            <div class="flex flex-col w-full sm:flex-row ">
                                <span>Tanggal Dibuat: </span>
                                <span class="indent-2">{{ $izinId->created_at->format('Y-m-d : H:i:s') }}</span>
                            </div>
                            <div class="flex flex-col w-full break-words whitespace-normal">
                                <span>Alasan izin: </span>
                                <span class="textarea textarea-bordered">{{ $izinId->alasan_izin }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-center mb-4 sm:justify-end">
            @if(Auth::user()->role_id == 2)
                <a href="{{ route('data-izin.admin') }}" class="mx-2 btn btn-error sm:mx-10">Back</a>
            @elseif (Auth::user()->jabatan_id == 15)
                <a href="{{ route('mitra_izin') }}" class="mx-2 btn btn-error sm:mx-10">Back</a>
            @else
                <a href="{{ route('lead_izin') }}" class="mx-2 btn btn-error sm:mx-10">Back</a>
            @endif
        </div>
    </x-main-div>
</x-app-layout>
