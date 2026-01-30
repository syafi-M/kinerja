<x-app-layout>
    <x-main-div>
        <div class="min-h-screen opacity-100 bg-gradient-to-br from-gray-100 via-blue-50 to-indigo-100">

            <!-- Modern Header Bar -->
            <div class="sticky top-0 z-50 mb-6 border-b shadow-sm backdrop-blur-xl bg-white/70 border-gray-200/50">
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

               {{-- Alerts Section --}}
                @if ($izin || $expert || count($notActiveUsers) > 0)
                    <div class="mb-6 space-y-4">

                        {{-- Pending Approvals --}}
                        @if ($izin)
                            <a href="{{ route('data-izin.admin') }}"
                            class="group relative block overflow-hidden rounded-2xl bg-gradient-to-r from-sky-500 to-blue-600 p-6 shadow-lg transition-all hover:scale-[1.01] hover:shadow-xl">

                                <div class="absolute inset-0 transition-transform duration-700 -translate-x-full -skew-x-12 bg-white/10 group-hover:translate-x-full"></div>

                                <div class="relative flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center justify-center h-14 w-14 rounded-xl bg-white/20 backdrop-blur">
                                            <i class="text-2xl text-white ri-notification-3-line"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-white">Pending Approvals</h3>
                                            <p class="text-sm text-blue-100">{{ $izin }} izin menunggu persetujuan Anda</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-12 h-12 bg-white shadow-lg rounded-xl">
                                            <span class="text-xl font-bold text-blue-600">{{ $izin }}</span>
                                        </div>
                                        <i class="text-xl text-white transition-transform ri-arrow-right-line group-hover:translate-x-1"></i>
                                    </div>
                                </div>
                            </a>
                        @endif

                        {{-- Expiring Contracts --}}
                        @if ($expert)
                            <div x-data="{ open: false }" class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-2xl">

                                <div @click="open = !open"
                                    class="px-6 py-4 transition cursor-pointer bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/20 backdrop-blur">
                                                <i class="text-xl text-white ri-time-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-base font-bold text-white">Kontrak Akan Berakhir</h3>
                                                <p class="text-xs text-red-100">{{ count($expert) }} kontrak memerlukan perhatian</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-lg">
                                                <span class="text-lg font-bold text-red-600">{{ count($expert) }}</span>
                                            </div>
                                            <i class="text-2xl text-white transition"
                                            :class="open ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="open" x-collapse class="overflow-y-auto divide-y max-h-96">
                                    @foreach ($expert as $ex)
                                        <div class="flex items-center justify-between px-6 py-4 group hover:bg-gray-50">
                                            <div class="flex items-center flex-1 min-w-0 gap-4">
                                                <div class="flex items-center justify-center w-12 h-12 transition rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 group-hover:scale-110">
                                                    <i class="text-lg text-gray-600 ri-building-line"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-900 truncate">{{ $ex->client->name }}</p>
                                                    <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
                                                        <i class="text-xs text-red-500 ri-calendar-line"></i>
                                                        {{ Carbon\Carbon::createFromFormat('Y-m-d', $ex->experied)->isoFormat('DD MMMM YYYY') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <a href="{{ url('kerjasamas/'.$ex->id.'/edit') }}"
                                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition rounded-lg shadow-md bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 hover:shadow-lg">
                                                <i class="ri-edit-line"></i> Update
                                            </a>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endif

                        {{-- Not Active Users --}}
                        @if (count($notActiveUsers) > 0)
                            <div x-data="{ open: false }" class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-2xl">

                                <div @click="open = !open"
                                    class="px-6 py-4 transition cursor-pointer bg-gradient-to-r from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-white/20 backdrop-blur">
                                                <i class="text-xl text-white ri-user-line"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-base font-bold text-white">User Tidak Aktif</h3>
                                                <p class="text-xs text-yellow-100">{{ count($notActiveUsers) }} user tidak aktif dalam 1 bulan terakhir atau lebih</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-10 h-10 bg-white rounded-lg shadow-lg">
                                                <span class="text-lg font-bold text-yellow-600">{{ count($notActiveUsers) }}</span>
                                            </div>
                                            <i class="text-2xl text-white transition"
                                            :class="open ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="open" x-collapse class="overflow-y-auto divide-y max-h-96">
                                    @foreach ($notActiveUsers as $user)
                                        <div class="flex items-center justify-between px-6 py-4 group hover:bg-gray-50">
                                            <div class="flex items-center flex-1 min-w-0 gap-4 overflow-hidden">
                                                <div class="flex items-center justify-center w-12 h-12 transition rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 group-hover:scale-110">
                                                    <i class="text-lg text-gray-600 ri-user-line"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-gray-900 truncate">{{ $user->name }} | {{ ucwords(strtolower($user->nama_lengkap)) }}</p>
                                                    <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
                                                        <i class="text-xs text-yellow-500 ri-time-line"></i>
                                                        Terakhir aktif: {{ $user->absensi()->latest()->first() ? Carbon\Carbon::parse($user->absensi()->latest()->first()->created_at)->diffForHumans() : 'Belum pernah absen' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ url('users/'.$user->id.'/edit') }}"
                                                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition rounded-lg shadow-md bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 hover:shadow-lg">
                                                <i class="ri-edit-line"></i> Update
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Menu Grid with Alpine.js -->
                <div x-data="menuController()" class="space-y-4 " @click.away="activeMenu = null">

                    <!-- Search Bar -->
                    <div class="relative">
                        <input x-model="search" type="text" placeholder="Cari menu..."
                            class="w-full px-4 py-3 pl-12 transition-all bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="absolute text-xl text-gray-400 -translate-y-1/2 ri-search-line left-4 top-1/2"></i>
                    </div>

                    <!-- Menu Grid -->
                    <div class="flex flex-wrap justify-center gap-4 pb-36">
                        <template x-for="menu in filteredMenus()" :key="menu.key">
                            <div class="relative w-[calc(50%-1rem)] sm:w-[calc(33.33%-1rem)] lg:w-[calc(25%-1rem)] xl:w-[calc(20%-1rem)] overflow-visible">

                                <div @click.stop="menu.items ? toggleMenu(menu.key) : window.location.href = menu.link"
                                    class="relative p-4 transition-all bg-white border cursor-pointer rounded-2xl hover:shadow-lg hover:-translate-y-1 min-h-20"
                                    :class="[menu.classes.hoverBg, menu.classes.border]">

                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-xl"
                                            :class="[menu.classes.bg, menu.classes.iconText]">
                                            <i class="text-xl" :class="menu.icon"></i>
                                        </div>
                                        <h3 class="text-sm font-semibold text-gray-800" x-text="menu.title"></h3>
                                    </div>

                                    <template x-if="menu.items">
                                        <i class="absolute text-lg text-gray-400 transition-transform duration-300 ri-arrow-down-s-line top-2 right-2"
                                        :class="activeMenu === menu.key && 'rotate-180'"></i>
                                    </template>
                                </div>

                                <div x-show="activeMenu === menu.key"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                                    class="absolute block z-[40] top-full left-0 right-0 p-2 mt-2 bg-white border border-gray-200 shadow-lg rounded-xl min-w-[200px]">

                                    <div class="flex flex-col gap-1">
                                        <template x-for="item in menu.items" :key="item.label">
                                            <a :href="item.route"
                                            class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 transition rounded-lg hover:bg-blue-50 hover:text-blue-600">
                                                <i :class="item.icon" class="text-base text-gray-400"></i>
                                                <span x-text="item.label"></span>
                                            </a>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

    </x-main-div>
    <x-footer-component />

    <script>
        function menuController() {
            return {
                search: '',
                activeMenu: null,

                init() {
                    // Close submenu when scrolling
                    window.addEventListener('scroll', () => {
                        this.activeMenu = null;
                    });
                },

                menus: [
                    {
                        key: 'user',
                        title: 'Menu User',
                        icon: 'ri-folder-user-line',
                        classes: {
                            bg: 'bg-blue-50',
                            hoverBg: 'hover:bg-blue-100',
                            border: 'border-blue-200',
                            iconBg: 'bg-blue-100',
                            iconText: 'text-blue-600',
                        },
                        items: [
                            { label: 'Data User', route: '{{ route("users.index") }}', icon: 'ri-user-line' },
                            { label: 'Tambah User', route: '{{ route("users.create") }}', icon: 'ri-add-line' },
                        ]
                    },
                    {
                        key: 'divisi',
                        title: 'Menu Divisi & Jabatan',
                        icon: 'ri-organization-chart',
                        classes: {
                            bg: 'bg-purple-50',
                            hoverBg: 'hover:bg-purple-100',
                            border: 'border-purple-200',
                            iconBg: 'bg-purple-100',
                            iconText: 'text-purple-600',
                        },
                        items: [
                            { label: 'Data Divisi', route: '{{ route("divisi.index") }}', icon: 'ri-building-4-line' },
                            { label: 'Tambah Divisi', route: '{{ route("divisi.create") }}', icon: 'ri-add-line' },
                            { label: 'Data Jabatan', route: '{{ route("jabatan.index") }}', icon: 'ri-award-line' },
                        ]
                    },
                    {
                        key: 'client',
                        title: 'Menu Klien & Lokasi',
                        icon: 'ri-briefcase-4-line',
                        classes: {
                            bg: 'bg-green-50',
                            hoverBg: 'hover:bg-green-100',
                            border: 'border-green-200',
                            iconBg: 'bg-green-100',
                            iconText: 'text-green-600',
                        },
                        items: [
                            { label: 'Data Client', route: '{{ route("data-client.index") }}', icon: 'ri-briefcase-line' },
                            { label: 'Tambah Client', route: '{{ route("data-client.create") }}', icon: 'ri-add-line' },
                            { label: 'Data Kerjasama', route: '{{ route("kerjasama.index") }}', icon: 'ri-file-list-3-line' },
                            { label: 'Data Lokasi', route: '{{ route("lokasi.index") }}', icon: 'ri-pin-distance-line' },
                        ]
                    },
                    {
                        key: 'shift',
                        title: 'Menu Shift & Jadwal',
                        icon: 'ri-calendar-event-line',
                        classes: {
                            bg: 'bg-indigo-50',
                            hoverBg: 'hover:bg-indigo-100',
                            border: 'border-indigo-200',
                            iconBg: 'bg-indigo-100',
                            iconText: 'text-indigo-600',
                        },
                        items: [
                            { label: 'Data Shift', route: '{{ route("shift.index") }}', icon: 'ri-calendar-2-line' },
                            { label: 'Data Jadwal User', route: '{{ route("admin-jadwal.index") }}', icon: 'ri-calendar-event-line' },
                        ]
                    },
                    {
                        key: 'poin',
                        title: 'Menu Poin',
                        icon: 'ri-star-line',
                        classes: {
                            bg: 'bg-yellow-50',
                            hoverBg: 'hover:bg-yellow-100',
                            border: 'border-yellow-200',
                            iconBg: 'bg-yellow-100',
                            iconText: 'text-yellow-600',
                        },
                        items: [
                            { label: 'Data Poin', route: '{{ route("point.index") }}', icon: 'ri-star-line' },
                            { label: 'Tambah Poin', route: '{{ route("point.create") }}', icon: 'ri-add-line' },
                        ]
                    },
                    {
                        key: 'absensi',
                        title: 'Menu Absensi',
                        icon: 'ri-list-check-3',
                        classes: {
                            bg: 'bg-gray-50',
                            hoverBg: 'hover:bg-gray-100',
                            border: 'border-gray-200',
                            iconBg: 'bg-gray-100',
                            iconText: 'text-gray-600',
                        },
                        items: [
                            { label: 'Data Absensi', route: '{{ route("admin.absen") }}', icon: 'ri-list-check-3' },
                            { label: 'Data Izin', route: '{{ route("data-izin.admin") }}', icon: 'ri-shield-user-line' },
                            { label: 'Data Lembur', route: '{{ route("lemburList") }}', icon: 'ri-hourglass-2-line' },
                            { label: 'Data Sholat', route: '{{ route('reportSholat.index') }}', icon: 'ri-shield-check-line' },
                        ]
                    },
                    {
                        key: 'perlengkapan',
                        title: 'Menu Perlengkapan',
                        icon: 'ri-tools-line',
                        classes: {
                            bg: 'bg-teal-50',
                            hoverBg: 'hover:bg-teal-100',
                            border: 'border-teal-200',
                            iconBg: 'bg-teal-100',
                            iconText: 'text-teal-600',
                        },
                        items: [
                            { label: 'Data Perlengkapan', route: '{{ route("perlengkapan.index") }}', icon: 'ri-hammer-line' },
                            { label: 'Tambah Perlengkapan', route: '{{ route("perlengkapan.create") }}', icon: 'ri-add-line' },
                        ]
                    },
                    {
                        key: 'laporan',
                        title: 'Menu Laporan',
                        icon: 'ri-task-line',
                        classes: {
                            bg: 'bg-red-50',
                            hoverBg: 'hover:bg-red-100',
                            border: 'border-red-200',
                            iconBg: 'bg-red-100',
                            iconText: 'text-red-600',
                        },
                        items: [
                            { label: 'Data Laporan', route: '{{ route("laporan.index") }}', icon: 'ri-calendar-2-line' },
                            { label: 'Laporan Mitra', route: '{{ route("laporanMitra.index") }}', icon: 'ri-file-text-line' },
                            { label: 'Data QR Code', route: '{{ route("qrcode.index") }}', icon: 'ri-qr-code-line' },
                        ]
                    },
                    {
                        key: 'checkpoint',
                        title: 'Menu Checkpoint',
                        icon: 'ri-check-double-line',
                        classes: {
                            bg: 'bg-purple-50',
                            hoverBg: 'hover:bg-purple-100',
                            border: 'border-purple-200',
                            iconBg: 'bg-purple-100',
                            iconText: 'text-purple-600',
                        },
                        items: [
                            { label: 'Data Checkpoint', route: '{{ route("admin.cp.index") }}', icon: 'ri-check-double-line' },
                            { label: 'Data Pekerjaan CP', route: '{{ route("pekerjaanCp.index") }}', icon: 'ri-file-list-line' },
                        ]
                    },
                    {
                        key: 'berita',
                        title: 'Menu Berita',
                        icon: 'ri-newspaper-line',
                        classes: {
                            bg: 'bg-indigo-50',
                            hoverBg: 'hover:bg-indigo-100',
                            border: 'border-indigo-200',
                            iconBg: 'bg-indigo-100',
                            iconText: 'text-indigo-600',
                        },
                        items: [
                            { label: 'Data Berita', route: '{{ route("news.index") }}', icon: 'ri-news-line' },
                        ]
                    },
                    {
                        key: 'slip',
                        title: 'Menu Slip Gaji',
                        icon: 'ri-file-text-line',
                        classes: {
                            bg: 'bg-blue-50',
                            hoverBg: 'hover:bg-blue-100',
                            border: 'border-blue-200',
                            iconBg: 'bg-blue-100',
                            iconText: 'text-blue-600',
                        },
                        items: [
                            { label: 'Data Slip Gaji', route: '{{ route("admin-slip") }}', icon: 'ri-file-text-line' },
                        ]
                    }
                ],

                filteredMenus() {
                    return this.menus.filter(m =>
                        m.title.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                toggleMenu(key) {
                    this.activeMenu = this.activeMenu === key ? null : key;
                }
            }
        }
    </script>
</x-app-layout>
