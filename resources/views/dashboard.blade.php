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
    {{-- <script src="{{ URL::asset('src/js/serviceWorker.min.js') }}"></script> --}}

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
            0% {
                transform: rotate(-15deg);
            }

            50% {
                transform: rotate(-10deg);
            }

            100% {
                transform: rotate(-15deg);
            }
        }

        @keyframes hanging-wiggle2 {
            0% {
                transform: rotate(10deg);
            }

            50% {
                transform: rotate(15deg);
            }

            100% {
                transform: rotate(10deg);
            }
        }

        .hanging {
            position: absolute;
            top: 0;
            transform-origin: top center;
            /* Makes it swing from the top */
            animation: hanging-wiggle 2s ease-in-out infinite alternate;
        }

        .hanging2 {
            position: absolute;
            top: 0;
            transform-origin: top center;
            /* Makes it swing from the top */
            animation: hanging-wiggle2 2.5s ease-in-out infinite alternate;
        }
    </style>

</head>

<body class="font-sans antialiased bg-slate-400">
    <div class="min-h-screen" style="padding-bottom: 4rem;">
        @include('../layouts/navbar')
        <div class="flex items-center justify-start">
            @if ($absenP && $absenP?->absensi_type_pulang == null)
                <div id="statusAbsensiContainer" class="inset-0 px-4 py-2 ml-5 font-semibold text-center rounded-tr-lg rounded-bl-lg shadow-md w-fit"
                    style="background-color: #fafafa; font-size: 10pt; {{ $rillSholat ? '' : 'margin-bottom: 10px;' }}">
                    <p id="statusAbsensiText">Memeriksa status...</p>
                </div>
            @endif

            @if ($izin)
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
        <div class="mx-5 rounded-md shadow-md sm:mx-10 bg-slate-500">
            <main>
                @auth
                    @php
                        $jabatan = Auth::user()->divisi->jabatan->code_jabatan;
                    @endphp
                    @if (in_array($jabatan, ['MITRA', 'LEADER', 'CO-CS']))
                        <div class="flex justify-start px-4 mr-10 bg-amber-500 w-fit"
                            style="border-radius: 5px 0px 24px 0px;">
                            <span class="my-1 text-xs font-semibold text-center text-white sm:pr-5">
                                <i class="text-center">Anda
                                    Login Sebagai, {{ $jabatan }}</i>
                            </span>
                        </div>
                    @endif
                @endauth
                <div class="mx-5 rounded-md sm:mx-10 bg-slate-500 ">
                    <div class="py-5">
                        <div class="flex items-end justify-end mr-3">
                            <span style="max-width: 250px; background-color: #0C642F"
                                class="flex justify-center gap-1 px-4 py-1 text-xs font-bold text-white rounded-full shadow-md sm:hidden">{{ Carbon\Carbon::now()->isoFormat('dddd, D/MMMM/Y') }},
                                <span id="jam"></span>
                            </span>
                        </div>
                        <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                            <div class="flex justify-end w-full mx-10">
                                <div
                                    class="items-end justify-end hidden px-4 py-2 mb-5 ml-10 font-semibold text-center bg-red-500 rounded-tr-lg rounded-bl-lg shadow-md md:flex w-fit text-md sm:text-xl text-slate-100 ">
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
                                    <i class="text-xl ri-todo-line"></i>
                                    <button class="text-sm font-bold uppercase">Kehadiran</button>
                                </div>
                                {{-- menu menu dashboard absensi --}}
                                @php
                                    $hariIni = Carbon\Carbon::now()->format('N');
                                    $tampilkanAbsensi =
                                        !in_array($hariIni, [6, 7]) ||
                                        Auth::user()->kerjasama_id != 1 ||
                                        Auth::user()->devisi_id != 26;
                                    $codeJabatan = Auth::user()->divisi->jabatan->code_jabatan;
                                    $absensiRoute = match ($codeJabatan) {
                                        'CO-CS' => 'absensi-karyawan-co-cs.index',
                                        'CO-SCR' => 'absensi-karyawan-co-scr.index',
                                        default => '',
                                    };
                                @endphp
                                <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="ngabsen">
                                    <a href="{{ $tampilkanAbsensi ? route('absensi.index') : 'javascript:void(0);' }}"
                                        class="w-full btn btn-info">{{ $tampilkanAbsensi ? 'Kehadiran' : 'Tidak Ada Jadwal' }}</a>
                                </div>
                                @if (!empty($absensiRoute))
                                    <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="ngabsenK">
                                        <a href="{{ route($absensiRoute) }}" class="w-full btn btn-info">Kehadiran
                                            karyawan</a>
                                    </div>
                                @endif
                                <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="ngIzin">
                                    <a href="{{ route('izin.create') }}" class="w-full btn btn-info">Izin</a>
                                </div>
                                <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="btnRiwayat">
                                    <a href="javascript:void(0);" class="w-full btn btn-success">Riwayat</a>
                                </div>
                                <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20" id="isiAbsen">
                                    <a href="historyAbsensi" class="w-full btn btn-info">Riwayat Kehadiran</a>
                                </div>
                                <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20" id="isiLembur">
                                    <a href="{{ route('lemburIndexUser') }}" class="w-full btn btn-info">Riwayat
                                        Lembur</a>
                                </div>
                                <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20" id="isiIzin">
                                    <a href="{{ route('izin.index') }}" class="w-full btn btn-info">Riwayat Izin</a>
                                </div>
                        </div>
                        <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                            @if (Auth::user()->kerjasama_id == 1)
                                <div id="btnCP"
                                    class="w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="ri-list-check-3"></i>
                                    <button class="text-sm font-bold uppercase">
                                        Kinerja harian
                                    </button>
                                </div>
                                <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="isiIndex">
                                    <a href="/checkpoint-user" class="w-full btn btn-info">Data
                                        Rencana Kerja</a>
                                </div>
                                <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="tambahCP">
                                    <!--<a href="{{ route('checkpoint-user.create') }}" class="w-full btn btn-info" {{ \Carbon\Carbon::now()->isWeekend() ? '' : 'disabled' }}>Tambah Planning (sabtu - minggu )</a>-->
                                    <a href="{{ $cex ? route('checkpoint-user.edit', $cex->id) : route('checkpoint-user.create') }}"
                                        class="w-full btn btn-info">{{ $cex ? 'Ubah Rencana Kerja' : 'Tambah Rencana Kerja' }}
                                        (sabtu - minggu )</a>
                                </div>
                                <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="kirimCP">
                                    <!--<a href="{{ route('checkpoint-user.create') }}" class="w-full btn btn-info" {{ \Carbon\Carbon::now()->isWeekend() ? 'disabled' : '' }}>Kirim Bukti (senin - jum'at)</a>-->
                                    <a href="{{ route('editBukti-checkpoint-user') }}" {{ !$cex ? 'disabled' : '' }}
                                        style="{{ !$cex ? 'background: #7dd3fc; border: none; color: #1e293b;' : '' }}"
                                        class="w-full btn btn-info ">{{ $cex ? 'Kirim Bukti' : 'Buat Rencana Kerja Terlebih Dahulu' }}
                                        (senin - jum'at)</a>
                                </div>
                            @else
                                <div class="w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s"
                                    disabled>
                                    <i class="ri-list-check-3"></i>
                                    <button class="text-sm font-bold uppercase" disabled>
                                        Kinerja harian
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                            <div id="btnRating"
                                class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                <i class="text-xl ri-user-star-line"></i>
                                <button class="text-sm font-bold uppercase">Rating</button>
                            </div>
                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="cekMe">
                                <a href="{{ route('ratingSaya', Auth::user()->id) }}"
                                    class="w-full btn btn-info">Check Rating Saya</a>
                            </div>
                            @if (Auth::user()->role_id == 2)
                                <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="cekRate">
                                    <a href="{{ route('admin-rating.index') }}"
                                        class="w-full btn btn-info">Rating</a>
                                </div>
                            @elseif(Auth::user()->divisi->jabatan->code_jabatan == 'LEADER')
                                <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="cekRate">
                                    <a href="{{ route('leader-rating.index') }}"
                                        class="w-full btn btn-info">Rating</a>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                            <div id="btnLaporan"
                                class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                <i class="text-xl ri-speak-line"></i>
                                <button class="text-sm font-bold uppercase">Laporan</button>
                            </div>
                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="tambahLaporan">
                                <a href="https://laporan-sac.sac-po.com"
                                    class="w-full btn btn-info">Tambah Laporan</a>
                            </div>
                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="cekLaporan">
                                <a href="https://laporan-sac.sac-po.com" class="w-full btn btn-info">Riwayat
                                    Laporan</a>
                            </div>

                        </div>

                        @if (Auth::user()->id == 7 || Auth::user()->id == 10 || Auth::user()->id == 5)
                            <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                                <a href="{{ route('slip-karyawan') }}" id=""
                                    class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="text-xl ri-wallet-3-line"></i>
                                    <button class="text-sm font-bold uppercase">Slip Gaji Karyawan</button>
                                </a>
                            </div>
                            <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                                <a href="{{ route('manajemen_absensi') }}" id=""
                                    class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="text-xl ri-wallet-3-line"></i>
                                    <button class="text-sm font-bold uppercase">Absensi Karyawan</button>
                                </a>
                            </div>
                            <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                                <a href="{{ route('manajemen_laporan') }}" id=""
                                    class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="text-xl ri-wallet-3-line"></i>
                                    <button class="text-sm font-bold uppercase">Laporan Karyawan</button>
                                </a>
                            </div>
                            <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                                <a href="{{ route('manajemen_user') }}" id=""
                                    class=" w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="text-xl ri-wallet-3-line"></i>
                                    <button class="text-sm font-bold uppercase">Data Karyawan</button>
                                </a>
                            </div>
                        @endif

                        @if (Auth::user()->divisi->jabatan->code_jabatan == 'CO-CS')
                            <div class="flex items-center w-full px-5 mt-5 space-y-4 overflow-hidden sm:px-16">
                                <a href="{{ route('leaderView') }}" class="w-full btn btn-info"><i
                                        class="text-xl ri-pass-pending-line"></i>Menu Leader</a>
                            </div>
                        @elseif(Auth::user()->divisi->jabatan->code_jabatan == 'CO-SCR')
                            <div class="flex items-center w-full mt-5 space-y-4 overflow-hidden sm:px-16">
                                <a href="{{ route('danruView') }}" class="w-full btn btn-info"><i
                                        class="text-xl ri-pass-pending-line"></i>Menu Danru</a>
                            </div>
                        @elseif(Auth::user()?->jabatan?->code_jabatan == 'SPV-W')
                            <div class="flex items-center w-full mt-5 space-y-4 overflow-hidden sm:px-16">
                                <a href="{{ route('SPVWiew') }}" class="w-full btn btn-info"><i
                                        class="text-xl ri-pass-pending-line"></i>Menu SPV Wilayah</a>
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
                                    'kinerja' => 'direksi.cp.index',
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
                                'kinerja' => 'ri-sparkling-line',
                                'kontrak' => 'ri-pass-pending-line',
                            ];
                        @endphp
                        @if (array_key_exists($jabatan, $routes))
                            <div class="w-full gap-2">
                                @if (Auth::user()->divisi->jabatan->code_jabatan == 'DIREKSI')
                                    {{-- absensi --}}
                                    <div id="btnAbsensi"
                                        class="w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                        <i class="text-xl ri-todo-line"></i>
                                        <button class="text-sm font-bold uppercase">Kehadiran</button>
                                    </div>
                                    {{-- menu menu dashboard absensi --}}
                                    @php
                                        $hariIni = Carbon\Carbon::now()->format('N');
                                        $tampilkanAbsensi =
                                            !in_array($hariIni, [6, 7]) || Auth::user()->kerjasama_id != 1;
                                        $codeJabatan = Auth::user()?->divisi?->jabatan?->code_jabatan;
                                        $absensiRoute = match ($codeJabatan) {
                                            'CO-CS' => 'absensi-karyawan-co-cs.index',
                                            'CO-SCR' => 'absensi-karyawan-co-scr.index',
                                            default => null,
                                        };
                                    @endphp
                                    <div class="flex flex-col gap-2 mt-2">
                                        <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16"
                                            id="ngabsen">
                                            <a href="{{ $tampilkanAbsensi ? route('absensi.index') : 'javascript:void(0);' }}"
                                                class="w-full btn btn-info">{{ $tampilkanAbsensi ? 'Kehadiran' : 'Tidak Ada Jadwal' }}</a>
                                        </div>
                                        @if ($absensiRoute)
                                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16"
                                                id="ngabsenK">
                                                <a href="{{ route($absensiRoute) }}"
                                                    class="w-full btn btn-info">Kehadiran karyawan</a>
                                            </div>
                                        @endif
                                        <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16"
                                            id="ngIzin">
                                            <a href="{{ route('izin.create') }}" class="w-full btn btn-info">Izin</a>
                                        </div>
                                        <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16"
                                            id="btnRiwayat">
                                            <a href="#" class="w-full btn btn-success">Riwayat</a>
                                        </div>
                                        <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20"
                                            id="isiAbsen">
                                            <a href="historyAbsensi" class="w-full btn btn-info">Riwayat Kehadiran</a>
                                        </div>
                                        <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20"
                                            id="isiLembur">
                                            <a href="{{ route('lemburIndexUser') }}"
                                                class="w-full btn btn-info">Riwayat Lembur</a>
                                        </div>
                                        <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20"
                                            id="isiIzin">
                                            <a href="{{ route('izin.index') }}" class="w-full btn btn-info">Riwayat
                                                Izin</a>
                                        </div>
                                    </div>
                                @endif

                                <div
                                    class="grid w-full grid-cols-2 gap-2 mt-5 space-y-0 overflow-hidden sm:grid-cols-3">
                                    @foreach ($routes[$jabatan] as $key => $route)
                                        <div class="w-full md:space-y-4 overflow-hidden rounded-lg {{ $key == 'rating' && $jabatan == 'DIREKSI' ? 'hidden' : '' }}"
                                            id="L{{ $key }}">
                                            <a href="{{ route($route) }}"
                                                class="relative flex items-center justify-center w-full btn btn-info">
                                                <i class="{{ $icons[$key] }} text-xl"></i>{{ ucfirst($key) }}
                                                @if ($jabatan == 'DIREKSI' && $key == 'kinerja')
                                                    <span class="absolute text-center bg-yellow-500"
                                                        style="padding: 20px 25px 5px 35px; right: -20px; top: -18px; transform: rotate(35deg);">
                                                        <p style="transform: rotate(-35deg);">
                                                            {{ $totcex }}
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
                        <div class="flex items-center justify-center w-full gap-x-2">
                            <div id="btnAbsi" style="{{ Auth::user()->kerjasama_id != 1 ?: 'width: 50%;' }}"
                                class=" px-2 mt-5 flex justify-center items-center gap-2 bg-blue-400 rounded-md h-11 hover:bg-blue-500 transition-all ease-linear .2s">
                                <i class="text-xl ri-git-repository-line"></i>
                                <a href="https://baca-alquran.sac-po.com" class="text-sm font-bold uppercase">
                                    {{ Auth::user()->kerjasama_id != 1 ? 'Baca Al-Qur`an' : 'Al-Qur`an' }}
                                </a>
                            </div>
                            <div style="{{ Auth::user()->kerjasama_id != 1 ?: 'width: 50%;' }}"
                                class="{{ Auth::user()->kerjasama_id == 1 ? 'flex' : 'hidden' }} px-5 mt-5 justify-center items-center gap-2 bg-blue-400 rounded-md h-11 hover:bg-blue-500 transition-all ease-linear .2s">
                                <i class="text-xl ri-newspaper-line"></i>
                                <a href="https://sppd-online.sac-po.com/login" class="text-sm font-bold uppercase">
                                    SPPD
                                </a>
                            </div>
                        </div>

                        @if ($absenP)
                            {{-- handle Pulang --}}
                            <div class="flex flex-col items-center justify-center sm:justify-end">
                                @php
                                    $luweh1Dino = Carbon\Carbon::createFromFormat(
                                        'Y-m-d, H:i:s',
                                        $absenP?->created_at->format('Y-m-d, H:i:s'),
                                    )->diffInHours(Carbon\Carbon::now());
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
                                            class="items-center justify-center hidden px-3 py-1 mt-5 mr-0 text-xl text-white uppercase transition duration-100 ease-out bg-yellow-600 rounded-md shadow-md hover:bg-yellow-700 hover:shadow-none all sm:mr-2">
                                            <i class="font-sans text-3xl ri-run-line"></i>
                                            <span class="font-bold">Pulang</span>
                                        </button>
                                    </div>
                                    <div
                                        class="fixed inset-0 hidden transition-all duration-300 ease-in-out modalp bg-slate-500/10 backdrop-blur-sm">
                                        <div class="p-5 mx-2 rounded-md shadow bg-slate-200 w-fit">
                                            <div class="flex justify-end mb-3">
                                                <button class="scale-90 btn btn-error close">&times;</button>
                                            </div>
                                            <form action="{{ route('data.update', $absenP->id) }}" method="POST"
                                                class="flex items-center justify-center">
                                                @csrf
                                                @method('PUT')
                                                <div class="flex flex-col justify-center">
                                                    <div class="flex flex-col gap-2">
                                                        <p class="text-lg font-semibold text-center">Apakah Anda Yakin
                                                            Ingin Pulang Sekarang?</p>
                                                        @if (Auth::user()->name != 'DIREKSI' && Auth::user()->jabatan_id != 35)
                                                            <span id="labelWaktu" class="text-center"></span>
                                                            <span class="flex justify-center">
                                                                <span id="jam2"
                                                                    class="text-sm font-semibold underline badge badge-info text-slate-800"></span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center justify-center">
                                                        <button type="submit"
                                                            class="flex items-center justify-center px-3 py-1 mt-5 mr-0 text-xl text-white uppercase transition duration-100 ease-out bg-yellow-600 rounded-md shadow-md hover:bg-yellow-700 hover:shadow-none all sm:mr-2">
                                                            <i class="font-sans text-3xl ri-run-line"></i>
                                                            <span class="font-bold">Pulang Sekarang</span>
                                                        </button>
                                                        <input id="lat" name="lat_user" value=""
                                                            class="hidden lat" />
                                                        <input id="long" name="long_user" value=""
                                                            class="hidden long" />
                                                        <div id="map" class="hidden"></div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                @if (count($hitungNews) > 0)
                    <div>
                        @if (session()->has('is_modal'))
                            <!-- Display your modal here -->
                            <div class="modalNews">
                                <div style="z-index: 9000;"
                                    class="fixed inset-0 flex items-center justify-center w-full h-screen transition-all duration-300 ease-in-out bg-slate-500/10 backdrop-blur-sm">
                                    <div class="flex items-center justify-center">
                                        <div style="z-index: 9001;"
                                            class="relative inset-0 p-3 mx-10 my-10 rounded-md shadow bg-slate-200 w-fit">
                                            @if (Carbon\Carbon::now()->lessThan(Carbon\Carbon::parse('2025-04-09')))
                                                <img src="{{ URL::asset('/logo/ketupat-3.png') }}" width="24%"
                                                    class="hanging"
                                                    style="z-index: 9000; left: 0px; padding: 10px; border-radius: 100%; filter: drop-shadow(0 3px 3px rgb(0 0 0 / 0.15));" />
                                                <img src="{{ URL::asset('/logo/ketupat-1.png') }}" width="24%"
                                                    class="hanging2"
                                                    style="z-index: 8999; left: 0px; padding: 10px; border-radius: 100%; filter: drop-shadow(0 3px 3px rgb(0 0 0 / 0.15));" />
                                            @endif
                                            <div class="flex justify-end mb-3">
                                                <button class="scale-90 btn btn-error closeNews">&times;</button>
                                            </div>
                                            <div class="flex w-full overflow-x-auto carousel divImage">
                                                @php
                                                    $no = 1;
                                                @endphp
                                                @forelse($hitungNews as $new)
                                                    <a id="slide{{ $no++ }}"
                                                        class="relative carousel-item w-fit"
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
                                                <div class="flex items-center justify-center mt-3">
                                                    <span
                                                        class="text-xs font-semibold text-center text-slate-700">Geser
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
            <div class="flex justify-center pt-10 mx-10 sm:justify-start">
                <div
                    class="inset-0 flex flex-col justify-start px-4 py-2 mb-5 font-semibold text-white bg-red-500 rounded-lg shadow-md w-fit text-md sm:text-xl">
                    <p class="p-1 px-2 text-xs bg-yellow-500 rounded-full w-fit">Warning</p>
                    <p style="padding-left: 3px;">Kamu Sudah Tidak Absen Pulang {{ count($warn) }}x</p>
                </div>
            </div>
        @endif
    </div>

    <script defer>
        (function($) {
            // Cache DOM elements
            const elements = {
                lat: $('.lat'),
                long: $('.long'),
                labelMap: $('#labelMap'),
                tutor: $('#tutor'),
                pulangBtn: $('#modalPulangBtn'),
                pulangBtnText: $('#modalPulangBtn span')
            };

            // Get location data from server
            const lokasiMitra = {!! json_encode($lokasiMitra) !!};
            const userCoopId = @json(Auth::user()->kerjasama_id);

            // pre calculated loc
            const processedLocations = lokasiMitra.map(loc => ({
                lat: loc.latitude,
                lng: loc.longtitude,
                radius: parseFloat(loc.radius),
                center: L.latLng(loc.latitude, loc.longtitude)
            }));

            let watchId = null;
            let lastPosition = null;
            let positionCheckThrottle = null;
            const THROTTLE_DELAY = 1000;

            // Check if geolocation is supported
            if (!navigator.geolocation) {
                handleGeolocationNotSupported();
                return;
            }

            // Set up geolocation watching
            const watchOptions = {
                enableHighAccuracy: true,
                maximumAge: 0
            };

            watchId = navigator.geolocation.watchPosition(
                handlePositionUpdate,
                handleGeolocationError,
                watchOptions
            );

            $(window).on('beforeunload', function() {
                if (watchId !== null) {
                    navigator.geolocation.clearWatch(watchId);
                }
            });

            // Helper functions
            function handlePositionUpdate(position) {
                if (positionCheckThrottle) {
                    clearTimeout(positionCheckThrottle);
                };

                positionCheckThrottle = setTimeout(function() {
                    processPositionUpdate(position);
                }, THROTTLE_DELAY);
            }

            function processPositionUpdate(position) {
                const { latitude, longitude } = position.coords;

                // Skip if position hasn't changed significantly
                if (lastPosition) {
                    const distance = calculateDistance(
                        latitude, longitude,
                        lastPosition.lat, lastPosition.lng
                    );

                    // Only process if moved more than 5 meters
                    if (distance < 5) return;
                }

                // Update last position
                lastPosition = { lat: latitude, lng: longitude };

                // Update form fields
                elements.lat.val(latitude);
                elements.long.val(longitude);
                elements.tutor.removeClass('hidden');

                // Handle different coop types
                if (userCoopId === 1) {
                    // For coop 1, always enable the button without radius check
                    updateButtonState(true);
                } else {
                    // For other coops, check if user is within any location's radius
                    checkLocationRadius(latitude, longitude);
                }
            }

            // Optimized distance calculation (Haversine formula)
            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371e3; // Earth's radius in meters
                const φ1 = lat1 * Math.PI/180;
                const φ2 = lat2 * Math.PI/180;
                const Δφ = (lat2-lat1) * Math.PI/180;
                const Δλ = (lon2-lon1) * Math.PI/180;

                const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                        Math.cos(φ1) * Math.cos(φ2) *
                        Math.sin(Δλ/2) * Math.sin(Δλ/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

                return R * c; // Distance in meters
            }

            // Optimized radius checking
            function checkLocationRadius(userLat, userLng) {
                let withinAnyRadius = false;

                // Use our pre-processed locations for faster checks
                for (const location of processedLocations) {
                    const distance = calculateDistance(
                        userLat, userLng,
                        location.lat, location.lng
                    );

                    if (distance <= location.radius) {
                        withinAnyRadius = true;
                        break; // Exit early if we find a match
                    }
                }

                updateButtonState(withinAnyRadius);
            }

            function updateButtonState(isWithinRadius) {
                if (isWithinRadius) {
                    elements.pulangBtn
                        .prop('disabled', false)
                        .removeClass('btn-disabled');
                    elements.pulangBtnText.html('Pulang');
                } else {
                    elements.pulangBtn
                        .prop('disabled', true)
                        .addClass('btn-disabled');
                    elements.pulangBtnText.html('Diluar Radius!');
                }
            }

            function handleGeolocationError(error) {
                console.error("Geolocation error:", error);

                // Show user-friendly error message based on error code
                let errorMessage = "Location access error. ";
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += "Please allow location access.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += "Location information unavailable.";
                        break;
                    case error.TIMEOUT:
                        errorMessage += "Location request timed out.";
                        break;
                    default:
                        errorMessage += "Unknown error occurred.";
                }

                elements.labelMap.text(errorMessage).removeClass('hidden');
            }

            function handleGeolocationNotSupported() {
                alert('Geolocation is not supported by your browser.');
                elements.labelMap.text('Geolocation is not supported by your browser.').removeClass('hidden');
            }
        }) (jQuery);
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
                    var waktuIzinDetik = jamIzin * 3600 + menitIzin * 60 +
                        180; // Tambah 180 detik (3 menit)
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
                    '<input class="my-2 input input-bordered" placeholder="Add Name ...." name="name[]" type="text"/>'
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

            $('#btnRiwayat').click(function() {
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
        // Store user data once to avoid repeated server-side calls
        const userData = {!! json_encode(Auth::user()) !!};
        const userJabatanCode = @json(Auth::user()->jabatan->code_jabatan);
        const userDevisiId = @json(Auth::user()->devisi_id);
        const isSupervisorOrSpecialDept = userJabatanCode === "SPV-W" || userDevisiId === 12;
        const shift = {!! json_encode($absenP?->shift) !!};

        // Cache DOM elements
        const elements = {
            jam: $('#jam'),
            jam2: $('#jam2'),
            endTime: $('#endTime').attr('endTimer'),
            btnPulang: $('#modalPulangBtn'),
            labelWaktu: $('#labelWaktu'),
            statusContainer: $('#statusAbsensiContainer'),
            statusText: $('#statusAbsensiText')
        };

        // Constants
        const ABSEN_CREATED_TIME = @json($absenP?->created_at->format('Y-m-d H:i:s'));
        const MINUTES_BEFORE_SHIFT_END = 60;
        const MINUTES_AFTER_SHIFT_END = -90;
        const SUPERVISOR_MIN_WORK_TIME = 390;
        const IS_OVERNIGHT_SHIFT = shift?.is_overnight == true;

        $(document).ready(function() {
            startClock();
            startShiftTimer();
            initializeModals();
        });

        function startClock() {
            setInterval(() => {
                const now = new Date();
                elements.jam.text(now.toLocaleTimeString('en-GB'));
            }, 1000);
        }

        function startShiftTimer() {
            function update() {
                const now = new Date();

                // 1. Hitung Durasi Kerja (Sejak Absen Masuk)
                let diffWorkMinutes = 0;
                if (ABSEN_CREATED_TIME) {
                    const startTime = new Date(ABSEN_CREATED_TIME);
                    diffWorkMinutes = Math.floor((now - startTime) / (1000 * 60));
                }

                // 2. Hitung Sisa Waktu Shift
                let sisaMenitShift = 0;
                let timeDiffStr = "";

                if (elements.endTime && elements.endTime.includes(':')) {
                    const [endH, endM] = elements.endTime.split(':').map(Number);
                    let targetEnd = new Date();
                    targetEnd.setHours(endH, endM, 0, 0);

                    // LOGIKA OVERNIGHT
                    if (IS_OVERNIGHT_SHIFT) {
                        // Ambil jam masuk dari ABSEN_CREATED_TIME untuk perbandingan
                        const checkInDate = new Date(ABSEN_CREATED_TIME);
                        const startH = checkInDate.getHours();

                        // Jika jam pulang (misal 07:00) lebih kecil dari jam masuk (misal 22:00)
                        // Maka target pulang adalah besoknya dari hari absen masuk
                        if (endH < startH) {
                            targetEnd = new Date(checkInDate);
                            targetEnd.setDate(checkInDate.getDate() + 1);
                            targetEnd.setHours(endH, endM, 0, 0);
                        }
                    }

                    const diffMs = targetEnd - now;
                    sisaMenitShift = Math.floor(diffMs / (1000 * 60));

                    // Format string waktu (tetap hitung meski sudah lewat/negatif untuk keperluan UI)
                    const absoluteMs = Math.abs(diffMs);
                    const h = Math.floor(absoluteMs / 3600000);
                    const m = Math.floor((absoluteMs % 3600000) / 60000);
                    const s = Math.floor((absoluteMs % 60000) / 1000);

                    const prefix = diffMs < 0 ? "-" : "";
                    timeDiffStr = `${prefix}${h} jam ${padZero(m)} menit ${padZero(s)} detik`;
                }

                // UI Rendering
                renderUI(sisaMenitShift, timeDiffStr, diffWorkMinutes);

                // Tombol Pulang Logic
                // Sekarang sisaMenitShift bisa bernilai negatif (misal -30 untuk lewat 30 menit)
                updateCheckoutButton(diffWorkMinutes, sisaMenitShift);
            }

            setInterval(update, 1000);
            update();
        }

        function renderUI(sisaMenit, timeStr, durasiKerja) {
            if (IS_OVERNIGHT_SHIFT) {
                elements.labelWaktu.text('Shift Lintas Hari');
                // Jika sisaMenit < 0 berarti sudah lewat jam pulang
                elements.jam2.text(sisaMenit > MINUTES_AFTER_SHIFT_END ? (sisaMenit > 0 ? `Sisa: ${timeStr}` : '~ Waktunya Pulang ~') : '~ Shift Berakhir ~');
            } else {
                if (sisaMenit < MINUTES_AFTER_SHIFT_END) {
                    elements.jam2.text('~ Masa Absen Pulang Hampir Habis ~');
                    elements.labelWaktu.text('');
                } else if (sisaMenit <= 0) {
                    elements.jam2.text('~ Waktunya Pulang (Lembur) ~');
                    elements.labelWaktu.text('Shift Selesai');
                } else {
                    elements.jam2.text(timeStr);
                    elements.labelWaktu.text('Shift Anda Masih');
                }
            }
        }

        function updateCheckoutButton(durasiKerja, sisaMenit) {
            // Direksi selalu bisa pulang
            if (userData.name === "DIREKSI") {
                showCheckoutBtn(true);
                return;
            }

            // Supervisor: Minimal kerja 6.5 jam
            if (isSupervisorOrSpecialDept) {
                showCheckoutBtn(durasiKerja >= SUPERVISOR_MIN_WORK_TIME);
                return;
            }

            // 3. Staff Normal:
            // Muncul jika: Sisa waktu <= 120 menit (2 jam sebelum)
            // DAN tetap muncul sampai: sisaMenit >= -90 (1 jam 30 menit setelah jam pulang)
            const sudahWaktunyaPulang = sisaMenit <= MINUTES_BEFORE_SHIFT_END && sisaMenit >= MINUTES_AFTER_SHIFT_END;

            showCheckoutBtn(sudahWaktunyaPulang);
        }

        function showCheckoutBtn(show) {
            if (show) {
                // TAMPILKAN TOMBOL PULANG & PESAN MERAH
                elements.btnPulang.removeClass('hidden').addClass('flex');

                if (elements.statusContainer.length > 0) {
                    elements.statusContainer
                        .css({'background-color': '#8F0000', 'color': '#DEDEDE'}) // Merah;
                    elements.statusText.text('Waktunya Absen Pulang!');
                }
            } else {
                // SEMBUNYIKAN TOMBOL & TAMPILKAN PESAN HIJAU
                elements.btnPulang.addClass('hidden').removeClass('flex');

                if (elements.statusContainer.length > 0) {
                    elements.statusContainer
                        .css({'background-color': '#006118', 'color': '#DEDEDE', 'text-align': 'left'}); // Hijau

                    let ketWaktu = "";

                    if (isSupervisorOrSpecialDept && ABSEN_CREATED_TIME) {
                        // LOGIKA SUPERVISOR: Created_at + 390 Menit
                        let appearanceTime = new Date(ABSEN_CREATED_TIME);
                        appearanceTime.setMinutes(appearanceTime.getMinutes() + SUPERVISOR_MIN_WORK_TIME);

                        const jamTampil = padZero(appearanceTime.getHours());
                        const menitTampil = padZero(appearanceTime.getMinutes());

                        ketWaktu = `<br><span style="font-size: 8pt; opacity: 0.8; font-weight: normal;">
                                        Tombol absen pulang muncul jam <span style="font-weight: bold; text-decoration: underline; text-underline-offset: 1px;">${jamTampil}:${menitTampil}</span>
                                    </span>`;

                    } else if (elements.endTime) {
                        // LOGIKA STAFF NORMAL: EndTime - 120 Menit
                        const [endH, endM] = elements.endTime.split(':').map(Number);
                        let appearanceTime = new Date();
                        appearanceTime.setHours(endH - 1, endM, 0);

                        const jamTampil = padZero(appearanceTime.getHours());
                        const menitTampil = padZero(appearanceTime.getMinutes());

                        ketWaktu = `<br><span style="font-size: 8pt; opacity: 0.8; font-weight: normal;">
                                        Tombol absen pulang muncul jam <span style="font-weight: bold; text-decoration: underline; text-underline-offset: 1px;">${jamTampil}:${menitTampil}</span>
                                    </span>`;
                    }

                    // Gunakan .html() agar tag span & br terbaca
                    elements.statusText.html('Sudah Absen Masuk' + ketWaktu);
                }
            }
        }

        function padZero(num) {
            return num < 10 ? `0${num}` : num;
        }

        function initializeModals() {
            // Handler untuk modal siang (yang sudah ada)
            $('#modalSiangBtn').click(() => {
                $('.modalSiang').removeClass('hidden').addClass('flex justify-center items-center');
            });

            $('.close').click(() => {
                $('.modalSiang').addClass('hidden').removeClass('flex');
            });

            // --- TAMBAHKAN DI SINI UNTUK MODAL NEWS ---
            $('.closeNews').on('click', function() {
                // Gunakan .addClass('hidden') jika pakai Tailwind
                // Atau .hide() untuk jQuery standar
                $('.modalNews').addClass('hidden');

                // Opsional: hapus dari DOM agar tidak berat
                setTimeout(() => {
                    $('.modalNews').remove();
                }, 100);
            });
        }

        // Tambahan: Peringatan jika mencoba menutup tab/browser sebelum absen pulang
        window.addEventListener('beforeunload', function (e) {
            // Jika tombol pulang sudah muncul (berarti sudah boleh pulang) tapi belum klik absen
            if (!elements.btnPulang.hasClass('hidden')) {
                const confirmationMessage = 'Anda belum absen pulang! Tetap keluar?';
                (e || window.event).returnValue = confirmationMessage; // Standard browser
                return confirmationMessage; // Legacy browser
            }
        });

        // function checkDevTools(){const _0x50807b=(function(){let _0x56fa82=!![];return function(_0x31b2cc,_0x2a45e0){const _0x17aadc=_0x56fa82?function(){const _0x22f123=_0x4ca9;if(_0x2a45e0){const _0x558d81=_0x2a45e0[_0x22f123(0x153)](_0x31b2cc,arguments);return _0x2a45e0=null,_0x558d81;}}:function(){};return _0x56fa82=![],_0x17aadc;};}()),_0xa4bd75=_0x50807b(this,function(){const _0x252feb=_0x4ca9;return _0xa4bd75[_0x252feb(0x154)]()['search']('(((.+)+)+)+$')[_0x252feb(0x154)]()['constructor'](_0xa4bd75)[_0x252feb(0x155)]('(((.+)+)+)+$');});_0xa4bd75();const _0x294e43=(function(){let _0x28b3e8=!![];return function(_0x268a01,_0x95a4da){const _0x7d6f4c=_0x28b3e8?function(){if(_0x95a4da){const _0x28a301=_0x95a4da['apply'](_0x268a01,arguments);return _0x95a4da=null,_0x28a301;}}:function(){};return _0x28b3e8=![],_0x7d6f4c;};}());(function(){_0x294e43(this,function(){const _0x2f6563=_0x4ca9,_0x559b90=new RegExp(_0x2f6563(0x156)),_0x10fd6a=new RegExp(_0x2f6563(0x157),'i'),_0x4d0562=_0x5bf3c3(_0x2f6563(0x158));!_0x559b90['test'](_0x4d0562+_0x2f6563(0x159))||!_0x10fd6a[_0x2f6563(0x15a)](_0x4d0562+_0x2f6563(0x15b))?_0x4d0562('0'):_0x5bf3c3();})();}());const _0x2fa66b=(function(){let _0x180d03=!![];return function(_0x27b016,_0x138ddd){const _0x18f01a=_0x180d03?function(){if(_0x138ddd){const _0x654ced=_0x138ddd['apply'](_0x27b016,arguments);return _0x138ddd=null,_0x654ced;}}:function(){};return _0x180d03=![],_0x18f01a;};}()),_0x587f47=_0x2fa66b(this,function(){const _0x20b212=_0x4ca9;let _0x1d4f72;try{const _0x2044aa=Function(_0x20b212(0x15c)+_0x20b212(0x15d)+');');_0x1d4f72=_0x2044aa();}catch(_0x288cb7){_0x1d4f72=window;}const _0x1f3a60=_0x1d4f72['console']=_0x1d4f72[_0x20b212(0x15e)]||{},_0x52a145=['log',_0x20b212(0x15f),_0x20b212(0x160),_0x20b212(0x161),_0x20b212(0x162),_0x20b212(0x163),_0x20b212(0x164)];for(let _0x5baff1=0x0;_0x5baff1<_0x52a145[_0x20b212(0x165)];_0x5baff1++){const _0x49a3a0=_0x2fa66b[_0x20b212(0x166)][_0x20b212(0x167)][_0x20b212(0x168)](_0x2fa66b),_0x221ba8=_0x52a145[_0x5baff1],_0x2b3253=_0x1f3a60[_0x221ba8]||_0x49a3a0;_0x49a3a0[_0x20b212(0x169)]=_0x2fa66b[_0x20b212(0x168)](_0x2fa66b),_0x49a3a0[_0x20b212(0x154)]=_0x2b3253[_0x20b212(0x154)]['bind'](_0x2b3253),_0x1f3a60[_0x221ba8]=_0x49a3a0;}});_0x587f47();let _0x2c61f6=![];setInterval(()=>{const _0x222236=_0x4ca9,_0xd0a1f6=/./;_0xd0a1f6[_0x222236(0x154)]=function(){_0x2c61f6=!![];};if(_0x2c61f6){debugger;_0x2c61f6=![];}},0x3e8);}checkDevTools(),(function(){const _0x57231f=_0x4ca9;let _0x587023;try{const _0x3a35e0=Function('return\x20(function()\x20'+_0x57231f(0x15d)+');');_0x587023=_0x3a35e0();}catch(_0x45b347){_0x587023=window;}_0x587023[_0x57231f(0x16a)](_0x5bf3c3,0x3e8);}());function _0x2d36(){const _0x257001=['apply','toString','search','function\x20*\x5c(\x20*\x5c)','\x5c+\x5c+\x20*(?:[a-zA-Z_$][0-9a-zA-Z_$]*)','init','chain','test','input','return\x20(function()\x20','{}.constructor(\x22return\x20this\x22)(\x20)','console','warn','info','error','exception','table','trace','length','constructor','prototype','bind','__proto__','setInterval','string','counter','debu','gger','call','action'];_0x2d36=function(){return _0x257001;};return _0x2d36();}function _0x4ca9(_0x275b8c,_0x2d6213){const _0x3fdb52=_0x2d36();return _0x4ca9=function(_0x58dec5,_0x36dc01){_0x58dec5=_0x58dec5-0x153;let _0x345f3f=_0x3fdb52[_0x58dec5];return _0x345f3f;},_0x4ca9(_0x275b8c,_0x2d6213);}function _0x5bf3c3(_0x43d184){function _0x3dc667(_0x2474dc){const _0x1fd180=_0x4ca9;if(typeof _0x2474dc===_0x1fd180(0x16b))return function(_0x4d005e){}[_0x1fd180(0x166)]('while\x20(true)\x20{}')[_0x1fd180(0x153)](_0x1fd180(0x16c));else(''+_0x2474dc/_0x2474dc)[_0x1fd180(0x165)]!==0x1||_0x2474dc%0x14===0x0?function(){return!![];}[_0x1fd180(0x166)](_0x1fd180(0x16d)+_0x1fd180(0x16e))[_0x1fd180(0x16f)](_0x1fd180(0x170)):function(){return![];}[_0x1fd180(0x166)](_0x1fd180(0x16d)+'gger')[_0x1fd180(0x153)]('stateObject');_0x3dc667(++_0x2474dc);}try{if(_0x43d184)return _0x3dc667;else _0x3dc667(0x0);}catch(_0x362bcc){}}
    </script>
</body>

</html>
