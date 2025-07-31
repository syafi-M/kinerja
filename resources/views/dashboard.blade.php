<!DOCTYPE html>
<html lang="id" data-theme="bumblebee">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME', 'Kinerja SAC-PONOROGO') }}</title>
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <script src="{{ URL::asset('src/js/jquery-min.js') }}"></script>
    <!--<script src="{{ URL::asset('src/js/serviceWorker.min.js') }}"></script>-->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Leaflet --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />


    <style>
        #map {
            height: 180px;
        }

        @media (min-width: 640px) {
            #akuImage {
                max-width: 300px;
                aspect-ratio: 1/1;
            }
        }

        .divImage {
            scroll-snap-type: x var(--tw-scroll-snap-strictness);
        }

        .clicked {
            transform: scale(0.95);
        }
        
        @keyframes hanging-wiggle {
            0% { transform: rotate(-15deg); }
            50% { transform: rotate(-10deg); }
            100% { transform: rotate(-15deg); }
        }
        
        @keyframes hanging-wiggle2 {
            0% { transform: rotate(10deg); }
            50% { transform: rotate(15deg); }
            100% { transform: rotate(10deg); }
        }
        
        .hanging {
            position: absolute;
            top: 0;
            transform-origin: top center; /* Makes it swing from the top */
            animation: hanging-wiggle 2s ease-in-out infinite alternate;
        }
        
        .hanging2 {
            position: absolute;
            top: 0;
            transform-origin: top center; /* Makes it swing from the top */
            animation: hanging-wiggle2 2.5s ease-in-out infinite alternate;
        }
    </style>

</head>

