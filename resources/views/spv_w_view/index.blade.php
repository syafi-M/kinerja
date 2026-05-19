<x-app-layout>
    @php
        $spvwClientId = request('client_id', session('spvw.selected_client_id'));
    @endphp

    @php
        $selectedClientId = (int) ($selectedClientId ?? 0);
        $selectedClientName = $selectedClientId > 0
            ? optional(collect($clients ?? [])->firstWhere('id', $selectedClientId))->name
            : null;
        $appendClient = static fn(string $url) => $selectedClientId > 0
            ? $url . (str_contains($url, '?') ? '&' : '?') . 'client_id=' . $selectedClientId
            : $url;
        $rekapMenus = [
            [
                'key' => 'lembur',
                'title' => 'Lembur',
                'description' => 'Catat lembur personil dan pantau status pengajuannya.',
                'icon' => 'ri-time-line',
                'icon_bg' => 'bg-sky-100 ring-sky-200',
                'icon_color' => 'text-sky-700',
                'pengajuan_url' => $appendClient(route('spvw.overtime-application.create', array_filter(['client_id' => $spvwClientId]))),
                'riwayat_url' => $appendClient(route('spvw.overtime-application.history', array_filter(['client_id' => $spvwClientId]))),
            ],
            [
                'key' => 'personil-keluar',
                'title' => 'Personil Keluar',
                'description' => 'Ajukan personil keluar dari site atau area kerja.',
                'icon' => 'ri-user-unfollow-line',
                'icon_bg' => 'bg-amber-100 ring-amber-200',
                'icon_color' => 'text-amber-700',
                'pengajuan_url' => $appendClient(route('spvw.person-is-out.create', array_filter(['client_id' => $spvwClientId]))),
                'riwayat_url' => $appendClient(route('spvw.person-is-out.history', array_filter(['client_id' => $spvwClientId]))),
            ],
            [
                'key' => 'personil-masuk',
                'title' => 'Personil Masuk',
                'description' => 'Input personil baru atau perpindahan masuk.',
                'icon' => 'ri-user-follow-line',
                'icon_bg' => 'bg-emerald-100 ring-emerald-200',
                'icon_color' => 'text-emerald-700',
                'pengajuan_url' => $appendClient(route('spvw.person-in.index', array_filter(['client_id' => $spvwClientId]))),
                'riwayat_url' => $appendClient(route('spvw.person.in.history', array_filter(['client_id' => $spvwClientId]))),
            ],
            [
                'key' => 'potongan',
                'title' => 'Cutting',
                'description' => 'Kelola potongan performa dengan riwayat yang mudah dicek.',
                'icon' => 'ri-auction-fill',
                'icon_bg' => 'bg-red-100 ring-red-200',
                'icon_color' => 'text-red-700',
                'pengajuan_url' => $appendClient(route('spvw.cutting.index', array_filter(['client_id' => $spvwClientId]))),
                'riwayat_url' => $appendClient(route('spvw.cutting.history', array_filter(['client_id' => $spvwClientId]))),
            ],
            [
                'key' => 'lepas-training',
                'title' => 'Lepas Training',
                'description' => 'Rekap personil yang selesai masa training.',
                'icon' => 'ri-footprint-fill',
                'icon_bg' => 'bg-indigo-100 ring-indigo-200',
                'icon_color' => 'text-indigo-700',
                'pengajuan_url' => $appendClient(route('spvw.finished-training.index', array_filter(['client_id' => $spvwClientId]))),
                'riwayat_url' => $appendClient(route('spvw.finished-training.history', array_filter(['client_id' => $spvwClientId]))),
            ],
        ];

        $isSPV = in_array(strtoupper(auth()->user()->jabatan->code_jabatan ?? ''), ['SPV-W'], true);
        $dueDateLabel = ($dueDate ?? null) ? \Carbon\Carbon::parse($dueDate->due_date)->format('d M Y') : null;
        $codeJabatan = strtoupper(auth()->user()->jabatan->code_jabatan ?? '');
        $selectedMode = in_array(($selectedMode ?? 'pengajuan'), ['pengajuan', 'riwayat'], true) ? $selectedMode : 'pengajuan';
    @endphp

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush

    <x-main-div>
        <div class="w-full px-3 py-4 mx-auto max-w-7xl sm:px-5 lg:px-6">
            <div
                class="mb-4 overflow-hidden bg-white border rounded-lg shadow-sm border-white/60 ring-1 ring-slate-900/5">
                <div class="grid gap-0 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div class="p-4 sm:p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="max-w-2xl">
                                <div
                                    class="mb-2 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-medium text-slate-600">
                                    <i class="text-sm ri-stack-line text-sky-700"></i>
                                    Data Rekap
                                </div>
                                <h1 class="text-xl font-bold leading-tight text-slate-900 sm:text-2xl">
                                    Rekapitulasi
                                </h1>
                                <p class="mt-1 text-sm leading-5 text-slate-500">
                                    Buat pengajuan dan cek riwayat rekap dalam satu halaman.
                                </p>
                            </div>

                            <div class="hidden grid-cols-2 gap-2 sm:grid sm:w-52">
                                <div class="px-3 py-2 border rounded-lg border-slate-200 bg-slate-50">
                                    <p class="text-xs font-medium text-slate-500">Menu</p>
                                    <p class="text-xl font-semibold text-slate-900">{{ count($rekapMenus) }}</p>
                                </div>
                                <div class="px-3 py-2 border rounded-lg border-slate-200 bg-slate-50">
                                    <p class="text-xs font-medium text-slate-500">Akses</p>
                                    <p class="text-xl font-semibold text-slate-900">{{ $codeJabatan }}</p>
                                </div>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('spvw.rekap.index', array_filter(['client_id' => $spvwClientId])) }}" id="spvw-client-filter-form"
                            class="mt-4 rounded-xl border border-slate-200 bg-white/90 p-2.5 shadow-sm ring-1 ring-slate-900/5">
                            <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                                <div
                                    class="inline-flex items-center gap-2 px-3 text-xs font-semibold tracking-wide uppercase border rounded-lg min-h-11 border-slate-200 bg-slate-50 text-slate-600 sm:w-auto">
                                    <i class="text-sm ri-filter-3-line text-slate-500"></i>
                                    Mitra
                                </div>

                                <div class="flex-1 min-w-0">
                                    <label for="client_id" class="sr-only">Pilih client</label>
                                    <select name="client_id" id="client_id"
                                        onchange="window.handleSpvwClientFilterChange && window.handleSpvwClientFilterChange()"
                                        class="w-full text-sm font-medium bg-white rounded-lg min-h-11 border-slate-300 text-slate-700 focus:border-2 focus:border-sky-500 focus:ring-sky-500">
                                        <option value="">Semua client</option>
                                        @foreach (($clients ?? collect()) as $client)
                                            <option value="{{ $client->id }}"
                                                @selected($selectedClientId === (int) $client->id)>
                                                {{ capitalizeWords($client->name) ?: 'Client #' . $client->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
                                    <button type="submit"
                                        class="hidden min-h-11 items-center justify-center gap-1.5 rounded-lg bg-slate-900 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                        <i class="text-base ri-search-line"></i>
                                        <span>Pakai</span>
                                    </button>
                                    <a href="{{ route('spvw.rekap.index', ['reset_filter' => 1]) }}"
                                        class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                        <i class="text-base ri-refresh-line"></i>
                                        <span>Reset Filter</span>
                                    </a>
                                </div>
                            </div>

                            <div class="mt-2 flex min-h-7 items-center justify-between rounded-lg bg-slate-50 px-2.5 py-1.5">
                                <p class="text-[11px] text-slate-500">
                                    {{ $selectedClientId > 0 ? 'Filter client tetap aktif sampai logout.' : 'Mode cepat: filter langsung aktif saat client dipilih.' }}
                                </p>
                                @if ($selectedClientId > 0)
                                    <div class="flex items-center gap-1.5">
                                        <span
                                            class="hidden sm:inline-flex items-center gap-1 rounded-md bg-white px-2 py-0.5 text-[11px] font-medium text-slate-600 ring-1 ring-slate-200">
                                            <i class="ri-building-line"></i>
                                            {{ capitalizeWords($selectedClientName ?: 'Client #' . $selectedClientId) }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-1 rounded-md bg-sky-100 px-2 py-0.5 text-[11px] font-semibold text-sky-700">
                                            <i class="ri-check-line"></i>
                                            Aktif
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="p-4 border-t border-slate-200 bg-slate-50/80 lg:border-l lg:border-t-0 sm:p-5">
                        <div class="flex flex-col justify-center h-full gap-2">
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-flex items-center justify-center bg-white rounded-lg shadow-sm h-9 w-9 shrink-0 text-slate-700 ring-1 ring-slate-200">
                                    <i class="text-lg ri-calendar-check-line"></i>
                                </span>
                                <div>
                                    <p class="text-xs font-medium text-slate-500">Batas rekap
                                    </p>
                                    <p class="text-base font-semibold text-slate-900">{{ $dueDateLabel ?? 'Belum diatur' }}
                                    </p>
                                </div>
                            </div>

                            @if (($isExempted ?? false) === true)
                                <div
                                    class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                    <i class="ri-shield-check-line"></i>
                                    Pengecualian aktif permanen
                                </div>
                            @elseif (($isAfterDueDate ?? false) === true)
                                <div
                                    class="inline-flex w-fit items-center gap-2 rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                    <i class="ri-lock-line"></i>
                                    Pengecualian sudah ditutup
                                </div>
                            @elseif ($dueDateLabel)
                                <div
                                    class="inline-flex w-fit items-center gap-2 rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-800">
                                    <i class="ri-time-line"></i>
                                    Pengecualian masih tersedia
                                </div>
                            @else
                                <div
                                    class="inline-flex w-fit items-center gap-2 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                                    <i class="ri-error-warning-line"></i>
                                    Menunggu pengaturan admin
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($isSPV)
                <div class="p-3 mb-4 bg-white border rounded-lg shadow-sm border-slate-200">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-center gap-2.5 text-sm text-slate-600">
                            <span
                                class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                                <i class="ri-information-line"></i>
                            </span>
                            <div class="flex items-center">
                                @if ($dueDate ?? null)
                                    <p>Batas rekap:
                                        <span class="font-semibold text-slate-800">{{ $dueDateLabel }}</span>
                                    </p>
                                @else
                                    <p class="font-medium text-amber-700">Batas rekap belum diatur admin.</p>
                                @endif
                                @if (($isExempted ?? false) === true)
                                    <p class="mt-1 text-emerald-700">Pengecualian penalti aktif permanen.</p>
                                @endif
                            </div>
                        </div>
                        @if (($isExempted ?? false) === false)
                            @if (($isAfterDueDate ?? false) === false)
                                <form method="POST" action="{{ route('spvw.rekap.exemption.self') }}" class="lg:shrink-0">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg px-3.5 py-2 text-sm font-semibold transition lg:w-auto {{ ($isRekapEmpty ?? false) ? 'bg-emerald-600 text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2' : 'cursor-not-allowed bg-slate-200 text-slate-500' }}"
                                        {{ ($isRekapEmpty ?? false) ? '' : 'disabled' }}>
                                        <i class="{{ ($isRekapEmpty ?? false) ? 'ri-shield-check-line' : 'ri-lock-line' }}"></i>
                                        Aktifkan pengecualian penalti
                                    </button>
                                </form>
                            @else
                                <button type="button"
                                    class="inline-flex min-h-10 cursor-not-allowed items-center justify-center gap-2 rounded-lg bg-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-500"
                                    disabled>
                                    <i class="ri-lock-line"></i>
                                    Pengecualian sudah ditutup
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            <form id="spvw-mode-form" method="GET" action="{{ route('spvw.rekap.index', array_filter(['client_id' => $spvwClientId])) }}" class="hidden">
                <input type="hidden" name="mode" id="spvw-mode-input" value="{{ $selectedMode }}">
                <input type="hidden" name="client_id" value="{{ $selectedClientId }}">
            </form>

            <section
                x-data="{
                    mode: '{{ $selectedMode }}',
                    setMode(nextMode) {
                        this.mode = nextMode;
                        const input = document.getElementById('spvw-mode-input');
                        const form = document.getElementById('spvw-mode-form');
                        if (!input || !form) return;
                        input.value = nextMode;
                        form.submit();
                    }
                }"
                class="overflow-hidden bg-white border rounded-lg shadow-sm border-slate-200">
                <div class="p-3 border-b border-slate-200 bg-slate-50 sm:p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-white rounded-lg bg-slate-900">
                                <i class="ri-command-line"></i>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-900">Aksi Rekap</h2>
                                <p class="text-xs leading-4 text-slate-500">Pilih jenis rekap sesuai kebutuhan.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 p-1 bg-white rounded-lg shadow-sm ring-1 ring-slate-200 sm:w-72">
                            <button type="button" @click="setMode('pengajuan')"
                                class="inline-flex items-center justify-center gap-2 px-3 text-sm font-semibold transition rounded-md min-h-9"
                                :class="mode === 'pengajuan' ? 'bg-emerald-600 text-white shadow-sm' :
                                    'text-slate-600 hover:bg-slate-50'">
                                <i class="ri-file-add-line"></i>
                                Pengajuan
                            </button>
                            <button type="button" @click="setMode('riwayat')"
                                class="inline-flex items-center justify-center gap-2 px-3 text-sm font-semibold transition rounded-md min-h-9"
                                :class="mode === 'riwayat' ? 'bg-indigo-600 text-white shadow-sm' :
                                    'text-slate-600 hover:bg-slate-50'">
                                <i class="ri-history-line"></i>
                                Riwayat
                            </button>
                        </div>
                    </div>

                    @if ($selectedClientId <= 0)
                        <div class="mt-3 flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs text-amber-800">
                            <i class="ri-error-warning-line mt-0.5 text-sm min-w-4"></i>
                            <p>Pilih client terlebih dahulu. Setelah itu menu pengajuan dan riwayat baru bisa dipakai.</p>
                        </div>
                    @endif
                </div>

                <div x-show="mode === 'pengajuan'" x-cloak>
                    <div class="divide-y divide-slate-100">
                        @foreach ($rekapMenus as $menu)
                            <a href="{{ $selectedClientId > 0 ? $menu['pengajuan_url'] : '#' }}"
                                @if ($selectedClientId <= 0) aria-disabled="true" @endif
                                class="flex items-center justify-between gap-3 px-4 py-3 transition group min-h-16 {{ $selectedClientId > 0 ? 'hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-inset active:bg-slate-100' : 'cursor-not-allowed bg-slate-50/70 opacity-70' }}">
                                <span class="flex items-center min-w-0 gap-3">
                                    <span
                                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ring-1 {{ $menu['icon_bg'] }}">
                                        <i class="{{ $menu['icon'] }} {{ $menu['icon_color'] }} text-lg"></i>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold truncate text-slate-900">
                                            {{ $menu['title'] }}
                                        </span>
                                        <span class="mt-0.5 block text-xs leading-4 text-slate-500">
                                            {{ $menu['description'] }}
                                        </span>
                                    </span>
                                </span>
                                <span
                                    class="inline-flex h-8 shrink-0 items-center gap-1 rounded-lg px-2.5 text-xs font-semibold ring-1 {{ $selectedClientId > 0 ? 'bg-emerald-50 text-emerald-700 ring-emerald-100 transition group-hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 ring-slate-200' }}">
                                    {{ $selectedClientId > 0 ? 'Buat' : 'Pilih Client' }}
                                    <i class="ri-arrow-right-line"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div x-show="mode === 'riwayat'" x-cloak>
                    <div class="divide-y divide-slate-100">
                        @foreach ($rekapMenus as $menu)
                            <a href="{{ $selectedClientId > 0 ? $menu['riwayat_url'] : '#' }}"
                                @if ($selectedClientId <= 0) aria-disabled="true" @endif
                                class="flex items-center justify-between gap-3 px-4 py-3 transition group min-h-16 {{ $selectedClientId > 0 ? 'hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-inset active:bg-slate-100' : 'cursor-not-allowed bg-slate-50/70 opacity-70' }}">
                                <span class="flex items-center min-w-0 gap-3">
                                    <span
                                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg ring-1 {{ $menu['icon_bg'] }}">
                                        <i class="{{ $menu['icon'] }} {{ $menu['icon_color'] }} text-lg"></i>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold truncate text-slate-900">
                                            {{ $menu['title'] }}
                                        </span>
                                        <span class="mt-0.5 block text-xs leading-4 text-slate-500">
                                            {{ $menu['description'] }}
                                        </span>
                                    </span>
                                </span>
                                <span
                                    class="inline-flex h-8 shrink-0 items-center gap-1 rounded-lg px-2.5 text-xs font-semibold ring-1 {{ $selectedClientId > 0 ? 'bg-indigo-50 text-indigo-700 ring-indigo-100 transition group-hover:bg-indigo-100' : 'bg-slate-100 text-slate-500 ring-slate-200' }}">
                                    {{ $selectedClientId > 0 ? 'Lihat' : 'Pilih Client' }}
                                    <i class="ri-arrow-right-line"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </x-main-div>

    <script>
        window.handleSpvwClientFilterChange = function() {
            const form = document.getElementById('spvw-client-filter-form');
            if (!form) return;
            form.requestSubmit ? form.requestSubmit() : form.submit();
        };
    </script>
</x-app-layout>
