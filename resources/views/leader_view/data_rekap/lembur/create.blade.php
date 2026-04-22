<x-app-layout>
    <x-main-div>
        <div class="w-full max-w-3xl px-3 py-4 mx-auto sm:px-5 lg:px-6">
            <div class="p-4 mb-4 bg-white border rounded-lg shadow-sm border-white/60 ring-1 ring-slate-900/5">
                <div class="flex items-center gap-3">
                    <a href="{{ route('index.rekap.data.leader') }}"
                        class="inline-flex items-center justify-center w-10 h-10 ml-1 transition rounded-lg sm:ml-0 shrink-0 bg-slate-100 text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
                        aria-label="Kembali ke rekapitulasi">
                        <i class="text-xl ri-arrow-left-line"></i>
                    </a>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">Data Rekap</p>
                        <h1 class="text-xl font-bold leading-tight truncate text-slate-900 sm:text-2xl">
                            Pengajuan Lembur
                        </h1>
                        <p class="mt-1 text-sm leading-5 text-slate-500">
                            Isi data lembur personil dengan lengkap sebelum disimpan.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('overtime-application.store') }}" method="POST" x-data="overtimeForm()"
                class="space-y-4">
                @csrf

                <section class="p-4 bg-white border rounded-lg shadow-sm border-slate-200 sm:p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="user_id" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Nama Pegawai <span class="text-red-500">*</span>
                            </label>
                            <select name="user_id" id="user_id"
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                required>
                                <option value="">Pilih nama pegawai</option>
                                @forelse($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} - {{ capitalizeWords($user->nama_lengkap) ?? 'N/A' }}
                                    </option>
                                @empty
                                    <option value="" disabled>Tidak ada data pegawai</option>
                                @endforelse
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="date_overtime" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Tanggal Lembur <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date_overtime" id="date_overtime"
                                value="{{ old('date_overtime') }}"
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                required>
                            @error('date_overtime')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="p-4 bg-white border rounded-lg shadow-sm border-slate-200 sm:p-5">
                    <div class="mb-3">
                        <h2 class="text-sm font-semibold text-slate-800">
                            Jenis Lembur <span class="text-red-500">*</span>
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500">Pilih salah satu sesuai perhitungan lembur.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5">
                        <label
                            class="flex items-start gap-3 p-3 transition border rounded-lg cursor-pointer min-h-14 border-slate-200 hover:bg-slate-50"
                            :class="selectedType === 'shift' ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-200' : ''">
                            <input type="radio" name="type_overtime" value="shift" x-model="selectedType"
                                class="w-4 h-4 mt-1 text-sky-600 focus:ring-sky-500"
                                {{ old('type_overtime') == 'shift' ? 'checked' : '' }} required>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-slate-800">Shift</span>
                                <span class="mt-0.5 block text-xs leading-4 text-slate-500">Lembur berdasarkan shift
                                    kerja.</span>
                            </span>
                        </label>

                        <label
                            class="flex items-start gap-3 p-3 transition border rounded-lg cursor-pointer min-h-14 border-slate-200 hover:bg-slate-50"
                            :class="selectedType === 'jam' ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-200' : ''">
                            <input type="radio" name="type_overtime" value="jam" x-model="selectedType"
                                class="w-4 h-4 mt-1 text-sky-600 focus:ring-sky-500"
                                {{ old('type_overtime') == 'jam' ? 'checked' : '' }} required>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-slate-800">Jam</span>
                                <span class="mt-0.5 block text-xs leading-4 text-slate-500">Isi jumlah jam lembur.</span>
                            </span>
                        </label>

                        <div x-show="selectedType === 'jam'" x-collapse class="pl-0 sm:pl-7">
                            <label for="type_overtime_manual_jam" class="mb-1.5 block text-xs font-medium text-slate-600">
                                Jumlah jam
                            </label>
                            <input type="number" inputmode="numeric" min="1" name="type_overtime_manual"
                                id="type_overtime_manual_jam" x-model="manualType" placeholder="Contoh: 2"
                                value="{{ old('type_overtime_manual') }}"
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                :required="selectedType === 'jam'" :disabled="selectedType !== 'jam'">
                            <p class="mt-1 text-xs text-slate-500">Gunakan angka, misalnya 1, 2, atau 3.</p>
                        </div>

                        <label
                            class="flex items-start gap-3 p-3 transition border rounded-lg cursor-pointer min-h-14 border-slate-200 hover:bg-slate-50"
                            :class="selectedType === 'lainnya' ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-200' : ''">
                            <input type="radio" name="type_overtime" value="lainnya" x-model="selectedType"
                                class="w-4 h-4 mt-1 text-sky-600 focus:ring-sky-500"
                                {{ old('type_overtime') == 'lainnya' ? 'checked' : '' }} required>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-slate-800">Lainnya</span>
                                <span class="mt-0.5 block text-xs leading-4 text-slate-500">Isi nominal atau nilai manual.</span>
                            </span>
                        </label>

                        <div x-show="selectedType === 'lainnya'" x-collapse class="pl-0 sm:pl-7">
                            <label for="type_overtime_manual_lainnya"
                                class="mb-1.5 block text-xs font-medium text-slate-600">
                                Nominal / nilai manual
                            </label>
                            <input type="number" inputmode="numeric" min="0" name="type_overtime_manual"
                                id="type_overtime_manual_lainnya" x-model="manualType" placeholder="Contoh: 50000"
                                value="{{ old('type_overtime_manual') }}"
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                :required="selectedType === 'lainnya'" :disabled="selectedType !== 'lainnya'">
                            <p class="mt-1 text-xs text-slate-500">Gunakan angka, misalnya 50000 atau 20000.</p>
                        </div>
                    </div>

                    @error('type_overtime')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('type_overtime_manual')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </section>

                <section class="p-4 bg-white border rounded-lg shadow-sm border-slate-200 sm:p-5">
                    <label for="desc" class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Keperluan Lembur <span class="text-red-500">*</span>
                    </label>
                    <textarea name="desc" id="desc" rows="4" placeholder="Contoh: Menyelesaikan pekerjaan tambahan di area..."
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                        required>{{ old('desc') }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">Tuliskan keperluan lembur secara singkat dan jelas.</p>
                    @error('desc')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </section>

                <div
                    class="sticky sm:bottom-16 z-20 mx-0.5 sm:-mx-3 border-t rounded-md border-slate-200 bg-white/95 p-3 shadow-[0_-8px_18px_rgba(15,23,42,0.08)] backdrop-blur sm:static sm:mx-0 sm:rounded-lg sm:border sm:shadow-sm">
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <button type="submit"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">
                            <i class="ri-save-line"></i>
                            Simpan Pengajuan
                        </button>
                        <a href="{{ route('index.rekap.data.leader') }}"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                            <i class="ri-close-line"></i>
                            Batal
                        </a>
                    </div>
                </div>
            </form>

            <div class="p-4 mt-4 border rounded-lg border-sky-100 bg-sky-50">
                <div class="flex items-start gap-3">
                    <i class="ri-information-line mt-0.5 text-xl text-sky-600"></i>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Informasi Penting</p>
                        <ul class="mt-2 space-y-1 text-xs leading-5 list-disc list-inside text-slate-600">
                            <li>Pastikan data pegawai dan tanggal lembur sudah benar.</li>
                            <li>Finalisasi data dilakukan sebelum akhir bulan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </x-main-div>

    <script>
        function overtimeForm() {
            return {
                selectedType: '{{ old('type_overtime') ?? '' }}',
                manualType: '{{ old('type_overtime_manual') ?? '' }}',

                init() {
                    this.$watch('selectedType', value => {
                        if (!['jam', 'lainnya'].includes(value)) {
                            this.manualType = '';
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>
