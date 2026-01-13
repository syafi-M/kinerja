<nav class="mx-5 mb-5 pt-5 capitalize">
    <div class="flex pl-2 w-full h-auto bg-slate-500 shadow-md rounded-md justify-between">
        @auth
            @php
                $user = Auth::user();
                $defaultImg = asset('/logo/person.png');
                $imgSrc = match (true) {
                    $user->image === 'no-image.jpg' => $defaultImg,
                    Storage::disk('public')->exists("images/{$user->image}") => asset("storage/images/{$user->image}"),
                    Storage::disk('public')->exists("user/{$user->image}") => asset("storage/user/{$user->image}"),
                    default => $defaultImg,
                };
            @endphp

            <a href="{{ route('profile.index') }}" style="width: {{ $user->role_id == 2 ? '70%' : '85%' }};">
                <div class="flex items-center gap-1">
                    <div class="mx-2 my-2 overflow-hidden flex items-center justify-center bg-slate-300 rounded-full shadow-md shadow-slate-600 hover:shadow-none transition-all duration-200 ease-in-out"
                        style="width: 2.5rem; height: 2.5rem; padding: 0.25rem;">
                        <img class="w-full h-full object-cover object-center block rounded-full" src="{{ $imgSrc }}"
                            alt="profile-logo" />
                    </div>

                    <div class="flex flex-row gap-1" style="max-width: 65%;">
                        <p class="font-semibold text-white text-sm line-clamp-1 break-words">
                            {{ ucwords(strtolower($user->nama_lengkap)) }}</p>
                    </div>
                </div>
            </a>
        @endauth

        @auth
            {{-- Mobile Slip Gaji Button --}}
            <div class="flex md:hidden mx-5 items-center" style="width: 28%;">
                <form action="{{ route('slip-gaji.index') }}" method="get">
                    <input type="hidden" name="bulan" value="{{ now()->subMonth()->format('Y-m') }}" />
                    <button class="btn btn-sm btn-warning overflow-hidden">
                        <span class="flex items-center gap-1 overflow-hidden">
                            <i class="ri-bank-card-line w-4 h-4"></i>
                            <p class="overflow-hidden text-center">Slip</p>
                        </span>
                    </button>
                </form>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex gap-3 mr-7">
                <x-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.index')">
                    {{ __('Dashboard') }}
                </x-nav-link>

                {{-- Admin Tool --}}
                @if ($user->role_id == 2 && $user->divisi->jabatan->code_jabatan !== 'SPV-A')
                    <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')">
                        {{ __('Admin Tool') }}
                    </x-nav-link>
                @endif

                {{-- Logout --}}
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center mt-4 px-2 font-semibold rounded-md text-slate-700 bg-yellow-300 hover:bg-yellow-400 hover:text-white shadow-md transition ease-in-out duration-200">
                        Logout
                    </button>
                </form>
            </div>
        @else
            {{-- Guest (Login Button) --}}
            <div class="md:flex gap-3 mr-7">
                <x-nav-link class="mr-5 px-5 py-1" :href="route('login')" :active="true">
                    {{ __('Login') }}
                </x-nav-link>
            </div>
        @endauth
    </div>
</nav>
