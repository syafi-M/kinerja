@props(['title' => 'Dashboard Mitra', 'maxWidth' => 'max-w-6xl'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'SAC-PONOROGO') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/jqueryNew.min.js') }}"></script>

    <style>
        body { overflow-x: hidden; transition: background-color 0.8s ease; }
        .theme-card { transition: all 0.5s ease; }
        #sidebar { transition: width 0.25s ease, transform 0.3s ease; }

        @media (min-width: 1024px) {
            #appContent {
                padding-left: 18rem;
                transition: padding-left 0.25s ease;
            }
            body.sidebar-collapsed #sidebar {
                width: 5.5rem !important;
            }
            body.sidebar-collapsed #appContent {
                padding-left: 5.5rem;
            }
            body.sidebar-collapsed #sidebar .sidebar-text,
            body.sidebar-collapsed #sidebar .sidebar-section-title,
            body.sidebar-collapsed #sidebar .sidebar-user-card,
            body.sidebar-collapsed #sidebar .sidebar-logout-text {
                display: none;
            }
            body.sidebar-collapsed #sidebar .sidebar-brand,
            body.sidebar-collapsed #sidebar .sidebar-item,
            body.sidebar-collapsed #sidebar .sidebar-logout-btn {
                justify-content: center;
            }
            body.sidebar-collapsed #sidebar .sidebar-indicator {
                display: none;
            }
        }
    </style>
