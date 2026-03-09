<x-mitra-layout title="Dashboard Mitra">
    @php
        $today = \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y');
        $hour = \Carbon\Carbon::now()->format('H');
        $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
        $jabatan = auth()->user()->divisi->jabatan->code_jabatan ?? auth()->user()->divisi->code_jabatan ?? '-';
    @endphp

    <!-- Header Section (Lebih Terang dari Background) -->
    <div class="p-6 border shadow-xl bg-slate-700 border-slate-600 rounded-3xl">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-blue-400">Portal Mitra System</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-white">{{ $greeting }}, {{ auth()->user()->name }}</h1>
                <p class="mt-1 text-sm text-slate-300">Kelola operasional dan pantau kinerja tim Anda dalam satu dasbor.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="px-3 py-1.5 text-xs font-bold border rounded-xl bg-slate-800/50 text-slate-300 border-slate-600">
                    <i class="mr-1 text-blue-400 ri-calendar-line"></i> {{ $today }}
                </div>
                <div class="px-3 py-1.5 text-xs font-bold text-blue-300 rounded-xl bg-blue-500/10 border border-blue-500/30">
                    <i class="mr-1 ri-shield-user-line"></i> {{ $jabatan }}
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats (Informative & Clean) -->
    <div class="grid grid-cols-2 gap-4 mt-6 md:grid-cols-4">
        <div class="p-5 border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Karyawan</p>
            <p class="mt-1 text-2xl font-black text-white">{{ $jumlahKaryawan }}</p>
        </div>
        <div class="p-5 border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Hadir Hari Ini</p>
            <p class="mt-1 text-2xl font-black text-emerald-400">{{ $jumlahAbsensiHariIni }}</p>
        </div>
        <div class="p-5 border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Lembur Berjalan</p>
            <p class="mt-1 text-2xl font-black text-amber-400">{{ $jumlahLemburHariIni }}</p>
        </div>
        <div class="p-5 border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Sistem Status</p>
            <p class="flex items-center gap-2 mt-1 text-sm font-black text-blue-400">
                <span class="relative flex w-2 h-2">
                    <span class="absolute inline-flex w-full h-full bg-blue-400 rounded-full opacity-75 animate-ping"></span>
                    <span class="relative inline-flex w-2 h-2 bg-blue-500 rounded-full"></span>
                </span>
                ONLINE
            </p>
        </div>
    </div>

    <!-- Menu Grid (Interactive Cards) -->
    <div class="mt-10">
        <h2 class="mb-4 ml-1 text-xs font-black tracking-[0.3em] uppercase text-slate-500">Navigasi Utama</h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            
            <!-- Kehadiran -->
            <a href="{{ route('mitra_absensi') }}" class="relative flex items-center gap-5 p-6 transition-all border shadow-sm bg-slate-700 group rounded-2xl border-slate-600 hover:border-blue-500/50 hover:bg-slate-600">
                <div class="flex items-center justify-center text-blue-400 transition-all shadow-inner w-14 h-14 rounded-xl bg-blue-500/10 group-hover:bg-blue-500 group-hover:text-white group-hover:scale-110">
                    <i class="text-2xl ri-fingerprint-line"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-white transition-colors group-hover:text-blue-300">Kehadiran</p>
                    <p class="text-xs italic text-slate-400">Riwayat absensi tim</p>
                </div>
                <i class="absolute transition-all text-slate-600 right-6 ri-arrow-right-s-line group-hover:text-blue-400 group-hover:translate-x-1"></i>
            </a>

            <!-- Laporan -->
            <a href="{{ route('mitra_laporan') }}" class="relative flex items-center gap-5 p-6 transition-all border shadow-sm bg-slate-700 group rounded-2xl border-slate-600 hover:border-emerald-500/50 hover:bg-slate-600">
                <div class="flex items-center justify-center transition-all shadow-inner w-14 h-14 rounded-xl bg-emerald-500/10 text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white group-hover:scale-110">
                    <i class="text-2xl ri-file-list-3-line"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-white transition-colors group-hover:text-emerald-300">Laporan Kerja</p>
                    <p class="text-xs italic text-slate-400">Review aktivitas harian</p>
                </div>
                <i class="absolute transition-all text-slate-600 right-6 ri-arrow-right-s-line group-hover:text-emerald-400 group-hover:translate-x-1"></i>
            </a>

            <!-- Lembur -->
            <a href="{{ route('mitra_lembur') }}" class="relative flex items-center gap-5 p-6 transition-all border shadow-sm bg-slate-700 group rounded-2xl border-slate-600 hover:border-amber-500/50 hover:bg-slate-600">
                <div class="flex items-center justify-center transition-all shadow-inner w-14 h-14 rounded-xl bg-amber-500/10 text-amber-400 group-hover:bg-amber-600 group-hover:text-white group-hover:scale-110">
                    <i class="text-2xl ri-time-line"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-white transition-colors group-hover:text-amber-300">Data Lembur</p>
                    <p class="text-xs italic text-slate-400">Kelola lembur karyawan</p>
                </div>
                <i class="absolute transition-all text-slate-600 right-6 ri-arrow-right-s-line group-hover:text-amber-400 group-hover:translate-x-1"></i>
            </a>

            <!-- Data User -->
            <a href="{{ route('mitra_user') }}" class="relative flex items-center gap-5 p-6 transition-all border shadow-sm bg-slate-700 group rounded-2xl border-slate-600 hover:border-indigo-500/50 hover:bg-slate-600">
                <div class="flex items-center justify-center text-indigo-400 transition-all shadow-inner w-14 h-14 rounded-xl bg-indigo-500/10 group-hover:bg-indigo-600 group-hover:text-white group-hover:scale-110">
                    <i class="text-2xl ri-group-line"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-white transition-colors group-hover:text-indigo-300">Data Karyawan</p>
                    <p class="text-xs italic text-slate-400">Daftar personel mitra</p>
                </div>
                <i class="absolute transition-all text-slate-600 right-6 ri-arrow-right-s-line group-hover:text-indigo-400 group-hover:translate-x-1"></i>
            </a>

            <!-- Laporan Bulanan -->
            @if (Route::has('mitra_rekap'))
                <a href="{{ route('mitra_rekap') }}" class="relative flex items-center gap-5 p-6 transition-all border shadow-sm bg-slate-700 group rounded-2xl border-slate-600 hover:border-rose-500/50 hover:bg-slate-600">
                    <div class="flex items-center justify-center transition-all shadow-inner w-14 h-14 rounded-xl bg-rose-500/10 text-rose-400 group-hover:bg-rose-600 group-hover:text-white group-hover:scale-110">
                        <i class="text-2xl ri-calendar-schedule-line"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-white transition-colors group-hover:text-rose-300">Rekap Bulanan</p>
                        <p class="text-xs italic text-slate-400">Performa bulanan tim</p>
                    </div>
                    <i class="absolute transition-all text-slate-600 right-6 ri-arrow-right-s-line group-hover:text-rose-400 group-hover:translate-x-1"></i>
                </a>
            @endif

            <!-- Rekap -->
            {{-- @if (Route::has('mitra_rekap'))
                <a href="{{ route('mitra_rekap') }}" class="relative flex items-center gap-5 p-6 transition-all border shadow-sm bg-slate-700 group rounded-2xl border-slate-600 hover:border-cyan-500/50 hover:bg-slate-600">
                    <div class="flex items-center justify-center transition-all shadow-inner text-cyan-400 w-14 h-14 rounded-xl bg-cyan-500/10 group-hover:bg-cyan-600 group-hover:text-white group-hover:scale-110">
                        <i class="text-2xl ri-bar-chart-grouped-line"></i>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-white transition-colors group-hover:text-cyan-300">Rekap Data</p>
                        <p class="text-xs italic text-slate-400">Pengajuan dan riwayat rekap</p>
                    </div>
                    <i class="absolute transition-all text-slate-600 right-6 ri-arrow-right-s-line group-hover:text-cyan-400 group-hover:translate-x-1"></i>
                </a>
            @endif --}}
        </div>
    </div>
</x-mitra-layout>
