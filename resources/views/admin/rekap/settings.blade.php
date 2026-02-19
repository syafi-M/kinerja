<x-app-layout>
    <x-main-div>
        <div class="mx-auto max-w-3xl p-4 sm:p-6 lg:p-8">
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h1 class="text-2xl font-bold text-slate-800">Pengaturan Due Date Rekap</h1>
                <p class="mt-1 text-sm text-slate-500">Jika hari ini melewati due date, user CO-CS/CO-SCR akan diblokir membuat absensi baru (kecuali exempt).</p>

                @if (!$setting)
                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700">
                        Due date belum diatur. Sistem saat ini tidak melakukan blokir absensi berbasis due date.
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.rekap.settings.update') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="due_date" class="mb-1 block text-sm font-semibold text-slate-700">Due Date Global</label>
                        <input type="date" id="due_date" name="due_date"
                            value="{{ old('due_date', optional($setting?->due_date)->format('Y-m-d')) }}"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                            required>
                        @error('due_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Simpan Due Date
                    </button>
                </form>

                @if ($setting)
                    <div class="mt-6 border-t border-slate-200 pt-4 text-sm text-slate-600">
                        <p>Due date aktif: <span class="font-semibold text-slate-800">{{ $setting->due_date->format('d M Y') }}</span></p>
                        <p>Diubah oleh: <span class="font-semibold text-slate-800">{{ $setting->updater->nama_lengkap ?? '-' }}</span></p>
                        <p>Terakhir update: <span class="font-semibold text-slate-800">{{ $setting->updated_at?->format('d M Y H:i') }}</span></p>
                    </div>
                @endif
            </div>
        </div>
    </x-main-div>
</x-app-layout>
