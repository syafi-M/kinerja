<x-admin-layout :fullWidth="true">
    @section('title', 'Laporan Mitra')

    <div x-data="{ delOpen: false, deleteId: null }" class="mx-auto w-full max-w-screen-xl space-y-4 px-2 sm:px-3 lg:px-4">
        <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-blue-600">Laporan Mitra</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Data Laporan Mitra</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola dokumen PDF laporan dari masing-masing mitra.</p>
                </div>
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                    <label class="flex h-10 w-full items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 sm:w-72">
                        <i class="ri-search-2-line text-base text-gray-500"></i>
                        <input type="search" id="searchInput" class="w-full border-none bg-transparent text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none" placeholder="Cari laporan mitra..." />
                    </label>
                    <a href="{{ route('admin.index') }}" class="inline-flex h-10 items-center rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50">Dashboard</a>
                    @if(Auth::user()->id == 2)
                        <a href="{{ route('laporanMitra.create') }}" class="inline-flex h-10 items-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white hover:bg-blue-700"><i class="ri-add-line mr-1.5"></i>Laporan</a>
                    @endif
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="w-full overflow-x-auto">
                <table class="w-full min-w-[700px] divide-y divide-gray-100" id="searchTable">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <tr>
                            <th class="px-4 py-3 sm:px-5">#</th>
                            <th class="px-4 py-3 sm:px-5">Nama Mitra</th>
                            <th class="px-4 py-3 sm:px-5">File PDF</th>
                            @if(Auth::user()->id == 2)
                                <th class="px-4 py-3 text-right sm:px-5">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @php $no = 1; @endphp
                        @forelse ($laporanMitra as $index => $i)
                            <tr class="hover:bg-blue-50/40">
                                <td class="px-4 py-3 sm:px-5">{{ $no++ }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800 sm:px-5">{{ $i->kerjasama->client->name }}</td>
                                <td class="px-4 py-3 sm:px-5">
                                    <div id="pdf-container-{{$index}}" class="pdf-container"></div>
                                </td>
                                @if(Auth::user()->id == 2)
                                    <td class="px-4 py-3 sm:px-5">
                                        <div class="flex justify-end gap-1.5">
                                            <x-btn-edit>{{ route('laporanMitra.edit', $i->id) }}</x-btn-edit>
                                            <button type="button" @click="delOpen = true; deleteId = {{ $i->id }}" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2 py-1 text-[11px] font-semibold text-red-700 hover:bg-red-100">Hapus</button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->id == 2 ? 4 : 3 }}" class="px-4 py-8 text-center text-sm text-gray-500 sm:px-5">Data laporan mitra kosong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div x-show="delOpen" x-cloak style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/35 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h3 class="text-sm font-semibold text-gray-800">Konfirmasi Hapus Laporan</h3>
                    <button @click="delOpen = false" type="button" class="rounded-lg border border-gray-200 px-2.5 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-50">Tutup</button>
                </div>
                <form :action="`{{ url('laporanMitra') }}/${deleteId}`" method="POST" class="space-y-4 p-5">
                    @csrf
                    @method('DELETE')
                    <p class="text-sm text-gray-600">Apakah Anda yakin ingin menghapus laporan ini?</p>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="delOpen = false" class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <style>[x-cloak]{display:none!important;}</style>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
        <script>
            function renderPDF(url, container) {
                pdfjsLib.getDocument(url).promise.then(function(pdf) {
                    pdf.getPage(1).then(function(page) {
                        const viewport = page.getViewport({ scale: 0.45 });
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        canvas.style.maxWidth = '250px';
                        canvas.style.height = 'auto';
                        container.appendChild(canvas);
                        page.render({ canvasContext: context, viewport: viewport });
                    });
                });
            }

            $(function() {
                @foreach ($laporanMitra as $index => $i)
                    renderPDF("{{ asset('storage/pdf/'. $i->file_pdf) }}", document.getElementById("pdf-container-{{$index}}"));
                @endforeach
            });
        </script>
    @endpush
</x-admin-layout>
