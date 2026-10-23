<x-app-layout>
    @section('title', 'Kalender Checkpoint')
    <div class="w-full max-w-5xl px-2 mx-auto space-y-4 sm:px-3 lg:px-4">
        <section class="mt-10 p-4 bg-white border border-gray-100 shadow-sm rounded-2xl sm:p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span
                        class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-sky-50 text-xl text-sky-600"><i
                            class="ri-user-line"></i></span>
                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-bold capitalize text-gray-900 sm:text-xl">
                            {{ strtolower($employee->nama_lengkap) }}</h1>
                        <p class="mt-0.5 truncate text-xs text-gray-500">{{ $employee->jabatan->nama_jabatan ?? '-' }}
                            &middot; {{ $employee->divisi->name ?? '-' }} &middot;
                            {{ $employee->kerjasama->client->name ?? '-' }}</p>
                    </div>
                </div>
                <a href="{{ route('direksi.cp.index') }}"
                    class="inline-flex h-10 items-center justify-center rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50"><i
                        class="mr-1.5 ri-arrow-left-line"></i> Kembali</a>
            </div>
        </section>

        @php
            // The three numbers the recap always reports, in display order. The
            // labels live here instead of the service so the service stays a
            // plain data source.
            $recapCards = [
                'attendance' => [
                    'label' => 'Absensi Kehadiran',
                    'unit' => 'hari kerja',
                    'icon' => 'ri-fingerprint-line',
                    'chip' => 'bg-sky-50 text-sky-600',
                    'bar' => 'bg-sky-500',
                ],
                'checkpoint' => [
                    'label' => 'Kinerja Harian',
                    'unit' => 'hari kerja',
                    'icon' => 'ri-task-line',
                    'chip' => 'bg-emerald-50 text-emerald-600',
                    'bar' => 'bg-emerald-500',
                ],
                'work_order' => [
                    'label' => 'Perintah Kerja',
                    'unit' => 'perintah kerja',
                    'icon' => 'ri-file-list-3-line',
                    'chip' => 'bg-purple-50 text-purple-600',
                    'bar' => 'bg-purple-500',
                ],
            ];
        @endphp

        <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5" data-performance-recap
            @if (! $recap['visible']) data-recap-locked @endif>

            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-indigo-50 text-indigo-600">
                        <i class="ri-bar-chart-2-line"></i>
                    </span>
                    Rekap Persentase
                </h2>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500">{{ $start->translatedFormat('F Y') }}</span>
                    @if ($recap['visible'])
                        <span
                            class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Terbuka</span>
                    @else
                        <span
                            class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700">Terkunci</span>
                    @endif
                </div>
            </div>

            @if ($recap['visible'])

                <div class="mt-3 grid gap-2.5 sm:grid-cols-3">
                    @foreach ($recapCards as $key => $card)
                        @php
                            $metric = $recap['metrics'][$key];
                            $percent = $metric['percent'];
                        @endphp

                        <div class="rounded-xl border border-gray-100 bg-gray-50/60 p-3" data-recap-metric="{{ $key }}"
                            @if (! is_null($percent)) data-recap-percent="{{ $percent }}" @endif>

                            <div class="flex items-center gap-2">
                                <span
                                    class="grid h-8 w-8 shrink-0 place-items-center rounded-lg {{ $card['chip'] }} text-base">
                                    <i class="{{ $card['icon'] }}"></i>
                                </span>
                                <p class="min-w-0 text-xs font-semibold leading-tight text-gray-600">
                                    {{ $card['label'] }}</p>
                            </div>

                            <p class="mt-3 text-2xl font-bold tabular-nums text-gray-900">
                                {{ is_null($percent) ? '—' : $percent . '%' }}
                            </p>

                            <p class="mt-0.5 text-[11px] text-gray-500">
                                @if (is_null($percent))
                                    Belum ada perintah kerja
                                @else
                                    {{ $metric['achieved'] }} dari {{ $metric['total'] }} {{ $card['unit'] }}
                                @endif
                            </p>

                            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-gray-200">
                                <div class="h-full rounded-full {{ $card['bar'] }}"
                                    style="width: {{ $percent ?? 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @php
                    $excludedHolidays = collect($recap['excludedHolidays'] ?? [])
                        ->map(
                            fn ($name, $date) => \Carbon\Carbon::parse($date)->translatedFormat('d M Y') .
                                (filled($name) ? ' (' . $name . ')' : ''),
                        )
                        ->implode(', ');
                    $holidayTooltip =
                        $excludedHolidays === ''
                            ? 'Tidak ada hari libur nasional pada periode ini.'
                            : 'Hari libur nasional yang dikecualikan: ' . $excludedHolidays;
                @endphp

                <p class="mt-2.5 flex items-center gap-1.5 text-[10px] leading-relaxed text-gray-400"
                    title="{{ $holidayTooltip }}">
                    <i class="ri-information-line text-[13px]"></i>
                    <span
                        class="cursor-help underline decoration-gray-300 decoration-dotted underline-offset-2">
                        Hari libur nasional dan akhir pekan tidak dihitung sebagai hari kerja.
                    </span>
                </p>

            @else

                <div
                    class="mt-3 flex items-center gap-2.5 rounded-xl border border-amber-100 bg-amber-50 px-3 py-2.5 text-xs text-amber-800">
                    <i class="ri-lock-2-line text-base"></i>
                    <p>
                        Rekap hanya bisa dilihat setiap akhir pekan (Sabtu &amp; Minggu) dan 3 hari terakhir bulan.
                        Dibuka lagi <span
                            class="font-semibold">{{ $recap['nextVisibleAt']->translatedFormat('l, d F Y') }}</span>.
                    </p>
                </div>

            @endif

        </section>

        @php
            // Days that still hold evidence direksi has not reviewed. The
            // calendar rows already carry their per-day status counts, so this
            // costs no extra query — it just makes those days findable without
            // scanning the whole grid for the amber marker.
            $pendingDays = $calendar
                ->filter(fn ($item) => $item['hasData'] && $item['recordId'] && $item['counts']['process'] > 0)
                ->values();
            $pendingTotal = (int) $pendingDays->sum(fn ($item) => $item['counts']['process']);
        @endphp

        <section
            class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5" data-pending-days>

            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-amber-50 text-amber-600">
                        <i class="ri-time-line"></i>
                    </span>
                    Tanggal Belum Di Approve
                    @if ($pendingDays->isNotEmpty())
                        <span
                            class="rounded-full bg-amber-500 px-2 py-0.5 text-[11px] font-bold text-white">{{ $pendingTotal }}</span>
                    @endif
                </h2>
                <span class="text-xs font-semibold text-gray-500">{{ $start->translatedFormat('F Y') }}</span>
            </div>

            @if ($pendingDays->isEmpty())
                <p
                    class="mt-3 flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2.5 text-xs text-emerald-700">
                    <i class="ri-checkbox-circle-line text-base"></i>
                    {{ $totalRecords > 0
                        ? 'Semua bukti pekerjaan bulan ini sudah di-approve Direksi.'
                        : 'Belum ada bukti pekerjaan yang diisi bulan ini.' }}
                </p>
            @else
                <p class="mt-2 text-xs text-gray-500">
                    {{ $pendingTotal }} bukti pekerjaan pada {{ $pendingDays->count() }} tanggal belum
                    di-approve Direksi. Klik tanggal untuk meninjau.
                </p>

                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($pendingDays as $item)
                        <a href="{{ route('direksi.cp.history.show', $item['recordId']) }}"
                            data-pending-date="{{ $item['date']->toDateString() }}"
                            data-pending-date-count="{{ $item['counts']['process'] }}"
                            title="Tinjau bukti {{ $item['date']->translatedFormat('l, d F Y') }}"
                            class="group inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-semibold text-amber-800 transition duration-300 hover:border-amber-300 hover:bg-amber-100 active:scale-[.98]">
                            <span
                                class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-amber-500 text-[11px] font-bold tabular-nums text-white">{{ $item['date']->day }}</span>
                            <span>{{ $item['date']->translatedFormat('d M') }}</span>
                            <span
                                class="rounded-md bg-white px-1.5 py-0.5 text-[10px] font-bold tabular-nums text-amber-700">{{ $item['counts']['process'] }}
                                bukti</span>
                            <i
                                class="ri-arrow-right-s-line text-sm text-amber-500 transition group-hover:translate-x-0.5"></i>
                        </a>
                    @endforeach
                </div>
            @endif

        </section>

        <section
    class="w-full rounded-2xl border border-gray-100 bg-white p-2.5 shadow-sm sm:p-4 md:p-5">

    {{-- Header --}}
    <div class="mb-3 flex items-center justify-between gap-2 sm:mb-4">

        {{-- Previous --}}
        <a href="{{ route('direksi.cp.calendar', [
            'user' => $employee->id,
            'month' => $previousMonth,
            'type' => $type
        ]) }}"
            class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-gray-200 text-gray-500 transition hover:border-purple-300 hover:text-purple-600 sm:h-10 sm:w-10"
            aria-label="Bulan sebelumnya">

            <i class="text-lg ri-arrow-left-s-line sm:text-xl"></i>
        </a>


        {{-- Month --}}
        <div class="min-w-0 flex-1 text-center">

            <h2 class="truncate text-base font-bold text-gray-900 sm:text-lg">
                {{ $start->translatedFormat('F Y') }}
            </h2>

            <p class="truncate text-[10px] text-gray-500 sm:text-xs">
                {{ $totalRecords }} Bukti Pekerjaan
            </p>

        </div>


        {{-- Next --}}
        <a href="{{ route('direksi.cp.calendar', [
            'user' => $employee->id,
            'month' => $nextMonth,
            'type' => $type
        ]) }}"
            class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-gray-200 text-gray-500 transition hover:border-purple-300 hover:text-purple-600 sm:h-10 sm:w-10"
            aria-label="Bulan berikutnya">

            <i class="text-lg ri-arrow-right-s-line sm:text-xl"></i>
        </a>

    </div>


    @php
        $daysBefore = $start->dayOfWeekIso - 1;
        $daysInMonth = $start->daysInMonth;
        $daysAfter = (7 - (($daysBefore + $daysInMonth) % 7)) % 7;
        $nextStart = $start->copy()->addMonth()->startOfMonth();
    @endphp


    {{-- Day Header --}}
    <div
        class="grid grid-cols-7 gap-1 pb-2 text-center text-[8px] font-semibold uppercase tracking-[.06em] text-gray-400 sm:gap-1.5 sm:pb-3 sm:text-[10px] sm:tracking-[.1em] md:text-[11px]">

        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)

            <div class="min-w-0 truncate">
                {{ $day }}
            </div>

        @endforeach

    </div>


    {{-- Calendar --}}
    <div class="grid grid-cols-7 gap-1 sm:gap-1.5 p-2">


        {{-- Previous Month --}}
        @for ($i = $daysBefore; $i > 0; $i--)

            @php
                $date = $start->copy()->subDays($i);
            @endphp

            <span
                class="flex min-h-[48px] min-w-0 cursor-not-allowed flex-col justify-between overflow-hidden rounded-lg border border-gray-100 bg-gray-50/60 p-1.5 text-gray-300 sm:min-h-[64px] sm:rounded-xl sm:p-2 md:min-h-[74px] md:p-2.5"
                aria-disabled="true">

                <span class="overflow-hidden text-[10px] leading-none sm:text-xs md:text-sm">
                    {{ $date->day }}
                </span>

            </span>

        @endfor


        {{-- Current Month --}}
        @foreach ($calendar as $index => $item)

            @php
                $date = $item['date'];
                $isHoliday = $holidays->has($date->toDateString());
                $holidayName = $holidays->get($date->toDateString());
                $cellTitle = $date->translatedFormat('l, d F Y');
                if ($isHoliday && filled($holidayName)) {
                    $cellTitle .= ' — ' . $holidayName;
                }
                // "Approved" here means the day has work and nothing left pending.
                $approved = $item['hasData'] && $item['counts']['process'] === 0;
                if ($approved) {
                    $state = 'border-transparent bg-sky-500 text-white hover:bg-sky-600';
                } elseif ($item['hasData']) {
                    $state = 'border-transparent bg-emerald-500 text-white hover:bg-emerald-600';
                } elseif ($isHoliday) {
                    $state = 'border-rose-200 bg-rose-100 text-rose-600';
                } elseif ($date->isWeekend()) {
                    $state = 'border-rose-100 bg-rose-50 text-rose-400';
                } else {
                    $state = 'border-gray-200 bg-white text-gray-700 hover:border-purple-300';
                }
            @endphp


            @if ($item['hasData'] && $item['recordId'])

                <a
                    href="{{ route('direksi.cp.history.show', $item['recordId']) }}"

                    style="animation-delay: {{ min($index * 12, 320) }}ms"

                    title="{{ $cellTitle }}"

                    @if ($isHoliday) data-calendar-holiday="{{ $date->toDateString() }}" @endif

                    class="reveal-cell group relative flex min-h-[48px] min-w-0 flex-col justify-between overflow-hidden rounded-lg border p-1.5 font-medium transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_-12px_rgba(15,23,42,.22)] sm:min-h-[64px] sm:rounded-xl sm:p-2 md:min-h-[74px] md:p-2.5
                    {{ $state }}
                    {{ $date->isToday() ? 'ring-2 ring-sky-400 ring-offset-1 sm:ring-offset-2' : '' }}">

                    <span class="flex items-center justify-between gap-1 overflow-hidden">
                        <span class="truncate text-[10px] leading-none sm:text-xs md:text-sm">
                            {{ $date->day }}
                        </span>

                        @if ($isHoliday)
                            <span
                                class="grid h-3 w-3 shrink-0 place-items-center rounded-full bg-white/95 shadow-sm sm:h-3.5 sm:w-3.5"
                                title="{{ filled($holidayName) ? 'Libur nasional: ' . $holidayName : 'Libur nasional' }}">
                                <i class="ri-flag-2-fill text-[7px] text-rose-500 sm:text-[8px]"></i>
                            </span>
                        @endif
                    </span>

                    <x-checkpoint-status-row :counts="$item['counts']" :has-data="$item['hasData']" size="md" />
                </a>


            @else

                <span
                    title="{{ $cellTitle }}"

                    @if ($isHoliday) data-calendar-holiday="{{ $date->toDateString() }}" @endif

                    class="flex min-h-[48px] min-w-0 flex-col justify-between overflow-hidden rounded-lg border p-1.5 font-medium sm:min-h-[64px] sm:rounded-xl sm:p-2 md:min-h-[74px] md:p-2.5
                    {{ $state }}
                    {{ $date->isToday() ? 'ring-2 ring-sky-400 ring-offset-1 sm:ring-offset-2' : '' }}">

                    <span class="flex items-center justify-between gap-1 overflow-hidden">
                        <span class="truncate text-[10px] leading-none sm:text-xs md:text-sm">
                            {{ $date->day }}
                        </span>

                        @if ($isHoliday)
                            <span
                                class="grid h-3 w-3 shrink-0 place-items-center rounded-full bg-white/95 shadow-sm sm:h-3.5 sm:w-3.5"
                                title="{{ filled($holidayName) ? 'Libur nasional: ' . $holidayName : 'Libur nasional' }}">
                                <i class="ri-flag-2-fill text-[7px] text-rose-500 sm:text-[8px]"></i>
                            </span>
                        @endif
                    </span>

                    @if ($isHoliday)

                        <span
                            class="truncate text-[7px] font-semibold uppercase leading-tight text-rose-500 sm:text-[9px] md:text-[10px]">
                            Libur Nasional
                        </span>

                    @elseif ($date->isWeekend())

                        <span
                            class="hidden truncate text-[8px] font-semibold uppercase opacity-85 sm:block sm:text-[9px] md:text-[10px]">
                            Libur
                        </span>

                    @endif

                </span>

            @endif

        @endforeach


        {{-- Next Month --}}
        @for ($i = 1; $i <= $daysAfter; $i++)

            @php
                $date = $nextStart->copy()->addDays($i - 1);
            @endphp

            <span
                class="flex min-h-[48px] min-w-0 cursor-not-allowed flex-col justify-between overflow-hidden rounded-lg border border-gray-100 bg-gray-50/60 p-1.5 text-gray-300 sm:min-h-[64px] sm:rounded-xl sm:p-2 md:min-h-[74px] md:p-2.5"
                aria-disabled="true">

                <span class="overflow-hidden text-[10px] leading-none sm:text-xs md:text-sm">
                    {{ $date->day }}
                </span>

            </span>

        @endfor

    </div>


    {{-- Legend --}}
    <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] text-gray-500 sm:mt-5 sm:gap-x-5 sm:text-xs w-full">

            <span class="flex items-center gap-1.5 sm:gap-2">
                <span class="m-1 h-2.5 w-2.5 shrink-0 rounded-[3px] bg-sky-500 sm:h-3 sm:w-3"></span>
                Data Sudah Di Verifikasi Semua
            </span>

            <span class="flex items-center gap-1.5 sm:gap-2">
                <span class="m-1 h-2.5 w-2.5 shrink-0 rounded-[3px] bg-emerald-500 sm:h-3 sm:w-3"></span>
                Ada Checkpoint / Ada Yang Belum Di Verifikasi
            </span>

            <span class="flex items-center gap-1.5 sm:gap-2">
                <span class="m-1 h-2.5 w-2.5 shrink-0 rounded-[3px] bg-rose-50 ring-1 ring-rose-100 sm:h-3 sm:w-3"></span>
                Sabtu &amp; Minggu
            </span>

            <span class="flex items-center gap-1.5 sm:gap-2">
                <i class="ri-flag-2-fill text-[11px] text-rose-500 sm:text-[12px]"></i>
                Libur nasional
            </span>

            <span class="mx-1 hidden h-4 w-px bg-gray-200 sm:block"></span>

            <span class="flex items-center gap-1.5 sm:gap-2">
                <i class="ri-checkbox-circle-fill text-[13px] text-sky-600"></i>
                Disetujui
            </span>

            <span class="flex items-center gap-1.5 sm:gap-2">
                <i class="ri-close-circle-fill text-[13px] text-rose-600"></i>
                Ditolak
            </span>

            <span class="flex items-center gap-1.5 sm:gap-2">
                <i class="ri-time-line text-[13px] text-amber-600"></i>
                Menunggu verifikasi
            </span>

        </div>

    </section>
    </div>
</x-app-layout>
