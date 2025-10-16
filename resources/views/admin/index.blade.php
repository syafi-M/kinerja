<x-app-layout>
    <x-main-div>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">

            <!-- Modern Header Bar -->
            <div class="sticky top-0 z-50 backdrop-blur-xl bg-white/70 border-b border-gray-200/50 shadow-sm mb-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="ri-dashboard-3-line text-white text-xl"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-gray-900">Dashboard Admin</h1>
                                <p class="text-xs text-gray-500">Welcome back, Admin</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 rounded-lg border border-emerald-200">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                <span class="text-xs font-medium text-emerald-700">{{ $online }} Online</span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg">
                                <i class="ri-global-line text-gray-600 text-sm"></i>
                                <span class="text-xs font-mono text-gray-700">{{ $ip }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

                <!-- Alerts Section -->
                @if ($izin || $expert)
                    <div class="mb-6 space-y-4">
                        <!-- Pending Approvals Alert -->
                        @if ($izin)
                            <a href="{{ route('data-izin.admin') }}"
                                class="block group relative overflow-hidden rounded-2xl bg-gradient-to-r from-sky-500 to-blue-600 p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.01]">
                                <div
                                    class="absolute inset-0 bg-white/10 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700">
                                </div>
                                <div class="relative flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-14 h-14 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                                            <i class="ri-notification-3-line text-white text-2xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-white font-bold text-lg mb-1">Pending Approvals</h3>
                                            <p class="text-blue-100 text-sm">{{ $izin }} izin menunggu
                                                persetujuan Anda</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
                                            <span class="text-blue-600 font-bold text-xl">{{ $izin }}</span>
                                        </div>
                                        <i
                                            class="ri-arrow-right-line text-white text-xl group-hover:translate-x-1 transition-transform"></i>
                                    </div>
                                </div>
                            </a>
                        @endif

                        <!-- Expiring Contracts -->
                        @if ($expert)
                            <div x-data="{ openContracts: false }"
                                class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                                <div @click="openContracts = !openContracts"
                                    class="cursor-pointer bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4 hover:from-red-600 hover:to-pink-700 transition-all duration-300">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center">
                                                <i class="ri-time-line text-white text-xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-white font-bold text-base">Kontrak Akan Berakhir</h3>
                                                <p class="text-red-100 text-xs">{{ count($expert) }} kontrak memerlukan
                                                    perhatian</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg">
                                                <span class="text-red-600 font-bold text-lg">{{ count($expert) }}</span>
                                            </div>
                                            <i :class="openContracts ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                                class="text-white text-2xl transition-transform duration-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="openContracts" x-collapse
                                    class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                                    @foreach ($expert as $ex)
                                        <div
                                            class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150 flex items-center justify-between group overflow-hidden">
                                            <div class="flex items-center gap-4 flex-1 overflow-hidden">
                                                <div
                                                    class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                                    <i class="ri-building-line text-gray-600 text-lg"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-gray-900 truncate">
                                                        {{ $ex->client->name }}</p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <i class="ri-calendar-line text-red-500 text-xs"></i>
                                                        <p class="text-sm text-gray-600">
                                                            {{ Carbon\Carbon::createFromFormat('Y-m-d', $ex->experied)->isoFormat('DD MMMM YYYY') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2 overflow-hidden">
                                                <a href="{{ url('kerjasamas/' . $ex->id . '/edit') }}">
                                                    <i class="ri-edit-line"></i>
                                                    <span>Update</span>
                                                </a>
                                            </div>
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
                            class="w-full px-4 py-3 pl-12 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
                    </div>

                    <!-- Menu Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">

                        <!-- Menu User -->
                        <div x-show="filterMenu('Menu User')" x-transition class="menu-item">
                            <div @click="toggleMenu('user')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-blue-50 hover:to-indigo-50 rounded-2xl p-6 border border-gray-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'user' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-blue-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-folder-user-line text-2xl text-blue-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu User</h3>
                            </div>
                            <div x-show="activeMenu === 'user'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('users.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-user-line"></i> Data User
                                </a>
                                <a href="{{ route('users.create') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-user-add-line"></i> Tambah User
                                </a>
                            </div>
                        </div>

                        <!-- Menu Devisi -->
                        <div x-show="filterMenu('Menu Devisi')" x-transition class="menu-item">
                            <div @click="toggleMenu('devisi')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-purple-50 hover:to-pink-50 rounded-2xl p-6 border border-gray-200 hover:border-purple-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'devisi' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-purple-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-group-2-line text-2xl text-purple-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Devisi</h3>
                            </div>
                            <div x-show="activeMenu === 'devisi'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('devisi.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-team-line"></i> Data Devisi
                                </a>
                                <a href="{{ route('devisi.create') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-add-line"></i> Tambah Devisi
                                </a>
                            </div>
                        </div>

                        <!-- Menu Client -->
                        <div x-show="filterMenu('Menu Client')" x-transition class="menu-item">
                            <div @click="toggleMenu('client')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-green-50 hover:to-emerald-50 rounded-2xl p-6 border border-gray-200 hover:border-green-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'client' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-green-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-p2p-line text-2xl text-green-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Client</h3>
                            </div>
                            <div x-show="activeMenu === 'client'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('data-client.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-user-5-line"></i> Data Client
                                </a>
                                <a href="{{ route('data-client.create') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-add-line"></i> Tambah Client
                                </a>
                            </div>
                        </div>

                        <!-- Menu Shift -->
                        <div x-show="filterMenu('Menu Shift')" x-transition class="menu-item">
                            <div @click="toggleMenu('shift')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-orange-50 hover:to-amber-50 rounded-2xl p-6 border border-gray-200 hover:border-orange-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'shift' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-orange-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-orange-100 to-amber-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-timer-line text-2xl text-orange-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Shift</h3>
                            </div>
                            <div x-show="activeMenu === 'shift'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('shift.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-timer-flash-line"></i> Data Shift
                                </a>
                                <a href="{{ route('shift.create') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-add-line"></i> Tambah Shift
                                </a>
                            </div>
                        </div>

                        <!-- Data Ruangan -->
                        <div x-show="filterMenu('Data Ruangan')" x-transition class="menu-item">
                            <a href="{{ route('ruangan.index') }}"
                                class="block group relative bg-white hover:bg-gradient-to-br hover:from-cyan-50 hover:to-sky-50 rounded-2xl p-6 border border-gray-200 hover:border-cyan-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-cyan-100 to-sky-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-door-open-line text-2xl text-cyan-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Data Ruangan</h3>
                            </a>
                        </div>

                        <!-- Data Point -->
                        <div x-show="filterMenu('Data Point')" x-transition class="menu-item">
                            <a href="{{ route('point.index') }}"
                                class="block group relative bg-white hover:bg-gradient-to-br hover:from-yellow-50 hover:to-amber-50 rounded-2xl p-6 border border-gray-200 hover:border-yellow-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-yellow-100 to-amber-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-coins-line text-2xl text-yellow-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Data Point</h3>
                            </a>
                        </div>

                        <!-- Data Area -->
                        <div x-show="filterMenu('Data Area')" x-transition class="menu-item">
                            <a href="{{ route('area.index') }}"
                                class="block group relative bg-white hover:bg-gradient-to-br hover:from-red-50 hover:to-rose-50 rounded-2xl p-6 border border-gray-200 hover:border-red-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-red-100 to-rose-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-map-pin-2-line text-2xl text-red-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Data Area</h3>
                            </a>
                        </div>

                        <!-- Menu Kerjasama -->
                        <div x-show="filterMenu('Menu Kerjasama')" x-transition class="menu-item">
                            <div @click="toggleMenu('kerjasama')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-indigo-50 hover:to-purple-50 rounded-2xl p-6 border border-gray-200 hover:border-indigo-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'kerjasama' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-indigo-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-shake-hands-line text-2xl text-indigo-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Kerjasama</h3>
                            </div>
                            <div x-show="activeMenu === 'kerjasama'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('kerjasamas.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-hand-coin-line"></i> Data Kerjasama
                                </a>
                                <a href="{{ route('kerjasamas.create') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-add-line"></i> Tambah Kerjasama
                                </a>
                            </div>
                        </div>

                        <!-- Menu Absensi -->
                        <div x-show="filterMenu('Menu Absensi')" x-transition class="menu-item">
                            <div @click="toggleMenu('absen')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-teal-50 hover:to-cyan-50 rounded-2xl p-6 border border-gray-200 hover:border-teal-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'absen' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-teal-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-teal-100 to-cyan-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-calendar-todo-line text-2xl text-teal-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Absensi</h3>
                            </div>
                            <div x-show="activeMenu === 'absen'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('admin.absen') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-list-check-3"></i> Data Absensi
                                </a>
                                <a href="{{ route('data-izin.admin') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-shield-user-line"></i> Data Izin
                                </a>
                            </div>
                        </div>

                        <!-- Menu Perlengkapan -->
                        <div x-show="filterMenu('Menu Perlengkapan')" x-transition class="menu-item">
                            <div @click="toggleMenu('perlengkapan')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-slate-50 hover:to-gray-50 rounded-2xl p-6 border border-gray-200 hover:border-slate-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'perlengkapan' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-slate-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-slate-100 to-gray-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-tools-line text-2xl text-slate-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Perlengkapan</h3>
                            </div>
                            <div x-show="activeMenu === 'perlengkapan'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('perlengkapan.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-hammer-line"></i> Data Perlengkapan
                                </a>
                                <a href="{{ route('perlengkapan.create') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-add-line"></i> Tambah Perlengkapan
                                </a>
                            </div>
                        </div>

                        <!-- Menu Lembur -->
                        <div x-show="filterMenu('Menu Lembur')" x-transition class="menu-item">
                            <div @click="toggleMenu('lembur')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-violet-50 hover:to-purple-50 rounded-2xl p-6 border border-gray-200 hover:border-violet-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'lembur' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-violet-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-violet-100 to-purple-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-time-line text-2xl text-violet-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Lembur</h3>
                            </div>
                            <div x-show="activeMenu === 'lembur'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('lemburList') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-hourglass-2-line"></i> Data Lembur
                                </a>
                            </div>
                        </div>

                        <!-- Menu Jabatan -->
                        <div x-show="filterMenu('Menu Jabatan')" x-transition class="menu-item">
                            <div @click="toggleMenu('jabatan')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-fuchsia-50 hover:to-pink-50 rounded-2xl p-6 border border-gray-200 hover:border-fuchsia-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'jabatan' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-fuchsia-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-fuchsia-100 to-pink-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-medal-line text-2xl text-fuchsia-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Jabatan</h3>
                            </div>
                            <div x-show="activeMenu === 'jabatan'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('jabatan.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-award-line"></i> Data Jabatan
                                </a>
                                <a href="{{ route('jabatan.create') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-add-line"></i> Tambah Jabatan
                                </a>
                            </div>
                        </div>

                        <!-- Menu Lokasi -->
                        <div x-show="filterMenu('Menu Lokasi')" x-transition class="menu-item">
                            <div @click="toggleMenu('lokasi')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-lime-50 hover:to-green-50 rounded-2xl p-6 border border-gray-200 hover:border-lime-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'lokasi' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-lime-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-lime-100 to-green-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-road-map-line text-2xl text-lime-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Lokasi</h3>
                            </div>
                            <div x-show="activeMenu === 'lokasi'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('lokasi.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-pin-distance-line"></i> Data Lokasi
                                </a>
                                <a href="{{ route('lokasi.create') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-map-pin-add-line"></i> Tambah Lokasi
                                </a>
                            </div>
                        </div>

                        <!-- Menu Jadwal -->
                        <div x-show="filterMenu('Menu Jadwal')" x-transition class="menu-item">
                            <div @click="toggleMenu('jadwal')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-rose-50 hover:to-red-50 rounded-2xl p-6 border border-gray-200 hover:border-rose-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'jadwal' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-rose-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-rose-100 to-red-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-calendar-event-line text-2xl text-rose-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Jadwal</h3>
                            </div>
                            <div x-show="activeMenu === 'jadwal'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('admin-jadwal.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-calendar-2-line"></i> Data Jadwal User
                                </a>
                            </div>
                        </div>

                        <!-- Menu Laporan -->
                        <div x-show="filterMenu('Menu Laporan')" x-transition class="menu-item">
                            <div @click="toggleMenu('laporan')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-sky-50 hover:to-blue-50 rounded-2xl p-6 border border-gray-200 hover:border-sky-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'laporan' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-sky-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-sky-100 to-blue-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-task-line text-2xl text-sky-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Laporan</h3>
                            </div>
                            <div x-show="activeMenu === 'laporan'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('laporan.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-calendar-2-line"></i> Data Laporan
                                </a>
                            </div>
                        </div>

                        <!-- Menu Check Point -->
                        <div x-show="filterMenu('Check Point')" x-transition class="menu-item">
                            <div @click="toggleMenu('CP')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-emerald-50 hover:to-teal-50 rounded-2xl p-6 border border-gray-200 hover:border-emerald-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'CP' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-emerald-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-map-pin-range-line text-2xl text-emerald-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Check Point</h3>
                            </div>
                            <div x-show="activeMenu === 'CP'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('admin.cp.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-checkbox-circle-line"></i> Data Check Point
                                </a>
                            </div>
                        </div>

                        <!-- Menu Pekerjaan CP -->
                        <div x-show="filterMenu('Pekerjaan CP')" x-transition class="menu-item">
                            <div @click="toggleMenu('PCP')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-amber-50 hover:to-yellow-50 rounded-2xl p-6 border border-gray-200 hover:border-amber-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'PCP' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-amber-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-amber-100 to-yellow-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-file-list-3-line text-2xl text-amber-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Pekerjaan CP</h3>
                            </div>
                            <div x-show="activeMenu === 'PCP'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('pekerjaanCp.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-file-list-line"></i> Data Pekerjaan CP
                                </a>
                            </div>
                        </div>

                        <!-- Menu Berita -->
                        <div x-show="filterMenu('Menu Berita')" x-transition class="menu-item">
                            <div @click="toggleMenu('News')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-pink-50 hover:to-rose-50 rounded-2xl p-6 border border-gray-200 hover:border-pink-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'News' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-pink-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-pink-100 to-rose-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-newspaper-line text-2xl text-pink-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Menu Berita</h3>
                            </div>
                            <div x-show="activeMenu === 'News'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('news.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-news-line"></i> Data Berita
                                </a>
                            </div>
                        </div>

                        <!-- Menu Sub Area -->
                        <div x-show="filterMenu('Sub Area')" x-transition class="menu-item">
                            <div @click="toggleMenu('SubArea')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-gray-50 hover:to-slate-50 rounded-2xl p-6 border border-gray-200 hover:border-gray-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'SubArea' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-gray-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-gray-100 to-slate-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-map-2-line text-2xl text-gray-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Sub Area</h3>
                            </div>
                            <div x-show="activeMenu === 'SubArea'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('subarea.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-map-pin-line"></i> Data Sub Area
                                </a>
                            </div>
                        </div>

                        <!-- Menu Checklist -->
                        <div x-show="filterMenu('Checklist')" x-transition class="menu-item">
                            <div @click="toggleMenu('Checklist')"
                                class="cursor-pointer group relative bg-white hover:bg-gradient-to-br hover:from-blue-50 hover:to-indigo-50 rounded-2xl p-6 border border-gray-200 hover:border-blue-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i :class="activeMenu === 'Checklist' ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"
                                        class="text-blue-500 text-xl"></i>
                                </div>
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-checkbox-multiple-line text-2xl text-blue-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Checklist</h3>
                            </div>
                            <div x-show="activeMenu === 'Checklist'" x-collapse class="mt-2 space-y-2 pl-2">
                                <a href="{{ route('admin-checklist.index') }}"
                                    class="block px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition-all hover:pl-6">
                                    <i class="ri-check-double-line"></i> Data Checklist
                                </a>
                            </div>
                        </div>

                        <!-- QR Code -->
                        <div x-show="filterMenu('QR Code')" x-transition class="menu-item">
                            <a href="{{ route('qrcode.index') }}"
                                class="block group relative bg-white hover:bg-gradient-to-br hover:from-purple-50 hover:to-indigo-50 rounded-2xl p-6 border border-gray-200 hover:border-purple-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-qr-code-line text-2xl text-purple-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">QR Code</h3>
                            </a>
                        </div>

                        <!-- List Pekerjaan -->
                        <div x-show="filterMenu('List Pekerjaan')" x-transition class="menu-item">
                            <a href="{{ route('listPekerjaan.index') }}"
                                class="block group relative bg-white hover:bg-gradient-to-br hover:from-green-50 hover:to-lime-50 rounded-2xl p-6 border border-gray-200 hover:border-green-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-green-100 to-lime-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-list-check text-2xl text-green-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">List Pekerjaan</h3>
                            </a>
                        </div>

                        <!-- Laporan Mitra -->
                        <div x-show="filterMenu('Laporan Mitra')" x-transition class="menu-item">
                            <a href="{{ route('laporanMitra.index') }}"
                                class="block group relative bg-white hover:bg-gradient-to-br hover:from-orange-50 hover:to-red-50 rounded-2xl p-6 border border-gray-200 hover:border-orange-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-orange-100 to-red-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-file-text-line text-2xl text-orange-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Laporan Mitra</h3>
                            </a>
                        </div>

                        <!-- Data Sholat -->
                        <div x-show="filterMenu('Data Sholat')" x-transition class="menu-item">
                            <a href="{{ route('reportSholat.index') }}"
                                class="block group relative bg-white hover:bg-gradient-to-br hover:from-teal-50 hover:to-emerald-50 rounded-2xl p-6 border border-gray-200 hover:border-teal-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-teal-100 to-emerald-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-shield-check-line text-2xl text-teal-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Data Sholat</h3>
                            </a>
                        </div>

                        <!-- Data Slip -->
                        <div x-show="filterMenu('Data Slip')" x-transition class="menu-item">
                            <a href="{{ route('admin-slip') }}"
                                class="block group relative bg-white hover:bg-gradient-to-br hover:from-yellow-50 hover:to-orange-50 rounded-2xl p-6 border border-gray-200 hover:border-yellow-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <i class="ri-wallet-3-line text-2xl text-yellow-600"></i>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm">Data Slip</h3>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </x-main-div>
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
