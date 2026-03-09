<x-mitra-layout title="Riwayat Izin">
    @php
        $penempatan = auth()->user()->id == 175 ? 'Semua Mitra' : (auth()->user()->kerjasama->client->name ?? '-');
    @endphp

    <div class="p-6 border shadow-xl bg-slate-700 border-slate-600 rounded-3xl">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-rose-400">Absensi Izin</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-white">Riwayat Izin {{ $penempatan }}</h1>
                <p class="mt-1 text-sm text-slate-300">Kelola persetujuan izin karyawan berdasarkan penempatan kerja.</p>
            </div>
            <div class="w-full md:w-72">
                <input
                    id="searchInput"
                    type="text"
                    placeholder="Cari nama / alasan..."
                    class="w-full text-sm input input-sm input-bordered bg-slate-200 text-slate-800 border-slate-300"
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

    <div class="mt-6 overflow-hidden border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
        <div class="p-4 border-b border-slate-600/50">
            <h2 class="text-xs font-black tracking-[0.3em] uppercase text-slate-400">Daftar Izin</h2>
        </div>
        <div class="p-4">
            @if($izin->isEmpty())
                <p class="text-sm italic text-slate-400">Belum ada data izin.</p>
            @else
                <div class="overflow-x-auto">
                    <table id="searchTable" class="w-full text-sm text-left text-slate-300">
                        <thead class="text-xs uppercase bg-slate-800/50 text-slate-400">
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
                                <tr class="border-b border-slate-700/50 hover:bg-slate-800/30">
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
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-amber-500 text-amber-950">process</span>
                                        @elseif($item->approve_status == 'accept')
                                            <span class="px-2 py-1 text-xs font-semibold text-white rounded bg-emerald-600">accept</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded">denied</span>
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
                                            <a href="{{ route('izin.show', $item->id) }}" class="text-xl text-sky-400 hover:text-sky-300">
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

            if (!searchInput || !rows.length) return;

            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();
                rows.forEach(function (row) {
                    const content = row.textContent.toLowerCase();
                    row.style.display = content.includes(keyword) ? '' : 'none';
                });
            });
        });
    </script>
</x-mitra-layout>
