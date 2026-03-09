@props([
    'isDashboardActive' => false,
    'activeMenu' => null,
    'isUserMenuActive' => false,
    'isDivisiMenuActive' => false,
    'isKlienMenuActive' => false,
    'isShiftMenuActive' => false,
    'isAbsensiMenuActive' => false,
    'isPoinMenuActive' => false,
    'isPerlengkapanMenuActive' => false,
    'isLaporanMenuActive' => false,
    'isCheckpointMenuActive' => false,
    'isBeritaMenuActive' => false,
    'isRekapMenuActive' => false,
    'isGajiMenuActive' => false,
])

<!-- SIDEBAR -->
<aside :class="sidebarOpen ? 'w-60' : 'w-24'"
    class="fixed inset-y-0 left-0 z-50 transition-all duration-300 bg-white border-r shadow-xl border-gray-200/50 backdrop-blur-xl bg-white/80">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="flex items-center h-16 px-4 border-b border-gray-100">
            <div
                class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl shrink-0">
                <i class="text-xl text-white ri-flashlight-fill"></i>
            </div>
            <span x-show="sidebarOpen" class="ml-3 text-lg font-bold text-gray-800">KINERJA APP</span>
            <!-- Toggle Sidebar Button -->
            <button @click="sidebarOpen = !sidebarOpen"
                class="p-2 ml-auto text-gray-600 transition rounded-lg shrink-0 hover:bg-gray-100">
                <i class="text-xl" :class="sidebarOpen ? 'ri-indent-decrease' : 'ri-indent-increase'"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
            <!-- Dashboard Link (Tetap) -->
            <a href="{{ route('admin.index') }}"
                class="flex items-center p-3 transition rounded-xl group {{ $isDashboardActive ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }}">
                <i class="text-xl ri-dashboard-3-line"></i>
                <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Dashboard</span>
            </a>

            <!-- Menu User (Blue) -->
            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'user' ? null : 'user')"
                    :class="(openMenu === 'user' || {{ $isUserMenuActive ? 'true' : 'false' }}) ?
                    'bg-blue-50 border-blue-200 text-blue-600' : 'text-gray-600 hover:bg-blue-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">

                    <div :class="(openMenu === 'user' || {{ $isUserMenuActive ? 'true' : 'false' }}) ?
                    'bg-blue-100 text-blue-600' : 'bg-gray-100 group-hover:bg-blue-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-folder-user-line"></i>
                    </div>

                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">User</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'user' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'user' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('users.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('users.index') ? 'text-blue-700 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50' }}"><i
                            class="mr-3 ri-user-line"></i>Data User</a>
                    <a href="{{ route('users.create') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('users.create') ? 'text-blue-700 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50' }}"><i
                            class="mr-3 ri-add-line"></i>Tambah User</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'divisi' ? null : 'divisi')"
                    :class="(openMenu === 'divisi' || {{ $isDivisiMenuActive ? 'true' : 'false' }}) ?
                    'bg-purple-50 border-purple-200 text-purple-600' : 'text-gray-600 hover:bg-purple-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'divisi' || {{ $isDivisiMenuActive ? 'true' : 'false' }}) ?
                    'bg-purple-100 text-purple-600' : 'bg-gray-100 group-hover:bg-purple-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-organization-chart"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Divisi & Jabatan</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'divisi' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'divisi' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('divisi.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('divisi.index') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i
                            class="mr-3 ri-building-4-line"></i>Data Divisi</a>
                    <a href="{{ route('divisi.create') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('divisi.create') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i
                            class="mr-3 ri-add-line"></i>Tambah Divisi</a>
                    <a href="{{ route('jabatan.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('jabatan.index') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i
                            class="mr-3 ri-award-line"></i>Data Jabatan</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'klien' ? null : 'klien')"
                    :class="(openMenu === 'klien' || {{ $isKlienMenuActive ? 'true' : 'false' }}) ?
                    'bg-green-50 border-green-200 text-green-600' : 'text-gray-600 hover:bg-green-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'klien' || {{ $isKlienMenuActive ? 'true' : 'false' }}) ?
                    'bg-green-100 text-green-600' : 'bg-gray-100 group-hover:bg-green-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-briefcase-4-line"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Klien & Lokasi</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'klien' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'klien' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('data-client.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('data-client.index') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"><i
                            class="mr-3 ri-briefcase-line"></i>Data Client</a>
                    <a href="{{ route('data-client.create') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('data-client.create') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"><i
                            class="mr-3 ri-add-line"></i>Tambah Client</a>
                    <a href="{{ route('kerjasama.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('kerjasama.index') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"><i
                            class="mr-3 ri-file-list-3-line"></i>Data Kerjasama</a>
                    <a href="{{ route('lokasi.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('lokasi.index') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"><i
                            class="mr-3 ri-pin-distance-line"></i>Data Lokasi</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'shift' ? null : 'shift')"
                    :class="(openMenu === 'shift' || {{ $isShiftMenuActive ? 'true' : 'false' }}) ?
                    'bg-indigo-50 border-indigo-200 text-indigo-600' : 'text-gray-600 hover:bg-indigo-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'shift' || {{ $isShiftMenuActive ? 'true' : 'false' }}) ?
                    'bg-indigo-100 text-indigo-600' : 'bg-gray-100 group-hover:bg-indigo-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-calendar-event-line"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Shift & Jadwal</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'shift' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'shift' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('shift.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('shift.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50' }}"><i
                            class="mr-3 ri-calendar-2-line"></i>Data Shift</a>
                    <a href="{{ route('admin-jadwal.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('admin-jadwal.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50' }}"><i
                            class="mr-3 ri-calendar-event-line"></i>Jadwal User</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'absensi' ? null : 'absensi')"
                    :class="(openMenu === 'absensi' || {{ $isAbsensiMenuActive ? 'true' : 'false' }}) ?
                    'bg-gray-100 border-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-100'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'absensi' || {{ $isAbsensiMenuActive ? 'true' : 'false' }}) ?
                    'bg-gray-200 text-gray-800' : 'bg-gray-100 group-hover:bg-gray-200'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-list-check-3"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Absensi</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'absensi' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'absensi' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('admin.absen') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('admin.absen') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}"><i
                            class="mr-3 ri-list-check-3"></i>Data Absensi</a>
                    <a href="{{ route('data-izin.admin') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('data-izin.admin') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}"><i
                            class="mr-3 ri-shield-user-line"></i>Data Izin</a>
                    <a href="{{ route('lemburList') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('lemburList') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}"><i
                            class="mr-3 ri-hourglass-2-line"></i>Data Lembur</a>
                    <a href="{{ route('reportSholat.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('reportSholat.index') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}"><i
                            class="mr-3 ri-shield-check-line"></i>Data Sholat</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'poin' ? null : 'poin')"
                    :class="(openMenu === 'poin' || {{ $isPoinMenuActive ? 'true' : 'false' }}) ?
                    'bg-yellow-50 border-yellow-200 text-yellow-600' : 'text-gray-600 hover:bg-yellow-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'poin' || {{ $isPoinMenuActive ? 'true' : 'false' }}) ?
                    'bg-yellow-100 text-yellow-600' : 'bg-gray-100 group-hover:bg-yellow-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-star-line"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Poin</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'poin' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'poin' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('point.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('point.index') ? 'text-yellow-700 bg-yellow-50' : 'text-gray-500 hover:text-yellow-600 hover:bg-yellow-50' }}"><i
                            class="mr-3 ri-star-line"></i>Data Poin</a>
                    <a href="{{ route('point.create') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('point.create') ? 'text-yellow-700 bg-yellow-50' : 'text-gray-500 hover:text-yellow-600 hover:bg-yellow-50' }}"><i
                            class="mr-3 ri-add-line"></i>Tambah Poin</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'perlengkapan' ? null : 'perlengkapan')"
                    :class="(openMenu === 'perlengkapan' || {{ $isPerlengkapanMenuActive ? 'true' : 'false' }}) ?
                    'bg-teal-50 border-teal-200 text-teal-600' : 'text-gray-600 hover:bg-teal-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'perlengkapan' || {{ $isPerlengkapanMenuActive ? 'true' : 'false' }}) ?
                    'bg-teal-100 text-teal-600' : 'bg-gray-100 group-hover:bg-teal-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-tools-line"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Perlengkapan</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'perlengkapan' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'perlengkapan' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('perlengkapan.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('perlengkapan.index') ? 'text-teal-700 bg-teal-50' : 'text-gray-500 hover:text-teal-600 hover:bg-teal-50' }}"><i
                            class="mr-3 ri-hammer-line"></i>Data Perlengkapan</a>
                    <a href="{{ route('perlengkapan.create') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('perlengkapan.create') ? 'text-teal-700 bg-teal-50' : 'text-gray-500 hover:text-teal-600 hover:bg-teal-50' }}"><i
                            class="mr-3 ri-add-line"></i>Tambah Perlengkapan</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'laporan' ? null : 'laporan')"
                    :class="(openMenu === 'laporan' || {{ $isLaporanMenuActive ? 'true' : 'false' }}) ?
                    'bg-red-50 border-red-200 text-red-600' : 'text-gray-600 hover:bg-red-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'laporan' || {{ $isLaporanMenuActive ? 'true' : 'false' }}) ?
                    'bg-red-100 text-red-600' : 'bg-gray-100 group-hover:bg-red-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-task-line"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Laporan</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'laporan' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'laporan' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('laporanMitra.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('laporanMitra.index') ? 'text-red-700 bg-red-50' : 'text-gray-500 hover:text-red-600 hover:bg-red-50' }}"><i
                            class="mr-3 ri-file-text-line"></i>Laporan Mitra</a>
                    <a href="{{ route('qrcode.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('qrcode.index') ? 'text-red-700 bg-red-50' : 'text-gray-500 hover:text-red-600 hover:bg-red-50' }}"><i
                            class="mr-3 ri-qr-code-line"></i>Data QR Code</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'checkpoint' ? null : 'checkpoint')"
                    :class="(openMenu === 'checkpoint' || {{ $isCheckpointMenuActive ? 'true' : 'false' }}) ?
                    'bg-purple-50 border-purple-200 text-purple-600' : 'text-gray-600 hover:bg-purple-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'checkpoint' || {{ $isCheckpointMenuActive ? 'true' : 'false' }}) ?
                    'bg-purple-100 text-purple-600' : 'bg-gray-100 group-hover:bg-purple-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-check-double-line"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Checkpoint</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'checkpoint' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'checkpoint' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('admin.cp.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('admin.cp.index') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i
                            class="mr-3 ri-check-double-line"></i>Data Checkpoint</a>
                    <a href="{{ route('pekerjaanCp.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('pekerjaanCp.index') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i
                            class="mr-3 ri-file-list-line"></i>Data Pekerjaan CP</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'berita' ? null : 'berita')"
                    :class="(openMenu === 'berita' || {{ $isBeritaMenuActive ? 'true' : 'false' }}) ?
                    'bg-indigo-50 border-indigo-200 text-indigo-600' : 'text-gray-600 hover:bg-indigo-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'berita' || {{ $isBeritaMenuActive ? 'true' : 'false' }}) ?
                    'bg-indigo-100 text-indigo-600' : 'bg-gray-100 group-hover:bg-indigo-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-newspaper-line"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Berita</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'berita' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'berita' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('news.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('news.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50' }}"><i
                            class="mr-3 ri-news-line"></i>Data Berita</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'rekap' ? null : 'rekap')"
                    :class="(openMenu === 'rekap' || {{ $isRekapMenuActive ? 'true' : 'false' }}) ?
                    'bg-sky-50 border-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300 focus-visible:ring-offset-2">
                    <div :class="(openMenu === 'rekap' || {{ $isRekapMenuActive ? 'true' : 'false' }}) ?
                    'bg-sky-100 text-sky-700' : 'bg-gray-100 group-hover:bg-sky-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-file-chart-line"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Rekapitulasi</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'rekap' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'rekap' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('admin.rekap.index') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300 focus-visible:ring-offset-2 {{ request()->routeIs('admin.rekap.index') || request()->routeIs('admin.rekap.overtimes') || request()->routeIs('admin.rekap.person-out') || request()->routeIs('admin.rekap.person-in') || request()->routeIs('admin.rekap.cutting') || request()->routeIs('admin.rekap.finished-training') ? 'text-sky-700 bg-sky-50' : 'text-gray-500 hover:text-sky-600 hover:bg-sky-50' }}"><i
                            class="mr-3 ri-dashboard-line"></i>Dashboard Rekap</a>
                    <a href="{{ route('admin.rekap.settings') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300 focus-visible:ring-offset-2 {{ request()->routeIs('admin.rekap.settings') ? 'text-sky-700 bg-sky-50' : 'text-gray-500 hover:text-sky-600 hover:bg-sky-50' }}"><i
                            class="mr-3 ri-settings-line"></i>Pengaturan Rekap</a>
                </div>
            </div>

            <div class="space-y-1">
                <button @click="openMenu = (openMenu === 'gaji' ? null : 'gaji')"
                    :class="(openMenu === 'gaji' || {{ $isGajiMenuActive ? 'true' : 'false' }}) ?
                    'bg-blue-50 border-blue-200 text-blue-600' : 'text-gray-600 hover:bg-blue-50'"
                    class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                    <div :class="(openMenu === 'gaji' || {{ $isGajiMenuActive ? 'true' : 'false' }}) ?
                    'bg-blue-100 text-blue-600' : 'bg-gray-100 group-hover:bg-blue-100'"
                        class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                        <i class="text-lg ri-file-text-line"></i>
                    </div>
                    <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                        <span class="text-sm font-semibold">Slip Gaji</span>
                        <i class="transition-transform duration-200 ri-arrow-down-s-line"
                            :class="openMenu === 'gaji' ? 'rotate-180' : ''"></i>
                    </div>
                </button>
                <div x-show="openMenu === 'gaji' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('admin-slip') }}"
                        class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('admin-slip') ? 'text-blue-700 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50' }}"><i
                            class="mr-3 ri-file-list-line"></i>Data Slip Gaji</a>
                </div>
            </div>
        </nav>

        <!-- Sidebar Footer (Logout) -->
        <div class="p-4 border-t border-gray-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center w-full p-3 text-red-500 transition hover:bg-red-50 rounded-xl">
                    <i class="text-xl ri-logout-box-r-line"></i>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
