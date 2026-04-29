<x-mitra-layout title="Riwayat Izin">
    @php
        $penempatan = auth()->user()->id == 175 ? 'Semua Mitra' : (auth()->user()->kerjasama->client->name ?? '-');
    @endphp

    <div class="p-6 rounded-3xl mitra-panel mitra-mobile-card">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em]" style="color: var(--mitra-danger);">Absensi Izin</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight mitra-text-strong">Riwayat Izin {{ $penempatan }}</h1>
                <p class="mt-1 text-sm mitra-text-soft">Kelola persetujuan izin karyawan berdasarkan penempatan kerja.</p>
            </div>
            <div class="w-full md:w-72">
                <input
                    id="searchInput"
                    type="text"
                    placeholder="Cari nama / alasan..."
                    class="w-full text-sm input input-sm input-bordered mitra-input"
                />
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="p-3 mt-4 text-sm border text-emerald-200 rounded-xl bg-emerald-600/30 border-emerald-500/40">
            {{ session('message') }}
        </div>
    @elseif(session('msgError'))
        <div class="p-3 mt-4 text-sm border text-amber-100 rounded-xl bg-amber-600/30 border-amber-500/40">
            Warning: {{ session('msgError') }}
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-2xl mitra-panel-soft">
        <div class="p-4 border-b border-slate-600/50">
            <h2 class="text-xs font-black tracking-[0.3em] uppercase mitra-section-title">Daftar Izin</h2>
        </div>
        <div class="p-4">
            @if($izin->isEmpty())
                <p class="text-sm italic mitra-empty-state">Belum ada data izin.</p>
            @else
                <div class="space-y-3 md:hidden">
                    @foreach ($izin as $item)
                        <article class="p-4 mitra-mobile-list-card izin-search-item">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold mitra-text-strong">{{ capitalizeWords($item->user->nama_lengkap ?? '-') }}</p>
                                    <p class="mt-1 text-sm mitra-text-soft">{{ $item->shift->shift_name ?? '-' }}</p>
                                </div>
                                <div>
                                    @if ($item->approve_status == 'process')
                                        <span class="mitra-status-badge mitra-status-pending">process</span>
                                    @elseif($item->approve_status == 'accept')
                                        <span class="mitra-status-badge mitra-status-success">accept</span>
                                    @else
                                        <span class="mitra-status-badge mitra-status-danger">denied</span>
                                    @endif
                                </div>
                            </div>
                            @if(auth()->user()->id == 175)
                                <div class="mt-3">
                                    <p class="text-[11px] font-semibold uppercase mitra-meta-label">Penempatan</p>
                                    <p class="mt-1 text-sm mitra-text-strong">
                                        {{ $item->user->kerjasama->client->panggilan ?: ($item->user->kerjasama->client->name ?? '-') }}
                                    </p>
                                </div>
                            @endif
                            <div class="mt-3">
                                <p class="text-[11px] font-semibold uppercase mitra-meta-label">Alasan Izin</p>
                                <p class="mt-1 text-sm mitra-text-strong">{{ $item->alasan_izin }}</p>
                            </div>
                            <div class="flex items-center justify-end gap-2 mt-4">
                                @if ($item->approve_status == 'process')
                                    <form action="{{ route('lead_acc', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-xs">
                                            <i class="ri-check-double-line"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('lead_denied', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-error btn-xs">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('izin.show', $item->id) }}" class="text-xl mitra-link-accent">
                                    <i class="ri-eye-fill"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="hidden -mx-4 px-4 md:block md:mx-0 md:px-0 mitra-table-wrap">
                    <table id="searchTable" class="w-full text-sm text-left mitra-table mitra-mobile-table-wide">
                        <thead class="text-xs uppercase bg-slate-800/50">
                            <tr>
                                <th class="px-4 py-3">Nama Lengkap</th>
                                <th class="px-4 py-3">Shift</th>
                                @if(auth()->user()->id == 175)
                                    <th class="px-4 py-3">Penempatan</th>
                                @endif
                                <th class="px-4 py-3">Alasan Izin</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($izin as $item)
                                <tr class="border-b mitra-table-row hover:bg-slate-800/30">
                                    <td class="px-4 py-3">{{ capitalizeWords($item->user->nama_lengkap ?? '-') }}</td>
                                    <td class="px-4 py-3">{{ $item->shift->shift_name ?? '-' }}</td>
                                    @if(auth()->user()->id == 175)
                                        <td class="px-4 py-3">
                                            {{ $item->user->kerjasama->client->panggilan ?: ($item->user->kerjasama->client->name ?? '-') }}
                                        </td>
                                    @endif
                                    <td class="px-4 py-3">{{ $item->alasan_izin }}</td>
                                    <td class="px-4 py-3">
                                        @if ($item->approve_status == 'process')
                                            <span class="mitra-status-badge mitra-status-pending">process</span>
                                        @elseif($item->approve_status == 'accept')
                                            <span class="mitra-status-badge mitra-status-success">accept</span>
                                        @else
                                            <span class="mitra-status-badge mitra-status-danger">denied</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($item->approve_status == 'process')
                                                <form action="{{ route('lead_acc', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-xs">
                                                        <i class="ri-check-double-line"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('lead_denied', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-error btn-xs">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('izin.show', $item->id) }}" class="text-xl mitra-link-accent">
                                                <i class="ri-eye-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">
                    {{ $izin->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('#searchTable tbody tr');
            const cards = document.querySelectorAll('.izin-search-item');

            if (!searchInput || (!rows.length && !cards.length)) return;

            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();
                rows.forEach(function (row) {
                    const content = row.textContent.toLowerCase();
                    row.style.display = content.includes(keyword) ? '' : 'none';
                });
                cards.forEach(function (card) {
                    const content = card.textContent.toLowerCase();
                    card.style.display = content.includes(keyword) ? '' : 'none';
                });
            });
        });
    </script>
</x-mitra-layout>
