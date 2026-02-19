<x-admin-layout>
    @section('title', 'Halaman Dashboard')

    @php
        $expiringContracts = is_countable($expert) ? count($expert) : 0;
        $inactiveUsers = count($notActiveUsers);
    @endphp

    <div class="pb-10 space-y-6">
        <section class="p-5 border shadow-sm rounded-2xl border-white/60 bg-white/70 backdrop-blur sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-blue-600">Admin Overview</p>
            <div class="flex flex-col gap-2 mt-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Dashboard Admin</h2>
                    <p class="mt-1 text-sm text-gray-600">Ringkasan status operasional hari ini.</p>
                </div>
                <a href="{{ route('data-izin.admin') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-700 transition border border-blue-200 rounded-xl bg-blue-50 hover:bg-blue-100">
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
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $ex->client->name }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        Berakhir: {{ Carbon\Carbon::createFromFormat('Y-m-d', $ex->experied)->isoFormat('DD MMMM YYYY') }}
                                    </p>
                                </div>
                                <a href="{{ url('kerjasamas/'.$ex->id.'/edit') }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                    Update
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="px-5 py-6 text-sm text-gray-500">Tidak ada kontrak yang mendekati masa berakhir.</p>
                @endif
            </article>

            <article class="bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">User Tidak Aktif</h3>
                        <p class="text-xs text-gray-500">Pantau user yang butuh follow-up</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-lg bg-amber-50 text-amber-700">{{ $inactiveUsers }}</span>
                </div>

                @if ($inactiveUsers > 0)
                    <div class="overflow-y-auto divide-y divide-gray-100 max-h-80">
                        @foreach ($notActiveUsers as $user)
                            <div class="flex items-center justify-between gap-3 px-5 py-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }} | {{ ucwords(strtolower($user->nama_lengkap)) }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        Terakhir aktif:
                                        {{ $user->absensi()->latest()->first() ? Carbon\Carbon::parse($user->absensi()->latest()->first()->created_at)->diffForHumans() : 'Belum pernah absen' }}
                                    </p>
                                </div>
                                <a href="{{ url('users/'.$user->id.'/edit') }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                    Update
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="px-5 py-6 text-sm text-gray-500">Tidak ada user tidak aktif.</p>
                @endif
            </article>
        </section>
    </div>
</x-admin-layout>
