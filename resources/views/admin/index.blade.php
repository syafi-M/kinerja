<x-admin-layout>
    @section('title', 'Halaman Dashboard')

    @php
        $expiringContracts = $expiringContractsCount ?? $expert->count();
        $inactiveUsers = $inactiveUsersCount ?? $notActiveUsers->count();
    @endphp

    <div class="pb-10 space-y-6">
        <section class="p-5 border shadow-sm rounded-2xl border-white/60 bg-white/70 backdrop-blur sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-blue-600">Admin Overview</p>
            <div class="flex flex-col gap-2 mt-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Dashboard Admin</h2>
                    <p class="mt-1 text-sm text-gray-600">Ringkasan status operasional hari ini.</p>
                </div>
                <a href="{{ route('admin.izin.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-700 transition border border-blue-200 rounded-xl bg-blue-50 hover:bg-blue-100">
                    <i class="ri-notification-3-line"></i>
                    Review Izin
                </a>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <article class="p-5 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">Pending Izin</p>
                    <span class="p-2 text-blue-600 bg-blue-100 rounded-lg"><i class="ri-notification-3-line"></i></span>
                </div>
                <p class="mt-4 text-3xl font-bold text-gray-900">{{ $izin ?? 0 }}</p>
                <p class="mt-1 text-xs text-gray-500">Perlu approval admin</p>
            </article>

            <article class="p-5 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">Kontrak Berakhir</p>
                    <span class="p-2 text-red-600 bg-red-100 rounded-lg"><i class="ri-time-line"></i></span>
                </div>
                <p class="mt-4 text-3xl font-bold text-gray-900">{{ $expiringContracts }}</p>
                <p class="mt-1 text-xs text-gray-500">Butuh tindak lanjut</p>
            </article>

            <article class="p-5 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">User Tidak Aktif</p>
                    <span class="p-2 rounded-lg bg-amber-100 text-amber-600"><i class="ri-user-line"></i></span>
                </div>
                <p class="mt-4 text-3xl font-bold text-gray-900">{{ $inactiveUsers }}</p>
                <p class="mt-1 text-xs text-gray-500">Tidak aktif 1 bulan+</p>
            </article>
        </section>

        <section class="grid gap-4 xl:grid-cols-2">
            {{-- Kontrak Akan Berakhir --}}
            <article class="bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Kontrak Akan Berakhir</h3>
                        <p class="text-xs text-gray-500">Daftar kontrak prioritas</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold text-red-700 rounded-lg bg-red-50">{{ $expiringContracts }}</span>
                </div>
                @if ($expiringContracts > 0)
                    <div class="overflow-y-auto divide-y divide-gray-100 max-h-80">
                        @foreach ($expert as $ex)
                            <div class="flex items-center justify-between gap-3 px-5 py-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $ex->client->name ?? 'Client tidak ditemukan' }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        Berakhir:
                                        {{ Carbon\Carbon::createFromFormat('Y-m-d', $ex->experied)->isoFormat('DD MMMM YYYY') }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.kerjasama.edit', $ex->id) }}"
                                    class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                    Update
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="px-5 py-6 text-sm text-gray-500">Tidak ada kontrak yang mendekati masa berakhir.</p>
                @endif
            </article>

            {{-- User Tidak Aktif --}}
            <article class="bg-white border border-gray-100 shadow-sm rounded-2xl"
                x-data="{
                    // --- raw data from server ---
                    allUsers: @js($notActiveUsers->map(fn($u) => [
                        'id'             => $u->id,
                        'name'           => $u->name,
                        'nama_lengkap'   => $u->nama_lengkap ?? '',
                        'last_attendance'=> $u->last_attendance,
                    ])->values()),

                    // --- search & pagination ---
                    search: '',
                    perPage: 10,
                    page: 1,

                    get filtered() {
                        const q = this.search.toLowerCase().trim();
                        if (!q) return this.allUsers;
                        return this.allUsers.filter(u =>
                            u.name.toLowerCase().includes(q) ||
                            u.nama_lengkap.toLowerCase().includes(q)
                        );
                    },
                    get totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.perPage)); },
                    get paginated() {
                        const start = (this.page - 1) * this.perPage;
                        return this.filtered.slice(start, start + this.perPage);
                    },
                    prevPage() { if (this.page > 1) this.page--; },
                    nextPage() { if (this.page < this.totalPages) this.page++; },
                    resetPage() { this.page = 1; },

                    // --- checkbox ---
                    selected: [],
                    get allChecked() { return this.paginated.length > 0 && this.paginated.every(u => this.selected.includes(u.id)); },
                    toggleAll() {
                        const ids = this.paginated.map(u => u.id);
                        if (this.allChecked) this.selected = this.selected.filter(id => !ids.includes(id));
                        else this.selected = [...new Set([...this.selected, ...ids])];
                    },
                    toggle(id) {
                        if (this.selected.includes(id)) this.selected = this.selected.filter(i => i !== id);
                        else this.selected.push(id);
                    },

                    // --- single hard delete modal ---
                    open: false,
                    loading: false,
                    deleting: false,
                    userId: null,
                    userName: '',
                    relations: null,
                    checkRoute: '{{ route('admin.user.check-relations', '__ID__') }}',
                    deleteRoute: '{{ route('admin.user.hard-delete', '__ID__') }}',
                    openModal(id, name) {
                        this.userId = id;
                        this.userName = name;
                        this.relations = null;
                        this.loading = true;
                        this.deleting = false;
                        this.open = true;
                        fetch(this.checkRoute.replace('__ID__', id), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.json())
                        .then(data => { this.relations = data; this.loading = false; })
                        .catch(() => { this.loading = false; });
                    },

                    // --- bulk delete modal ---
                    bulkOpen: false,
                    bulkDeleting: false,
                    get bulkDeleteRoute() {
                        return '{{ route('admin.user.hard-delete', '__ID__') }}';
                    },

                    diffForHumans(dateStr) {
                        if (!dateStr) return 'Belum pernah absen';
                        const diff = Date.now() - new Date(dateStr).getTime();
                        const days = Math.floor(diff / 86400000);
                        if (days < 30) return days + ' hari yang lalu';
                        const months = Math.floor(days / 30);
                        if (months < 12) return months + ' bulan yang lalu';
                        return Math.floor(months / 12) + ' tahun yang lalu';
                    }
                }">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">User Tidak Aktif</h3>
                        <p class="text-xs text-gray-500">
                            <span x-text="filtered.length"></span> / {{ $inactiveUsers }} user
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span x-show="selected.length > 0" x-cloak>
                            <button @click="bulkOpen = true"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                <i class="ri-delete-bin-line"></i>
                                Hapus (<span x-text="selected.length"></span>)
                            </button>
                        </span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-amber-50 text-amber-700">{{ $inactiveUsers }}</span>
                    </div>
                </div>

                {{-- Search --}}
                <div class="px-5 py-3 border-b border-gray-100">
                    <div class="relative">
                        <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" x-model="search" @input="resetPage()"
                            placeholder="Cari nama atau username..."
                            class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition">
                    </div>
                </div>

                {{-- List --}}
                <div class="divide-y divide-gray-100">
                    {{-- Select all row --}}
                    <div x-show="paginated.length > 0"
                        class="flex items-center gap-3 px-5 py-2 bg-gray-50">
                        <input type="checkbox" :checked="allChecked" @change="toggleAll()"
                            class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer">
                        <span class="text-xs text-gray-500">Pilih semua di halaman ini</span>
                    </div>

                    <template x-for="user in paginated" :key="user.id">
                        <div class="flex items-center gap-3 px-5 py-3">
                            <input type="checkbox"
                                :checked="selected.includes(user.id)"
                                @change="toggle(user.id)"
                                class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer shrink-0">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate"
                                    x-text="user.name + (user.nama_lengkap ? ' | ' + user.nama_lengkap : '')"></p>
                                <p class="mt-0.5 text-xs text-gray-500"
                                    x-text="'Terakhir aktif: ' + diffForHumans(user.last_attendance)"></p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <a :href="'/users/' + user.id + '/edit'"
                                    class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                    Update
                                </a>
                                <button type="button"
                                    @click="openModal(user.id, user.name)"
                                    title="Hard Delete"
                                    class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="paginated.length === 0" class="px-5 py-6 text-sm text-gray-500 text-center">
                        <span x-text="search ? 'Tidak ada hasil untuk &quot;' + search + '&quot;' : 'Tidak ada user tidak aktif.'"></span>
                    </div>
                </div>

                {{-- Pagination --}}
                <div x-show="totalPages > 1"
                    class="flex items-center justify-between px-5 py-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500">
                        Halaman <span x-text="page"></span> / <span x-text="totalPages"></span>
                        &nbsp;·&nbsp;<span x-text="filtered.length"></span> user
                    </p>
                    <div class="flex items-center gap-1">
                        <button @click="prevPage()" :disabled="page === 1"
                            class="px-2.5 py-1.5 text-xs font-semibold border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            <i class="ri-arrow-left-s-line"></i>
                        </button>
                        <button @click="nextPage()" :disabled="page === totalPages"
                            class="px-2.5 py-1.5 text-xs font-semibold border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </div>
                </div>

                {{-- =================== Modal Single Hard Delete =================== --}}
                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
                    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-6 space-y-4" @click.stop>
                        <div class="flex items-start gap-3">
                            <span class="p-2 bg-red-100 rounded-lg text-red-600 shrink-0">
                                <i class="ri-error-warning-line text-lg"></i>
                            </span>
                            <div>
                                <h4 class="text-base font-bold text-gray-900">Hard Delete User</h4>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    Hapus permanen: <strong x-text="userName"></strong>
                                </p>
                            </div>
                        </div>

                        <div x-show="loading" class="py-4 text-center text-sm text-gray-500">
                            <i class="ri-loader-4-line animate-spin mr-1"></i> Mengecek relasi data...
                        </div>

                        <div x-show="!loading && relations !== null" class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Relasi Data Ditemukan</p>
                            <div class="rounded-xl border border-gray-100 divide-y divide-gray-100 text-sm">
                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-gray-600"><i class="ri-calendar-check-line mr-1 text-gray-400"></i>Data Absensi</span>
                                    <span class="font-semibold" :class="relations?.absensi > 0 ? 'text-red-600' : 'text-gray-400'"
                                        x-text="(relations?.absensi ?? 0) + ' record'"></span>
                                </div>
                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-gray-600"><i class="ri-user-settings-line mr-1 text-gray-400"></i>Data Karyawan (ID)</span>
                                    <span class="font-semibold" :class="relations?.employes_user_id > 0 ? 'text-red-600' : 'text-gray-400'"
                                        x-text="(relations?.employes_user_id ?? 0) + ' record'"></span>
                                </div>
                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-gray-600"><i class="ri-user-settings-line mr-1 text-gray-400"></i>Data Karyawan (Nama)</span>
                                    <span class="font-semibold" :class="relations?.employes_by_name > 0 ? 'text-red-600' : 'text-gray-400'"
                                        x-text="(relations?.employes_by_name ?? 0) + ' record'"></span>
                                </div>
                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-gray-600"><i class="ri-money-dollar-circle-line mr-1 text-gray-400"></i>Slip Gaji</span>
                                    <span class="font-semibold" :class="relations?.slip_gaji > 0 ? 'text-red-600' : 'text-gray-400'"
                                        x-text="(relations?.slip_gaji ?? 0) + ' record'"></span>
                                </div>
                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-gray-600"><i class="ri-file-chart-line mr-1 text-gray-400"></i>Data Rekap</span>
                                    <span class="font-semibold" :class="relations?.rekap_total > 0 ? 'text-red-600' : 'text-gray-400'"
                                        x-text="(relations?.rekap_total ?? 0) + ' record'"></span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                                <i class="ri-alert-line mr-1 text-amber-600"></i>
                                Semua data di atas akan <strong>dihapus permanen</strong> dan tidak bisa dikembalikan.
                            </p>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="open = false"
                                class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <form :action="deleteRoute.replace('__ID__', userId)" method="POST" @submit="deleting = true">
                                @csrf
                                @method('DELETE')
                                <button type="submit" :disabled="loading || deleting"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                                    <i class="ri-delete-bin-line" x-show="!deleting"></i>
                                    <i class="ri-loader-4-line animate-spin" x-show="deleting"></i>
                                    <span x-text="deleting ? 'Menghapus...' : 'Hapus Permanen'"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- =================== Modal Bulk Hard Delete =================== --}}
                <div x-show="bulkOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="bulkOpen = false"></div>
                    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-6 space-y-4" @click.stop>
                        <div class="flex items-start gap-3">
                            <span class="p-2 bg-red-100 rounded-lg text-red-600 shrink-0">
                                <i class="ri-error-warning-line text-lg"></i>
                            </span>
                            <div>
                                <h4 class="text-base font-bold text-gray-900">Bulk Hard Delete</h4>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    Hapus permanen <strong x-text="selected.length"></strong> user yang dipilih.
                                </p>
                            </div>
                        </div>

                        <div class="rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700 space-y-1 max-h-40 overflow-y-auto">
                            <template x-for="id in selected" :key="id">
                                <div x-text="allUsers.find(u => u.id === id)?.name + ' — ' + (allUsers.find(u => u.id === id)?.nama_lengkap || '-')"></div>
                            </template>
                        </div>

                        <p class="text-xs text-gray-500 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                            <i class="ri-alert-line mr-1 text-amber-600"></i>
                            Semua data terkait tiap user akan <strong>dihapus permanen</strong> dan tidak bisa dikembalikan.
                        </p>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="bulkOpen = false"
                                class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                Batal
                            </button>
                            {{-- Submit satu per satu via hidden form --}}
                            <button type="button"
                                :disabled="bulkDeleting"
                                @click="
                                    bulkDeleting = true;
                                    (async () => {
                                        for (const id of selected) {
                                            const form = document.createElement('form');
                                            form.method = 'POST';
                                            form.action = deleteRoute.replace('__ID__', id);
                                            form.innerHTML = '<input name=_token value=\'{{ csrf_token() }}\'><input name=_method value=DELETE>';
                                            document.body.appendChild(form);
                                            form.submit();
                                            await new Promise(r => setTimeout(r, 300));
                                        }
                                    })();
                                "
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                                <i class="ri-delete-bin-line" x-show="!bulkDeleting"></i>
                                <i class="ri-loader-4-line animate-spin" x-show="bulkDeleting"></i>
                                <span x-text="bulkDeleting ? 'Menghapus...' : 'Hapus Semua'"></span>
                            </button>
                        </div>
                    </div>
                </div>

            </article>
        </section>
    </div>
</x-admin-layout>
