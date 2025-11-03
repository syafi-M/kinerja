<x-app-layout>
    <div
        class="min-h-screen -mt-[25pt] mx-5 mb-20 rounded-md shadow-md opacity-100 sm:mx-10 bg-slate-500 bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">

        <!-- Modern Header Bar -->
        <div
            class="sticky top-0 z-50 mb-6 border-b shadow-sm backdrop-blur-xl bg-white/70 border-gray-200/50 rounded-t-md">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl">
                            <i class="text-xl text-white ri-dashboard-3-line"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900">Dashboard Admin</h1>
                            <p class="text-xs text-gray-500">Welcome back, Admin</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div
                            class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 rounded-lg border border-emerald-200">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-xs font-medium text-emerald-700">{{ $online }} Online</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg">
                            <i class="text-sm text-gray-600 ri-global-line"></i>
                            <span class="font-mono text-xs text-gray-700">{{ $ip }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 pb-20 mx-auto max-w-7xl sm:px-6 lg:px-8">

            <!-- Alerts Section -->
            @if ($izin || $expert)
                <div class="mb-6 space-y-4">
                    <!-- Pending Approvals Alert -->
                    @if ($izin)
                        <a href="{{ route('data-izin.admin') }}"
                            class="relative block p-5 overflow-hidden transition-all duration-300 bg-blue-600 shadow-md group rounded-xl hover:shadow-lg">
                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center justify-center w-12 h-12 bg-blue-500 rounded-lg">
                                        <i class="text-xl text-white ri-notification-3-line"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-white">Pending Approvals</h3>
                                        <p class="text-sm text-blue-100">{{ $izin }} izin menunggu persetujuan
                                            Anda</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 bg-white rounded-lg">
                                        <span class="text-lg font-bold text-blue-600">{{ $izin }}</span>
                                    </div>
                                    <i
                                        class="text-xl text-white transition-transform ri-arrow-right-line group-hover:translate-x-1"></i>
                                </div>
                            </div>
                        </a>
                    @endif

                    <!-- Expiring Contracts -->
                    @if ($expert)
                        <div x-data="{ openContracts: false }"
                            class="overflow-hidden bg-white border border-gray-200 shadow-md rounded-xl">
                            <div @click="openContracts = !openContracts"
                                class="px-5 py-4 transition-all duration-300 bg-red-500 cursor-pointer hover:bg-red-600">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-10 h-10 bg-red-400 rounded-lg">
                                            <i class="text-xl text-white ri-time-line"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-white">Kontrak Mitra Akan Berakhir</h3>
                                            <p class="text-xs text-red-100">{{ count($expert) }} kontrak memerlukan
                                                perhatian</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-10 h-10 bg-white rounded-lg">
                                            <span class="text-lg font-bold text-red-600">{{ count($expert) }}</span>
                                        </div>
                                        <i :class="openContracts ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                            class="text-xl text-white transition-transform duration-300"></i>
                                    </div>
                                </div>
                            </div>
                            <div x-show="openContracts" x-collapse
                                class="overflow-y-auto divide-y divide-gray-100 max-h-96">
                                @foreach ($expert as $ex)
                                    <div
                                        class="flex items-center justify-between px-5 py-4 transition-colors duration-150 hover:bg-gray-50">
                                        <div class="flex items-center flex-1 gap-4">
                                            <div
                                                class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg">
                                                <i class="text-lg text-gray-600 ri-building-line"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-900 truncate">{{ $ex->client->name }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <i class="text-xs text-red-500 ri-calendar-line"></i>
                                                    <p class="text-sm text-gray-600">
                                                        {{ Carbon\Carbon::createFromFormat('Y-m-d', $ex->experied)->isoFormat('DD MMMM YYYY') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ url('kerjasamas/' . $ex->id . '/edit') }}"
                                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-amber-500 hover:bg-amber-600">
                                            <i class="ri-edit-line"></i>
                                            <span>Update</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Menu Grid with Alpine.js -->
            <div x-data="menuController()" class="space-y-4">

                <!-- Search Bar -->
                <div class="relative">
                    <input x-model="search" type="text" placeholder="Cari menu..."
                        class="w-full px-4 py-3 pl-12 transition-all bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <i class="absolute text-xl text-gray-400 -translate-y-1/2 ri-search-line left-4 top-1/2"></i>
                </div>

                <!-- Menu Grid -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">

                    <!-- Menu User -->
                    <div x-show="filterMenu('Menu User')" x-transition class="menu-item">
                        <div @click="toggleMenu('user')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-blue-300 hover:bg-blue-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'user' ? 'rotate-180' : ''">
                                <i class="text-xl text-blue-500 transition-colors ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-blue-100 rounded-lg group-hover:bg-blue-200 group-hover:scale-105">
                                <i class="text-2xl text-blue-600 ri-folder-user-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu User</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'user'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('users.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-blue-500 rounded-lg hover:bg-blue-600 hover:translate-x-1">
                                <i class="text-lg ri-user-line"></i>
                                <span>Data User</span>
                            </a>
                            <a href="{{ route('users.create') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-blue-500 rounded-lg hover:bg-blue-600 hover:translate-x-1">
                                <i class="text-lg ri-user-add-line"></i>
                                <span>Tambah User</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Devisi -->
                    <div x-show="filterMenu('Menu Devisi')" x-transition class="menu-item">
                        <div @click="toggleMenu('devisi')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-purple-300 hover:bg-purple-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'devisi' ? 'rotate-180' : ''">
                                <i class="text-xl text-purple-500 transition-colors ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-purple-100 rounded-lg group-hover:bg-purple-200 group-hover:scale-105">
                                <i class="text-2xl text-purple-600 ri-group-2-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Devisi</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'devisi'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('devisi.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-purple-500 rounded-lg hover:bg-purple-600 hover:translate-x-1">
                                <i class="text-lg ri-team-line"></i>
                                <span>Data Devisi</span>
                            </a>
                            <a href="{{ route('devisi.create') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-purple-500 rounded-lg hover:bg-purple-600 hover:translate-x-1">
                                <i class="text-lg ri-add-line"></i>
                                <span>Tambah Devisi</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Client -->
                    <div x-show="filterMenu('Menu Client')" x-transition class="menu-item">
                        <div @click="toggleMenu('client')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-green-300 hover:bg-green-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'client' ? 'rotate-180' : ''">
                                <i class="text-xl text-green-500 transition-colors ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-green-100 rounded-lg group-hover:bg-green-200 group-hover:scale-105">
                                <i class="text-2xl text-green-600 ri-p2p-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Client</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'client'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('data-client.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-green-500 rounded-lg hover:bg-green-600 hover:translate-x-1">
                                <i class="text-lg ri-user-5-line"></i>
                                <span>Data Client</span>
                            </a>
                            <a href="{{ route('data-client.create') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-green-500 rounded-lg hover:bg-green-600 hover:translate-x-1">
                                <i class="text-lg ri-add-line"></i>
                                <span>Tambah Client</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Shift -->
                    <div x-show="filterMenu('Menu Shift')" x-transition class="menu-item">
                        <div @click="toggleMenu('shift')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-orange-300 hover:bg-orange-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'shift' ? 'rotate-180' : ''">
                                <i class="text-xl text-orange-500 transition-colors ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-orange-100 rounded-lg group-hover:bg-orange-200 group-hover:scale-105">
                                <i class="text-2xl text-orange-600 ri-timer-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Shift</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'shift'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('shift.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-orange-500 rounded-lg hover:bg-orange-600 hover:translate-x-1">
                                <i class="text-lg ri-timer-flash-line"></i>
                                <span>Data Shift</span>
                            </a>
                            <a href="{{ route('shift.create') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-orange-500 rounded-lg hover:bg-orange-600 hover:translate-x-1">
                                <i class="text-lg ri-add-line"></i>
                                <span>Tambah Shift</span>
                            </a>
                        </div>
                    </div>

                    <!-- Data Ruangan -->
                    <div x-show="filterMenu('Data Ruangan')" x-transition class="menu-item">
                        <a href="{{ route('ruangan.index') }}"
                            class="relative block p-5 transition-all duration-300 bg-white border border-gray-200 rounded-xl group hover:shadow-md hover:border-cyan-300 hover:bg-cyan-50">
                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 rounded-lg bg-cyan-100 group-hover:bg-cyan-200 group-hover:scale-105">
                                <i class="text-2xl text-cyan-600 ri-door-open-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Data Ruangan</h3>
                        </a>
                    </div>

                    <!-- Data Point -->
                    <div x-show="filterMenu('Data Point')" x-transition class="menu-item">
                        <a href="{{ route('point.index') }}"
                            class="relative block p-5 transition-all duration-300 bg-white border border-gray-200 rounded-xl group hover:shadow-md hover:border-yellow-300 hover:bg-yellow-50">
                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-yellow-100 rounded-lg group-hover:bg-yellow-200 group-hover:scale-105">
                                <i class="text-2xl text-yellow-600 ri-coins-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Data Point</h3>
                        </a>
                    </div>

                    <!-- Data Area -->
                    <div x-show="filterMenu('Data Area')" x-transition class="menu-item">
                        <a href="{{ route('area.index') }}"
                            class="relative block p-5 transition-all duration-300 bg-white border border-gray-200 rounded-xl group hover:shadow-md hover:border-red-300 hover:bg-red-50">
                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-red-100 rounded-lg group-hover:bg-red-200 group-hover:scale-105">
                                <i class="text-2xl text-red-600 ri-map-pin-2-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Data Area</h3>
                        </a>
                    </div>

                    <!-- Menu Kerjasama -->
                    <div x-show="filterMenu('Menu Kerjasama')" x-transition class="menu-item">
                        <div @click="toggleMenu('kerjasama')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-indigo-300 hover:bg-indigo-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'kerjasama' ? 'rotate-180' : ''">
                                <i class="text-xl text-indigo-500 transition-colors ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 group-hover:scale-105">
                                <i class="text-2xl text-indigo-600 ri-shake-hands-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Kerjasama</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'kerjasama'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('kerjasamas.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-indigo-500 rounded-lg hover:bg-indigo-600 hover:translate-x-1">
                                <i class="text-lg ri-hand-coin-line"></i>
                                <span>Data Kerjasama</span>
                            </a>
                            <a href="{{ route('kerjasamas.create') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-indigo-500 rounded-lg hover:bg-indigo-600 hover:translate-x-1">
                                <i class="text-lg ri-add-line"></i>
                                <span>Tambah Kerjasama</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Absensi -->
                    <div x-show="filterMenu('Menu Absensi')" x-transition class="menu-item">
                        <div @click="toggleMenu('absen')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-teal-300 hover:bg-teal-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'absen' ? 'rotate-180' : ''">
                                <i class="text-xl text-teal-500 transition-colors ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-teal-100 rounded-lg group-hover:bg-teal-200 group-hover:scale-105">
                                <i class="text-2xl text-teal-600 ri-calendar-todo-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Absensi</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'absen'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('admin.absen') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-teal-500 rounded-lg hover:bg-teal-600 hover:translate-x-1">
                                <i class="text-lg ri-list-check-3"></i>
                                <span>Data Absensi</span>
                            </a>
                            <a href="{{ route('data-izin.admin') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-teal-500 rounded-lg hover:bg-teal-600 hover:translate-x-1">
                                <i class="text-lg ri-shield-user-line"></i>
                                <span>Data Izin</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Perlengkapan -->
                    <div x-show="filterMenu('Menu Perlengkapan')" x-transition class="menu-item">
                        <div @click="toggleMenu('perlengkapan')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-slate-300 hover:bg-slate-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'perlengkapan' ? 'rotate-180' : ''">
                                <i class="text-xl transition-colors text-slate-500 ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 rounded-lg bg-slate-100 group-hover:bg-slate-200 group-hover:scale-105">
                                <i class="text-2xl text-slate-600 ri-tools-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Perlengkapan</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'perlengkapan'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('perlengkapan.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-slate-500 hover:bg-slate-600 hover:translate-x-1">
                                <i class="text-lg ri-hammer-line"></i>
                                <span>Data Perlengkapan</span>
                            </a>
                            <a href="{{ route('perlengkapan.create') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-slate-500 hover:bg-slate-600 hover:translate-x-1">
                                <i class="text-lg ri-add-line"></i>
                                <span>Tambah Perlengkapan</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Lembur -->
                    <div x-show="filterMenu('Menu Lembur')" x-transition class="menu-item">
                        <div @click="toggleMenu('lembur')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-violet-300 hover:bg-violet-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'lembur' ? 'rotate-180' : ''">
                                <i class="text-xl transition-colors text-violet-500 ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 rounded-lg bg-violet-100 group-hover:bg-violet-200 group-hover:scale-105">
                                <i class="text-2xl text-violet-600 ri-time-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Lembur</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'lembur'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('lemburList') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-violet-500 hover:bg-violet-600 hover:translate-x-1">
                                <i class="text-lg ri-hourglass-2-line"></i>
                                <span>Data Lembur</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Jabatan -->
                    <div x-show="filterMenu('Menu Jabatan')" x-transition class="menu-item">
                        <div @click="toggleMenu('jabatan')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-fuchsia-300 hover:bg-fuchsia-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'jabatan' ? 'rotate-180' : ''">
                                <i class="text-xl transition-colors text-fuchsia-500 ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 rounded-lg bg-fuchsia-100 group-hover:bg-fuchsia-200 group-hover:scale-105">
                                <i class="text-2xl text-fuchsia-600 ri-medal-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Jabatan</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'jabatan'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('jabatan.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-fuchsia-500 hover:bg-fuchsia-600 hover:translate-x-1">
                                <i class="text-lg ri-award-line"></i>
                                <span>Data Jabatan</span>
                            </a>
                            <a href="{{ route('jabatan.create') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-fuchsia-500 hover:bg-fuchsia-600 hover:translate-x-1">
                                <i class="text-lg ri-add-line"></i>
                                <span>Tambah Jabatan</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Lokasi -->
                    <div x-show="filterMenu('Menu Lokasi')" x-transition class="menu-item">
                        <div @click="toggleMenu('lokasi')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-lime-300 hover:bg-lime-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'lokasi' ? 'rotate-180' : ''">
                                <i class="text-xl transition-colors text-lime-500 ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 rounded-lg bg-lime-100 group-hover:bg-lime-200 group-hover:scale-105">
                                <i class="text-2xl text-lime-600 ri-road-map-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Lokasi</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'lokasi'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('lokasi.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-lime-500 hover:bg-lime-600 hover:translate-x-1">
                                <i class="text-lg ri-pin-distance-line"></i>
                                <span>Data Lokasi</span>
                            </a>
                            <a href="{{ route('lokasi.create') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-lime-500 hover:bg-lime-600 hover:translate-x-1">
                                <i class="text-lg ri-map-pin-add-line"></i>
                                <span>Tambah Lokasi</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Jadwal -->
                    <div x-show="filterMenu('Menu Jadwal')" x-transition class="menu-item">
                        <div @click="toggleMenu('jadwal')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-rose-300 hover:bg-rose-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'jadwal' ? 'rotate-180' : ''">
                                <i class="text-xl transition-colors text-rose-500 ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 rounded-lg bg-rose-100 group-hover:bg-rose-200 group-hover:scale-105">
                                <i class="text-2xl text-rose-600 ri-calendar-event-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Jadwal</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'jadwal'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('admin-jadwal.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-rose-500 hover:bg-rose-600 hover:translate-x-1">
                                <i class="text-lg ri-calendar-2-line"></i>
                                <span>Data Jadwal User</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Laporan -->
                    <div x-show="filterMenu('Menu Laporan')" x-transition class="menu-item">
                        <div @click="toggleMenu('laporan')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-sky-300 hover:bg-sky-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'laporan' ? 'rotate-180' : ''">
                                <i class="text-xl transition-colors text-sky-500 ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 rounded-lg bg-sky-100 group-hover:bg-sky-200 group-hover:scale-105">
                                <i class="text-2xl text-sky-600 ri-task-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Laporan</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'laporan'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('laporan.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-sky-500 hover:bg-sky-600 hover:translate-x-1">
                                <i class="text-lg ri-calendar-2-line"></i>
                                <span>Data Laporan</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Check Point -->
                    <div x-show="filterMenu('Check Point')" x-transition class="menu-item">
                        <div @click="toggleMenu('CP')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-emerald-300 hover:bg-emerald-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'CP' ? 'rotate-180' : ''">
                                <i class="text-xl transition-colors text-emerald-500 ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 rounded-lg bg-emerald-100 group-hover:bg-emerald-200 group-hover:scale-105">
                                <i class="text-2xl text-emerald-600 ri-map-pin-range-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Check Point</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'CP'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('admin.cp.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-emerald-500 hover:bg-emerald-600 hover:translate-x-1">
                                <i class="text-lg ri-checkbox-circle-line"></i>
                                <span>Data Check Point</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Pekerjaan CP -->
                    <div x-show="filterMenu('Pekerjaan CP')" x-transition class="menu-item">
                        <div @click="toggleMenu('PCP')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-amber-300 hover:bg-amber-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'PCP' ? 'rotate-180' : ''">
                                <i class="text-xl transition-colors text-amber-500 ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 rounded-lg bg-amber-100 group-hover:bg-amber-200 group-hover:scale-105">
                                <i class="text-2xl text-amber-600 ri-file-list-3-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Pekerjaan CP</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'PCP'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('pekerjaanCp.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 rounded-lg bg-amber-500 hover:bg-amber-600 hover:translate-x-1">
                                <i class="text-lg ri-file-list-line"></i>
                                <span>Data Pekerjaan CP</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Berita -->
                    <div x-show="filterMenu('Menu Berita')" x-transition class="menu-item">
                        <div @click="toggleMenu('News')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-pink-300 hover:bg-pink-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'News' ? 'rotate-180' : ''">
                                <i class="text-xl text-pink-500 transition-colors ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-pink-100 rounded-lg group-hover:bg-pink-200 group-hover:scale-105">
                                <i class="text-2xl text-pink-600 ri-newspaper-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Menu Berita</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'News'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('news.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-pink-500 rounded-lg hover:bg-pink-600 hover:translate-x-1">
                                <i class="text-lg ri-news-line"></i>
                                <span>Data Berita</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Sub Area -->
                    <div x-show="filterMenu('Sub Area')" x-transition class="menu-item">
                        <div @click="toggleMenu('SubArea')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-gray-300 hover:bg-gray-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'SubArea' ? 'rotate-180' : ''">
                                <i class="text-xl text-gray-500 transition-colors ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-gray-100 rounded-lg group-hover:bg-gray-200 group-hover:scale-105">
                                <i class="text-2xl text-gray-600 ri-map-2-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Sub Area</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'SubArea'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('subarea.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-gray-500 rounded-lg hover:bg-gray-600 hover:translate-x-1">
                                <i class="text-lg ri-map-pin-line"></i>
                                <span>Data Sub Area</span>
                            </a>
                        </div>
                    </div>

                    <!-- Menu Checklist -->
                    <div x-show="filterMenu('Checklist')" x-transition class="menu-item">
                        <div @click="toggleMenu('Checklist')"
                            class="relative p-5 transition-all duration-300 bg-white border border-gray-200 cursor-pointer rounded-xl group hover:shadow-md hover:border-blue-300 hover:bg-blue-50">
                            <!-- Toggle Icon -->
                            <div class="absolute transition-transform duration-300 top-3 right-3"
                                :class="activeMenu === 'Checklist' ? 'rotate-180' : ''">
                                <i class="text-xl text-blue-500 transition-colors ri-arrow-down-s-line"></i>
                            </div>

                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-blue-100 rounded-lg group-hover:bg-blue-200 group-hover:scale-105">
                                <i class="text-2xl text-blue-600 ri-checkbox-multiple-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Checklist</h3>
                        </div>

                        <!-- Submenu -->
                        <div x-show="activeMenu === 'Checklist'" x-collapse
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="pl-2 mt-2 space-y-2 overflow-hidden">
                            <a href="{{ route('admin-checklist.index') }}"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-white transition-all duration-200 bg-blue-500 rounded-lg hover:bg-blue-600 hover:translate-x-1">
                                <i class="text-lg ri-check-double-line"></i>
                                <span>Data Checklist</span>
                            </a>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div x-show="filterMenu('QR Code')" x-transition class="menu-item">
                        <a href="{{ route('qrcode.index') }}"
                            class="relative block p-5 transition-all duration-300 bg-white border border-gray-200 rounded-xl group hover:shadow-md hover:border-purple-300 hover:bg-purple-50">
                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-purple-100 rounded-lg group-hover:bg-purple-200 group-hover:scale-105">
                                <i class="text-2xl text-purple-600 ri-qr-code-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">QR Code</h3>
                        </a>
                    </div>

                    <!-- List Pekerjaan -->
                    <div x-show="filterMenu('List Pekerjaan')" x-transition class="menu-item">
                        <a href="{{ route('listPekerjaan.index') }}"
                            class="relative block p-5 transition-all duration-300 bg-white border border-gray-200 rounded-xl group hover:shadow-md hover:border-green-300 hover:bg-green-50">
                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-green-100 rounded-lg group-hover:bg-green-200 group-hover:scale-105">
                                <i class="text-2xl text-green-600 ri-list-check"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">List Pekerjaan</h3>
                        </a>
                    </div>

                    <!-- Laporan Mitra -->
                    <div x-show="filterMenu('Laporan Mitra')" x-transition class="menu-item">
                        <a href="{{ route('laporanMitra.index') }}"
                            class="relative block p-5 transition-all duration-300 bg-white border border-gray-200 rounded-xl group hover:shadow-md hover:border-orange-300 hover:bg-orange-50">
                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-orange-100 rounded-lg group-hover:bg-orange-200 group-hover:scale-105">
                                <i class="text-2xl text-orange-600 ri-file-text-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Laporan Mitra</h3>
                        </a>
                    </div>

                    <!-- Data Sholat -->
                    <div x-show="filterMenu('Data Sholat')" x-transition class="menu-item">
                        <a href="{{ route('reportSholat.index') }}"
                            class="relative block p-5 transition-all duration-300 bg-white border border-gray-200 rounded-xl group hover:shadow-md hover:border-teal-300 hover:bg-teal-50">
                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-teal-100 rounded-lg group-hover:bg-teal-200 group-hover:scale-105">
                                <i class="text-2xl text-teal-600 ri-shield-check-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Data Sholat</h3>
                        </a>
                    </div>

                    <!-- Data Slip -->
                    <div x-show="filterMenu('Data Slip')" x-transition class="menu-item">
                        <a href="{{ route('admin-slip') }}"
                            class="relative block p-5 transition-all duration-300 bg-white border border-gray-200 rounded-xl group hover:shadow-md hover:border-yellow-300 hover:bg-yellow-50">
                            <!-- Icon Container -->
                            <div
                                class="flex items-center justify-center w-12 h-12 mb-3 transition-all duration-300 bg-yellow-100 rounded-lg group-hover:bg-yellow-200 group-hover:scale-105">
                                <i class="text-2xl text-yellow-600 ri-wallet-3-line"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="text-sm font-semibold text-gray-800">Data Slip</h3>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <x-footer-component />

    <script>
        function menuController() {
            return {
                activeMenu: null,
                search: '',

                toggleMenu(menuName) {
                    this.activeMenu = this.activeMenu === menuName ? null : menuName;
                },

                filterMenu(menuName) {
                    if (this.search === '') return true;
                    return menuName.toLowerCase().includes(this.search.toLowerCase());
                }
            }
        }
    </script>
</x-app-layout>
