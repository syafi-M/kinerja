<x-app-layout>
    @php
        $rekapMenus = [
            [
                'key' => 'lembur',
                'title' => 'Pengajuan Lembur',
                'icon' => 'ri-time-line',
                'icon_bg' => 'bg-sky-100',
                'icon_color' => 'text-sky-600',
                'badge_class' => 'badge-info',
                'accent_ring' => 'ring-sky-500/20',
                'card_tone' => 'bg-sky-50',
                'pengajuan_url' => route('overtime-application.create'),
                'riwayat_url' => route('overtime-application.show', 1),
            ],
            [
                'key' => 'personil-keluar',
                'title' => 'Personil Keluar',
                'icon' => 'ri-user-unfollow-line',
                'icon_bg' => 'bg-amber-100',
                'icon_color' => 'text-amber-600',
                'badge_class' => 'badge-warning',
                'accent_ring' => 'ring-amber-500/20',
                'card_tone' => 'bg-amber-50',
                'pengajuan_url' => route('person-is-out.create'),
                'riwayat_url' => route('person-is-out.show', 1),
            ],
            [
                'key' => 'personil-masuk',
                'title' => 'Personil Masuk',
                'icon' => 'ri-user-follow-line',
                'icon_bg' => 'bg-emerald-100',
                'icon_color' => 'text-emerald-600',
                'badge_class' => 'badge-success',
                'accent_ring' => 'ring-emerald-500/20',
                'card_tone' => 'bg-emerald-50',
                'pengajuan_url' => route('person-in.index'),
                'riwayat_url' => route('person.in.history'),
            ],
            [
                'key' => 'potongan',
                'title' => 'Cutting',
                'icon' => 'ri-auction-fill',
                'icon_bg' => 'bg-red-100',
                'icon_color' => 'text-red-600',
                'badge_class' => 'badge-error',
                'accent_ring' => 'ring-red-500/20',
                'card_tone' => 'bg-red-50',
                'pengajuan_url' => route('cutting.index'),
                'riwayat_url' => route('cutting.history'),
            ],
            [
                'key' => 'lepas-training',
                'title' => 'Lepas Training',
                'icon' => 'ri-footprint-fill',
                'icon_bg' => 'bg-violet-100',
                'icon_color' => 'text-violet-600',
                'badge_class' => 'badge-secondary',
                'accent_ring' => 'ring-violet-500/20',
                'card_tone' => 'bg-violet-50',
                'pengajuan_url' => route('finished-training.index'),
                'riwayat_url' => route('finished-training.history'),
            ],
        ];
    @endphp

    <x-main-div>
        <div class="mx-auto w-full max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
            <div class="mb-6 rounded-md border border-slate-200 bg-white p-6 shadow-md sm:p-7">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-bold text-slate-800 sm:text-3xl">Rekapitulasi</h1>
                    <p class="text-sm text-slate-600 sm:text-base">Rekap Data dan Pengajuan Rekap</p>
                </div>
            </div>

            @php
                $isCO = in_array(strtoupper(auth()->user()->jabatan->code_jabatan ?? ''), ['CO-CS', 'CO-SCR'], true);
            @endphp
            @if ($isCO)
                <div class="mb-6 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-slate-600">
                            @if ($dueDate ?? null)
                                <p>Due date rekap: <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($dueDate->due_date)->format('d M Y') }}</span></p>
                            @else
                                <p class="text-amber-700">Due date rekap belum diatur admin.</p>
                            @endif
                            @if (($isExempted ?? false) === true)
                                <p class="mt-1 text-emerald-700">Status exemption: aktif permanen.</p>
                            @endif
                        </div>
                        @if (($isExempted ?? false) === false)
                            @if (($isAfterDueDate ?? false) === false)
                                <form method="POST" action="{{ route('rekap.exemption.self') }}">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg px-4 py-2 text-sm font-semibold {{ ($isRekapEmpty ?? false) ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'cursor-not-allowed bg-slate-200 text-slate-500' }}"
                                        {{ ($isRekapEmpty ?? false) ? '' : 'disabled' }}>
                                        Data Rekap Kosong (Exempt Penalty)
                                    </button>
                                </form>
                            @else
                                <button type="button"
                                    class="cursor-not-allowed rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-500"
                                    disabled>
                                    Exempt Tidak Tersedia (Lewat Due Date)
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            <div x-data="{ activeAccordion: 'lembur' }" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($rekapMenus as $menu)
                    <div
                        class="card overflow-hidden rounded-md border border-slate-200 {{ $menu['card_tone'] }} shadow-sm ring-1 {{ $menu['accent_ring'] }} transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="card-body gap-4 p-5 sm:p-6">
                            <button type="button"
                                @click="activeAccordion = activeAccordion === '{{ $menu['key'] }}' ? null : '{{ $menu['key'] }}'"
                                class="flex w-full items-start justify-between gap-3 text-left">
                                <div class="flex min-w-0 items-start gap-3">
                                    <span
                                        class="mt-0.5 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $menu['icon_bg'] }}">
                                        <i class="{{ $menu['icon'] }} {{ $menu['icon_color'] }} text-xl"></i>
                                    </span>
                                    <div class="min-w-0 space-y-1">
                                        <h2 class="text-xl font-bold leading-tight text-slate-800">{{ $menu['title'] }}
                                        </h2>
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-2 overflow-hidden">
                                    <i class="ri-arrow-down-s-line text-xl text-slate-500 transition-transform duration-300 overflow-hidden"
                                        :class="{ 'rotate-180': activeAccordion === '{{ $menu['key'] }}' }"></i>
                                </div>
                            </button>

                            <div x-show="activeAccordion === '{{ $menu['key'] }}'" x-collapse
                                class="grid grid-cols-1 gap-3 pt-2 sm:grid-cols-2">
                                <a href="{{ $menu['pengajuan_url'] }}"
                                    class="btn h-11 justify-start gap-2 border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                                    <i class="ri-file-add-line text-sky-600"></i>
                                    Pengajuan
                                </a>
                                <a href="{{ $menu['riwayat_url'] }}"
                                    class="btn h-11 justify-start gap-2 border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                                    <i class="ri-history-line text-indigo-600"></i>
                                    Riwayat
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-main-div>
</x-app-layout>
