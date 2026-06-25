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

    @if ($shouldTrackPulang)
        {{-- Leaflet --}}
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endif


    @include('dashboard.partials.styles')

</head>

<body class="font-sans antialiased bg-slate-400">
    <div class="min-h-screen" style="padding-bottom: 4rem;">
        @include('../layouts/navbar')
        <div class="flex items-center justify-start">
            @if ($absenP && $absenP?->absensi_type_pulang == null)
                <div id="statusAbsensiContainer"
                    class="inset-0 px-4 py-2 ml-5 font-semibold text-center rounded-tr-lg rounded-bl-lg shadow-md w-fit"
                    style="background-color: #fafafa; font-size: 10pt; margin-bottom: 10px;">
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

        @include('dashboard.partials.prayer-modal')
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
                        {{-- Handle Rekap & Lembur --}}
                        @php
                            $jabatanMatch = match ($codeJabatan) {
                                'CO-CS' => true,
                                'CO-SCR' => true,
                                'SPV-W' => true,
                                default => false,
                            };
                        @endphp
                        @if ($jabatanMatch && Auth::user()->id != '7')
                            <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                                <div id="btnRekap"
                                    onclick="window.location='{{ $codeJabatan == 'SPV-W' ? route('spvw.rekap.index') : route('index.rekap.data.leader') }}'"
                                    class="w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="ri-calendar-schedule-line"></i>
                                    <button class="text-sm font-bold uppercase">
                                        Data Rekap (Trial)
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- TO Manajemen SPV And MRT --}}

                        @if (auth()->user()->jabatan_id == 4 || auth()->user()->jabatan_id == 14)
                            <div class="flex flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                                <div id="btnRekap" onclick="window.location='{{ route('manajemen_rekap') }}'"
                                    class="w-full flex justify-center items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                    <i class="ri-calendar-schedule-line"></i>
                                    <button class="text-sm font-bold uppercase">
                                        Data Rekap
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- End Handle --}}
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
                                    <a href="{{ route('admin.rating.index') }}"
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
                                <a href="https://laporan-sac.sac-po.com" class="w-full btn btn-info">Tambah
                                    Laporan</a>
                            </div>
                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="cekLaporan">
                                <a href="https://laporan-sac.sac-po.com" class="w-full btn btn-info">Riwayat
                                    Laporan</a>
                            </div>

                        </div>

                        {{-- Button Pengajuan Kontrak --}}
                        <div
                            class="{{ !optional($kontrak)->tgl_mulai_kontrak ||
                            \Carbon\Carbon::parse(optional($kontrak)->tgl_selesai_kontrak)->isPast()
                                ? 'flex'
                                : 'hidden' }} flex-col items-center justify-center gap-2 px-2 pt-2 overflow-hidden">
                            <a href="{{ $kontrak?->isPending() || $kontrak?->isActive() ? 'javascript:void(0)' : route('form-kontrak-request') }}"
                                class="{{ $kontrak?->isPending() || $kontrak?->isActive() ? 'bg-gray-400/40 text-gray-600 cursor-not-allowed' : 'bg-amber-400 hover:bg-amber-500 transition-all ease-linear .2s' }} w-full h-11 rounded-md flex justify-center items-center gap-2 ">
                                <i class="text-xl ri-file-list-3-line"></i>
                                <span class="text-sm font-bold uppercase">Pengajuan Kontrak</span>
                            </a>
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
                                    'jadwal' => $role_id == 2 ? 'admin.jadwal.index' : 'mitra_jadwal',
                                    'absensi' => 'mitra_absensi',
                                    'izin' => 'mitra_izin',
                                    'lembur' => 'mitra_lembur',
                                    'laporan' => 'mitra_laporan',
                                    'rating' => 'mitra-rating.index',
                                    'laporan bulanan' => 'mitra-laporan-bulanan.index',
                                ],
                                'LEADER' => [
                                    'karyawan' => 'lead_user',
                                    'jadwal' => $role_id == 2 ? 'admin.jadwal.index' : 'leader-jadwal.index',
                                    'absensi' => 'lead_absensi',
                                    'izin' => 'lead_izin',
                                    'lembur' => 'lead_lembur',
                                    'laporan' => 'lead_laporan',
                                    'rating' => 'leader-rating.index',
                                ],
                                'DIREKSI' => [
                                    'karyawan' => 'direksi_user',
                                    'jadwal' => $role_id == 2 ? 'admin.jadwal.index' : 'direksi_jadwal',
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

                        @include('dashboard.partials.checkout-modal')
                    </div>
                </div>

                @include('dashboard.partials.news-modal')

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

    <div
        class="{{ $kontrak && $kontrak->isPending() ? 'hidden' : '' }} mx-5 mt-5 sm:mx-10">
        @if ($kontrak && $kontrak->isActive())
            {{-- APPROVED --}}
            <div
                class="mx-auto flex max-w-3xl flex-col-reverse gap-4 rounded-xl border border-white/70 bg-gradient-to-br from-white to-green-50 p-4 shadow-lg shadow-slate-700/10 sm:p-5 md:flex-row md:items-center md:justify-between">
                <div class="w-full text-center md:text-left">
                    <span
                        class="mb-2 inline-flex rounded-full bg-green-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-green-700">
                        Approved
                    </span>
                    <h3 class="mb-2 text-lg font-black text-blue-700 sm:text-xl">
                        Status Pengajuan Saat Ini
                    </h3>

                    <p class="mb-4 text-sm leading-relaxed text-slate-700 sm:text-base">
                        Selamat! Pengajuan anda telah di setujui silahkan
                        lihat detailnya atau tanda tangani kontrak anda.
                    </p>

                    <div class="flex flex-col gap-2 sm:flex-row sm:justify-center md:justify-start">
                        <a href="{{ route('form-kontrak-preview', ['token' => \Illuminate\Support\Facades\Crypt::encryptString($kontrak?->id)]) }}"
                            onclick="window.open(this.href, '_blank'); window.location.reload(); return false;"
                            class="rounded-2xl bg-yellow-400 px-4 py-2.5 text-center text-sm font-bold text-slate-900 shadow-sm transition hover:bg-yellow-500">
                            Lihat Detail
                        </a>

                        <a href="{{ route('form-kontrak-index', ['token' => \Illuminate\Support\Facades\Crypt::encryptString($kontrak?->id)]) }}"
                            class="rounded-2xl bg-green-600 px-4 py-2.5 text-center text-sm font-bold text-white shadow-sm transition hover:bg-green-700">
                            Tanda Tangan Kontrak
                        </a>
                    </div>
                </div>

                <div
                    class="mx-auto flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-green-600 text-white shadow-lg shadow-green-700/20 sm:h-20 sm:w-20 md:mx-0">
                    <i class="ri-check-line text-4xl sm:text-5xl"></i>
                </div>
            </div>
        @elseif($kontrak && $kontrak->isPending())
            {{-- PENDING --}}
            <div
                class="mx-auto flex max-w-3xl flex-col-reverse gap-4 rounded-xl border border-white/70 bg-gradient-to-br from-white to-blue-50 p-4 shadow-lg shadow-slate-700/10 sm:p-5 md:flex-row md:items-center md:justify-between">
                <div class="w-full text-center md:text-left">
                    <span
                        class="mb-2 inline-flex rounded-full bg-blue-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-blue-700">
                        Pending
                    </span>
                    <h3 class="mb-2 text-lg font-black text-blue-700 sm:text-xl">
                        Status Pengajuan Saat Ini
                    </h3>

                    <p class="text-sm leading-relaxed text-slate-700 sm:text-base">
                        Data anda telah terkirim ke HRD perusahaan,
                        jika belum di approve silahkan pengajuan kembali dalam
                        <span class="font-black text-red-600">
                            {{ max(0, 30 - (int) $kontrak->created_at->diffInDays(now())) }} Hari
                        </span>
                    </p>
                </div>

                <div
                    class="mx-auto flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-blue-500 text-white shadow-lg shadow-blue-700/20 sm:h-20 sm:w-20 md:mx-0">
                    <i class="ri-information-line text-4xl sm:text-5xl"></i>
                </div>
            </div>
        @elseif ($kontrak && $kontrak->isProses())
            {{-- PROSES --}}
            <div
                class="mx-auto flex max-w-3xl flex-col-reverse gap-4 rounded-xl border border-white/70 bg-gradient-to-br from-white to-blue-50 p-4 shadow-lg shadow-slate-700/10 sm:p-5 md:flex-row md:items-center md:justify-between">
                <div class="w-full text-center md:text-left">
                    <span
                        class="mb-2 inline-flex rounded-full bg-amber-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-amber-700">
                        Proses
                    </span>
                    <h3 class="mb-2 text-lg font-black text-amber-700 sm:text-xl">
                        Status Pengajuan Saat Ini
                    </h3>

                    <p class="text-sm leading-relaxed text-slate-700 sm:text-base">
                        Data anda telah terkirim ke HRD perusahaan,
                        dan sedang di proses oleh HRD, harap Ditunggu.
                    </p>
                </div>

                <div
                    class="mx-auto flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-amber-500 text-white shadow-lg shadow-amber-700/20 sm:h-20 sm:w-20 md:mx-0">
                    <i class="ri-time-line text-4xl sm:text-5xl"></i>
                </div>
            </div>
        @endif
    </div>
    @include('dashboard.partials.scripts.checkout-tracking')
    @include('dashboard.partials.scripts.dashboard-ui')
    @include('dashboard.partials.scripts.shift-timer')
    <x-flasher-theme />
</body>

</html>
