<x-app-layout>
    @php
        $rekapMenus = [
            [
                'key' => 'lembur',
                'title' => 'Lembur',
                'description' => 'Catat lembur personil dan pantau status pengajuannya.',
                'icon' => 'ri-time-line',
                'icon_bg' => 'bg-sky-100 ring-sky-200',
                'icon_color' => 'text-sky-700',
                'pengajuan_url' => route('overtime-application.create'),
                'riwayat_url' => route('overtime-application.history'),
            ],
            [
                'key' => 'personil-keluar',
                'title' => 'Personil Keluar',
                'description' => 'Ajukan personil keluar dari site atau area kerja.',
                'icon' => 'ri-user-unfollow-line',
                'icon_bg' => 'bg-amber-100 ring-amber-200',
                'icon_color' => 'text-amber-700',
                'pengajuan_url' => route('person-is-out.create'),
                'riwayat_url' => route('person-is-out.history'),
            ],
            [
                'key' => 'personil-masuk',
                'title' => 'Personil Masuk',
                'description' => 'Input personil baru atau perpindahan masuk.',
                'icon' => 'ri-user-follow-line',
                'icon_bg' => 'bg-emerald-100 ring-emerald-200',
                'icon_color' => 'text-emerald-700',
                'pengajuan_url' => route('person-in.index'),
                'riwayat_url' => route('person.in.history'),
            ],
            [
                'key' => 'potongan',
                'title' => 'Cutting',
                'description' => 'Kelola potongan performa dengan riwayat yang mudah dicek.',
                'icon' => 'ri-auction-fill',
                'icon_bg' => 'bg-red-100 ring-red-200',
                'icon_color' => 'text-red-700',
                'pengajuan_url' => route('cutting.index'),
                'riwayat_url' => route('cutting.history'),
            ],
            [
                'key' => 'lepas-training',
                'title' => 'Lepas Training',
                'description' => 'Rekap personil yang selesai masa training.',
                'icon' => 'ri-footprint-fill',
                'icon_bg' => 'bg-indigo-100 ring-indigo-200',
                'icon_color' => 'text-indigo-700',
                'pengajuan_url' => route('finished-training.index'),
                'riwayat_url' => route('finished-training.history'),
            ],
        ];

        $isCO = in_array(strtoupper(auth()->user()->jabatan->code_jabatan ?? ''), ['CO-CS', 'CO-SCR'], true);
        $dueDateLabel = ($dueDate ?? null) ? \Carbon\Carbon::parse($dueDate->due_date)->format('d M Y') : null;
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
                                    <p class="text-xl font-semibold text-slate-900">CO</p>
                                </div>
                            </div>
                        </div>
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

            @if ($isCO)
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
                                <form method="POST" action="{{ route('rekap.exemption.self') }}" class="lg:shrink-0">
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

            <section x-data="{ mode: 'pengajuan' }"
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
                            <button type="button" @click="mode = 'pengajuan'"
                                class="inline-flex items-center justify-center gap-2 px-3 text-sm font-semibold transition rounded-md min-h-9"
                                :class="mode === 'pengajuan' ? 'bg-emerald-600 text-white shadow-sm' :
                                    'text-slate-600 hover:bg-slate-50'">
                                <i class="ri-file-add-line"></i>
                                Pengajuan
                            </button>
                            <button type="button" @click="mode = 'riwayat'"
                                class="inline-flex items-center justify-center gap-2 px-3 text-sm font-semibold transition rounded-md min-h-9"
                                :class="mode === 'riwayat' ? 'bg-indigo-600 text-white shadow-sm' :
                                    'text-slate-600 hover:bg-slate-50'">
                                <i class="ri-history-line"></i>
                                Riwayat
                            </button>
                        </div>
                    </div>
                </div>

                <div x-show="mode === 'pengajuan'" x-cloak>
                    <div class="divide-y divide-slate-100">
                        @foreach ($rekapMenus as $menu)
                            <a href="{{ $menu['pengajuan_url'] }}"
                                class="flex items-center justify-between gap-3 px-4 py-3 transition group min-h-16 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-inset active:bg-slate-100">
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
                                    class="inline-flex h-8 shrink-0 items-center gap-1 rounded-lg bg-emerald-50 px-2.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100 transition group-hover:bg-emerald-100">
                                    Buat
                                    <i class="ri-arrow-right-line"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div x-show="mode === 'riwayat'" x-cloak>
                    <div class="divide-y divide-slate-100">
                        @foreach ($rekapMenus as $menu)
                            <a href="{{ $menu['riwayat_url'] }}"
                                class="flex items-center justify-between gap-3 px-4 py-3 transition group min-h-16 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-inset active:bg-slate-100">
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
                                    class="inline-flex h-8 shrink-0 items-center gap-1 rounded-lg bg-indigo-50 px-2.5 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100 transition group-hover:bg-indigo-100">
                                    Lihat
                                    <i class="ri-arrow-right-line"></i>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </x-main-div>
</x-app-layout>
