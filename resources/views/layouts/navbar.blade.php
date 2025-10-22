<nav class="mb-5 capitalize fixed top-0 w-screen z-[99999] shadow-lg" x-data="{ sidebarOpen: false }">
    <div
        class="flex pl-2 w-full h-auto bg-gradient-to-r from-slate-600 to-slate-500 shadow-lg justify-between items-center backdrop-blur-sm">
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
                class="group transition-all duration-300 ease-out hover:bg-slate-600/30 rounded-lg"
                style="width: {{ $user->role_id == 2 ? '70%' : '85%' }};">
                <div class="flex items-center gap-2 py-1">
                    <div class="relative mx-2 my-2 overflow-hidden flex items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300 rounded-full shadow-lg ring-2 ring-slate-400/50 group-hover:ring-yellow-400/60 group-hover:shadow-xl group-hover:scale-105 transition-all duration-300 ease-out"
                        style="width: 2.5rem; height: 2.5rem; padding: 0.25rem;">
                        <img class="w-full h-full object-cover object-center block rounded-full" src="{{ $imgSrc }}"
                            alt="profile-logo" loading="lazy" />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </div>

                    <div class="flex flex-col" style="max-width: calc(100% - 4rem);">
                        <p
                            class="font-semibold text-white text-sm line-clamp-1 break-words group-hover:text-yellow-300 transition-colors duration-200">
                            {{ ucwords(strtolower($user->nama_lengkap)) }}
                        </p>
                        <span class="text-xs text-slate-300">View Profile</span>
                    </div>
                </div>
            </a>
        @endauth

        @auth
            {{-- Mobile Menu Toggle Button --}}
            <div class="flex md:hidden mx-3 items-center">
                <button @click="sidebarOpen = true"
                    class="w-full px-3 py-2 text-xs font-semibold bg-yellow-400 hover:bg-yellow-500 text-slate-800 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 ease-out flex items-center justify-center gap-1.5">
                    <i class="ri-menu-line text-base"></i>
                </button>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center gap-2 mr-5 py-2">
                <x-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.index')"
                    class="px-4 py-2 font-medium text-sm rounded-lg hover:bg-slate-600/40 transition-all duration-200">
                    {{ __('Dashboard') }}
                </x-nav-link>

                {{-- Admin Tool --}}
                @if ($user->role_id == 2 && $user->divisi->jabatan->code_jabatan !== 'SPV-A')
                    <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')"
                        class="px-4 py-2 font-medium text-sm rounded-lg hover:bg-slate-600/40 transition-all duration-200">
                        {{ __('Admin Tool') }}
                    </x-nav-link>
                @endif

                {{-- Logout --}}
                <form action="{{ route('logout') }}" method="post" class="py-1">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 font-semibold text-sm rounded-md text-slate-800 bg-yellow-400 hover:bg-yellow-500 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 ease-out">
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        @else
            {{-- Guest (Login Button) --}}
            <div class="flex items-center mr-5 py-2">
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
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden" style="display: none;">
        </div>

        {{-- Mobile Sidebar --}}
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 h-full w-72 bg-gradient-to-b from-slate-700 to-slate-800 shadow-2xl z-50 md:hidden overflow-y-auto"
            style="display: none;">

            {{-- Sidebar Header --}}
            <div class="sticky top-0 bg-gradient-to-r from-slate-600 to-slate-500 p-4 shadow-lg z-10">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="ri-menu-2-line text-yellow-400"></i>
                        Menu
                    </h2>
                    <button @click="sidebarOpen = false"
                        class="p-2 rounded-lg bg-slate-600/50 hover:bg-slate-600 text-white transition-colors duration-200">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- Sidebar Content --}}
            <div class="p-4 space-y-3">
                @include('partials.sidebar-content')
            </div>

            {{-- Sidebar Footer --}}
            <div class="p-4 bg-gradient-to-t from-slate-900 to-transparent">
                <p class="text-xs text-center text-slate-400/80">
                    Kinerja APP V 1.2.1-beta
                </p>
            </div>
        </div>
    @endauth
</nav>
