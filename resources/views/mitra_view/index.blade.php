<x-mitra-layout title="Dashboard Mitra">
    @php
        $today = \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y');
        $hour = \Carbon\Carbon::now()->format('H');
        $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
        $jabatan = auth()->user()->divisi->jabatan->code_jabatan ?? auth()->user()->divisi->code_jabatan ?? '-';
    @endphp

    <!-- Header Section -->
    <div class="p-6 rounded-3xl mitra-panel mitra-mobile-card">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em] mitra-accent">Portal Mitra System</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight mitra-text-strong">{{ $greeting }}, {{ auth()->user()->name }}</h1>
                <p class="mt-1 text-sm mitra-text-soft">Kelola operasional dan pantau kinerja tim Anda dalam satu dasbor.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="px-3 py-1.5 text-xs font-bold border rounded-xl mitra-panel-soft mitra-text-soft">
                    <i class="mr-1 mitra-accent ri-calendar-line"></i> {{ $today }}
                </div>
                <div class="px-3 py-1.5 text-xs font-bold rounded-xl border mitra-theme-badge">
                    <i class="mr-1 ri-shield-user-line"></i> {{ $jabatan }}
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-4 mt-6 md:grid-cols-4">
        <div class="p-5 rounded-2xl mitra-panel-soft theme-card">
            <p class="text-[10px] font-black uppercase tracking-widest mitra-text-muted">Total Karyawan</p>
            <p class="mt-1 text-2xl font-black mitra-text-strong">{{ $jumlahKaryawan }}</p>
        </div>
        <div class="p-5 rounded-2xl mitra-panel-soft theme-card">
            <p class="text-[10px] font-black uppercase tracking-widest mitra-text-muted">Hadir Hari Ini</p>
            <p class="mt-1 text-2xl font-black" style="color: var(--mitra-success);">{{ $jumlahAbsensiHariIni }}</p>
        </div>
        <div class="p-5 rounded-2xl mitra-panel-soft theme-card">
            <p class="text-[10px] font-black uppercase tracking-widest mitra-text-muted">Lembur Berjalan</p>
            <p class="mt-1 text-2xl font-black" style="color: var(--mitra-warning);">{{ $jumlahLemburHariIni }}</p>
        </div>
        <div class="p-5 rounded-2xl mitra-panel-soft theme-card">
            <p class="text-[10px] font-black uppercase tracking-widest mitra-text-muted">Sistem Status</p>
            <p class="flex items-center gap-2 mt-1 text-sm font-black mitra-accent">
                <span class="relative flex w-2 h-2">
                    <span class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping" style="background: var(--mitra-accent);"></span>
                    <span class="relative inline-flex w-2 h-2 rounded-full" style="background: var(--mitra-accent-strong);"></span>
                </span>
                ONLINE
            </p>
        </div>
    </div>

    <!-- Menu Grid -->
    <div class="mt-10">
        <h2 class="mb-4 ml-1 text-xs font-black tracking-[0.3em] uppercase mitra-text-muted">Navigasi Utama</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            
            <!-- Kehadiran -->
            <a href="{{ route('mitra_absensi') }}" class="relative flex items-center gap-5 p-6 transition-all border group rounded-2xl mitra-panel-soft mitra-nav-card mitra-mobile-card mitra-mobile-stack hover:-translate-y-0.5">
                <div class="flex items-center justify-center transition-all shadow-inner w-14 h-14 rounded-xl group-hover:scale-110" style="color: var(--mitra-accent); background: var(--mitra-accent-soft);">
                    <i class="text-2xl ri-fingerprint-line"></i>
                </div>
                <div>
                    <p class="text-lg font-bold transition-colors mitra-text-strong">Kehadiran</p>
                    <p class="text-xs italic mitra-text-muted">Riwayat absensi tim</p>
                </div>
                <i class="absolute transition-all right-6 ri-arrow-right-s-line group-hover:translate-x-1 mitra-text-muted"></i>
            </a>

            <!-- Laporan -->
            <a href="{{ route('mitra_laporan') }}" class="relative flex items-center gap-5 p-6 transition-all border group rounded-2xl mitra-panel-soft mitra-nav-card mitra-mobile-card mitra-mobile-stack hover:-translate-y-0.5">
                <div class="flex items-center justify-center transition-all shadow-inner w-14 h-14 rounded-xl group-hover:scale-110" style="color: var(--mitra-success); background: color-mix(in srgb, var(--mitra-success) 14%, transparent);">
                    <i class="text-2xl ri-file-list-3-line"></i>
                </div>
                <div>
                    <p class="text-lg font-bold transition-colors mitra-text-strong">Laporan Kerja</p>
                    <p class="text-xs italic mitra-text-muted">Review aktivitas harian</p>
                </div>
                <i class="absolute transition-all right-6 ri-arrow-right-s-line group-hover:translate-x-1 mitra-text-muted"></i>
            </a>

            <!-- Lembur -->
            <a href="{{ route('mitra_lembur') }}" class="relative flex items-center gap-5 p-6 transition-all border group rounded-2xl mitra-panel-soft mitra-nav-card mitra-mobile-card mitra-mobile-stack hover:-translate-y-0.5">
                <div class="flex items-center justify-center transition-all shadow-inner w-14 h-14 rounded-xl group-hover:scale-110" style="color: var(--mitra-warning); background: color-mix(in srgb, var(--mitra-warning) 16%, transparent);">
                    <i class="text-2xl ri-time-line"></i>
                </div>
                <div>
                    <p class="text-lg font-bold transition-colors mitra-text-strong">Data Lembur</p>
                    <p class="text-xs italic mitra-text-muted">Kelola lembur karyawan</p>
                </div>
                <i class="absolute transition-all right-6 ri-arrow-right-s-line group-hover:translate-x-1 mitra-text-muted"></i>
            </a>

            <!-- Data User -->
            <a href="{{ route('mitra_user') }}" class="relative flex items-center gap-5 p-6 transition-all border group rounded-2xl mitra-panel-soft mitra-nav-card mitra-mobile-card mitra-mobile-stack hover:-translate-y-0.5">
                <div class="flex items-center justify-center transition-all shadow-inner w-14 h-14 rounded-xl group-hover:scale-110" style="color: #6366f1; background: rgba(99, 102, 241, 0.12);">
                    <i class="text-2xl ri-group-line"></i>
                </div>
                <div>
                    <p class="text-lg font-bold transition-colors mitra-text-strong">Data Karyawan</p>
                    <p class="text-xs italic mitra-text-muted">Daftar personel mitra</p>
                </div>
                <i class="absolute transition-all right-6 ri-arrow-right-s-line group-hover:translate-x-1 mitra-text-muted"></i>
            </a>

            <!-- Laporan Bulanan -->
            @if (Route::has('mitra_rekap'))
                <a href="{{ route('mitra_rekap') }}" class="relative flex items-center gap-5 p-6 transition-all border group rounded-2xl mitra-panel-soft mitra-nav-card mitra-mobile-card mitra-mobile-stack hover:-translate-y-0.5">
                    <div class="flex items-center justify-center transition-all shadow-inner w-14 h-14 rounded-xl group-hover:scale-110" style="color: #e11d48; background: rgba(225, 29, 72, 0.12);">
                        <i class="text-2xl ri-calendar-schedule-line"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold transition-colors mitra-text-strong">Rekap Bulanan</p>
                        <p class="text-xs italic mitra-text-muted">Performa bulanan tim</p>
                    </div>
                    <i class="absolute transition-all right-6 ri-arrow-right-s-line group-hover:translate-x-1 mitra-text-muted"></i>
                </a>
            @endif
        </div>
    </div>
</x-mitra-layout>
