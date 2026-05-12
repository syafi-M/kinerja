@props(['fullWidth' => false])
<!DOCTYPE html>
<html lang="en">

<x-admin-layout.head />

<body class="font-sans antialiased">
    <x-flasher />
    <x-session-toast />
    <x-flasher-theme />
    @php
        $isDashboardActive = request()->routeIs('admin.index');
        $isUserMenuActive = request()->routeIs('admin.user.*');
        $isDivisiMenuActive = request()->routeIs('admin.divisi.*') || request()->routeIs('admin.jabatan.*');
        $isKlienMenuActive =
            request()->routeIs('admin.client.*') || request()->routeIs('admin.kerjasama.*') || request()->routeIs('admin.lokasi.*');
        $isShiftMenuActive = request()->routeIs('admin.shift.*') || request()->routeIs('admin.jadwal.*');
        $isAbsensiMenuActive =
            request()->routeIs('admin.absen') ||
            request()->routeIs('admin.izin.*') ||
            request()->routeIs('lemburList') ||
            request()->routeIs('admin.report-sholat.*');
        $isPoinMenuActive = request()->routeIs('admin.point.*');
        $isPerlengkapanMenuActive = request()->routeIs('admin.perlengkapan.*');
        $isLaporanMenuActive =
            request()->routeIs('laporan.*') || request()->routeIs('laporanMitra.*') || request()->routeIs('qrcode.*');
        $isCheckpointMenuActive = request()->routeIs('admin.cp.*') || request()->routeIs('admin.pekerjaan-cp.*');
        $isBeritaMenuActive = request()->routeIs('admin.news.*');
        $isRekapMenuActive = request()->routeIs('admin.rekap.*');
        $isGajiMenuActive = request()->routeIs('admin.slip.index');

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
        isDashboardActive: {{ $isDashboardActive ? 'true' : 'false' }},
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
