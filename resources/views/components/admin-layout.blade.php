@props(['fullWidth' => false])
<!DOCTYPE html>
<html lang="en">

<x-admin-layout.head />

<body class="font-sans antialiased">
    @php
        $isDashboardActive = request()->routeIs('admin.index');
        $isUserMenuActive = request()->routeIs('users.*');
        $isDivisiMenuActive = request()->routeIs('divisi.*') || request()->routeIs('jabatan.*');
        $isKlienMenuActive =
            request()->routeIs('data-client.*') || request()->routeIs('kerjasama.*') || request()->routeIs('lokasi.*');
        $isShiftMenuActive = request()->routeIs('shift.*') || request()->routeIs('admin-jadwal.*');
        $isAbsensiMenuActive =
            request()->routeIs('admin.absen') ||
            request()->routeIs('data-izin.admin') ||
            request()->routeIs('lemburList') ||
            request()->routeIs('reportSholat.*');
        $isPoinMenuActive = request()->routeIs('point.*');
        $isPerlengkapanMenuActive = request()->routeIs('perlengkapan.*');
        $isLaporanMenuActive =
            request()->routeIs('laporan.*') || request()->routeIs('laporanMitra.*') || request()->routeIs('qrcode.*');
        $isCheckpointMenuActive = request()->routeIs('admin.cp.*') || request()->routeIs('pekerjaanCp.*');
        $isBeritaMenuActive = request()->routeIs('news.*');
        $isRekapMenuActive = request()->routeIs('admin.rekap.*');
        $isGajiMenuActive = request()->routeIs('admin-slip');

        $activeMenu = null;
        if ($isUserMenuActive) {
            $activeMenu = 'user';
        } elseif ($isDivisiMenuActive) {
            $activeMenu = 'divisi';
        } elseif ($isKlienMenuActive) {
            $activeMenu = 'klien';
        } elseif ($isShiftMenuActive) {
            $activeMenu = 'shift';
        } elseif ($isAbsensiMenuActive) {
            $activeMenu = 'absensi';
        } elseif ($isPoinMenuActive) {
            $activeMenu = 'poin';
        } elseif ($isPerlengkapanMenuActive) {
            $activeMenu = 'perlengkapan';
        } elseif ($isLaporanMenuActive) {
            $activeMenu = 'laporan';
        } elseif ($isCheckpointMenuActive) {
            $activeMenu = 'checkpoint';
        } elseif ($isBeritaMenuActive) {
            $activeMenu = 'berita';
        } elseif ($isRekapMenuActive) {
            $activeMenu = 'rekap';
        } elseif ($isGajiMenuActive) {
            $activeMenu = 'gaji';
        }
    @endphp
    <div class="flex min-h-screen bg-gradient-to-br from-gray-100 via-blue-50 to-indigo-100" x-data="{
        sidebarOpen: {{ $isDashboardActive ? 'true' : 'false' }},
        openMenu: {!! $activeMenu ? '\'' . $activeMenu . '\'' : 'null' !!}
    }">

        <x-admin-layout.sidebar :is-dashboard-active="$isDashboardActive" :active-menu="$activeMenu" :is-user-menu-active="$isUserMenuActive"
            :is-divisi-menu-active="$isDivisiMenuActive" :is-klien-menu-active="$isKlienMenuActive" :is-shift-menu-active="$isShiftMenuActive"
            :is-absensi-menu-active="$isAbsensiMenuActive" :is-poin-menu-active="$isPoinMenuActive"
            :is-perlengkapan-menu-active="$isPerlengkapanMenuActive" :is-laporan-menu-active="$isLaporanMenuActive"
            :is-checkpoint-menu-active="$isCheckpointMenuActive" :is-berita-menu-active="$isBeritaMenuActive"
            :is-rekap-menu-active="$isRekapMenuActive" :is-gaji-menu-active="$isGajiMenuActive" />

        <x-admin-layout.main :full-width="$fullWidth" :header-title="$headerTitle ?? null" :online="$online ?? null" :ip="$ip ?? null">
            {{ $slot }}
        </x-admin-layout.main>
    </div>

    <x-admin-layout.search-script />
    @stack('scripts')
</body>

</html>
