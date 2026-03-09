<x-mitra-layout title="Riwayat Laporan">
    @php
        $namaClient = auth()->user()->kerjasama->client->name ?? '-';
    @endphp

    <div class="p-6 border shadow-xl bg-slate-700 border-slate-600 rounded-3xl">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-emerald-400">Laporan Aktivitas</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-white">Riwayat Laporan {{ $namaClient }}</h1>
                <p class="mt-1 text-sm text-slate-300">Pantau dokumentasi progres pekerjaan tim secara berkala.</p>
            </div>
            <div class="w-full md:w-72">
                <input
                    id="searchInput"
                    type="text"
                    placeholder="Cari keterangan..."
                    class="w-full text-sm input input-sm input-bordered bg-slate-200 text-slate-800 border-slate-300"
                />
            </div>
        </div>
    </div>

    <div class="mt-6 overflow-hidden border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
        <div class="p-4 border-b border-slate-600/50">
            <h2 class="text-xs font-black tracking-[0.3em] uppercase text-slate-400">Daftar Laporan</h2>
        </div>
        <div class="p-4">
            @if($laporan->isEmpty())
                <p class="text-sm italic text-slate-400">Belum ada laporan yang tersedia.</p>
            @else
                <div class="overflow-x-auto">
                    <table id="searchTable" class="w-full text-sm text-left text-slate-300">
                        <thead class="text-xs uppercase bg-slate-800/50 text-slate-400">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3 text-center" colspan="3">Foto Progres</th>
                                <th class="px-4 py-3">Keterangan</th>
                                <th class="px-4 py-3 text-center">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporan as $item)
                                <tr class="border-b border-slate-700/50 hover:bg-slate-800/30">
                                    <td class="px-4 py-3">{{ $laporan->firstItem() + $loop->index }}.</td>

                                    <td class="px-4 py-3">
                                        @if ($item->img_before)
                                            <img
                                                src="http://laporan-sac.sac-po.com/storage/{{ $item->img_before }}"
                                                data-preview-src="http://laporan-sac.sac-po.com/storage/{{ $item->img_before }}"
                                                alt="Foto Before"
                                                class="object-cover rounded cursor-pointer imeg w-14 h-14 md:w-20 md:h-20"
                                            >
                                        @else
                                            <x-no-img class="w-14 h-14 md:w-20 md:h-20" />
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        @if ($item->img_proccess)
                                            <img
                                                src="http://laporan-sac.sac-po.com/storage/{{ $item->img_proccess }}"
                                                data-preview-src="http://laporan-sac.sac-po.com/storage/{{ $item->img_proccess }}"
                                                alt="Foto Process"
                                                class="object-cover rounded cursor-pointer imeg w-14 h-14 md:w-20 md:h-20"
                                            >
                                        @else
                                            <x-no-img class="w-14 h-14 md:w-20 md:h-20" />
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        @if ($item->img_final)
                                            <img
                                                src="http://laporan-sac.sac-po.com/storage/{{ $item->img_final }}"
                                                data-preview-src="http://laporan-sac.sac-po.com/storage/{{ $item->img_final }}"
                                                alt="Foto Final"
                                                class="object-cover rounded cursor-pointer imeg w-14 h-14 md:w-20 md:h-20"
                                            >
                                        @else
                                            <x-no-img class="w-14 h-14 md:w-20 md:h-20" />
                                        @endif
                                    </td>

                                    @if(auth()->user()->devisi_id == 8)
                                        <td class="px-4 py-3">{{ $item->note }}</td>
                                    @else
                                        <td class="px-4 py-3">{{ $item->keterangan }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-center">{{ $item->created_at?->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">
                    {{ $laporan->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="imageModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative p-3 border shadow-2xl bg-slate-900 rounded-2xl border-slate-700">
                <button
                    id="closeImageModal"
                    class="absolute text-lg leading-none btn btn-sm btn-error -top-3 -right-3"
                    type="button"
                >
                    &times;
                </button>
                <img id="modalImagePreview" src="" alt="Preview Laporan" class="object-contain w-full max-w-xs rounded md:max-w-sm">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('#searchTable tbody tr');

            if (searchInput && rows.length) {
                searchInput.addEventListener('input', function () {
                    const keyword = this.value.toLowerCase().trim();
                    rows.forEach(function (row) {
                        const content = row.textContent.toLowerCase();
                        row.style.display = content.includes(keyword) ? '' : 'none';
                    });
                });
            }

            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImagePreview');
            const closeBtn = document.getElementById('closeImageModal');
            const clickableImages = document.querySelectorAll('.imeg');

            clickableImages.forEach(function (img) {
                img.addEventListener('click', function () {
                    const src = this.getAttribute('data-preview-src');
                    if (!src || !modal || !modalImage) return;

                    modalImage.src = src;
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                });
            });

            if (closeBtn && modal) {
                closeBtn.addEventListener('click', function () {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
            }

            if (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            }
        });
    </script>
</x-mitra-layout>
