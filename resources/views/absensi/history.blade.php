<x-app-layout>
    <x-main-div>
        <style>
            /* Calendar Styles */
            .calendar-wrapper {
                font-family: system-ui, -apple-system, sans-serif;
            }

            .calendar-container {
                width: 100%;
                max-width: 350px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                padding: 10px;
            }

            .calendar-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 1rem;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            .calendar-header h3 {
                font-size: 1.125rem;
                font-weight: 600;
                color: #1e293b;
            }

            .calendar-header button {
                background: none;
                border: none;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 0.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #64748b;
                transition: all 0.2s;
            }

            .calendar-header button:hover {
                background: #e2e8f0;
                color: #1e293b;
            }

            .calendar-grid {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 1px;
                background: #e2e8f0;
                padding: 1px;
            }

            .day-header,
            .day {
                padding: 0.75rem 0.45rem;
                text-align: center;
                font-size: 0.875rem;
                background: white;
            }

            .day-header {
                font-weight: 600;
                color: #475569;
                font-size: 0.75rem;
                text-transform: uppercase;
            }

            .day {
                position: relative;
                min-height: 2.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: default;
            }

            .empty {
                background: #f8fafc;
                color: #94a3b8;
            }

            .faded {
                opacity: 0.5;
            }

            .weekend {
                background: #fef2f2;
                color: #dc2626;
            }

            .friday {
                background: #f0fdf4;
                color: #16a34a;
            }

            .cuti {
                background-color: #fee2e2;
                color: #dc2626;
                font-weight: 600;
            }

            .strikethrough {
                position: relative;
                color: #a16207;
                background-color: #fef3c7;
            }

            .strikethrough::before {
                content: '';
                position: absolute;
                left: 30%;
                right: 30%;
                top: 50%;
                height: 1px;
                background-color: #a16207;
            }

            .double-strikethrough {
                position: relative;
                color: #065f46;
                background-color: #d1fae5;
            }

            .double-strikethrough::before,
            .double-strikethrough::after {
                content: '';
                position: absolute;
                height: 1px;
                background: #065f46;
                left: 30%;
                right: 30%;
            }

            .double-strikethrough::before {
                top: 45%;
            }

            .double-strikethrough::after {
                top: 55%;
            }

            .cross-strikethrough {
                position: relative;
                color: #991b1b;
                background-color: #fecaca;
            }

            .cross-strikethrough::before,
            .cross-strikethrough::after {
                content: '';
                position: absolute;
                height: 1px;
                background: #991b1b;
                left: 35%;
                right: 35%;
                top: 50%;
            }

            .cross-strikethrough::before {
                transform: rotate(45deg);
            }

            .cross-strikethrough::after {
                transform: rotate(-45deg);
            }

            @media (max-width: 48rem) {
                .calendar-container {
                    transform: scale(0.85);
                    transform-origin: top center;
                }
            }
        </style>

        <div class="max-w-6xl px-4 py-8 mx-auto">
            <!-- Page Header -->
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold tracking-wide uppercase sm:text-3xl text-slate-800">Riwayat Kehadiran Saya</h1>
                <div class="w-24 h-1 mx-auto mt-2 rounded-full bg-amber-500"></div>
            </div>

            <!-- Calendar Section -->
            <div class="flex justify-center mb-10">
                <div class="calendar-wrapper">
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <form method="GET">
                                <input type="hidden" name="search" value="{{ $filter ? Carbon\Carbon::parse($filter)->subMonth(1)->format('Y-m') : Carbon\Carbon::now()->subMonth(1)->format('Y-m') }}">
                                <button type="submit" class="p-1 transition-colors rounded-lg hover:bg-slate-100">
                                    <i class="text-xl ri-arrow-left-s-line"></i>
                                </button>
                            </form>

                            <h3>{{ \Carbon\Carbon::create($year, $month)->isoFormat('MMMM Y') }}</h3>

                            <form method="GET">
                                <input type="hidden" name="search" value="{{ $filter ? Carbon\Carbon::parse($filter)->addMonth(1)->format('Y-m') : Carbon\Carbon::now()->addMonth(1)->format('Y-m') }}">
                                <button type="submit" class="p-1 transition-colors rounded-lg hover:bg-slate-100">
                                    <i class="text-xl ri-arrow-right-s-line"></i>
                                </button>
                            </form>
                        </div>

                        <div class="calendar-grid">
                            <div class="day-header">Min</div>
                            <div class="day-header">Sen</div>
                            <div class="day-header">Sel</div>
                            <div class="day-header">Rab</div>
                            <div class="day-header">Kam</div>
                            <div class="day-header">Jum</div>
                            <div class="day-header">Sab</div>

                            @php
                                $day = 1;
                                $prevMonth = \Carbon\Carbon::create($year, $month, 1)->subMonth();
                                $nextMonth = \Carbon\Carbon::create($year, $month, 1)->addMonth();
                                $daysInPrevMonth = $prevMonth->daysInMonth;
                                $weeks = ceil(($startOfMonth + $daysInMonth) / 7);

                                $datAbsen = $absen
                                    ->filter(function ($a) use ($month, $year) {
                                        $date = \Carbon\Carbon::parse($a->created_at);
                                        return $date->month == $month && $date->year == $year;
                                    })
                                    ->keyBy(fn($a) => \Carbon\Carbon::parse($a->created_at)->day);
                                $datAbsenPrev = $absen
                                    ->filter(function ($a) use ($prevMonth) {
                                        $date = \Carbon\Carbon::parse($a->created_at);
                                        return $date->month == $prevMonth->month && $date->year == $prevMonth->year;
                                    })
                                    ->keyBy(fn($a) => \Carbon\Carbon::parse($a->created_at)->day);
                                $datAbsenNext = $absen
                                    ->filter(function ($a) use ($nextMonth) {
                                        $date = \Carbon\Carbon::parse($a->created_at);
                                        return $date->month == $nextMonth->month && $date->year == $nextMonth->year;
                                    })
                                    ->keyBy(fn($a) => \Carbon\Carbon::parse($a->created_at)->day);

                                $holidays = $harLib->keyBy(fn($h) => \Carbon\Carbon::parse($h['tanggal'])->format('Y-m-d'));
                            @endphp

                            @for ($week = 0; $week < $weeks; $week++)
                                @for ($dow = 0; $dow < 7; $dow++)
                                    @php
                                        $classes = '';
                                        $classPrev = '';
                                        $classNext = '';

                                        if ($week == 0 && $dow < $startOfMonth) {
                                            $prevDay = $daysInPrevMonth - ($startOfMonth - $dow - 1);
                                            $prevDate = \Carbon\Carbon::create(
                                                $prevMonth->year,
                                                $prevMonth->month,
                                                $prevDay,
                                            )->format('Y-m-d');
                                            $holidayP = $holidays->get($prevDate);
                                            $attPrev = $datAbsenPrev[$prevDay] ?? null;

                                            $classPrev = $dow == 0 ? 'weekend' : ($dow == 5 ? 'friday' : '');
                                            if ($holidayP) {
                                                $classPrev .= ' cuti';
                                            }
                                            if ($attPrev) {
                                                if (
                                                    (!is_null($attPrev->absensi_type_masuk) &&
                                                        is_null($attPrev->absensi_type_pulang)) ||
                                                    $attPrev->keterangan == 'telat'
                                                ) {
                                                    $classPrev .= ' strikethrough';
                                                } elseif (
                                                    !is_null($attPrev->absensi_type_masuk) &&
                                                    !is_null($attPrev->absensi_type_pulang)
                                                ) {
                                                    $classPrev .= ' double-strikethrough';
                                                }
                                            }
                                        } elseif ($day > $daysInMonth) {
                                            $cellIndex = $week * 7 + $dow;
                                            $nextDay = $cellIndex + 1 - $daysInMonth - $startOfMonth;
                                            $nextDate = \Carbon\Carbon::create(
                                                $nextMonth->year,
                                                $nextMonth->month,
                                                $nextDay,
                                            )->format('Y-m-d');

                                            $holidayN = $holidays->get($nextDate);
                                            $attNext = $datAbsenNext[$nextDay] ?? null;

                                            $classNext = $dow == 0 ? 'weekend' : ($dow == 5 ? 'friday' : '');
                                            if ($holidayN) {
                                                $classNext .= ' cuti';
                                            }
                                            if ($attNext) {
                                                if (
                                                    (!is_null($attNext->absensi_type_masuk) &&
                                                        is_null($attNext->absensi_type_pulang)) ||
                                                    $attNext->keterangan == 'telat'
                                                ) {
                                                    $classNext .= ' strikethrough';
                                                } elseif (
                                                    !is_null($attNext->absensi_type_masuk) &&
                                                    !is_null($attNext->absensi_type_pulang)
                                                ) {
                                                    $classNext .= ' double-strikethrough';
                                                }
                                            }
                                        } else {
                                            $currentDate = Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
                                            $att = $datAbsen[$day] ?? null;

                                            if ($dow == 0) {
                                                $classes .= 'weekend ';
                                            }
                                            if ($dow == 5) {
                                                $classes .= 'friday ';
                                            }

                                            if ($att) {
                                                if (
                                                    (!is_null($att->absensi_type_masuk) &&
                                                        is_null($att->absensi_type_pulang)) ||
                                                    $att->keterangan == 'telat'
                                                ) {
                                                    $classes .= 'strikethrough ';
                                                } elseif (
                                                    !is_null($att->absensi_type_masuk) &&
                                                    !is_null($att->absensi_type_pulang)
                                                ) {
                                                    $classes .= 'double-strikethrough ';
                                                } elseif (
                                                    !is_null($att->absensi_type_masuk) &&
                                                    is_null($att->absensi_type_pulang) &&
                                                    $att->created_at->format('Ymd') == Carbon\Carbon::now()->format('Ymd')
                                                ) {
                                                    $classes .= 'cross-strikethrough ';
                                                }
                                            }

                                            // Holiday Check
                                            $holiday = $holidays->get($currentDate);
                                            if ($holiday) {
                                                $classes .= 'cuti ';
                                            }
                                        }
                                    @endphp

                                    {{-- Render Previous Month Days --}}
                                    @if ($week == 0 && $dow < $startOfMonth)
                                        <div class="day faded {{ $classPrev }}">
                                            {{ $prevDay }}
                                        </div>

                                        {{-- Render Next Month Days --}}
                                    @elseif ($day > $daysInMonth)
                                        <div class="day faded {{ $classNext }}">
                                            {{ $nextDay }}
                                        </div>
                                        @php $day++ @endphp

                                        {{-- Render Current Month Days --}}
                                    @else
                                        <div class="day {{ $classes }}">
                                            {{ $day }}
                                        </div>
                                        @php $day++ @endphp
                                    @endif
                                @endfor
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="flex justify-center mb-8">
                <div class="w-full max-w-md p-4 bg-white rounded-lg shadow">
                    <h3 class="mb-3 text-sm font-semibold text-gray-800">Keterangan:</h3>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 rounded double-strikethrough">
                                <i class="text-xs ri-infinity-line"></i>
                            </div>
                            <p class="ml-3 text-sm text-gray-700">: Berhasil Absen</p>
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 rounded strikethrough">
                                <i class="text-xs ri-infinity-line"></i>
                            </div>
                            <p class="ml-3 text-sm text-gray-700">: Belum Absen Pulang / Telat</p>
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 rounded cross-strikethrough">
                                <i class="text-xs ri-infinity-line"></i>
                            </div>
                            <p class="ml-3 text-sm text-gray-700">: Tidak Absen Pulang</p>
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-8 h-8 rounded cuti">
                                <i class="text-xs ri-infinity-line"></i>
                            </div>
                            <p class="ml-3 text-sm text-gray-700">: Tgl Merah</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="mb-6 overflow-hidden bg-white shadow-md rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">#</th>
                                @if (Auth::user()->name != 'DIREKSI')
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Shift</th>
                                @endif
                                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Absen Masuk</th>
                                @if (Auth::user()->kerjasama_id == 1)
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Absen Siang</th>
                                @endif
                                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Absen Keluar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ Auth::user()->jabatan->code_jabatan == 'SPV-W' ? 'hidden' : '' }}">Telat/Tidak</th>
                                <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $no = 1;
                                $totalPointsPerUser = [];
                                $userId = Auth::user()->id;
                            @endphp
                            @foreach ($absen as $arr)
                                @php
                                    if (
                                        $arr->user_id == $userId &&
                                        $arr->point_id != null &&
                                        $arr->point->client_id == Auth::user()->kerjasama->client_id
                                    ) {
                                        $point = intval($arr->point->sac_point);
                                        if (isset($totalPointsPerUser[$userId])) {
                                            $totalPointsPerUser[$userId] += $point;
                                        } else {
                                            $totalPointsPerUser[$userId] = $point;
                                        }
                                    }
                                @endphp

                                @if (Auth::user()->id != $arr->user_id)
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="mb-2 text-3xl text-gray-300 ri-inbox-line"></i>
                                                <p>Tidak Ada History Absen</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @break
                                @else
                                    <tr class="{{ Auth::user()->kerjasama_id == 1 ? ($arr->point_id == 1 ? 'bg-green-50' : ($arr->point_id == 2 ? 'bg-amber-50' : 'bg-red-50')) : '' }}">
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $no++ }}.</td>
                                        @if (Auth::user()->name != 'DIREKSI')
                                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $arr->shift?->shift_name }}</td>
                                        @endif
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $arr->tanggal_absen }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $arr->absensi_type_masuk }}</td>
                                        @if (Auth::user()->kerjasama_id == 1)
                                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                                {!! $arr->dzuhur ? 'Sudah Absen' : '<span class="font-semibold text-red-500">Belum Absen</span>' !!}
                                            </td>
                                        @endif
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                            {!! $arr->absensi_type_pulang == null
                                                ? '<span class="font-semibold text-red-500">Belum Absen Pulang</span>'
                                                : $arr->absensi_type_pulang !!}
                                        </td>

                                        @if (Auth::user()->name != 'DIREKSI')
                                            @php
                                                $jam_abs = $arr->absensi_type_masuk;
                                                $jam_abslen = strlen($jam_abs);

                                                $jam_str = $arr?->shift?->jam_start;
                                                $jam_strlen = strlen($jam_str);

                                                $jAbs = Carbon\Carbon::createFromFormat(
                                                    $jam_abslen == 5 ? 'H:i' : 'H:i:s',
                                                    $jam_abs,
                                                );
                                                $jJad = Carbon\Carbon::createFromFormat(
                                                    $jam_strlen == 5 ? 'H:i' : 'H:i:s',
                                                    $jam_str ? $jam_str : '00:00:00',
                                                );

                                                if (Auth::user()->kerjasama_id == 1) {
                                                    $jam_strlen == 5 ? $jJad : $jJad->addSeconds(59);
                                                }

                                                $jDiff = $jAbs->diff($jJad);

                                                $diffHasil = '';
                                                if ($jDiff->h > 0) {
                                                    $diffHasil .= $jDiff->format('%h Jam ');
                                                }
                                                if ($jDiff->i > 0) {
                                                    $diffHasil .= $jDiff->format('%i Menit ');
                                                }
                                                if ($jDiff->s > 0 && $jDiff->h == 0 && $jDiff->i == 0) {
                                                    $diffHasil .= $jDiff->format('%s Detik');
                                                }

                                                $diffHasil = trim($diffHasil);
                                            @endphp
                                            <td class="px-4 py-3 whitespace-nowrap text-sm {{ Auth::user()->jabatan->code_jabatan == 'SPV-W' ? 'hidden' : '' }}">
                                                @if (Auth::user()->kerjasama_id == 11 && $arr->created_at->format('Y-m-d') > '2025-06-03')
                                                    @if (
                                                        $arr->shift?->jam_start &&
                                                            Carbon\Carbon::parse($arr->absensi_type_masuk)->gt(Carbon\Carbon::parse($arr->shift->jam_start)))
                                                        <span class="font-medium text-red-600">Telat {{ $diffHasil }}</span>
                                                    @else
                                                        <span class="text-gray-600">Tidak</span>
                                                    @endif
                                                @else
                                                    {!! $arr->absensi_type_masuk > $arr?->shift?->jam_start
                                                        ? '<span class="font-medium text-red-600">' . 'Telat ' . $diffHasil . '</span>'
                                                        : '<span class="text-gray-600">Tidak</span>' !!}
                                                @endif
                                            </td>

                                            @php
                                                $badgeClass = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
                                                if ($arr->keterangan == 'masuk' && $arr->absensi_type_pulang !== null) {
                                                    if (
                                                        Auth::user()->kerjasama_id == 11 &&
                                                        $arr->created_at->format('Y-m-d') > '2025-06-03' &&
                                                        Carbon\Carbon::parse($arr->absensi_type_masuk)->gt(
                                                            Carbon\Carbon::parse($arr->shift->jam_start),
                                                        )
                                                    ) {
                                                        $badgeClass .= ' bg-red-100 text-red-800';
                                                        $label = 'Telat';
                                                    } else {
                                                        $badgeClass .= ' bg-green-100 text-green-800';
                                                        $label = $arr->keterangan;
                                                    }
                                                } elseif ($arr->keterangan == 'izin') {
                                                    $badgeClass .= ' bg-yellow-100 text-yellow-800';
                                                    $label = $arr->keterangan;
                                                } else {
                                                    $badgeClass .= ' bg-red-100 text-red-800';
                                                    $label = 'Tidak Masuk';
                                                }
                                            @endphp

                                            <td class="px-4 py-3 text-sm whitespace-nowrap">
                                                <span class="{{ $badgeClass }}">{{ $label }}</span>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Points Display -->
            @if (Auth::user()->kerjasama_id == 1)
                <div class="flex justify-center mb-6">
                    <div class="px-5 py-3 bg-green-600 rounded-lg shadow">
                        <span class="font-semibold text-white">
                            @if (auth()->user()->name == 'MEI' || auth()->user()->name == 'ZAKY')
                                Poin Anda {{ $absen->whereIn('user_id', ['424', '423'])->count() }}
                            @else
                                {{ !empty($totalPointsPerUser) ? 'Point Anda Sekarang ' . toRupiah(array_sum($totalPointsPerUser)) : '~ Point Belum Diperoleh ~' }}
                            @endif
                        </span>
                    </div>
                </div>
            @endif

            <!-- Modal for Attendance Percentage -->
            <div x-data="{ openModal: false }" class="flex justify-center mb-6">
                <button @click="openModal = true" class="px-4 py-2 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Lihat Persentase Kehadiran
                </button>

                <div x-show="openModal" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="openModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <div x-show="openModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900">
                                            Persentase Kehadiran
                                        </h3>

                                        @if ($status == 'BAIK')
                                            <div class="p-4 mb-4 bg-green-100 rounded-lg">
                                                <div class="text-center">
                                                    <span class="font-semibold text-green-800">
                                                        {{ $persentase > 100 ? '100%' : round($persentase) . '%' }}
                                                    </span>
                                                    <div class="mt-2">
                                                        <span class="px-2 py-1 font-medium text-red-600 bg-white rounded">
                                                            Telat {{ $telat }} kali
                                                        </span>
                                                    </div>
                                                    <div class="mt-2 font-semibold text-green-800">
                                                        Status Kehadiran {{ $status }}
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($status == 'CUKUP')
                                            <div class="p-4 mb-4 rounded-lg bg-amber-100">
                                                <div class="text-center">
                                                    <span class="font-semibold text-amber-800">
                                                        {{ round($persentase) . '%' }}
                                                    </span>
                                                    <div class="mt-2">
                                                        <span class="px-2 py-1 font-medium text-red-600 bg-white rounded">
                                                            Telat {{ $telat }} kali
                                                        </span>
                                                    </div>
                                                    <div class="mt-2 font-semibold text-amber-800">
                                                        Status Kehadiran {{ $status }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-4 mb-4 bg-red-100 rounded-lg">
                                                <div class="text-center">
                                                    <span class="font-semibold text-red-800">
                                                        {{ round($persentase) . '%' }}
                                                    </span>
                                                    <div class="mt-2">
                                                        <span class="px-2 py-1 font-medium text-red-600 bg-white rounded">
                                                            Telat {{ $telat }} kali
                                                        </span>
                                                    </div>
                                                    <div class="mt-2 font-semibold text-red-800">
                                                        Status Kehadiran {{ $status }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button @click="openModal = false" type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mb-6">
                <div id="pag-1">
                    {{ $absen->links() }}
                </div>
            </div>

            <!-- Back Button -->
            <div class="flex justify-center">
                <a href="{{ route('dashboard.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Kembali
                </a>
            </div>
        </div>
    </x-main-div>
</x-app-layout>
