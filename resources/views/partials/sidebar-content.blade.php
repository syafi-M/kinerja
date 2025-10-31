{{-- User Profile Card --}}
<div
    class="bg-gradient-to-br from-slate-600 to-slate-700 rounded-xl p-4 shadow-lg border {{ request()->routeIs('profile.*') ? 'border-yellow-500/30' : 'border-slate-500/30' }}">
    <div class="flex items-center gap-3 mb-3">
        <div class="relative flex items-center justify-center overflow-hidden rounded-full shadow-md bg-gradient-to-br from-slate-200 to-slate-300 ring-2 ring-yellow-400/40"
            style="width: 3rem; height: 3rem; padding: 0.25rem;">
            <img class="block object-cover object-center w-full h-full rounded-full" src="{{ $imgSrc }}"
                alt="profile-logo" loading="lazy" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-white truncate">
                {{ ucwords(strtolower($user->nama_lengkap)) }}
            </p>
            <p class="text-xs lowercase truncate text-slate-300">{{ $user->email }}</p>
        </div>
    </div>
    <a href="{{ route('profile.index') }}" @click="sidebarOpen = false"
        class="block w-full px-3 py-2 text-xs font-medium text-center text-white transition-colors duration-200 rounded-lg bg-slate-500 hover:bg-slate-600">
        <i class="mr-1 ri-user-line"></i>
        View Full Profile
    </a>
</div>

{{-- Navigation Links --}}
<div class="space-y-2">
    <a href="{{ route('dashboard.index') }}" @click="sidebarOpen = false"
        class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard.index') ? 'bg-yellow-400/10 border-l-4 border-yellow-400' : 'bg-slate-600/40 border-l-4 border-transparent' }} hover:bg-slate-600/60 text-white transition-all duration-200 group">
        <i
            class="ri-dashboard-line text-xl {{ request()->routeIs('dashboard.index') ? 'text-yellow-400' : 'text-slate-300' }} group-hover:text-yellow-400 transition-colors duration-200"></i>
        <span
            class="font-medium text-sm {{ request()->routeIs('dashboard.index') ? 'text-yellow-400' : '' }}">Dashboard</span>
        @if (request()->routeIs('dashboard.index'))
            <i class="ml-auto text-sm text-yellow-400 ri-checkbox-circle-fill"></i>
        @endif
    </a>

    {{-- Admin Tool --}}
    @if ($user->role_id == 2 && $user->divisi->jabatan->code_jabatan !== 'SPV-A')
        <a href="{{ route('admin.index') }}" @click="sidebarOpen = false"
            class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.index') ? 'bg-yellow-400/10 border-l-4 border-yellow-400' : 'bg-slate-600/40 border-l-4 border-transparent' }} hover:bg-slate-600/60 text-white transition-all duration-200 group">
            <i
                class="ri-admin-line text-xl {{ request()->routeIs('admin.index') ? 'text-yellow-400' : 'text-slate-300' }} group-hover:text-yellow-400 transition-colors duration-200"></i>
            <span class="font-medium text-sm {{ request()->routeIs('admin.index') ? 'text-yellow-400' : '' }}">Admin
                Tool</span>
            @if (request()->routeIs('admin.index'))
                <i class="ml-auto text-sm text-yellow-400 ri-checkbox-circle-fill"></i>
            @endif
        </a>
    @endif

    {{-- Slip Gaji --}}
    <form action="{{ route('slip-gaji.index') }}" method="get" class="w-full">
        <input type="hidden" name="bulan" value="{{ now()->subMonth()->format('Y-m') }}" />
        <button type="submit" @click="sidebarOpen = false"
            class="flex items-center w-full gap-3 px-4 py-3 text-white transition-all duration-200 border-l-4 border-transparent rounded-lg bg-slate-600/40 hover:bg-slate-600/60 group">
            <i
                class="text-xl transition-colors duration-200 ri-dashboard-line text-slate-300 group-hover:text-yellow-400"></i>
            <span class="text-sm font-medium">Slip Gaji</span>
        </button>
    </form>

    {{-- Baca Al-Qur'an --}}
    <a href="https://baca-alquran.sac-po.com" target="_blank" class="block w-full">
        <div
            class="flex items-center w-full gap-3 px-4 py-3 text-white transition-all duration-200 border-l-4 border-transparent rounded-lg bg-slate-600/40 hover:bg-slate-600/60 group">
            <i
                class="text-xl transition-colors duration-200 ri-git-repository-line text-slate-300 group-hover:text-yellow-400"></i>
            <span class="text-sm font-medium">
                {{ auth()->user()->kerjasama_id != 1 ? 'Baca Al-Qur`an' : 'Al-Qur`an' }}
            </span>
        </div>
    </a>

    {{-- SPPD (only shown if kerjasama_id == 1) --}}
    @if (auth()->user()->kerjasama_id == 1)
        <a href="https://sppd-online.sac-po.com/login" target="_blank" class="block w-full">
            <div
                class="flex items-center w-full gap-3 px-4 py-3 text-white transition-all duration-200 border-l-4 border-transparent rounded-lg bg-slate-600/40 hover:bg-slate-600/60 group">
                <i
                    class="text-xl transition-colors duration-200 ri-newspaper-line text-slate-300 group-hover:text-yellow-400"></i>
                <span class="text-sm font-medium">SPPD</span>
            </div>
        </a>
    @endif
    {{-- Divider --}}
    <div class="my-4 border-t border-slate-500/30"></div>
    {{-- Report Error --}}
    <a href="https://report-system-ten.vercel.app/" target="_blank" class="block w-full">
        <div
            class="flex items-center w-full gap-3 px-4 py-3 text-white transition-all duration-200 border-l-4 border-transparent rounded-lg bg-slate-600/40 hover:bg-slate-600/60 group">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="w-5 text-xl transition-colors duration-200 text-slate-300 group-hover:text-yellow-400 h-7">
                <path
                    d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM11 15H13V17H11V15ZM11 7H13V13H11V7Z">
                </path>
            </svg>
            <span class="text-sm font-medium">
                Laporkan Error
            </span>
        </div>
    </a>
</div>

{{-- Divider --}}
<div class="my-4 border-t border-slate-500/30"></div>

{{-- Logout Button --}}
<form action="{{ route('logout') }}" method="post">
    @csrf
    <button type="submit"
        class="flex items-center justify-center w-full gap-2 px-4 py-3 font-medium text-white transition-all duration-200 bg-red-600 rounded-lg shadow-md hover:bg-red-700 active:bg-red-800 hover:shadow-lg active:shadow-md">
        <i class="text-lg ri-logout-box-line"></i>
        <span>Logout</span>
    </button>
</form>
