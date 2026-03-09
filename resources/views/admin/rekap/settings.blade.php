<x-admin-layout :fullWidth="true">
    @section('title', 'Pengaturan Rekap')

    <div class="w-full px-2 py-6 mx-auto space-y-4 max-w-screen-xl sm:px-3 lg:px-4">
        <section class="p-4 bg-white border border-gray-100 shadow-sm rounded-2xl sm:p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-blue-600">Rekap Management</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Pengaturan Due Date Rekap</h1>
                    <p class="mt-1 text-sm text-gray-600">Jika hari ini melewati due date, user CO-CS/CO-SCR akan diblokir membuat absensi baru (kecuali exempt).</p>
                </div>
                <a href="{{ route('admin.index') }}" class="inline-flex h-10 items-center rounded-xl border border-red-200 bg-red-50 px-4 text-sm font-semibold text-red-700 transition hover:bg-red-100">Kembali</a>
            </div>
        </section>

        <section class="p-4 bg-white border border-gray-100 shadow-sm rounded-2xl sm:p-5">
            @if (!$setting)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700">
                    Due date belum diatur. Sistem saat ini tidak melakukan blokir absensi berbasis due date.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.rekap.settings.update') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="due_date" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600">Due Date Global</label>
                    <input
                        type="date"
                        id="due_date"
                        name="due_date"
                        value="{{ old('due_date', optional($setting?->due_date)->format('Y-m-d')) }}"
                        class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-800 focus:border-blue-300 focus:bg-white focus:outline-none"
                        required
                    >
                    @error('due_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex h-10 items-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Simpan Due Date
                    </button>
                </div>
            </form>

            @if ($setting)
                <div class="mt-5 border-t border-gray-100 pt-4 text-sm text-gray-600">
                    <p>Due date aktif: <span class="font-semibold text-gray-800">{{ $setting->due_date->format('d M Y') }}</span></p>
                    <p>Diubah oleh: <span class="font-semibold text-gray-800">{{ $setting->updater->nama_lengkap ?? '-' }}</span></p>
                    <p>Terakhir update: <span class="font-semibold text-gray-800">{{ $setting->updated_at?->format('d M Y H:i') }}</span></p>
                </div>
            @endif
        </section>
    </div>
</x-admin-layout>