<body class="font-sans antialiased  bg-slate-400">
    <div class="min-h-screen" style="padding-bottom: 4rem;">
        @include('../layouts/navbar')
        <div class="justify-start flex items-center">
            @if ($absenP && $luweh1Dino && $absenP?->absensi_type_pulang == null)
                <div class="text-center rounded-tr-lg rounded-bl-lg w-fit font-semibold py-2 px-4 shadow-md ml-5 inset-0"
                    style="color: #DEDEDE; background-color: #8F0000; font-size: 10pt; {{ $rillSholat ? '' : 'margin-bottom: 10px;' }}">
                    <p>Kamu Belum Absen Pulang !!</p>
                </div>
            @endif

            @if($izin)
                <span class="hidden">
                    <span id="waktuIzin" data-waktu="{{ $izin->updated_at->format('H:i') }}"></span>
                </span>
                <div id="inpoIzin"
                    class="text-center hidden rounded-tr-lg rounded-bl-lg my-5 sm:w-fit text-md sm:text-xl font-semibold text-slate-50 {{ $statusClass }} py-2 px-4 shadow-md ml-5 sm:ml-10 inset-0">
                    <p>{{ $statusMessage }}</p>
                </div>
            @endif
        </div>
        
        @if (Auth::user()->kerjasama_id == 1 && session()->has('point'))
            <div class="flex justify-end items-center mx-5 mb-5">
                <div class="flex flex-row gap-x-2 sm:w-fit px-4 py-1 text-white text-xs rounded-md shadow-sm"
                    style="background-color: #0C642F">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>{{ session('point') }}</span>
                </div>
            </div>
        @endif

        <div class="flex justify-center items-center">
            @if ($sholat)
                @if ($rillSholat)
                    <div
                        style="margin-top: 6pt; margin-bottom: 6pt; font-size: 10pt;"
                        class="text-center rounded-tr-lg rounded-bl-lg sm:w-fit font-semibold bg-slate-100 py-2 px-4 shadow-md mx-10 inset-0 capitalize">
                        <p>Sedang memasuki waktu {{ ucfirst($sholatSaatIni) }}</p>
                        @if(Auth::user()->kerjasama_id == 1)
                            <form action="{{ route('update' . ucfirst($sholatSaatIni), $sholat->id) }}" method="POST"
                                class="flex justify-center items-center">
                                @csrf
                                @method('PUT')
                                <div class="flex justify-center flex-col">
                                    <div class="flex justify-center items-center">
                                        <input id="lat" name="lat_user" value="" class="hidden lat" />
                                        <input id="long" name="long_user" value="" class="hidden long" />
                                        <button type="submit"
                                            class="bg-yellow-600 flex justify-center shadow-md hover:bg-yellow-700 text-white hover:shadow-none px-3 py-1 rounded-md transition all ease-out duration-100 mr-0 sm:mr-2 capitalize items-center" style="margin-top: 4pt; font-size: 12pt;">
                                            <i class="ri-sun-foggy-line"></i><span class="font-bold">Oke Siap</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <a href="{{route(Auth::user()->divisi->jabatan->code_jabatan == "CO-CS" ? 'leader-absenSholat' : 'danru-absenSholat' )}}"
                                class="flex justify-center items-center">
                                @csrf
                                @method('PUT')
                                <div class="flex justify-center flex-col">
                                    <div class="flex justify-center items-center">
                                        <input id="lat" name="lat_user" value="" class="hidden lat" />
                                        <input id="long" name="long_user" value="" class="hidden long" />
                                        <button type="submit"
                                            class="bg-yellow-600 flex justify-center shadow-md hover:bg-yellow-700 text-white hover:shadow-none px-3 py-1 rounded-md transition all ease-out duration-100 mr-0 sm:mr-2 uppercase items-center" style="margin-top: 4pt; font-size: 12pt;">
                                            <i class="ri-sun-foggy-line"></i><span class="font-bold">Oke</span>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        </div>
        <div class="sm:mx-10 mx-5 bg-slate-500 rounded-md shadow-md">
            <main>
                @auth
                    @php
                        $jabatan = Auth::user()->divisi->jabatan->code_jabatan;
                    @endphp
                    @if (in_array($jabatan, ['MITRA', 'LEADER', 'CO-CS']))
                        <div class="bg-amber-500 mr-10 w-fit flex justify-start px-4"
                            style="border-radius: 5px 0px 24px 0px;">
                            <span class="text-white text-center text-xs font-semibold my-1 sm:pr-5">
                                <i class="text-center">Anda
                                    Login Sebagai, {{ $jabatan }}</i>
                            </span>
                        </div>
                    @endif
                @endauth
                <div class="sm:mx-10 mx-5 bg-slate-500 rounded-md  ">
                    <div class="py-5">
                        <div class="flex items-end justify-end mr-3">
                            <span style="max-width: 250px; background-color: #0C642F"
                                class="text-xs flex gap-1 justify-center font-bold text-white sm:hidden px-4 py-1 rounded-full shadow-md">{{ Carbon\Carbon::now()->isoFormat('dddd, D/MMMM/Y') }},
                                <span id="jam"></span>
                            </span>
                        </div>
                        <div class="flex flex-col items-center gap-2 justify-center pt-2 px-2 overflow-hidden">
                            <div class="flex justify-end w-full mx-10">
                                <div
                                    class="text-center md:flex hidden justify-end items-end rounded-tr-lg rounded-bl-lg mb-5 w-fit text-md sm:text-xl font-semibold text-slate-100 bg-red-500 py-2 px-4 shadow-md ml-10 ">
                                    <span class="text-white">{{ Carbon\Carbon::now()->format('d-m-Y') }}</span>
                                </div>
                            </div>

                            {{-- Handle Check Kode Jabatan --}}
                            @if (Auth::user()->divisi->jabatan->code_jabatan != 'MITRA' &&
                                    Auth::user()->divisi->jabatan->code_jabatan != 'LEADER' &&
                                    Auth::user()->divisi->jabatan->code_jabatan != 'DIREKSI')

                                {{-- absensi --}}
                                <div id="btnAbsensi"
                                    class="w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="ri-todo-line text-xl"></i>
                                    <button class="uppercase font-bold text-sm">Kehadiran</button>
                                </div>
                                {{-- menu menu dashboard absensi --}}
                                @php
                                    $hariIni = Carbon\Carbon::now()->format('N');
                                    $tampilkanAbsensi = !in_array($hariIni, [6, 7]) || Auth::user()->kerjasama_id != 1 || Auth::user()->devisi_id != 26;
                                    $codeJabatan = Auth::user()->divisi->jabatan->code_jabatan;
                                    $absensiRoute = match($codeJabatan) {
                                        'CO-CS' => 'absensi-karyawan-co-cs.index',
                                        'CO-SCR' => 'absensi-karyawan-co-scr.index',
                                        default => '',
                                    };
                                @endphp
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="ngabsen">
                                    <a href="{{ $tampilkanAbsensi ? route('absensi.index') : 'javascript:void(0);' }}" class="btn btn-info w-full">{{ $tampilkanAbsensi ? 'Kehadiran' : 'Tidak Ada Jadwal' }}</a>
                                </div>
                                @if (!empty($absensiRoute))
                                    <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="ngabsenK">
                                        <a href="{{ route($absensiRoute) }}" class="btn btn-info w-full">Kehadiran karyawan</a>
                                    </div>
                                @endif
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="ngIzin">
                                    <a href="{{ route('izin.create') }}" class="btn btn-info w-full">Izin</a>
                                </div>
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="btnRiwayat">
                                    <a href="javascript:void(0);" class="btn btn-success w-full">Riwayat</a>
                                </div>
                                <div class="hidden w-full space-y-4 px-4 sm:px-20 overflow-hidden" id="isiAbsen">
                                    <a href="historyAbsensi" class="btn btn-info w-full">Riwayat Kehadiran</a>
                                </div>
                                <div class="hidden w-full space-y-4 px-4 sm:px-20 overflow-hidden" id="isiLembur">
                                    <a href="{{ route('lemburIndexUser') }}" class="btn btn-info w-full">Riwayat Lembur</a>
                                </div>
                                <div class="hidden w-full space-y-4 px-4 sm:px-20 overflow-hidden" id="isiIzin">
                                    <a href="{{ route('izin.index') }}" class="btn btn-info w-full">Riwayat Izin</a>
                                </div>
                        </div>
                        <div class="flex flex-col items-center gap-2 justify-center pt-2 px-2 overflow-hidden">
                            @if (Auth::user()->kerjasama_id == 1)
                                <div id="btnCP"
                                    class="w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="ri-list-check-3"></i>
                                    <button class="uppercase font-bold text-sm">
                                        Kinerja harian
                                    </button>
                                </div>
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="isiIndex">
                                    <a href="/checkpoint-user" class="btn btn-info w-full">Data
                                        Rencana Kerja</a>
                                </div>
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="tambahCP">
                                    <!--<a href="{{ route('checkpoint-user.create') }}" class="btn btn-info w-full" {{ \Carbon\Carbon::now()->isWeekend() ? '' : 'disabled' }}>Tambah Planning (sabtu - minggu )</a>-->
                                    <a href="{{ $cex ? route('checkpoint-user.edit', $cex->id) : route('checkpoint-user.create') }}"
                                        class="btn btn-info w-full">{{ $cex ? "Ubah Rencana Kerja" : "Tambah Rencana Kerja" }} (sabtu - minggu )</a>
                                </div>
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="kirimCP">
                                    <!--<a href="{{ route('checkpoint-user.create') }}" class="btn btn-info w-full" {{ \Carbon\Carbon::now()->isWeekend() ? 'disabled' : '' }}>Kirim Bukti (senin - jum'at)</a>-->
                                        <a href="{{ route('editBukti-checkpoint-user') }}"
                                            {{ !$cex ? 'disabled' : '' }}
                                            style="{{ !$cex ? 'background: #7dd3fc; border: none; color: #1e293b;' : '' }}"
                                            class="btn btn-info w-full ">{{ $cex ? "Kirim Bukti" : "Buat Rencana Kerja Terlebih Dahulu" }} (senin - jum'at)</a>
                                </div>
                            @else
                                <div class="w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s"
                                    disabled>
                                    <i class="ri-list-check-3"></i>
                                    <button class="uppercase font-bold text-sm" disabled>
                                        Kinerja harian
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-center gap-2 justify-center pt-2 px-2 overflow-hidden">
                            <div id="btnRating"
                                class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                <i class="ri-user-star-line text-xl"></i>
                                <button class="uppercase font-bold text-sm">Rating</button>
                            </div>
                            <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="cekMe">
                                <a href="{{ route('ratingSaya', Auth::user()->id) }}"
                                    class="btn btn-info w-full">Check Rating Saya</a>
                            </div>
                            @if (Auth::user()->role_id == 2)
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="cekRate">
                                    <a href="{{ route('admin-rating.index') }}"
                                        class="btn btn-info w-full">Rating</a>
                                </div>
                            @elseif(Auth::user()->divisi->jabatan->code_jabatan == 'LEADER')
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="cekRate">
                                    <a href="{{ route('leader-rating.index') }}"
                                        class="btn btn-info w-full">Rating</a>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-center gap-2 justify-center pt-2 px-2 overflow-hidden">
                            <div id="btnLaporan"
                                class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                <i class="ri-speak-line text-xl"></i>
                                <button class="uppercase font-bold text-sm">Laporan</button>
                            </div>
                            <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="tambahLaporan">
                                <a href="{{ Auth::user()->divisi->jabatan->code_jabatan != 'OCS' || Auth::user()->divisi->jabatan->code_jabatan != 'SCR' ? url('scan') : '#' }}"
                                    class="btn btn-info w-full">Tambah Laporan</a>
                            </div>
                            <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="cekLaporan">
                                <a href="{{ route('laporan.index') }}" class="btn btn-info w-full">Riwayat
                                    Laporan</a>
                            </div>

                        </div>
                        
                        @if(Auth::user()->id == 7 || Auth::user()->id == 10 || Auth::user()->id == 5)
                        <div class="flex flex-col items-center gap-2 justify-center pt-2 px-2 overflow-hidden">
                            <a href="{{ route('slip-karyawan') }}" id=""
                                class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                <i class="ri-wallet-3-line text-xl"></i>
                                <button class="uppercase font-bold text-sm">Slip Gaji Karyawan</button>
                            </a>
                        </div>
                        <div class="flex flex-col items-center gap-2 justify-center pt-2 px-2 overflow-hidden">
                            <a href="{{ route('manajemen_absensi') }}" id=""
                                class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                <i class="ri-wallet-3-line text-xl"></i>
                                <button class="uppercase font-bold text-sm">Absensi Karyawan</button>
                            </a>
                        </div>
                        <div class="flex flex-col items-center gap-2 justify-center pt-2 px-2 overflow-hidden">
                            <a href="{{ route('manajemen_laporan') }}" id=""
                                class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                <i class="ri-wallet-3-line text-xl"></i>
                                <button class="uppercase font-bold text-sm">Laporan Karyawan</button>
                            </a>
                        </div>
                        <div class="flex flex-col items-center gap-2 justify-center pt-2 px-2 overflow-hidden">
                            <a href="{{ route('manajemen_user') }}" id=""
                                class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                <i class="ri-wallet-3-line text-xl"></i>
                                <button class="uppercase font-bold text-sm">Data Karyawan</button>
                            </a>
                        </div>
                        @endif

                        @if (Auth::user()->divisi->jabatan->code_jabatan == 'CO-CS')
                            <div class="w-full space-y-4 px-5 mt-5 sm:px-16 overflow-hidden flex items-center">
                                <a href="{{ route('leaderView') }}" class="btn btn-info w-full"><i
                                        class="ri-pass-pending-line text-xl"></i>Menu Leader</a>
                            </div>
                        @elseif(Auth::user()->divisi->jabatan->code_jabatan == 'CO-SCR')
                            <div class="w-full space-y-4 mt-5 sm:px-16 overflow-hidden flex items-center">
                                <a href="{{ route('danruView') }}" class="btn btn-info w-full"><i
                                        class="ri-pass-pending-line text-xl"></i>Menu Danru</a>
                            </div>
                        @elseif(Auth::user()?->jabatan?->code_jabatan == 'SPV-W')
                            <div class="w-full space-y-4 mt-5 sm:px-16 overflow-hidden flex items-center">
                                <a href="{{ route('SPVWiew') }}" class="btn btn-info w-full"><i
                                        class="ri-pass-pending-line text-xl"></i>Menu SPV Wilayah</a>
                            </div>
                        @endif
                    @else
                        @php
                            $jabatan = Auth::user()->divisi->jabatan->code_jabatan;
                            $role_id = Auth::user()->role_id;
                            $routes = [
                                'MITRA' => [
                                    'karyawan' => 'mitra_user',
                                    'jadwal' => $role_id == 2 ? 'admin-jadwal.index' : 'mitra_jadwal',
                                    'absensi' => 'mitra_absensi',
                                    'izin' => 'mitra_izin',
                                    'lembur' => 'mitra_lembur',
                                    'laporan' => 'mitra_laporan',
                                    'rating' => 'mitra-rating.index',
                                    'laporan bulanan' => 'mitra-laporan-bulanan.index',
                                ],
                                'LEADER' => [
                                    'karyawan' => 'lead_user',
                                    'jadwal' => $role_id == 2 ? 'admin-jadwal.index' : 'leader-jadwal.index',
                                    'absensi' => 'lead_absensi',
                                    'izin' => 'lead_izin',
                                    'lembur' => 'lead_lembur',
                                    'laporan' => 'lead_laporan',
                                    'rating' => 'leader-rating.index',
                                ],
                                'DIREKSI' => [
                                    'karyawan' => 'direksi_user',
                                    'jadwal' => $role_id == 2 ? 'admin-jadwal.index' : 'direksi_jadwal',
                                    'absensi' => 'direksi_absensi',
                                    'izin' => 'direksi_izin',
                                    'lembur' => 'direksi_lembur',
                                    'laporan' => 'direksi_laporan',
                                    'rating' => 'direksi-rating.index',
                                    'rencana kerja' => 'direksi.cp.index',
                                    'kontrak' => 'direksi-cekKontrak',
                                ],
                            ];
                            $icons = [
                                'karyawan' => 'ri-pass-pending-line',
                                'jadwal' => 'ri-calendar-check-line',
                                'absensi' => 'ri-todo-line',
                                'izin' => 'ri-shield-user-line',
                                'lembur' => 'ri-time-line',
                                'laporan' => 'ri-image-add-line',
                                'laporan bulanan' => 'ri-image-add-line',
                                'rating' => 'ri-sparkling-line',
                                'rencana kerja' => 'ri-sparkling-line',
                                'kontrak' => 'ri-pass-pending-line',
                            ];
                        @endphp
                        @if(array_key_exists($jabatan, $routes))
						<div class="w-full gap-2">
						    @if(Auth::user()->divisi->jabatan->code_jabatan == 'DIREKSI')
                                {{-- absensi --}}
                                <div id="btnAbsensi"
                                    class="w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="ri-todo-line text-xl"></i>
                                    <button class="uppercase font-bold text-sm">Kehadiran</button>
                                </div>
                                {{-- menu menu dashboard absensi --}}
                                @php
                                    $hariIni = Carbon\Carbon::now()->format('N');
                                    $tampilkanAbsensi = !in_array($hariIni, [6, 7]) || Auth::user()->kerjasama_id != 1;
                                    $codeJabatan = Auth::user()->divisi->jabatan->code_jabatan;
                                    $absensiRoute = match($codeJabatan) {
                                        'CO-CS' => 'absensi-karyawan-co-cs.index',
                                        'CO-SCR' => 'absensi-karyawan-co-scr.index',
                                        default => '',
                                    };
                                @endphp
                                <div class="flex flex-col gap-2 mt-2">
                                    <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="ngabsen">
                                        <a href="{{ $tampilkanAbsensi ? route('absensi.index') : 'javascript:void(0);' }}" class="btn btn-info w-full">{{ $tampilkanAbsensi ? 'Kehadiran' : 'Tidak Ada Jadwal' }}</a>
                                    </div>
                                    @if (!empty($absensiRoute))
                                        <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="ngabsenK">
                                            <a href="{{ route($absensiRoute) }}" class="btn btn-info w-full">Kehadiran karyawan</a>
                                        </div>
                                    @endif
                                    <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="ngIzin">
                                        <a href="{{ route('izin.create') }}" class="btn btn-info w-full">Izin</a>
                                    </div>
                                    <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="btnRiwayat">
                                        <a href="#" class="btn btn-success w-full">Riwayat</a>
                                    </div>
                                    <div class="hidden w-full space-y-4 px-4 sm:px-20 overflow-hidden" id="isiAbsen">
                                        <a href="historyAbsensi" class="btn btn-info w-full">Riwayat Kehadiran</a>
                                    </div>
                                    <div class="hidden w-full space-y-4 px-4 sm:px-20 overflow-hidden" id="isiLembur">
                                        <a href="{{ route('lemburIndexUser') }}" class="btn btn-info w-full">Riwayat Lembur</a>
                                    </div>
                                    <div class="hidden w-full space-y-4 px-4 sm:px-20 overflow-hidden" id="isiIzin">
                                        <a href="{{ route('izin.index') }}" class="btn btn-info w-full">Riwayat Izin</a>
                                    </div>
                                </div>
						    @endif
                                
                            <div class="mt-5 sm:grid sm:grid-cols-3 sm:gap-2 sm:space-y-0 space-y-2 overflow-hidden w-full">
                                @foreach($routes[$jabatan] as $key => $route)
                                    <div class="w-full space-y-4 overflow-hidden rounded-lg {{ $key == 'rating' && $jabatan == 'DIREKSI' ? 'hidden' : '' }}" id="L{{ $key }}">
                                        <a href="{{ route($route) }}" class="btn btn-info w-full flex justify-center items-center relative">
                                            <i class="{{ $icons[$key] }} text-xl"></i>{{ ucfirst($key) }}
                                            @if($jabatan == 'DIREKSI' && $key == 'rencana kerja')
                                                <span class="bg-yellow-500 text-center absolute" style="padding: 20px 25px 5px 35px; right: -20px; top: -18px; transform: rotate(35deg);">
                                                    <p style="transform: rotate(-35deg);">
                                                        {{ count($totcex) }}
                                                    </p>
                                                </span>
                                            @endif
                                        </a>
                                    </div>
                                @endforeach
                            </div>
						</div>
                        @endif
                        @endif
						
                        {{-- quran --}}
                        <div class="flex w-full justify-center items-center gap-x-2">
                            <div id="btnAbsi"
                                style="{{ Auth::user()->kerjasama_id != 1 ?: 'width: 50%;' }}"
                                class=" px-2 mt-5 flex justify-center items-center gap-2 bg-blue-400 rounded-md h-11 hover:bg-blue-500 transition-all ease-linear .2s">
                                <i class="ri-git-repository-line text-xl"></i>
                                <a href="https://baca-alquran.sac-po.com" class="uppercase font-bold text-sm">
                                    {{ Auth::user()->kerjasama_id != 1 ? 'Baca Al-Qur`an' : 'Al-Qur`an'}}
                                </a>
                            </div>
                            <div style="{{ Auth::user()->kerjasama_id != 1 ?: 'width: 50%;' }}" class="{{ Auth::user()->kerjasama_id == 1 ? 'flex' : 'hidden' }} px-5 mt-5 justify-center items-center gap-2 bg-blue-400 rounded-md h-11 hover:bg-blue-500 transition-all ease-linear .2s">
                                <i class="ri-newspaper-line text-xl"></i>
                                <a href="https://sppd-online.sac-po.com/login" class="uppercase font-bold text-sm">
                                    SPPD
                                </a>
                            </div>
                        </div>
                        
                        @if($absenP)
                            {{-- handle Pulang --}}
                            <div class="flex flex-col justify-center items-center sm:justify-end">
                                    @php
                                        $luweh1Dino = Carbon\Carbon::createFromFormat('Y-m-d, H:i:s', $absenP?->created_at->format('Y-m-d, H:i:s'))->diffInHours(Carbon\Carbon::now());
                                    @endphp
                                    @if (Auth::user()->id == $absenP?->user_id && $absenP?->absensi_type_pulang == null)
                                        @php
                                            $now = now();
                                            $shiftEnd = \Carbon\Carbon::parse($absenP->shift?->jam_end);
                                            $timeDifference = $now->diffInMinutes($shiftEnd, false);
                                        @endphp
    
                                        <span class="hidden">
                                            <span id="userId" data-user-id="{{ $absenP->user_id }}"
                                                data-auth-user="{{ Auth::user()->id }}"></span>
                                            <span id="endTime" endTimer="{{ $absenP->shift?->jam_end }}"></span>
                                            <span id="startTime" startTimer="{{ $absenP->shift?->jam_start }}"></span>
                                        </span>
                                        
                                        <div>
                                            <button id="modalPulangBtn" data-absen="{{ $absenP }}"
                                                class="bg-yellow-600 hidden justify-center shadow-md hover:bg-yellow-700 text-white hover:shadow-none px-3 py-1 text-xl rounded-md transition all ease-out duration-100 mt-5 mr-0 sm:mr-2 uppercase items-center">
                                                <i class="ri-run-line font-sans text-3xl"></i>
                                                <span class="font-bold">Pulang</span>
                                            </button>
                                        </div>
                                        <div class="fixed inset-0 modalp hidden bg-slate-500/10 backdrop-blur-sm transition-all duration-300 ease-in-out">
                                            <div class="bg-slate-200 w-fit p-5 mx-2 rounded-md shadow">
                                                <div class="flex justify-end mb-3">
                                                    <button class="btn btn-error scale-90 close">&times;</button>
                                                </div>
                                                <form action="{{ route('data.update', $absenP->id) }}" method="POST" class="flex justify-center items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="flex justify-center flex-col">
                                                        <div class="flex flex-col gap-2">
                                                            <p class="text-center text-lg font-semibold">Apakah Anda Yakin Ingin Pulang Sekarang?</p>
                                                            <span id="labelWaktu"></span>
                                                            @if (Auth::user()->name != 'DIREKSI' || Auth::user()->jabatan->code_jabatan == "SPV-W")
                                                                <span class="flex justify-center">
                                                                    <span id="jam2" class="badge badge-info underline font-semibold text-slate-800 text-sm"></span>
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex justify-center items-center">
                                                            <button type="submit" class="bg-yellow-600 flex justify-center shadow-md hover:bg-yellow-700 text-white hover:shadow-none px-3 py-1 text-xl rounded-md transition all ease-out duration-100 mt-5 mr-0 sm:mr-2 uppercase items-center">
                                                                <i class="ri-run-line font-sans text-3xl"></i>
                                                                <span class="font-bold">Pulang Sekarang</span>
                                                            </button>
                                                            <input id="lat" name="lat_user" value="" class="hidden lat" />
                                                            <input id="long" name="long_user" value="" class="hidden long" />
                                                            <div id="map" class="hidden"></div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                            </div>
                        @endif
                        {{-- handle akhiri lembur 
                        <div class="flex justify-center sm:justify-end">
                            @foreach ($lembur as $i)
                                @if (Auth::user()->id == $i->user_id && $i->jam_selesai == null)
                                    <form action="{{ url('lembur/' . $i->id) }}" method="POST" class="tooltip">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="bg-yellow-600 flex justify-center shadow-md hover:bg-yellow-700 text-white hover:shadow-none px-3 py-1 text-xl rounded-md transition all ease-out duration-100 mt-5 mr-0 sm:mr-2 uppercase items-center"><i
                                                class="ri-run-line font-sans text-3xl"></i><span
                                                class="font-bold">Selasaikan Lembur</span>
                                        </button>
                                    </form>
                                @else
                                @endif
                            @endforeach
                        </div> --}}
                    </div>
                </div>

                @if (count($hitungNews) > 0)
                    <div>
                        @if (session()->has('is_modal'))
                            <!-- Display your modal here -->
                            <div class="modalNews">
                                <div style="z-index: 9000;"
                                    class="fixed w-full flex justify-center items-center inset-0 bg-slate-500/10 backdrop-blur-sm transition-all duration-300 ease-in-out h-screen">
                                    <div class="flex justify-center items-center">
                                        <div style="z-index: 9001;"
                                            class="bg-slate-200 inset-0 w-fit p-3 mx-10 my-10 rounded-md shadow relative">
                                            @if(Carbon\Carbon::now()->lessThan(Carbon\Carbon::parse('2025-04-09')))
                                            <img src="{{ URL::asset('/logo/ketupat-3.png') }}" width="24%"
                                                class="hanging"
                                                style="z-index: 9000; left: 0px; padding: 10px; border-radius: 100%; filter: drop-shadow(0 3px 3px rgb(0 0 0 / 0.15));" />
                                            <img src="{{ URL::asset('/logo/ketupat-1.png') }}" width="24%"
                                                class="hanging2"
                                                style="z-index: 8999; left: 0px; padding: 10px; border-radius: 100%; filter: drop-shadow(0 3px 3px rgb(0 0 0 / 0.15));" />
                                            @endif
                                            <div class="flex justify-end mb-3">
                                                <button class="btn btn-error scale-90 closeNews">&times;</button>
                                            </div>
                                            <div class="carousel overflow-x-auto w-full flex divImage">
                                                @php
                                                    $no = 1;
                                                @endphp
                                                @forelse($hitungNews as $new)
                                                    <a id="slide{{ $no++ }}"
                                                        class="carousel-item relative w-fit"
                                                        href="{{ route('newsDownload', $new->id) }}">
                                                        <img class="akuImage" id="akuImage"
                                                            src="{{ asset('storage/images/' . $new->image) }}"
                                                            data-src="{{ asset('storage/images/' . $new->image) }}"
                                                            alt="data-berita-image" />

                                                    </a>
                                                @empty
                                                @endforelse
                                            </div>
                                            @if (count($hitungNews) > 1)
                                                <div class="flex justify-center items-center mt-3">
                                                    <span
                                                        class="text-center text-xs text-slate-700 font-semibold">Geser
                                                        untuk melihat berita lainnya</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php
                                session()->forget('is_modal');
                            @endphp
                        @endif
                    </div>
                @endif

                <div class="flex justify-center">
                    <div class="fixed bottom-0 z-[999]">
                        <x-menu-mobile :cekAbsen="$cekAbsen" />
                    </div>
                </div>
            </main>
        </div>
            @if (count($warn) >= 3)
        <div class="pt-10 flex justify-center sm:justify-start mx-10">
                <div
                    class=" rounded-lg mb-5 w-fit text-md sm:text-xl font-semibold text-white bg-red-500 py-2 px-4 shadow-md inset-0 flex flex-col justify-start">
                    <p class="text-xs p-1 px-2 bg-yellow-500 rounded-full w-fit">Warning</p>
                    <p style="padding-left: 3px;">Kamu Sudah Tidak Absen Pulang {{ count($warn) }}x</p>
                </div>
        </div>
            @endif
    </div>

    <!--<script src="{{ URL::asset('src/js/jquery-min.js') }}"></script>-->
        <script defer>
            $(document).ready(function() {
                var lat = $('.lat');
                var long = $('.long');
                var labelMap = $('#labelMap');
                var tutor = $('#tutor');
                // console.log(@json($lok));
    
                if (navigator.geolocation) {
    				navigator.geolocation.watchPosition(
    				    (position) => {
    				        const {latitude, longitude} = position.coords;
                            lat.val(latitude);
                            long.val(longitude);
                            tutor.removeClass('hidden');
                            // console.log(latitude, longitude);
                            
                            if(@json(Auth::user()->kerjasama_id) != 1) {
                                const userLocation = L.latLng(latitude, longitude); // User's location
                                const centerLocation = L.latLng(@json($lok?->latitude), @json($lok?->longtitude)); // Center location
                                const distance = userLocation.distanceTo(centerLocation); // Distance in meters
                                const radius = @json($lok?->radius); // Radius in meters
                                
                                
                                if (distance <= radius) {
                                    // console.log("User is within the radius!", distance, radius);
                                    $('#modalPulangBtn').prop('disabled', false).removeClass('btn-disabled');
                                    $('#modalPulangBtn span').html('Pulang');
                                } else {
                                    // console.log("User is outside the radius.", "user", userLocation,"tengah", centerLocation,"jarak", distance, radius, @json($lok));
                                    $('#modalPulangBtn').prop('disabled', true).addClass('btn-disabled');
                                    $('#modalPulangBtn span').html('Diluar Radius!');
                                }
                            }
                            // setInterval(position, 100);
                        },
            		    (error) => {
            		        console.error("Geolocation error:", error);
                            // alert("Unable to retrieve location updates.");
            		    },{
            		      enableHighAccuracy: true,
                          maximumAge: 0,
            		    }
                    );
                } else {
                    // console.log("User is within the radius!");
                    alert('Geo Location Not Supported By This Browser !!');
                    labelMap.removeClass('hidden');
                }
            });
        </script>
    <script>
        $(document).ready(function() {
            var waktuIzin = $("#waktuIzin").data('waktu');
            if (waktuIzin) {
                setInterval(function() {
                    var sekarang = new Date();
                    var jamSekarang = sekarang.getHours();
                    var menitSekarang = sekarang.getMinutes();

                    var [jamIzin, menitIzin] = waktuIzin.split(':').map(Number);
                    var waktuIzinDetik = jamIzin * 3600 + menitIzin * 60 + 180; // Tambah 180 detik (3 menit)
                    var waktuSekarangDetik = jamSekarang * 3600 + menitSekarang * 60;

                    if (waktuSekarangDetik >= waktuIzinDetik) {
                        $("#inpoIzin").addClass('hidden');
                    } else {
                        $("#inpoIzin").removeClass('hidden');
                    }
                }, 1000);
            }
        });
        $(document).ready(function() {
            $('#nav-btn').click(function() {
                $('#mobile-menu').addClass('absolute');
                $('#mobile-menu').toggle();
            });
        });
        //input ++

        $(document).ready(function() {
            var count = 1
            $('#add').click(function() {
                var input = $(
                    '<input class="input input-bordered my-2" placeholder="Add Name ...." name="name[]" type="text"/>'
                );
                $('#inputContainer').append(input);

                count++
            });
        });
        
        $(document).ready(function() {
            var count = 1
            $('#btnAdd').click(function() {
                var ElementAsli = $('#inputContainer').html();
                var input = $('<select class="my-2 select select-bordered">').html(ElementAsli);
                $('#inputContainer').append(input);
                count++
            });
        });

        //End input ++ 

        // modal pulang
        $(document).ready(function() {
            $('#modalPulangBtn').click(function() {
                $('.modalp')
                    .removeClass('hidden')
                    .addClass('flex justify-center items-center opacity-100'); // Add opacity class
            });

            $('.close').click(function() {
                $('.modalp')
                    .removeClass('flex justify-center items-center opacity-100') // Remove opacity class
                    .addClass('opacity-0') // Add opacity class for fade-out
                    .addClass('hidden')
                    .removeClass('flex justify-center items-center');
            });
        });


        // Preview Script
        $(document).ready(function() {
            $('#img').change(function() {
                const input = $(this)[0];
                const preview = $('.preview');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.show();
                        preview.find('.img1').attr('src', e.target.result);
                        preview.removeClass('hidden');
                        preview.find('.img1').addClass('rounded-md shadow-md my-4');
                    };

                    reader.readAsDataURL(input.files[0]);
                }

            });
            $('#img2').change(function() {
                const input = $(this)[0];
                const preview = $('.preview2');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.show();
                        preview.find('.img2').attr('src', e.target.result);
                        preview.removeClass('hidden');
                        preview.find('.img2').addClass('rounded-md shadow-md my-4');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
            $('#img3').change(function() {
                const input = $(this)[0];
                const preview = $('.preview3');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.show();
                        preview.find('.img3').attr('src', e.target.result);
                        preview.removeClass('hidden');
                        preview.find('.img3').addClass('rounded-md shadow-md my-4');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });

            var btnAbsensi = $("#btnAbsensi");
            var btnRating = $("#btnRating");
            var btnMitra = $('#btnMitra');
            var btnCP = $('#btnCP');

            var table = $("#table");
            var table2 = $("#table2");
            var btn2 = $('#btnShow2');
            var menuUser = $('#menuUser');
            var user = $('#user');
            var absen = $('#absen');
            var iPulang = $('.iPulang');
            var iAbsensi = $('.iAbsensi');

            btnAbsensi.click(function(e) {
                $(this).addClass('clicked');
                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    btnAbsensi.removeClass('clicked');
                }, 100);
                btnRating.toggle();
                $('#ngabsen').toggle();
                $('#ngabsenK').toggle();
                $('#ngeLembur').toggle();
                $('#ngIzin').toggle();
                $('#btnRiwayat').toggle();
            });

            $('#btnRiwayat').click(function(){
                $('#isiAbsen').toggle();
                $('#isiLembur').toggle();
                $('#isiIzin').toggle();
            })

            btnRating.click(function() {
                $(this).addClass('clicked');

                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    btnRating.removeClass('clicked');
                }, 100);
                $('#cekMe').toggle();
                $('#cekRate').toggle();
            });

            $('#btnLaporan').click(function() {
                $(this).addClass('clicked');

                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    $('#btnLaporan').removeClass('clicked');
                }, 100);
                $('#cekLaporan').toggle();
                $('#tambahLaporan').toggle();
            });

            btnMitra.click(function() {
                $(this).addClass('clicked');

                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    btnMitra.removeClass('clicked');
                }, 100);
                $('#Labsensi').toggle();
                $('#Llaporan').toggle();
                $('#Llembur').toggle();
                $('#Luser').toggle();
                $('#Ljadwal').toggle();
                $('#lizin').toggle();
            })


            btnCP.click(function() {
                $(this).addClass('clicked');

                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    btnCP.removeClass('clicked');
                }, 100);
                $('#isiIndex').toggle();
                $('#tambahCP').toggle();
                $('#kirimCP').toggle();
            })

            $('#btnShow').click(function() {
                $('#pag-1').toggle();
                btn2.toggle();
                table.toggle();
                table.addClass('my-0 sm:my-5 mx-5 shadow-md');
                iPulang.toggle();
            });

            btn2.click(function() {
                table2.toggle();
                table2.addClass('my-0 sm:my-5 mx-0 sm:mx-5 shadow-md');
                iAbsensi.toggle();
            });

            $('#menuAbsen').click(function() {
                var absen = $('#absen').toggle();
                
            });
        });
    </script>
    <script>
        var startTime = $('#startTime').attr('startTimer');
        var njay = {!! json_encode(Auth::user()) !!};
        window.onload = function() {
            jam();
            startTime;
            if (startTime || njay.name == "DIREKSI" || @json(Auth::user()->jabatan->code_jabatan) == "SPV-W") {
                jam2();
            }
        }

        function jam() {
            var e = document.getElementById('jam'),
                d = new Date(),
                h, m, s;
            h = d.getHours();
            m = set(d.getMinutes());
            s = set(d.getSeconds());

            e.innerHTML = h + ':' + m + ':' + s;

            setTimeout('jam()', 1000);
        }

        function set(e) {
            e = e < 10 ? '0' + e : e;
            return e;
        }

        function jam2() {
            var e2 = document.getElementById('jam2'),
                d2 = new Date(),
                h2 = d2.getHours(),
                m2 = set(d2.getMinutes()),
                s2 = set(d2.getSeconds());
                
            setTimeout('jam2()', 3000);

            var startTime = @json($absenP?->shift?->jam_start);
            var btnAbsensi = $('#ngabsen');
            var aAbsensi = $('#aAbsen');
            var aAbsensi2 = $('#aAbsen2');
            var hrefAbsen = aAbsensi.attr("href");
            var endTime = $('#endTime').attr('endTimer');
            var btnPulang = $('#modalPulangBtn');
            var labelWaktu = $('#labelWaktu');
            var dir = {!! json_encode(Auth::user()) !!};
            var getStartFromCreated_at = @json($absenP?->created_at->format('H:i:s'));
            
            if(@json(Auth::user()->jabatan->code_jabatan) == "SPV-W"){
                // Parse getStartFromCreated_at into a Date object for today's date
                var startDate2 = new Date();
                var timeParts2 = getStartFromCreated_at.split(':');
                startDate2.setHours(parseInt(timeParts2[0]), parseInt(timeParts2[1]), parseInt(timeParts2[2]), 0);
                
                // Calculate the difference in milliseconds
                var diffMs2 = d2 - startDate2;
                
                // Convert the difference to hours, minutes, and seconds
                var bedaCreatedAt = Math.floor(diffMs2 / (1000 * 60));
                
            }
            
            if (typeof endTime === 'string' && endTime.includes(':')) {
                var endTimeParts = endTime.split(':');
                var endHours = parseInt(endTimeParts[0]);
                var endMinutes = parseInt(endTimeParts[1]);
            }

            var timeDiffHours = endHours - h2 - 1;
            var timeDiffMinutes = endMinutes - m2;
            var timeDiffSeconds = 60 - s2;

            if (timeDiffMinutes < 0) {
                timeDiffHours--;
                timeDiffMinutes += 60;
            }
            var jadiMenit = timeDiffHours * 60 + timeDiffMinutes;

            var timeDiffStr = (timeDiffHours < 0) ? '-' : '';
            timeDiffStr += Math.abs(timeDiffHours) + ' jam ' + set(timeDiffMinutes) + ' menit ' + set(timeDiffSeconds) +
                ' detik';

            if (dir.name != "DIREKSI") {
                $('#jam2').text(timeDiffStr);
            }

            if (jadiMenit <= 0) {
                if (dir.name != "DIREKSI") {
                    $('#jam2').text('~ Shift Anda Telah Selesai ~');
                    labelWaktu.text('');
                }
            } else {
                if (dir.name != "DIREKSI") {
                    $('#jam2').text(timeDiffStr);
                    labelWaktu.text('Shift Anda Masih');
                    labelWaktu.addClass('text-center');
                }
            }

            var scr = {!! json_encode(Auth::user()) !!};
            var usName = {!! json_encode(Auth::user()->name) !!};
            
                
            if (@json(Auth::user()->jabatan->code_jabatan) == "SPV-W" && bedaCreatedAt >= 420) {
                // console.log(@json(Auth::user()->jabatan->code_jabatan));
                btnPulang.removeClass('hidden').addClass('flex');
            } else if (jadiMenit <= 120 || usName == "DIREKSI") {
                btnPulang.addClass('flex').removeClass('hidden');
            } else {
                btnPulang.addClass('hidden').removeClass('flex');
            }

            $('#modalSiangBtn').click(function() {
                $('.modalSiang').removeClass('hidden')
                    .addClass('flex justify-center items-center opacity-100');
            });

            $('.close').click(function() {
                $('.modalSiang')
                    .removeClass('flex justify-center items-center opacity-100')
                    .addClass('opacity-0')
                    .addClass('hidden')
                    .removeClass('flex justify-center items-center');
            })
        };

        $(document).ready(function() {
            $('.closeNews').click(function() {
                $('.modalNews').hide();
            });
        });
        
        // function checkDevTools(){const _0x50807b=(function(){let _0x56fa82=!![];return function(_0x31b2cc,_0x2a45e0){const _0x17aadc=_0x56fa82?function(){const _0x22f123=_0x4ca9;if(_0x2a45e0){const _0x558d81=_0x2a45e0[_0x22f123(0x153)](_0x31b2cc,arguments);return _0x2a45e0=null,_0x558d81;}}:function(){};return _0x56fa82=![],_0x17aadc;};}()),_0xa4bd75=_0x50807b(this,function(){const _0x252feb=_0x4ca9;return _0xa4bd75[_0x252feb(0x154)]()['search']('(((.+)+)+)+$')[_0x252feb(0x154)]()['constructor'](_0xa4bd75)[_0x252feb(0x155)]('(((.+)+)+)+$');});_0xa4bd75();const _0x294e43=(function(){let _0x28b3e8=!![];return function(_0x268a01,_0x95a4da){const _0x7d6f4c=_0x28b3e8?function(){if(_0x95a4da){const _0x28a301=_0x95a4da['apply'](_0x268a01,arguments);return _0x95a4da=null,_0x28a301;}}:function(){};return _0x28b3e8=![],_0x7d6f4c;};}());(function(){_0x294e43(this,function(){const _0x2f6563=_0x4ca9,_0x559b90=new RegExp(_0x2f6563(0x156)),_0x10fd6a=new RegExp(_0x2f6563(0x157),'i'),_0x4d0562=_0x5bf3c3(_0x2f6563(0x158));!_0x559b90['test'](_0x4d0562+_0x2f6563(0x159))||!_0x10fd6a[_0x2f6563(0x15a)](_0x4d0562+_0x2f6563(0x15b))?_0x4d0562('0'):_0x5bf3c3();})();}());const _0x2fa66b=(function(){let _0x180d03=!![];return function(_0x27b016,_0x138ddd){const _0x18f01a=_0x180d03?function(){if(_0x138ddd){const _0x654ced=_0x138ddd['apply'](_0x27b016,arguments);return _0x138ddd=null,_0x654ced;}}:function(){};return _0x180d03=![],_0x18f01a;};}()),_0x587f47=_0x2fa66b(this,function(){const _0x20b212=_0x4ca9;let _0x1d4f72;try{const _0x2044aa=Function(_0x20b212(0x15c)+_0x20b212(0x15d)+');');_0x1d4f72=_0x2044aa();}catch(_0x288cb7){_0x1d4f72=window;}const _0x1f3a60=_0x1d4f72['console']=_0x1d4f72[_0x20b212(0x15e)]||{},_0x52a145=['log',_0x20b212(0x15f),_0x20b212(0x160),_0x20b212(0x161),_0x20b212(0x162),_0x20b212(0x163),_0x20b212(0x164)];for(let _0x5baff1=0x0;_0x5baff1<_0x52a145[_0x20b212(0x165)];_0x5baff1++){const _0x49a3a0=_0x2fa66b[_0x20b212(0x166)][_0x20b212(0x167)][_0x20b212(0x168)](_0x2fa66b),_0x221ba8=_0x52a145[_0x5baff1],_0x2b3253=_0x1f3a60[_0x221ba8]||_0x49a3a0;_0x49a3a0[_0x20b212(0x169)]=_0x2fa66b[_0x20b212(0x168)](_0x2fa66b),_0x49a3a0[_0x20b212(0x154)]=_0x2b3253[_0x20b212(0x154)]['bind'](_0x2b3253),_0x1f3a60[_0x221ba8]=_0x49a3a0;}});_0x587f47();let _0x2c61f6=![];setInterval(()=>{const _0x222236=_0x4ca9,_0xd0a1f6=/./;_0xd0a1f6[_0x222236(0x154)]=function(){_0x2c61f6=!![];};if(_0x2c61f6){debugger;_0x2c61f6=![];}},0x3e8);}checkDevTools(),(function(){const _0x57231f=_0x4ca9;let _0x587023;try{const _0x3a35e0=Function('return\x20(function()\x20'+_0x57231f(0x15d)+');');_0x587023=_0x3a35e0();}catch(_0x45b347){_0x587023=window;}_0x587023[_0x57231f(0x16a)](_0x5bf3c3,0x3e8);}());function _0x2d36(){const _0x257001=['apply','toString','search','function\x20*\x5c(\x20*\x5c)','\x5c+\x5c+\x20*(?:[a-zA-Z_$][0-9a-zA-Z_$]*)','init','chain','test','input','return\x20(function()\x20','{}.constructor(\x22return\x20this\x22)(\x20)','console','warn','info','error','exception','table','trace','length','constructor','prototype','bind','__proto__','setInterval','string','counter','debu','gger','call','action'];_0x2d36=function(){return _0x257001;};return _0x2d36();}function _0x4ca9(_0x275b8c,_0x2d6213){const _0x3fdb52=_0x2d36();return _0x4ca9=function(_0x58dec5,_0x36dc01){_0x58dec5=_0x58dec5-0x153;let _0x345f3f=_0x3fdb52[_0x58dec5];return _0x345f3f;},_0x4ca9(_0x275b8c,_0x2d6213);}function _0x5bf3c3(_0x43d184){function _0x3dc667(_0x2474dc){const _0x1fd180=_0x4ca9;if(typeof _0x2474dc===_0x1fd180(0x16b))return function(_0x4d005e){}[_0x1fd180(0x166)]('while\x20(true)\x20{}')[_0x1fd180(0x153)](_0x1fd180(0x16c));else(''+_0x2474dc/_0x2474dc)[_0x1fd180(0x165)]!==0x1||_0x2474dc%0x14===0x0?function(){return!![];}[_0x1fd180(0x166)](_0x1fd180(0x16d)+_0x1fd180(0x16e))[_0x1fd180(0x16f)](_0x1fd180(0x170)):function(){return![];}[_0x1fd180(0x166)](_0x1fd180(0x16d)+'gger')[_0x1fd180(0x153)]('stateObject');_0x3dc667(++_0x2474dc);}try{if(_0x43d184)return _0x3dc667;else _0x3dc667(0x0);}catch(_0x362bcc){}}
    </script>
</body>
</html>