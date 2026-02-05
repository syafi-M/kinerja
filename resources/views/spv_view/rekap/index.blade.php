<x-app-layout>
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
                            <a href="{{ route('manajemen_rekap_indexOvertimes', $mitra->id) }}"
                                class="w-full sm:w-auto flex-shrink-0 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center gap-2 group">
                                <span>Lihat Detail</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
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
    </x-main-div>

    @push('scripts')
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
        </script>
    @endpush
</x-app-layout>
