<x-mitra-layout title="Riwayat Lembur">
    @php
        $namaClient = auth()->user()->kerjasama->client->name ?? '-';
    @endphp

    <div class="p-6 border shadow-xl bg-slate-700 border-slate-600 rounded-3xl">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-amber-400">Lembur Karyawan</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-white">Riwayat Lembur {{ $namaClient }}</h1>
                <p class="mt-1 text-sm text-slate-300">Pantau catatan jam lembur seluruh personel mitra.</p>
            </div>
            <div class="w-full md:w-72">
                <input
                    id="searchInput"
                    type="text"
                    placeholder="Cari nama..."
                    class="w-full text-sm input input-sm input-bordered bg-slate-200 text-slate-800 border-slate-300"
                />
            </div>
        </div>
    </div>

    <div class="mt-6 overflow-hidden border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
        <div class="p-4 border-b border-slate-600/50">
            <h2 class="text-xs font-black tracking-[0.3em] uppercase text-slate-400">Daftar Lembur</h2>
        </div>
        <div class="p-4">
            @if($lembur->isEmpty())
                <p class="text-sm italic text-slate-400">Belum ada data lembur.</p>
            @else
                <div class="overflow-x-auto">
                    <table id="searchTable" class="w-full text-sm text-left text-slate-300">
                        <thead class="text-xs uppercase bg-slate-800/50 text-slate-400">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Foto</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Lama Lembur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lembur as $item)
                                <tr class="border-b border-slate-700/50 hover:bg-slate-800/30">
                                    <td class="px-4 py-3">{{ $lembur->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3">
                                        @if($item->image)
                                            <img
                                                src="{{ asset('storage/images/' . $item->image) }}"
                                                alt="Foto {{ $item->user->nama_lengkap ?? 'Karyawan' }}"
                                                class="object-cover w-12 h-12 rounded"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="flex items-center justify-center w-12 h-12 text-xs font-bold rounded bg-slate-600 text-slate-200">
                                                {{ strtoupper(substr($item->user->nama_lengkap ?? 'K', 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ capitalizeWords($item->user->nama_lengkap ?? '-') }}</td>
                                    <td class="px-4 py-3">{{ $item->created_at?->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3">
                                        @if ($item->jam_selesai == null)
                                            Belum Selesai Lembur
                                        @else
                                            @php
                                                $masuk = strtotime($item->jam_mulai);
                                                $keluar = strtotime($item->jam_selesai);
                                                $msk = date('H', $masuk);
                                                $klr = date('H', $keluar);
                                                $tot = $klr - $msk;
                                            @endphp
                                            @if($tot <= 0)
                                                <span class="text-red-400">0 Jam</span>
                                            @else
                                                {{ $tot . ' Jam' }}
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">
                    {{ $lembur->links() }}
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
