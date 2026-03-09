<x-mitra-layout title="Data Karyawan Mitra">
    <div class="p-6 border shadow-xl bg-slate-700 border-slate-600 rounded-3xl">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-blue-400">Data Personel Mitra</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-white">{{ $totalKaryawan }} Karyawan Terdaftar</h1>
                <p class="mt-1 text-sm text-slate-300">Kelola daftar karyawan aktif di area kerja Anda.</p>
            </div>
            <form id="mitraUserSearchForm" method="GET" action="{{ route('mitra_user') }}" class="flex flex-wrap items-center gap-2">
                <input
                    id="mitraUserSearchInput"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari nama, username, email..."
                    class="w-full md:w-72 text-sm input input-sm input-bordered bg-slate-200 text-slate-800 border-slate-300"
                />
                @if($search)
                    <a href="{{ route('mitra_user') }}" class="btn btn-sm btn-ghost text-slate-300 hover:text-white">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="mt-6 overflow-hidden border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
        <div class="p-4 border-b border-slate-600/50">
            <h2 class="text-xs font-black tracking-[0.3em] uppercase text-slate-400">Daftar Karyawan</h2>
        </div>
        <div class="p-4">
            @if($users->isEmpty())
                <p class="text-sm italic text-slate-400">Data karyawan tidak ditemukan.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-slate-300">
                        <thead class="text-xs uppercase bg-slate-800/50 text-slate-400">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Profil</th>
                                <th class="px-4 py-3">Username</th>
                                <th class="px-4 py-3">Nama Lengkap</th>
                                <th class="px-4 py-3">Jabatan</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Penempatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="border-b border-slate-700/50 hover:bg-slate-800/30">
                                    <td class="px-4 py-3">{{ $users->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3">
                                        @if($user->image && $user->image !== 'no-image.jpg' && \Illuminate\Support\Facades\Storage::disk('public')->exists('images/' . $user->image))
                                            <img
                                                class="object-cover w-10 h-10 rounded-full"
                                                loading="lazy"
                                                src="{{ asset('storage/images/' . $user->image) }}"
                                                alt="Foto {{ $user->nama_lengkap }}"
                                            >
                                        @else
                                            <div class="flex items-center justify-center w-10 h-10 text-xs font-bold rounded-full bg-slate-600 text-slate-200">
                                                {{ strtoupper(substr($user->nama_lengkap ?? 'K', 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $user->name }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-100">{{ ucwords(strtolower($user->nama_lengkap)) }}</td>
                                    <td class="px-4 py-3">{{ $user->divisi?->jabatan?->code_jabatan ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $user->email ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $user->kerjasama?->client?->panggilan ?: ($user->kerjasama?->client?->name ?? '-') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('mitraUserSearchForm');
            const input = document.getElementById('mitraUserSearchInput');
            if (!form || !input) return;

            let debounceTimer = null;
            input.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    form.submit();
                }, 300);
            });
        });
    </script>
</x-mitra-layout>
