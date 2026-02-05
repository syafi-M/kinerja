<x-app-layout>
    @push('styles')
        <style>
            #detailModal {
                transition: opacity 0.2s ease-in-out;
            }

            #detailModal.opacity-100 {
                opacity: 1;
            }
        </style>
    @endpush
    <x-main-div>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

            <!-- Header Section -->
            <div class="mb-6">
                <div class="flex flex-col gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Rekap Data Bulanan</h1>
                        <p class="text-white/80 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Data Berdasarkan Per Mitra
                        </p>
                    </div>

                    <!-- Search Section -->
                    <form action="{{ route('manajemen_rekap') }}" method="GET" class="w-full">
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                    placeholder="Cari mitra..."
                                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            <!-- Search Button (visible on mobile) -->
                            <button type="submit"
                                class="sm:hidden px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>

                            <!-- Clear Button -->
                            @if (request('search'))
                                <a href="{{ route('manajemen_rekap') }}"
                                    class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <!-- Total Mitra -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-indigo-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide font-medium">Total Mitra</p>
                            <p class="text-gray-900 text-2xl font-bold">{{ $client->total() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Halaman -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-emerald-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide font-medium">Halaman</p>
                            <p class="text-gray-900 text-2xl font-bold">{{ $client->currentPage() }} /
                                {{ $client->lastPage() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bulan Ini -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-violet-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide font-medium">Bulan Ini</p>
                            <p class="text-gray-900 text-2xl font-bold">{{ now()->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Info -->
            @if (request('search'))
                <div
                    class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-indigo-700 text-sm">
                            Hasil pencarian: <strong class="text-indigo-900">"{{ request('search') }}"</strong>
                            <span class="text-gray-600">({{ $client->total() }} mitra)</span>
                        </span>
                    </div>
                    <a href="{{ route('manajemen_rekap') }}"
                        class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition-colors">
                        Hapus Filter
                    </a>
                </div>
            @endif

            <!-- Client List -->
            <div class="space-y-3" id="clientList">
                @forelse($client as $mitra)
                    <div
                        class="bg-white border border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:shadow-md transition-all duration-200">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <!-- Avatar and Info -->
                            <div class="flex items-start sm:items-center gap-4 flex-1 min-w-0">
                                <!-- Avatar -->
                                <div
                                    class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                    {{ strtoupper(substr($mitra->name ?? 'M', 0, 1)) }}
                                </div>

                                <!-- Client Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-gray-900 font-semibold text-base sm:text-lg mb-1.5 break-words">
                                        {{ $mitra->name ?? 'Nama Mitra' }}
                                    </h3>
                                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:gap-4">
                                        @if ($mitra->email)
                                            <p
                                                class="text-gray-600 text-xs sm:text-sm flex items-center gap-1.5 break-all">
                                                <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <span class="break-all">{{ $mitra->email }}</span>
                                            </p>
                                        @endif
                                        @if ($mitra->phone)
                                            <p class="text-gray-600 text-xs sm:text-sm flex items-center gap-1.5">
                                                <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                    </path>
                                                </svg>
                                                {{ $mitra->phone }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <button
                                onclick="openModal({{ $mitra->id }}, '{{ addslashes($mitra->name ?? 'Nama Mitra') }}')"
                                class="w-full sm:w-auto flex-shrink-0 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2 group">
                                <span>Lihat Detail</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-gray-200 rounded-lg p-8 sm:p-12 text-center shadow-sm">
                        <div class="max-w-sm mx-auto">
                            <div
                                class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                            </div>
                            @if (request('search'))
                                <p class="text-gray-800 text-base sm:text-lg font-semibold mb-1">Tidak ada hasil
                                    pencarian</p>
                                <p class="text-gray-500 text-sm mb-4">Coba kata kunci lain atau hapus filter pencarian
                                </p>
                                <a href="{{ route('manajemen_rekap') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Hapus Filter
                                </a>
                            @else
                                <p class="text-gray-800 text-base sm:text-lg font-semibold mb-1">Tidak ada data mitra
                                </p>
                                <p class="text-gray-500 text-sm">Silakan tambahkan mitra baru untuk memulai</p>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($client->hasPages())
                <div class="mt-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        {{ $client->appends(['search' => request('search')])->links() }}
                    </div>
                </div>
            @endif

        </div>

        <!-- Modal Overlay -->
        <div id="detailModal" class="fixed inset-0 z-[999999] hidden" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

            <!-- Modal Container -->
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <!-- Modal Content -->
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all w-full max-w-md">
                        <!-- Modal Header -->
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                                        id="modalAvatar">
                                        M
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 leading-tight" id="modalTitle">
                                            Nama Mitra</h3>
                                        <p class="text-sm text-gray-500 mt-0.5">Pilih data yang ingin dilihat</p>
                                    </div>
                                </div>
                                <button type="button" onclick="closeModal()"
                                    class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none flex-shrink-0 ml-3">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="bg-white px-6 py-4 space-y-3">
                            <!-- Data Lembur Button -->
                            <a href="#" id="linkLembur"
                                class="group flex items-center gap-3 p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 hover:from-indigo-100 hover:to-indigo-200 border border-indigo-200 rounded-lg transition-all duration-200">
                                <div
                                    class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="text-gray-900 font-semibold text-sm mb-0.5 group-hover:text-indigo-700 transition-colors">
                                        Data Lembur
                                    </h4>
                                    <p class="text-gray-600 text-xs">
                                        Lihat rekap data lembur karyawan
                                    </p>
                                </div>
                                <svg class="w-5 h-5 text-indigo-600 group-hover:translate-x-1 transition-transform flex-shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>

                            <!-- Data Personil Keluar Button -->
                            <a href="#" id="linkPersonilKeluar"
                                class="group flex items-center gap-3 p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 hover:from-emerald-100 hover:to-emerald-200 border border-emerald-200 rounded-lg transition-all duration-200">
                                <div
                                    class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="text-gray-900 font-semibold text-sm mb-0.5 group-hover:text-emerald-700 transition-colors">
                                        Data Personil Keluar
                                    </h4>
                                    <p class="text-gray-600 text-xs">
                                        Lihat data karyawan yang keluar
                                    </p>
                                </div>
                                <svg class="w-5 h-5 text-emerald-600 group-hover:translate-x-1 transition-transform flex-shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-6 py-4 rounded-b-lg">
                            <button type="button" onclick="closeModal()"
                                class="w-full px-4 py-2.5 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 rounded-lg font-medium transition-colors">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-main-div>

    <script>
        // Auto-submit search on desktop (hidden on mobile since we have button)
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;

        if (searchInput && window.innerWidth >= 640) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (e.target.value.length >= 2 || e.target.value.length === 0) {
                        this.form.submit();
                    }
                }, 500);
            });
        }

        // Submit on Enter key for all devices
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    e.preventDefault();
                    this.form.submit();
                }
            });
        }

        // Modal Functions
        let scrollPosition = 0;

        function openModal(mitraId, mitraName) {
            const modal = document.getElementById('detailModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalAvatar = document.getElementById('modalAvatar');
            const linkLembur = document.getElementById('linkLembur');
            const linkPersonilKeluar = document.getElementById('linkPersonilKeluar');

            // Save current scroll position
            scrollPosition = window.pageYOffset || document.documentElement.scrollTop;

            // Set modal content
            modalTitle.textContent = mitraName;
            modalAvatar.textContent = mitraName.charAt(0).toUpperCase();

            // Set links with mitra ID
            linkLembur.href = `/Management/rekap-overtimes/${mitraId}`;
            linkPersonilKeluar.href = `/Management/rekap-person-out/${mitraId}`;

            // Show modal
            modal.classList.remove('hidden');

            // Prevent body scroll
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollPosition}px`;
            document.body.style.width = '100%';
        }

        function closeModal() {
            const modal = document.getElementById('detailModal');

            // Hide modal
            modal.classList.add('hidden');

            // Restore body scroll
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';

            // Restore scroll position
            window.scrollTo(0, scrollPosition);
        }

        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('bg-opacity-75')) {
                closeModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('detailModal');
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>
</x-app-layout>
