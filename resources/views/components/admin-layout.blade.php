@props(['fullWidth' => false])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Admin Kinerja SAC-PONOROGO</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Webcam CDN -->
    <script src="{{ URL::asset('js/jqueryNew.min.js') }}"></script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        /* Global polish for legacy admin pages migrated from x-app-layout */
        .legacy-admin .btn {
            border-radius: 0.75rem;
            min-height: 2.4rem;
            height: 2.4rem;
            font-size: 0.8rem;
            font-weight: 600;
            padding-inline: 0.85rem;
            box-shadow: none;
        }
        .legacy-admin .input,
        .legacy-admin .select,
        .legacy-admin .file-input,
        .legacy-admin textarea {
            border-radius: 0.75rem;
            border-color: rgb(229 231 235);
            background-color: rgb(249 250 251);
            min-height: 2.5rem;
            height: 2.5rem;
            font-size: 0.85rem;
        }
        .legacy-admin textarea { min-height: 6rem; height: auto; }
        .legacy-admin .table {
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid rgb(243 244 246);
            background: white;
        }
        .legacy-admin .table :where(th) {
            background: rgb(249 250 251) !important;
            color: rgb(75 85 99);
            font-size: 0.72rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .legacy-admin .table :where(td) {
            font-size: 0.85rem;
            color: rgb(55 65 81);
            vertical-align: top;
        }
        .legacy-admin .table tr:hover td { background: rgb(239 246 255 / 0.55); }
        .legacy-admin .bg-slate-100,
        .legacy-admin .bg-slate-50,
        .legacy-admin .bg-slate-500 {
            background: white !important;
            border: 1px solid rgb(243 244 246);
            border-radius: 1rem;
        }
        .legacy-admin .shadow,
        .legacy-admin .shadow-md {
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
        }
        .legacy-admin .rounded-md { border-radius: 0.85rem !important; }
        .legacy-admin .rounded { border-radius: 0.65rem !important; }
    </style>

</head>
<body class="font-sans antialiased">
    @php
        $isDashboardActive = request()->routeIs('admin.index');
        $isUserMenuActive = request()->routeIs('users.*');
        $isDivisiMenuActive = request()->routeIs('divisi.*') || request()->routeIs('jabatan.*');
        $isKlienMenuActive = request()->routeIs('data-client.*') || request()->routeIs('kerjasama.*') || request()->routeIs('lokasi.*');
        $isShiftMenuActive = request()->routeIs('shift.*') || request()->routeIs('admin-jadwal.*');
        $isAbsensiMenuActive = request()->routeIs('admin.absen') || request()->routeIs('data-izin.admin') || request()->routeIs('lemburList') || request()->routeIs('reportSholat.*');
        $isPoinMenuActive = request()->routeIs('point.*');
        $isPerlengkapanMenuActive = request()->routeIs('perlengkapan.*');
        $isLaporanMenuActive = request()->routeIs('laporan.*') || request()->routeIs('laporanMitra.*') || request()->routeIs('qrcode.*');
        $isCheckpointMenuActive = request()->routeIs('admin.cp.*') || request()->routeIs('pekerjaanCp.*');
        $isBeritaMenuActive = request()->routeIs('news.*');
        $isRekapMenuActive = request()->routeIs('admin.rekap.*');
        $isGajiMenuActive = request()->routeIs('admin-slip');

        $activeMenu = null;
        if ($isUserMenuActive) $activeMenu = 'user';
        elseif ($isDivisiMenuActive) $activeMenu = 'divisi';
        elseif ($isKlienMenuActive) $activeMenu = 'klien';
        elseif ($isShiftMenuActive) $activeMenu = 'shift';
        elseif ($isAbsensiMenuActive) $activeMenu = 'absensi';
        elseif ($isPoinMenuActive) $activeMenu = 'poin';
        elseif ($isPerlengkapanMenuActive) $activeMenu = 'perlengkapan';
        elseif ($isLaporanMenuActive) $activeMenu = 'laporan';
        elseif ($isCheckpointMenuActive) $activeMenu = 'checkpoint';
        elseif ($isBeritaMenuActive) $activeMenu = 'berita';
        elseif ($isRekapMenuActive) $activeMenu = 'rekap';
        elseif ($isGajiMenuActive) $activeMenu = 'gaji';
    @endphp
    <div class="flex min-h-screen bg-gradient-to-br from-gray-100 via-blue-50 to-indigo-100"
        x-data="{
            isDashboardPage: {{ $isDashboardActive ? 'true' : 'false' }},
            sidebarOpen: {{ $isDashboardActive ? 'true' : 'false' }},
            openMenu: {!! $activeMenu ? '\'' . $activeMenu . '\'' : 'null' !!},
            handleSidebarEnter() {
                if (!this.isDashboardPage) this.sidebarOpen = true;
            },
            handleSidebarLeave() {
                if (!this.isDashboardPage) this.sidebarOpen = false;
            }
        }">
        
        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'w-56' : 'w-16'" @mouseenter="handleSidebarEnter()" @mouseleave="handleSidebarLeave()" class="fixed inset-y-0 left-0 z-50 transition-all duration-300 bg-white border-r shadow-xl border-gray-200/50 backdrop-blur-xl bg-white/80">
            <div class="flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="flex items-center h-16 px-4 border-b border-gray-100">
                    <div class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl shrink-0">
                        <i class="text-xl text-white ri-flashlight-fill"></i>
                    </div>
                    <span x-show="sidebarOpen" class="ml-3 text-lg font-bold text-gray-800 truncate">KINERJA APP</span>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
                    <!-- Dashboard Link (Tetap) -->
                    <a href="{{ route('admin.index') }}" class="flex items-center p-3 transition rounded-xl group {{ $isDashboardActive ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }}">
                        <i class="text-xl ri-dashboard-3-line"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Dashboard</span>
                    </a>

                    <!-- Menu User (Blue) -->
                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'user' ? null : 'user')"
                                :class="(openMenu === 'user' || {{ $isUserMenuActive ? 'true' : 'false' }}) ? 'bg-blue-50 border-blue-200 text-blue-600' : 'text-gray-600 hover:bg-blue-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            
                            <div :class="(openMenu === 'user' || {{ $isUserMenuActive ? 'true' : 'false' }}) ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 group-hover:bg-blue-100'"
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
                            <a href="{{ route('users.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('users.index') ? 'text-blue-700 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50' }}"><i class="mr-3 ri-user-line"></i>Data User</a>
                            <a href="{{ route('users.create') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('users.create') ? 'text-blue-700 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50' }}"><i class="mr-3 ri-add-line"></i>Tambah User</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'divisi' ? null : 'divisi')"
                                :class="(openMenu === 'divisi' || {{ $isDivisiMenuActive ? 'true' : 'false' }}) ? 'bg-purple-50 border-purple-200 text-purple-600' : 'text-gray-600 hover:bg-purple-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'divisi' || {{ $isDivisiMenuActive ? 'true' : 'false' }}) ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 group-hover:bg-purple-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-organization-chart"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Divisi & Jabatan</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'divisi' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'divisi' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('divisi.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('divisi.index') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i class="mr-3 ri-building-4-line"></i>Data Divisi</a>
                            <a href="{{ route('divisi.create') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('divisi.create') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i class="mr-3 ri-add-line"></i>Tambah Divisi</a>
                            <a href="{{ route('jabatan.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('jabatan.index') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i class="mr-3 ri-award-line"></i>Data Jabatan</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'klien' ? null : 'klien')"
                                :class="(openMenu === 'klien' || {{ $isKlienMenuActive ? 'true' : 'false' }}) ? 'bg-green-50 border-green-200 text-green-600' : 'text-gray-600 hover:bg-green-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'klien' || {{ $isKlienMenuActive ? 'true' : 'false' }}) ? 'bg-green-100 text-green-600' : 'bg-gray-100 group-hover:bg-green-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-briefcase-4-line"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Klien & Lokasi</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'klien' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'klien' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('data-client.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('data-client.index') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"><i class="mr-3 ri-briefcase-line"></i>Data Client</a>
                            <a href="{{ route('data-client.create') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('data-client.create') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"><i class="mr-3 ri-add-line"></i>Tambah Client</a>
                            <a href="{{ route('kerjasama.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('kerjasama.index') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"><i class="mr-3 ri-file-list-3-line"></i>Data Kerjasama</a>
                            <a href="{{ route('lokasi.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('lokasi.index') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"><i class="mr-3 ri-pin-distance-line"></i>Data Lokasi</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'shift' ? null : 'shift')"
                                :class="(openMenu === 'shift' || {{ $isShiftMenuActive ? 'true' : 'false' }}) ? 'bg-indigo-50 border-indigo-200 text-indigo-600' : 'text-gray-600 hover:bg-indigo-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'shift' || {{ $isShiftMenuActive ? 'true' : 'false' }}) ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 group-hover:bg-indigo-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-calendar-event-line"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Shift & Jadwal</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'shift' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'shift' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('shift.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('shift.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50' }}"><i class="mr-3 ri-calendar-2-line"></i>Data Shift</a>
                            <a href="{{ route('admin-jadwal.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('admin-jadwal.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50' }}"><i class="mr-3 ri-calendar-event-line"></i>Jadwal User</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'absensi' ? null : 'absensi')"
                                :class="(openMenu === 'absensi' || {{ $isAbsensiMenuActive ? 'true' : 'false' }}) ? 'bg-gray-100 border-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-100'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'absensi' || {{ $isAbsensiMenuActive ? 'true' : 'false' }}) ? 'bg-gray-200 text-gray-800' : 'bg-gray-100 group-hover:bg-gray-200'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-list-check-3"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Absensi</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'absensi' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'absensi' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('admin.absen') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('admin.absen') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}"><i class="mr-3 ri-list-check-3"></i>Data Absensi</a>
                            <a href="{{ route('data-izin.admin') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('data-izin.admin') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}"><i class="mr-3 ri-shield-user-line"></i>Data Izin</a>
                            <a href="{{ route('lemburList') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('lemburList') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}"><i class="mr-3 ri-hourglass-2-line"></i>Data Lembur</a>
                            <a href="{{ route('reportSholat.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('reportSholat.index') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}"><i class="mr-3 ri-shield-check-line"></i>Data Sholat</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'poin' ? null : 'poin')"
                                :class="(openMenu === 'poin' || {{ $isPoinMenuActive ? 'true' : 'false' }}) ? 'bg-yellow-50 border-yellow-200 text-yellow-600' : 'text-gray-600 hover:bg-yellow-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'poin' || {{ $isPoinMenuActive ? 'true' : 'false' }}) ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 group-hover:bg-yellow-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-star-line"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Poin</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'poin' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'poin' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('point.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('point.index') ? 'text-yellow-700 bg-yellow-50' : 'text-gray-500 hover:text-yellow-600 hover:bg-yellow-50' }}"><i class="mr-3 ri-star-line"></i>Data Poin</a>
                            <a href="{{ route('point.create') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('point.create') ? 'text-yellow-700 bg-yellow-50' : 'text-gray-500 hover:text-yellow-600 hover:bg-yellow-50' }}"><i class="mr-3 ri-add-line"></i>Tambah Poin</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'perlengkapan' ? null : 'perlengkapan')"
                                :class="(openMenu === 'perlengkapan' || {{ $isPerlengkapanMenuActive ? 'true' : 'false' }}) ? 'bg-teal-50 border-teal-200 text-teal-600' : 'text-gray-600 hover:bg-teal-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'perlengkapan' || {{ $isPerlengkapanMenuActive ? 'true' : 'false' }}) ? 'bg-teal-100 text-teal-600' : 'bg-gray-100 group-hover:bg-teal-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-tools-line"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Perlengkapan</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'perlengkapan' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'perlengkapan' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('perlengkapan.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('perlengkapan.index') ? 'text-teal-700 bg-teal-50' : 'text-gray-500 hover:text-teal-600 hover:bg-teal-50' }}"><i class="mr-3 ri-hammer-line"></i>Data Perlengkapan</a>
                            <a href="{{ route('perlengkapan.create') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('perlengkapan.create') ? 'text-teal-700 bg-teal-50' : 'text-gray-500 hover:text-teal-600 hover:bg-teal-50' }}"><i class="mr-3 ri-add-line"></i>Tambah Perlengkapan</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'laporan' ? null : 'laporan')"
                                :class="(openMenu === 'laporan' || {{ $isLaporanMenuActive ? 'true' : 'false' }}) ? 'bg-red-50 border-red-200 text-red-600' : 'text-gray-600 hover:bg-red-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'laporan' || {{ $isLaporanMenuActive ? 'true' : 'false' }}) ? 'bg-red-100 text-red-600' : 'bg-gray-100 group-hover:bg-red-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-task-line"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Laporan</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'laporan' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'laporan' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('laporanMitra.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('laporanMitra.index') ? 'text-red-700 bg-red-50' : 'text-gray-500 hover:text-red-600 hover:bg-red-50' }}"><i class="mr-3 ri-file-text-line"></i>Laporan Mitra</a>
                            <a href="{{ route('qrcode.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('qrcode.index') ? 'text-red-700 bg-red-50' : 'text-gray-500 hover:text-red-600 hover:bg-red-50' }}"><i class="mr-3 ri-qr-code-line"></i>Data QR Code</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'checkpoint' ? null : 'checkpoint')"
                                :class="(openMenu === 'checkpoint' || {{ $isCheckpointMenuActive ? 'true' : 'false' }}) ? 'bg-purple-50 border-purple-200 text-purple-600' : 'text-gray-600 hover:bg-purple-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'checkpoint' || {{ $isCheckpointMenuActive ? 'true' : 'false' }}) ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 group-hover:bg-purple-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-check-double-line"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Checkpoint</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'checkpoint' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'checkpoint' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('admin.cp.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('admin.cp.index') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i class="mr-3 ri-check-double-line"></i>Data Checkpoint</a>
                            <a href="{{ route('pekerjaanCp.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('pekerjaanCp.index') ? 'text-purple-700 bg-purple-50' : 'text-gray-500 hover:text-purple-600 hover:bg-purple-50' }}"><i class="mr-3 ri-file-list-line"></i>Data Pekerjaan CP</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'berita' ? null : 'berita')"
                                :class="(openMenu === 'berita' || {{ $isBeritaMenuActive ? 'true' : 'false' }}) ? 'bg-indigo-50 border-indigo-200 text-indigo-600' : 'text-gray-600 hover:bg-indigo-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'berita' || {{ $isBeritaMenuActive ? 'true' : 'false' }}) ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 group-hover:bg-indigo-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-newspaper-line"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Berita</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'berita' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'berita' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('news.index') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('news.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50' }}"><i class="mr-3 ri-news-line"></i>Data Berita</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'rekap' ? null : 'rekap')"
                                @focus="openMenu = 'rekap'"
                                :class="(openMenu === 'rekap' || {{ $isRekapMenuActive ? 'true' : 'false' }}) ? 'bg-sky-50 border-sky-200 text-sky-700' : 'text-gray-600 hover:bg-sky-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300 focus-visible:ring-offset-2">
                            <div :class="(openMenu === 'rekap' || {{ $isRekapMenuActive ? 'true' : 'false' }}) ? 'bg-sky-100 text-sky-700' : 'bg-gray-100 group-hover:bg-sky-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-file-text-line"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Slip Gaji</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'rekap' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'rekap' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('admin.rekap.settings') }}" class="flex items-center p-2 text-xs font-medium rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300 focus-visible:ring-offset-2 {{ request()->routeIs('admin.rekap.settings') ? 'text-sky-700 bg-sky-50' : 'text-gray-500 hover:text-sky-600 hover:bg-sky-50' }}"><i class="mr-3 ri-file-chart-line"></i>Data Rekap</a>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <button @click="openMenu = (openMenu === 'gaji' ? null : 'gaji')"
                                :class="(openMenu === 'gaji' || {{ $isGajiMenuActive ? 'true' : 'false' }}) ? 'bg-blue-50 border-blue-200 text-blue-600' : 'text-gray-600 hover:bg-blue-50'"
                                class="flex items-center w-full p-1 transition border border-transparent rounded-xl group">
                            <div :class="(openMenu === 'gaji' || {{ $isGajiMenuActive ? 'true' : 'false' }}) ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 group-hover:bg-blue-100'" class="flex items-center justify-center w-8 h-8 rounded-lg shrink-0">
                                <i class="text-lg ri-file-text-line"></i>
                            </div>
                            <div x-show="sidebarOpen" class="flex items-center justify-between flex-1 ml-3">
                                <span class="text-sm font-semibold">Slip Gaji</span>
                                <i class="transition-transform duration-200 ri-arrow-down-s-line" :class="openMenu === 'gaji' ? 'rotate-180' : ''"></i>
                            </div>
                        </button>
                        <div x-show="openMenu === 'gaji' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('admin-slip') }}" class="flex items-center p-2 text-xs font-medium rounded-lg {{ request()->routeIs('admin-slip') ? 'text-blue-700 bg-blue-50' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50' }}"><i class="mr-3 ri-file-list-line"></i>Data Slip Gaji</a>
                        </div>
                    </div>
                </nav>

                <!-- Sidebar Footer (Logout) -->
                <div class="p-4 border-t border-gray-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center w-full p-3 text-red-500 transition hover:bg-red-50 rounded-xl">
                            <i class="text-xl ri-logout-box-r-line"></i>
                            <span x-show="sidebarOpen" class="ml-3 text-sm font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div :class="sidebarOpen ? 'ml-56' : 'ml-16'" class="flex flex-col flex-1 transition-all duration-300">
            
            <!-- Sticky Header -->
            <header class="sticky top-0 z-40 border-b shadow-sm backdrop-blur-xl bg-white/70 border-gray-200/50">
                <div class="{{ $fullWidth ? 'px-4 sm:px-6 lg:px-8' : 'px-4 mx-auto max-w-7xl sm:px-6 lg:px-8' }}">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center gap-4">
                            <!-- Toggle Sidebar Button -->
                            <button @click="if (isDashboardPage) sidebarOpen = !sidebarOpen" class="p-2 text-gray-600 transition rounded-lg hover:bg-gray-100">
                                <i class="text-xl" :class="sidebarOpen ? 'ri-indent-decrease' : 'ri-indent-increase'"></i>
                            </button>
                            <div>
                                <h1 class="text-lg font-bold text-gray-900">{{ $headerTitle ?? 'Dashboard Admin' }}</h1>
                                <p class="hidden text-xs text-gray-500 sm:block">Welcome back, Admin</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 rounded-lg border border-emerald-200">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span class="text-xs font-medium text-emerald-700">{{ $online ?? '0' }} Online</span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg border border-gray-200">
                                <i class="text-sm text-gray-600 ri-global-line"></i>
                                <span class="font-mono text-xs text-gray-700">{{ $ip ?? '127.0.0.1' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-3 sm:p-4 lg:p-6">
                <div class="legacy-admin {{ $fullWidth ? 'w-full' : 'mx-auto max-w-7xl' }}">
                    {{ $slot }}
                </div>
            </main>

        </div>
    </div>
    <script>
        (function() {
            const searchInput = document.getElementById('searchInput');
            const searchTable = document.getElementById('searchTable');

            if (!searchInput || !searchTable) return;
            if (searchInput.dataset.searchMode === 'server') return;

            const tableBody = searchTable.tBodies && searchTable.tBodies.length ? searchTable.tBodies[0] : null;
            if (!tableBody) return;

            const rows = Array.from(tableBody.querySelectorAll('tr'));
            const emptyRow = tableBody.querySelector('[data-search-empty-row]') || null;
            let debounceTimer = null;

            function applySearch() {
                const keyword = (searchInput.value || '').trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach((row) => {
                    if (emptyRow && row === emptyRow) return;

                    const text = (row.textContent || '').toLowerCase();
                    const isMatch = keyword === '' || text.includes(keyword);
                    row.style.display = isMatch ? '' : 'none';
                    if (isMatch) visibleCount += 1;
                });

                if (emptyRow) {
                    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(applySearch, 180);
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    applySearch();
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
