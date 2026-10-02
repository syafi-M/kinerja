<x-app-layout>
    <div class="mx-auto w-full max-w-5xl space-y-4 px-4 py-8 sm:px-8">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[.18em] text-sky-600">Direksi</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Work Order</h1>
            <p class="mt-1 text-sm text-slate-500">Pilih karyawan, tanggal, lalu tulis instruksi.</p>
        </section>

        @if (session('success'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

            {{-- @foreach ($users as $user) --}}
            {{-- {{ $employee?->id === $user->id ? 'border-sky-400 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-700' }} --}}
                {{-- <a href="{{ route('direksi.work-order.calendar', ['user' => $user->id]) }}"
                    class="rounded-xl border p-4 font-semibold transition hover:border-sky-300 hover:bg-sky-50 ">
                    <i class="ri-user-line mr-2"></i>{{ $user->nama_lengkap }}
                </a> --}}

            {{-- @endforeach --}}

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,.04),0_12px_32px_-16px_rgba(15,23,42,.12)] sm:p-7">
        <section class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($users as $user)
                <a href="{{ route('direksi.work-order.calendar', ['user' => $user->id])  }}"
                   class="mt-3 group rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,.04),0_12px_32px_-16px_rgba(15,23,42,.12)] transition duration-300 ease-[cubic-bezier(.22,1,.36,1)] hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-[0_16px_36px_-18px_rgba(15,23,42,.22)] active:scale-[.99]">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-sky-50 text-lg text-sky-600 transition duration-300 group-hover:bg-sky-500 group-hover:text-white"><i class="ri-user-line"></i></span>
                            <div class="min-w-0">
                                <h2 class="truncate font-semibold capitalize text-slate-900">{{ strtolower($user->nama_lengkap) }}</h2>
                                <p class="mt-0.5 truncate text-xs text-slate-500">{{ $user->jabatan->name_jabatan ?? '-' }}</p>
                            </div>
                        </div>
                        <i class="ri-arrow-right-line text-lg text-slate-300 transition duration-300 ease-[cubic-bezier(.22,1,.36,1)] group-hover:translate-x-1 group-hover:text-sky-500"></i>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-500"><i class="ri-building-line text-sky-500"></i>{{ $user->divisi->name ?? 'Divisi -' }}</div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-14 text-center text-sm text-slate-500">
                    <i class="ri-user-search-line mb-2 block text-3xl text-slate-300"></i>
                    Karyawan tidak ditemukan.
                </div>
            @endforelse
        </section>

    </div>
        <script>
            document.querySelectorAll('[data-date]').forEach(day => day.onclick = () => document.getElementById('order-date')
                .value = day.dataset.date);
        </script>
</x-app-layout>
