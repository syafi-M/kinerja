    <div class="mx-5 rounded-md shadow-md sm:mx-10 bg-slate-500">
        <main>
            @auth
                @php
                    $jabatan = Auth::user()->divisi->jabatan->code_jabatan;
                @endphp
                @if (in_array($jabatan, ['MITRA', 'LEADER', 'CO-CS']))
                    <div class="flex justify-start px-4 mr-10 bg-amber-500 w-fit" style="border-radius: 5px 0px 24px 0px;">
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
                        <span style="max-width: 235px; background-color: #0C642F"
                            class="flex justify-start min-w-[225px] gap-1 px-4 py-1 text-xs font-bold text-white rounded-full shadow-md text-start sm:hidden">{{ Carbon\Carbon::now()->isoFormat('dddd, D/MMMM/Y') }},
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
                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16 ngabsen">
                                <a href="{{ $tampilkanAbsensi ? route('absensi.index') : 'javascript:void(0);' }}"
                                    class="w-full btn btn-info">{{ $tampilkanAbsensi ? 'Kehadiran' : 'Tidak Ada Jadwal' }}</a>
                            </div>
                            @if (!empty($absensiRoute))
                                <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16 ngabsenK">
                                    <a href="{{ route($absensiRoute) }}" class="w-full btn btn-info">Kehadiran
                                        karyawan</a>
                                </div>
                            @endif
                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16 ngIzin">
                                <a href="{{ route('izin.create') }}" class="w-full btn btn-info">Izin</a>
                            </div>
                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16 btnRiwayat">
                                <a href="javascript:void(0);" class="w-full btn btn-success">Riwayat</a>
                            </div>
                            <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20 isiAbsen">
                                <a href="historyAbsensi" class="w-full btn btn-info">Riwayat Kehadiran</a>
                            </div>
                            <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20 isiLembur">
                                <a href="{{ route('lemburIndexUser') }}" class="w-full btn btn-info">Riwayat
                                    Lembur</a>
                            </div>
                            <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20 isiIzin">
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
                            <a href="{{ route('ratingSaya', Auth::user()->id) }}" class="w-full btn btn-info">Check
                                Rating Saya</a>
                        </div>
                        @if (Auth::user()->role_id == 2)
                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="cekRate">
                                <a href="{{ route('admin-rating.index') }}" class="w-full btn btn-info">Rating</a>
                            </div>
                        @elseif(Auth::user()->divisi->jabatan->code_jabatan == 'LEADER')
                            <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="cekRate">
                                <a href="{{ route('leader-rating.index') }}" class="w-full btn btn-info">Rating</a>
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
                            <a href="{{ Auth::user()->divisi->jabatan->code_jabatan != 'OCS' || Auth::user()->divisi->jabatan->code_jabatan != 'SCR' ? url('scan') : '#' }}"
                                class="w-full btn btn-info">Tambah Laporan</a>
                        </div>
                        <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16" id="cekLaporan">
                            <a href="{{ route('laporan.index') }}" class="w-full btn btn-info">Riwayat
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
                                    $tampilkanAbsensi = !in_array($hariIni, [6, 7]) || Auth::user()->kerjasama_id != 1;
                                    $codeJabatan = Auth::user()?->divisi?->jabatan?->code_jabatan;
                                    $absensiRoute = match ($codeJabatan) {
                                        'CO-CS' => 'absensi-karyawan-co-cs.index',
                                        'CO-SCR' => 'absensi-karyawan-co-scr.index',
                                        default => null,
                                    };
                                @endphp
                                <div class="flex flex-col gap-2 mt-2">
                                    <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16 ngabsen">
                                        <a href="{{ $tampilkanAbsensi ? route('absensi.index') : 'javascript:void(0);' }}"
                                            class="w-full btn btn-info">{{ $tampilkanAbsensi ? 'Kehadiran' : 'Tidak Ada Jadwal' }}</a>
                                    </div>
                                    @if ($absensiRoute)
                                        <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16 ngabsenK">
                                            <a href="{{ route($absensiRoute) }}"
                                                class="w-full btn btn-info">Kehadiran karyawan</a>
                                        </div>
                                    @endif
                                    <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16 ngIzin">
                                        <a href="{{ route('izin.create') }}" class="w-full btn btn-info">Izin</a>
                                    </div>
                                    <div class="hidden w-full px-2 space-y-4 overflow-hidden sm:px-16 btnRiwayat">
                                        <a href="#" class="w-full btn btn-success">Riwayat</a>
                                    </div>
                                    <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20 isiAbsen">
                                        <a href="historyAbsensi" class="w-full btn btn-info">Riwayat Kehadiran</a>
                                    </div>
                                    <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20 isiLembur">
                                        <a href="{{ route('lemburIndexUser') }}" class="w-full btn btn-info">Riwayat
                                            Lembur</a>
                                    </div>
                                    <div class="hidden w-full px-4 space-y-4 overflow-hidden sm:px-20 isiIzin">
                                        <a href="{{ route('izin.index') }}" class="w-full btn btn-info">Riwayat
                                            Izin</a>
                                    </div>
                                </div>
                            @endif

                            <div class="grid w-full grid-cols-2 gap-2 mt-5 space-y-0 overflow-hidden sm:grid-cols-3">
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

                    @if ($absenP)
    {{-- Handle Pulang --}}
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

            {{-- Hidden data elements --}}
            <div class="hidden">
                <span id="userId" data-user-id="{{ $absenP->user_id }}" data-auth-user="{{ Auth::user()->id }}"></span>
                <span id="endTime" endTimer="{{ $absenP->shift?->jam_end }}"></span>
                <span id="startTime" startTimer="{{ $absenP->shift?->jam_start }}"></span>
            </div>

            {{-- Checkout Button --}}
            <button id="modalPulangBtn" data-absen="{{ $absenP }}"
                class="flex items-center justify-center gap-2 px-4 py-2 mt-4 text-white transition-all duration-200 bg-yellow-600 rounded-lg shadow-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                <i class="text-xl ri-run-line"></i>
                <span class="font-semibold">Pulang</span>
            </button>

            {{-- Modal --}}
            <div id="checkoutModal" class="fixed inset-0 z-50 items-center justify-center hidden p-4 transition-opacity duration-300 bg-black/50 backdrop-blur-sm">
                <div class="w-full max-w-md p-6 transition-all duration-300 transform scale-95 bg-white shadow-xl opacity-0 rounded-xl">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-800">Konfirmasi Pulang</h3>
                        <button class="p-1 text-gray-500 rounded-full hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300 close-modal">
                            <i class="text-xl ri-close-line"></i>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="mb-6">
                        <p class="mb-4 text-center text-gray-700">Apakah Anda yakin ingin pulang sekarang?</p>

                        @if (Auth::user()->name != 'DIREKSI' && Auth::user()->jabatan_id != 35)
                            <div class="flex flex-col items-center justify-center p-3 mb-4 rounded-lg bg-blue-50">
                                <p class="text-sm font-medium text-blue-800">Waktu saat ini:</p>
                                <span id="jam2" class="text-lg font-bold text-blue-600"></span>
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <form action="{{ route('data.update', $absenP->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button type="button"
                                class="flex-1 px-4 py-2 font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 close-modal">
                                Batal
                            </button>

                            <button type="submit"
                                class="flex items-center justify-center flex-1 gap-2 px-4 py-2 font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                                <i class="ri-run-line"></i>
                                <span>Ya, Pulang Sekarang</span>
                            </button>
                        </div>

                        {{-- Hidden inputs for location --}}
                        <input id="lat" name="lat_user" value="" class="hidden lat" />
                        <input id="long" name="long_user" value="" class="hidden long" />
                        <div id="map" class="hidden"></div>
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
                            <div style="z-index: 99999;"
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
                                                <a id="slide{{ $no++ }}" class="relative carousel-item w-fit"
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
                                                <span class="text-xs font-semibold text-center text-slate-700">Geser
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

        </main>
    </div>
