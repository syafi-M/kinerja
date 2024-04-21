<!DOCTYPE html>
<html lang="en" data-theme="bumblebee">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME', 'Kinerja SAC-PONOROGO') }}</title>
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="{{ URL::asset('src/js/jquery-min.js') }}"></script>
    <script src="{{ URL::asset('src/js/serviceWorker.min.js') }}"></script>

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
    </style>

</head>

<body class="font-sans antialiased  bg-slate-400">
    <div class="min-h-screen pb-[12.5rem]">
        @include('../layouts/navbar')
        <div class="justify-start flex items-center">
            @forelse ($absen as $arr)
                @php
                    $strShift = strtolower($arr->shift?->shift_name ?? '');
                    $isMalamAndNotPulang =
                        stripos($strShift, 'malam') !== false &&
                        is_null($arr->absensi_type_pulang) &&
                        Carbon\Carbon::parse($arr->tanggal_absen)->isYesterday();
                    $isTodayAndNotPulang =
                        $arr->tanggal_absen == Carbon\Carbon::today()->toDateString() &&
                        is_null($arr->absensi_type_pulang);
                @endphp

                @if (Auth::user()->id == $arr->user_id && ($isMalamAndNotPulang || $isTodayAndNotPulang))
                    <div class="text-center rounded-tr-lg rounded-bl-lg mb-5 w-fit text-md sm:text-xl font-semibold py-2 px-4 shadow-md ml-5 inset-0"
                        style="color: #DEDEDE; background-color: #8F0000">
                        <p>Kamu Belum Absen Pulang !!</p>
                    </div>
                @endif
            @empty
            @endforelse

            @foreach ($lembur as $i)
                @if (Auth::user()->id == $i->user_id && $i->jam_selesai == null)
                    <div
                        class="text-center rounded-tr-lg rounded-bl-lg mb-5 sm:w-fit text-md sm:text-xl font-semibold text-slate-300 bg-red-500 py-2 px-4 shadow-md ml-5 sm:ml-10 inset-0">
                        <p>Kamu Belum Mengakhiri Lembur !!</p>
                    </div>
                @endif
            @endforeach
            @unless (count($lembur))
            @endunless

            @forelse ($izin as $i)
                @php
                    $today = Carbon\Carbon::now()->format('Y-m-d');
                    $updateDate = $i->updated_at->format('Y-m-d');
                    $updateTime = $i->updated_at->format('H:i');
                    $status = $i->approve_status;
                    $statusClass =
                        $status == 'process' ? 'bg-yellow-500' : ($status == 'accept' ? 'bg-green-500' : 'bg-red-500');
                    $statusMessage =
                        $status == 'process'
                            ? 'Izin Masih Dalam Proses !!'
                            : ($status == 'accept'
                                ? 'Izin Sudah Disetujui !!'
                                : 'Izin Anda Ditolak !!');
                @endphp

                @if ($updateDate == $today)
                    <span class="hidden">
                        <span id="waktuIzin" data-waktu="{{ $updateTime }}"></span>
                    </span>
                    <div id="inpoIzin"
                        class="text-center hidden rounded-tr-lg rounded-bl-lg mb-5 sm:w-fit text-md sm:text-xl font-semibold text-slate-50 {{ $statusClass }} py-2 px-4 shadow-md ml-5 sm:ml-10 inset-0">
                        <p>{{ $statusMessage }}</p>
                    </div>
                @endif
            @empty
            @endforelse
        </div>
        @if (Auth::user()->kerjasama_id == 1 && session()->has('point'))
            <div class="flex justify-end items-center mx-5 mb-5">
                <div class="flex flex-row gap-x-2 sm:w-fit px-4 py-1 text-white text-xs rounded-md shadow-sm"
                    style="background-color: #0C642F">
                    <i class="ri-checkbox-circle-line"></i><span>{{ session('point') }}</span>
                </div>
            </div>
        @endif

        <div class="flex justify-center items-center">
            @if ($sholat)
                @php
                    $waktuSekarang = Carbon\Carbon::now()->format('H:i');
                    $waktuSholat = [
                        'subuh' => ['start' => '03:30', 'end' => '04:00', 'status' => $sholat->subuh],
                        'dzuhur' => ['start' => '11:20', 'end' => '14:00', 'status' => $sholat->dzuhur],
                        'asar' => ['start' => '15:00', 'end' => '17:00', 'status' => $sholat->asar],
                        'magrib' => ['start' => '17:20', 'end' => '18:30', 'status' => $sholat->magrib],
                        'isya' => ['start' => '18:30', 'end' => '21:00', 'status' => $sholat->isya],
                    ];
                    $sholatSaatIni = null;
                    foreach ($waktuSholat as $namaSholat => $waktu) {
                        if (
                            $waktu['status'] === '0' &&
                            $waktuSekarang >= $waktu['start'] &&
                            $waktuSekarang <= $waktu['end']
                        ) {
                            $sholatSaatIni = $namaSholat;
                            break;
                        }
                    }
                @endphp
                @if ($sholatSaatIni)
                    <div
                        class="text-center rounded-tr-lg rounded-bl-lg mb-5 sm:w-fit text-md sm:text-xl font-semibold bg-slate-100 py-2 px-4 shadow-md mx-10 inset-0">
                        <p>Sedang memasuki waktu {{ ucfirst($sholatSaatIni) }}</p>
                        <form action="{{ route('update' . ucfirst($sholatSaatIni), $sholat->id) }}" method="POST"
                            class="flex justify-center items-center">
                            @csrf
                            @method('PUT')
                            <div class="flex justify-center flex-col">
                                <div class="flex justify-center items-center">
                                    <button type="submit"
                                        class="bg-yellow-600 flex justify-center shadow-md hover:bg-yellow-700 text-white hover:shadow-none px-3 py-1 text-xl rounded-md transition all ease-out duration-100 mt-5 mr-0 sm:mr-2 uppercase items-center">
                                        <i class="ri-sun-foggy-line"></i><span class="font-bold">Oke</span>
                                    </button>
                                </div>
                            </div>
                        </form>
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
                        <div class="bg-amber-500 mr-10 sm:w-fit flex justify-start px-4"
                            style="border-radius: 5px 0px 6px 0px">
                            <span class="text-white text-center text-xs font-semibold my-1 sm:pr-5">
                                <i class="text-center">Selamat Datang, Anda
                                    Masuk Sebagai, {{ $jabatan }}</i>
                            </span>
                        </div>
                    @endif
                @endauth
                <div class="sm:mx-10 mx-5 bg-slate-500 rounded-md  ">
                    <div class="py-5">
                        <div class="flex items-end justify-end mr-3">
                            <span style="max-width: 57vw; background-color: #0C642F"
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
                                    <button class="uppercase font-bold text-sm">Attendance( Kehadiran )</button>
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
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="ngabsen">
                                    @if ($tampilkanAbsensi)
                                        <a href="{{ route('absensi.index') }}" class="btn btn-info w-full">Kehadiran</a>
                                    @else
                                        <a href="#" class="btn btn-info w-full">Tidak Ada Jadwal</a>
                                    @endif
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
                                    <a href="/checkpoint-user?type=dikerjakan" class="btn btn-info w-full">Data
                                        Rencana Kerja</a>
                                </div>
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="tambahCP">
                                    <!--<a href="{{ route('checkpoint-user.create') }}" class="btn btn-info w-full" {{ \Carbon\Carbon::now()->isWeekend() ? '' : 'disabled' }}>Tambah Planning (sabtu - minggu )</a>-->
                                    <a href="{{ $cex ? route('checkpoint-user.edit', $cex->id) : route('checkpoint-user.create') }}"
                                        class="btn btn-info w-full">{{ $cex ? "Ubah Planning" : "Tambah Planning" }} (sabtu - minggu )</a>
                                </div>
                                <div class="hidden w-full space-y-4 px-2 sm:px-16 overflow-hidden" id="kirimCP">
                                    <!--<a href="{{ route('checkpoint-user.create') }}" class="btn btn-info w-full" {{ \Carbon\Carbon::now()->isWeekend() ? 'disabled' : '' }}>Kirim Bukti (senin - jum'at)</a>-->
                                    <a href="{{ route('editBukti-checkpoint-user') }}"
                                        class="btn btn-info w-full {{ (!$cex && !$cex2) ? 'btn-disabled' : '' }}">Kirim Bukti
                                        (senin - jum'at)</a>
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
                        @endif
                    @else
                        @php
                            $jabatan = Auth::user()->divisi->jabatan->code_jabatan;
                            $role_id = Auth::user()->role_id;
                            $routes = [
                                'MITRA' => [
                                    'user' => 'mitra_user',
                                    'jadwal' => $role_id == 2 ? 'admin-jadwal.index' : 'mitra_jadwal',
                                    'absensi' => 'mitra_absensi',
                                    'izin' => 'mitra_izin',
                                    'lembur' => 'mitra_lembur',
                                    'laporan' => 'mitra_laporan',
                                    'rating' => 'mitra-rating.index',
                                ],
                                'LEADER' => [
                                    'user' => 'lead_user',
                                    'jadwal' => $role_id == 2 ? 'admin-jadwal.index' : 'leader-jadwal.index',
                                    'absensi' => 'lead_absensi',
                                    'izin' => 'lead_izin',
                                    'lembur' => 'lead_lembur',
                                    'laporan' => 'lead_laporan',
                                    'rating' => 'leader-rating.index',
                                ],
                                'DIREKSI' => [
                                    'user' => 'direksi_user',
                                    'jadwal' => $role_id == 2 ? 'admin-jadwal.index' : 'direksi_jadwal',
                                    'absensi' => 'direksi_absensi',
                                    'izin' => 'direksi_izin',
                                    'lembur' => 'direksi_lembur',
                                    'laporan' => 'direksi_laporan',
                                    'rating' => 'direksi-rating.index',
                                    'rencana kerja' => 'direksi.cp.index',
                                ],
                            ];
                            $icons = [
                                'user' => 'ri-pass-pending-line',
                                'jadwal' => 'ri-calendar-check-line',
                                'absensi' => 'ri-todo-line',
                                'izin' => 'ri-shield-user-line',
                                'lembur' => 'ri-time-line',
                                'laporan' => 'ri-image-add-line',
                                'rating' => 'ri-sparkling-line',
                                'rencana kerja' => 'ri-sparkling-line',
                            ];
                        @endphp
                        @if(array_key_exists($jabatan, $routes))
						<div class="sm:grid sm:grid-cols-3 sm:gap-2 sm:space-y-0 space-y-2 overflow-hidden w-full">

                            @foreach($routes[$jabatan] as $key => $route)
                                <div class="w-full space-y-4 overflow-hidden {{ $key == 'rating' && $jabatan == 'DIREKSI' ? 'hidden' : '' }}" id="L{{ $key }}">
                                    <a href="{{ route($route) }}" class="btn btn-info w-full">
                                        <i class="{{ $icons[$key] }} text-xl"></i>{{ ucfirst($key) }}
                                    </a>
                                </div>
                            @endforeach
						</div>
                        @endif
                        @endif
						
                        {{-- quran --}}
                        <div class="flex items-center justify-center">
                            <div id="btnAbsi"
                                class="mx-10 mt-5 flex justify-center w-2/3 max-w-[300px] items-center gap-2 bg-amber-400 rounded-md h-11 hover:bg-amber-500 transition-all ease-linear .2s">
                                <i class="ri-todo-line text-xl"></i>
                                <a href="https://baca-alquran.sac-po.com" class="uppercase font-bold text-sm">
                                    Baca Alqur'an
                                </a>
                            </div>
                        </div>



                        {{-- handle Pulang --}}
                        <div class="flex flex-col justify-center items-center sm:justify-end">
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
                                                        @if (Auth::user()->name != 'DIREKSI')
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
                        {{-- handle akhiri lembur --}}
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
                        </div>
                    </div>
                </div>

                @if (count($hitungNews) > 0)
                    <div>
                        @if ($isModal)
                            <!-- Display your modal here -->
                            <div class="modalNews">
                                <div style="z-index: 9000;"
                                    class="fixed w-full flex justify-center items-center inset-0 bg-slate-500/10 backdrop-blur-sm transition-all duration-300 ease-in-out h-screen">
                                    <div class="flex justify-center items-center">
                                        <div style="z-index: 9001;"
                                            class="bg-slate-200 inset-0 w-fit p-3 mx-10 my-10 rounded-md shadow relative">
                                            <img src="{{ URL::asset('/logo/ketupat-2.png') }}" width="20%"
                                                style="z-index: 9000; position: absolute; top: 0px; left: 0px; transform: rotate(15deg);" />
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
                            {{ Session::forget('is_modal') }}
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
        <div class="pt-10 flex justify-center sm:justify-start mx-10">
            @if (count($warn) >= 3)
                <div
                    class=" rounded-lg mb-5 w-fit text-md sm:text-xl font-semibold text-white bg-red-500 py-2 px-4 shadow-md inset-0 flex flex-col justify-start">
                    <p class="text-xs p-1 px-2 bg-yellow-500 rounded-full w-fit">Warning</p>
                    <p style="padding-left: 3px;">Kamu Sudah Tidak Absen Pulang {{ count($warn) }}x</p>
                </div>
            @endif
        </div>
    </div>

    <!--<script src="{{ URL::asset('src/js/jquery-min.js') }}"></script>-->
    <script>
        $(document).ready(function() {
            var lat = $('.lat');
            var long = $('.long');
            var labelMap = $('#labelMap');
            var tutor = $('#tutor');

            if (navigator.geolocation) {
				navigator.geolocation.watchPosition(function(position) {
                    lat.val(position.coords.latitude);
                    long.val(position.coords.longitude);
                    tutor.removeClass('hidden');
                });
            } else {
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
            $("#searchInput").on("keyup", function() {
                let value = $(this).val().toLowerCase();
                $("#searchTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
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
            $(document).on('click', '#modalPulangBtn', function() {
                $('.modalp')
                    .removeClass('hidden')
                    .addClass('flex justify-center items-center opacity-100'); // Add opacity class
            });

            $(document).on('click', '.close', function() {
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



                // handle rate

                $("#searchInput").on("keyup", function() {
                    let value = $(this).val().toLowerCase();
                    $("#searchTable tbody tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });

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
            var menu1 = $('.menu1');
            var menu2 = $('.menu2');
            var menu3 = $('.menu3');
            var menu4 = $('.menu4');
            var menu5 = $('.menu5');
            var menu6 = $('.menu6');
            var menu7 = $('.menu7');
            var menu8 = $('.menu8');
            var menu9 = $('.menu9');
            var menu10 = $('.menu10');
            var menu11 = $('.menu11');
            var menu12 = $('.menu12');
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
                menu3.toggle();
                menu6.toggle();
                menu7.toggle();
                menu9.toggle();
                menu10.toggle();

            });
            
            
            $('#menuLembur').click(function() {
                $('#lembur').toggle();
                menu3.toggle();
                menu4.toggle();
                menu6.toggle();
                menu9.toggle();
                menu10.toggle();

            });
            
        });
    </script>
    <script>
        var startTime = $('#startTime').attr('startTimer');
        var njay = {!! json_encode(Auth::user()) !!};
        window.onload = function() {
            jam();
            startTime;
            if (startTime || njay.name == "DIREKSI") {
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

            var startTime = $('#startTime').attr('startTimer');
            var btnAbsensi = $('#ngabsen');
            var aAbsensi = $('#aAbsen');
            var aAbsensi2 = $('#aAbsen2');
            var hrefAbsen = aAbsensi.attr("href");
            var endTime = $('#endTime').attr('endTimer');
            var btnPulang = $('#modalPulangBtn');
            var labelWaktu = $('#labelWaktu');
            var dir = {!! json_encode(Auth::user()) !!};

            if (dir.name != "DIREKSI") {
                var startTimeParts = startTime.split(':');
                var startHours = parseInt(startTimeParts[0]);
                var startMinutes = parseInt(startTimeParts[1]);

                var startDiffMinutes = startHours * 60 + startMinutes;
                var nowDiffMinutes = h2 * 60 + m2;
            }

            var endTimeParts = endTime.split(':');
            var endHours = parseInt(endTimeParts[0]);
            var endMinutes = parseInt(endTimeParts[1]);

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

            if (jadiMenit <= 120 || usName == "DIREKSI") {
                btnPulang.addClass('flex').removeClass('hidden');
            } else {
                btnPulang.addClass('hidden').removeClass('flex');
            }

            $('#modalSiangBtn').click(function() {
                $('.modalSiang').removeClass('hidden')
                    .addClass('flex justify-center items-center opacity-100');
            });

            $(document).on('click', '.close', function() {
                $('.modalSiang')
                    .removeClass('flex justify-center items-center opacity-100')
                    .addClass('opacity-0')
                    .addClass('hidden')
                    .removeClass('flex justify-center items-center');
            });
        };

        $(document).ready(function() {
            $(document).on('click', '.closeNews', function() {
                $('.modalNews').addClass('hidden');
            });
        });
    </script>

</body>

</html>