</head>
<body id="mainBody" class="font-sans antialiased text-slate-200 bg-slate-800">
    @php
        $primaryMenus = [
            ['label' => 'Dashboard', 'route' => 'dashboard.index', 'icon' => 'ri-dashboard-3-line'],
            ['label' => 'Kehadiran', 'route' => 'mitra_absensi', 'icon' => 'ri-fingerprint-line'],
            ['label' => 'Karyawan', 'route' => 'mitra_user', 'icon' => 'ri-group-line'],
        ];
        $operationMenus = [
            ['label' => 'Laporan', 'route' => 'mitra_laporan', 'icon' => 'ri-file-list-3-line'],
            ['label' => 'Lembur', 'route' => 'mitra_lembur', 'icon' => 'ri-time-line'],
            ['label' => 'Izin', 'route' => 'mitra_izin', 'icon' => 'ri-article-line'],
            ['label' => 'Rekap Bulanan', 'route' => 'mitra_rekap', 'icon' => 'ri-calendar-schedule-line'],
        ];
    @endphp

    <div class="relative min-h-screen lg:flex">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex flex-col max-h-screen p-4 transition-transform duration-300 -translate-x-full border-r shadow-xl w-72 bg-slate-900/95 border-slate-700 lg:translate-x-0">
            <div class="flex items-center gap-3 px-2 py-3 sidebar-brand">
                <div class="flex items-center justify-center bg-blue-500 rounded-lg shadow-lg w-9 h-9 shadow-blue-500/20">
                    <i class="text-lg text-white ri-dashboard-3-fill"></i>
                </div>
                <div class="sidebar-text">
                    <p class="text-xs tracking-widest uppercase text-slate-400">Portal Mitra</p>
                    <p class="text-sm font-bold text-white">Dashboard</p>
                </div>
            </div>

            <div class="px-3 py-3 mt-3 border sidebar-user-card rounded-2xl bg-slate-800/80 border-slate-700">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Akun Aktif</p>
                <p class="mt-1 text-sm font-semibold text-white truncate">{{ auth()->user()->nama_lengkap ?? auth()->user()->name }}</p>
                <p class="text-xs truncate text-slate-400">{{ auth()->user()->email }}</p>
            </div>

            <nav class="flex-1 mt-6 overflow-y-auto">
                <p class="sidebar-section-title px-2 mb-2 text-[10px] font-black uppercase tracking-[0.22em] text-slate-500">Menu Utama</p>
                <div class="space-y-1">
                    @foreach($primaryMenus as $menu)
                        @if(Route::has($menu['route']))
                            @php $isActive = request()->routeIs($menu['route']) || request()->routeIs($menu['route'] . '.*'); @endphp
                            <a href="{{ route($menu['route']) }}"
                               title="{{ $menu['label'] }}"
                               class="sidebar-item relative flex items-center gap-3 px-3 py-2 rounded-xl border transition {{ $isActive ? 'bg-blue-500/20 text-blue-300 border-blue-500/30' : 'border-transparent text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span class="sidebar-indicator absolute left-0 w-1 rounded-r {{ $isActive ? 'h-6 bg-blue-400' : 'h-0' }}"></span>
                                <i class="{{ $menu['icon'] }} text-lg"></i>
                                <span class="text-sm font-semibold sidebar-text">{{ $menu['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>

                <p class="sidebar-section-title px-2 mt-2.5 mb-2 text-[10px] font-black uppercase tracking-[0.22em] text-slate-500">Operasional</p>
                <div class="space-y-1">
                    @foreach($operationMenus as $menu)
                        @if(Route::has($menu['route']))
                            @php $isActive = request()->routeIs($menu['route']) || request()->routeIs($menu['route'] . '.*'); @endphp
                            <a href="{{ route($menu['route']) }}"
                               title="{{ $menu['label'] }}"
                               class="sidebar-item relative flex items-center gap-3 px-3 py-2 rounded-xl border transition {{ $isActive ? 'bg-blue-500/20 text-blue-300 border-blue-500/30' : 'border-transparent text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span class="sidebar-indicator absolute left-0 w-1 rounded-r {{ $isActive ? 'h-6 bg-blue-400' : 'h-0' }}"></span>
                                <i class="{{ $menu['icon'] }} text-lg"></i>
                                <span class="text-sm font-semibold sidebar-text">{{ $menu['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </nav>

            <div class="pt-4 mt-4 border-t border-slate-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-logout-btn flex items-center justify-center w-full gap-2 px-4 py-2.5 text-xs font-bold transition rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white">
                        <i class="ri-logout-circle-r-line"></i>
                        <span class="sidebar-logout-text">LOGOUT</span>
                    </button>
                </form>
            </div>
        </aside>

        <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>

        <div id="appContent" class="flex flex-col flex-1 min-h-screen">
            <nav id="navbar" class="sticky top-0 z-30 border-b shadow-sm bg-slate-700 border-slate-600">
                <div class="flex items-center justify-between h-16 px-4 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button id="sidebarToggle" type="button" class="inline-flex items-center justify-center w-10 h-10 border rounded-lg lg:hidden border-slate-500 text-slate-200 hover:bg-slate-600">
                            <i class="text-lg ri-menu-line"></i>
                        </button>
                        <button id="sidebarCollapseToggle" type="button" class="items-center justify-center hidden w-10 h-10 border rounded-lg lg:inline-flex border-slate-500 text-slate-200 hover:bg-slate-600" title="Collapse sidebar">
                            <i id="sidebarCollapseIcon" class="text-lg ri-layout-left-2-line"></i>
                        </button>
                        <span class="text-sm font-bold tracking-tight text-white uppercase">{{ $title }}</span>
                    </div>
                </div>
            </nav>

            <main class="flex-1 pb-24">
                <div class="{{ $maxWidth }} px-4 py-8 mx-auto lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <script>
        function updateTheme() {
            const hour = new Date().getHours();
            const body = document.getElementById('mainBody');
            const navbar = document.getElementById('navbar');
            const sidebar = document.getElementById('sidebar');

            // Hapus class lama
            body.classList.remove('bg-slate-800', 'bg-slate-900', 'bg-zinc-800');
            navbar.classList.remove('bg-slate-700', 'bg-slate-800', 'bg-zinc-700');
            if (sidebar) {
                sidebar.classList.remove('bg-slate-900/95', 'bg-slate-800/95', 'bg-zinc-900/95');
            }

            if (hour >= 5 && hour < 15) {
                body.classList.add('bg-slate-800');
                navbar.classList.add('bg-slate-700', 'border-slate-600');
                if (sidebar) sidebar.classList.add('bg-slate-900/95');
            } else if (hour >= 15 && hour < 18) {
                body.classList.add('bg-zinc-800');
                navbar.classList.add('bg-zinc-700', 'border-zinc-600');
                if (sidebar) sidebar.classList.add('bg-zinc-900/95');
            } else {
                body.classList.add('bg-slate-900');
                navbar.classList.add('bg-slate-800', 'border-slate-700');
                if (sidebar) sidebar.classList.add('bg-slate-800/95');
            }
        }

        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarCollapseToggle = document.getElementById('sidebarCollapseToggle');
        const sidebarCollapseIcon = document.getElementById('sidebarCollapseIcon');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            if (!sidebar || !sidebarOverlay) return;
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            if (!sidebar || !sidebarOverlay) return;
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', openSidebar);
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        function setSidebarCollapsed(collapsed) {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            if (sidebarCollapseIcon) {
                sidebarCollapseIcon.className = collapsed
                    ? 'text-lg ri-layout-right-2-line'
                    : 'text-lg ri-layout-left-2-line';
            }
            try {
                localStorage.setItem('mitra_sidebar_collapsed', collapsed ? '1' : '0');
            } catch (error) {}
        }

        if (window.innerWidth >= 1024) {
            try {
                const stored = localStorage.getItem('mitra_sidebar_collapsed') === '1';
                setSidebarCollapsed(stored);
            } catch (error) {}
        }

        if (sidebarCollapseToggle) {
            sidebarCollapseToggle.addEventListener('click', function () {
                const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                setSidebarCollapsed(!isCollapsed);
            });
        }

        updateTheme();
        setInterval(updateTheme, 60000);
    </script>
</body>
</html>
