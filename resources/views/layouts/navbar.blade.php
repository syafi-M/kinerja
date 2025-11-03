<nav class="mb-5 capitalize fixed top-0 w-screen z-[99999] shadow-lg" x-data="{ sidebarOpen: false }">
    <div
        class="flex items-center justify-between w-full h-auto pl-2 shadow-lg bg-gradient-to-r from-slate-600 to-slate-500 backdrop-blur-sm">
        @auth
            @php
                $user = auth()->user();
                $defaultImg = asset('/logo/person.png');
                $imgSrc = match (true) {
                    $user->image === 'no-image.jpg' => $defaultImg,
                    Storage::disk('public')->exists("images/{$user->image}") => asset("storage/images/{$user->image}"),
                    Storage::disk('public')->exists("user/{$user->image}") => asset("storage/user/{$user->image}"),
                    default => $defaultImg,
                };
            @endphp

            <a href="{{ route('profile.index') }}"
                class="transition-all duration-300 ease-out rounded-lg group hover:bg-slate-600/30"
                style="width: {{ $user->role_id == 2 ? '70%' : '85%' }};">
                <div class="flex items-center gap-2 py-1">
                    <div class="relative flex items-center justify-center mx-2 my-2 overflow-hidden transition-all duration-300 ease-out rounded-full shadow-lg bg-gradient-to-br from-slate-200 to-slate-300 ring-2 ring-slate-400/50 group-hover:ring-yellow-400/60 group-hover:shadow-xl group-hover:scale-105"
                        style="width: 2.5rem; height: 2.5rem; padding: 0.25rem;">
                        <img class="block object-cover object-center w-full h-full rounded-full" src="{{ $imgSrc }}"
                            alt="profile-logo" loading="lazy" />
                        <div
                            class="absolute inset-0 transition-opacity duration-300 rounded-full opacity-0 bg-gradient-to-t from-black/10 to-transparent group-hover:opacity-100">
                        </div>
                    </div>

                    <div class="flex flex-col" style="max-width: calc(100% - 4rem);">
                        <p
                            class="text-sm font-semibold text-white break-words transition-colors duration-200 line-clamp-1 group-hover:text-yellow-300">
                            {{ ucwords(strtolower($user->nama_lengkap)) }}
                        </p>
                        <span class="text-xs text-slate-300">View Profile</span>
                    </div>
                </div>
            </a>
        @endauth

        @auth
            {{-- Mobile Menu Toggle Button --}}
            <div class="flex items-center mx-3 md:hidden">
                <button @click="sidebarOpen = true"
                    class="w-full px-3 py-2 text-xs font-semibold bg-yellow-400 hover:bg-yellow-500 text-slate-800 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 ease-out flex items-center justify-center gap-1.5">
                    <i class="text-base ri-menu-line"></i>
                </button>
            </div>

            {{-- Desktop Navigation --}}
            <div class="items-center hidden gap-2 py-2 mr-10 md:flex">
                <x-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.index')"
                    class="px-4 py-2 text-sm font-medium transition-all duration-200 rounded-lg translate-0 hover:bg-slate-600/60">
                    {{ __('Dashboard') }}
                </x-nav-link>

                {{-- Admin Tool --}}
                @if ($user->role_id == 2 && $user->divisi->jabatan->code_jabatan !== 'SPV-A')
                    <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')"
                        class="px-4 py-2 text-sm font-medium transition-all duration-200 rounded-lg hover:bg-slate-600/40">
                        {{ __('Admin Tool') }}
                    </x-nav-link>
                @endif

                {{-- Logout --}}
                <form action="{{ route('logout') }}" method="post" class="">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 font-semibold text-sm rounded-md text-slate-800 bg-yellow-400 hover:bg-yellow-500 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 ease-out">
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        @else
            {{-- Guest (Login Button) --}}
            <div class="flex items-center py-2 mr-5">
                <x-nav-link :href="route('login')" :active="true"
                    class="px-6 py-2 font-semibold text-sm bg-yellow-400 hover:bg-yellow-500 text-slate-800 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 ease-out">
                    {{ __('Login') }}
                </x-nav-link>
            </div>
        @endauth
    </div>

    @auth
    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm md:hidden" style="display: none;">
    </div>

    {{-- Mobile Sidebar --}}
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 z-50 h-full overflow-y-auto shadow-2xl w-72 bg-slate-800 md:hidden"
        style="display: none;">

        {{-- Sidebar Header --}}
        <div class="sticky top-0 z-10 p-4 shadow-lg bg-slate-600">
            <div class="flex items-center justify-between">
                <h2 class="flex items-center gap-2 text-lg font-bold text-white">
                    <i class="text-yellow-400 ri-menu-2-line"></i>
                    Menu
                </h2>
                <button @click="sidebarOpen = false"
                    class="p-2 text-white transition-colors duration-200 rounded-lg bg-slate-500 hover:bg-slate-400">
                    <i class="text-xl ri-close-line"></i>
                </button>
            </div>
        </div>

        {{-- Sidebar Content --}}
        <div class="p-4 space-y-3">
            @include('partials.sidebar-content')
        </div>

        {{-- Sidebar Footer --}}
        <div class="p-4 bg-slate-700">
            <p class="text-xs text-center text-slate-400/80">
                Kinerja APP V 1.2.1-beta
            </p>
        </div>
    </div>
@endauth
</nav>
