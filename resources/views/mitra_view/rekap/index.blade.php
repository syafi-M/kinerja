<x-mitra-layout title="Rekap Data">
    @php
        $rekapMenus = [
            [
                'key' => 'lembur',
                'title' => 'Pengajuan Lembur',
                'icon' => 'ri-time-line',
                'icon_wrap' => 'bg-sky-500/15 text-sky-300',
                'pengajuan_url' => route('overtime-application.create'),
                'riwayat_url' => route('overtime-application.show', 1),
            ],
            [
                'key' => 'personil-keluar',
                'title' => 'Personil Keluar',
                'icon' => 'ri-user-unfollow-line',
                'icon_wrap' => 'bg-amber-500/15 text-amber-300',
                'pengajuan_url' => route('person-is-out.create'),
                'riwayat_url' => route('person-is-out.show', 1),
            ],
            [
                'key' => 'personil-masuk',
                'title' => 'Personil Masuk',
                'icon' => 'ri-user-follow-line',
                'icon_wrap' => 'bg-emerald-500/15 text-emerald-300',
                'pengajuan_url' => route('person-in.index'),
                'riwayat_url' => route('person.in.history'),
            ],
            [
                'key' => 'potongan',
                'title' => 'Cutting',
                'icon' => 'ri-auction-fill',
                'icon_wrap' => 'bg-red-500/15 text-red-300',
                'pengajuan_url' => route('cutting.index'),
                'riwayat_url' => route('cutting.history'),
            ],
            [
                'key' => 'lepas-training',
                'title' => 'Lepas Training',
                'icon' => 'ri-footprint-fill',
                'icon_wrap' => 'bg-violet-500/15 text-violet-300',
                'pengajuan_url' => route('finished-training.index'),
                'riwayat_url' => route('finished-training.history'),
            ],
        ];
        $isCO = in_array(strtoupper(auth()->user()->jabatan->code_jabatan ?? ''), ['CO-CS', 'CO-SCR'], true);
    @endphp

    <div class="p-6 border shadow-xl bg-slate-700 border-slate-600 rounded-3xl">
        <div class="flex flex-col gap-2">
            <p class="text-[11px] font-black uppercase tracking-[0.25em] text-indigo-400">Rekapitulasi Mitra</p>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">Rekap Data dan Pengajuan</h1>
            <p class="text-sm text-slate-300">Akses modul pengajuan dan riwayat rekap aktivitas operasional.</p>
        </div>
    </div>

    @if ($isCO)
        <div class="p-4 mt-6 border bg-slate-700/50 rounded-2xl border-slate-600/50">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="text-sm text-slate-300">
                    @if ($dueDate ?? null)
                        <p>Due date rekap: <span class="font-semibold text-white">{{ \Carbon\Carbon::parse($dueDate->due_date)->format('d M Y') }}</span></p>
                    @else
                        <p class="text-amber-300">Due date rekap belum diatur admin.</p>
                    @endif
                    @if (($isExempted ?? false) === true)
                        <p class="mt-1 text-emerald-300">Status exemption: aktif permanen.</p>
                    @endif
                </div>
                @if (($isExempted ?? false) === false)
                    @if (($isAfterDueDate ?? false) === false)
                        <form method="POST" action="{{ route('rekap.exemption.self') }}">
                            @csrf
                            <button
                                type="submit"
                                class="text-sm font-semibold btn btn-sm {{ ($isRekapEmpty ?? false) ? 'btn-success' : 'btn-disabled' }}"
                                {{ ($isRekapEmpty ?? false) ? '' : 'disabled' }}
                            >
                                Data Rekap Kosong (Exempt Penalty)
                            </button>
                        </form>
                    @else
                        <button type="button" class="text-sm btn btn-sm btn-disabled" disabled>
                            Exempt Tidak Tersedia (Lewat Due Date)
                        </button>
                    @endif
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($rekapMenus as $menu)
            <div class="border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl {{ $menu['icon_wrap'] }}">
                            <i class="{{ $menu['icon'] }} text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white">{{ $menu['title'] }}</h2>
                            <p class="text-xs text-slate-400">Pilih aksi pengajuan atau lihat riwayat data.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-2 mt-4 sm:grid-cols-2">
                        <a href="{{ $menu['pengajuan_url'] }}" class="justify-start text-slate-100 btn btn-sm bg-slate-600 hover:bg-slate-500 border-slate-500">
                            <i class="ri-file-add-line"></i> Pengajuan
                        </a>
                        <a href="{{ $menu['riwayat_url'] }}" class="justify-start text-slate-100 btn btn-sm bg-slate-600 hover:bg-slate-500 border-slate-500">
                            <i class="ri-history-line"></i> Riwayat
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-mitra-layout>
