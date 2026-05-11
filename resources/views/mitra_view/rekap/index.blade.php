<x-mitra-layout title="Rekap Mitra">
    <div class="space-y-4">
        <section class="p-6 rounded-3xl mitra-panel mitra-mobile-card">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.25em] mitra-accent">Laporan Mitra</p>
                    <h1 class="mt-1 text-2xl font-extrabold tracking-tight mitra-text-strong">Data Laporan Mitra</h1>
                    <p class="mt-1 text-sm mitra-text-soft">Kelola dokumen PDF laporan mitra Anda.</p>
                </div>
                <div class="w-full md:w-72">
                    <input
                        type="search"
                        id="searchInput"
                        placeholder="Cari laporan mitra..."
                        class="w-full text-sm input input-sm input-bordered mitra-input"
                    >
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl mitra-panel-soft">
            <div class="space-y-3 p-4 md:hidden">
                @forelse ($laporanMitra as $index => $item)
                    <article class="p-4 mitra-mobile-list-card rekap-search-item">
                        <p class="text-[11px] font-black uppercase tracking-[0.2em] mitra-meta-label">
                            Mitra {{ $loop->iteration }}
                        </p>
                        <p class="mt-1 font-semibold mitra-text-strong">
                            {{ $item->kerjasama->client->name ?? '-' }}
                        </p>
                        <div class="mt-4 overflow-hidden rounded-xl border" style="border-color: var(--mitra-border);">
                            <div id="pdf-mobile-container-{{ $index }}" class="flex items-center justify-center min-h-[220px] p-3"></div>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-center mitra-empty-state">Data laporan mitra kosong.</p>
                @endforelse
            </div>
            <div class="hidden -mx-4 w-auto px-4 md:block md:mx-0 md:px-0 mitra-table-wrap">
                <table class="w-full min-w-[700px] text-sm text-left mitra-table mitra-mobile-table" id="searchTable">
                    <thead class="text-xs uppercase bg-slate-800/50">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Nama Mitra</th>
                            <th class="px-4 py-3">File PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanMitra as $index => $item)
                            <tr class="border-b mitra-table-row hover:bg-slate-800/30">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-semibold mitra-text-strong">{{ $item->kerjasama->client->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div id="pdf-container-{{ $index }}" class="pdf-container"></div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-sm text-center mitra-empty-state">Data laporan mitra kosong.</td>
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
                    const isMobileCard = container.id.startsWith('pdf-mobile-container-');
                    const scale = isMobileCard ? 0.58 : 0.45;
                    const viewport = page.getViewport({ scale: scale });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    canvas.style.maxWidth = isMobileCard ? '100%' : '250px';
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
                renderPDF("{{ asset('storage/pdf/' . $item->file_pdf) }}", document.getElementById("pdf-mobile-container-{{ $index }}"));
            @endforeach

            const searchInput = document.getElementById('searchInput');
            const rows = document.querySelectorAll('#searchTable tbody tr');
            const cards = document.querySelectorAll('.rekap-search-item');

            if (searchInput && (rows.length || cards.length)) {
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
            }
        });
    </script>
</x-mitra-layout>
