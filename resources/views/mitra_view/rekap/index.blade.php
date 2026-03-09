<x-mitra-layout title="Rekap Mitra">
    <div class="space-y-4">
        <section class="p-6 border shadow-xl bg-slate-700 border-slate-600 rounded-3xl">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.25em] text-cyan-400">Laporan Mitra</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-white">Data Laporan Mitra</h1>
                    <p class="mt-1 text-sm text-slate-300">Kelola dokumen PDF laporan mitra Anda.</p>
                </div>
                <div class="w-full md:w-72">
                    <input
                        type="search"
                        id="searchInput"
                        placeholder="Cari laporan mitra..."
                        class="w-full text-sm input input-sm input-bordered bg-slate-200 text-slate-800 border-slate-300"
                    >
                </div>
            </div>
        </section>

        <section class="overflow-hidden border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
            <div class="w-full overflow-x-auto">
                <table class="w-full min-w-[700px] text-sm text-left text-slate-300" id="searchTable">
                    <thead class="text-xs uppercase bg-slate-800/50 text-slate-400">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Nama Mitra</th>
                            <th class="px-4 py-3">File PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanMitra as $index => $item)
                            <tr class="border-b border-slate-700/50 hover:bg-slate-800/30">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-100">{{ $item->kerjasama->client->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div id="pdf-container-{{ $index }}" class="pdf-container"></div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-sm text-center text-slate-400">Data laporan mitra kosong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        function renderPDF(url, container) {
            if (!container) return;
            pdfjsLib.getDocument(url).promise.then(function (pdf) {
                pdf.getPage(1).then(function (page) {
                    const viewport = page.getViewport({ scale: 0.45 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.style.maxWidth = '250px';
                    canvas.style.height = 'auto';

                    container.innerHTML = '';
                    container.appendChild(canvas);
                    page.render({ canvasContext: context, viewport: viewport });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            @foreach ($laporanMitra as $index => $item)
                renderPDF("{{ asset('storage/pdf/' . $item->file_pdf) }}", document.getElementById("pdf-container-{{ $index }}"));
            @endforeach

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
        });
    </script>
</x-mitra-layout>
