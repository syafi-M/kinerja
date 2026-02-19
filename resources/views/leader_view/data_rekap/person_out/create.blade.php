<x-app-layout>
    <x-main-div>
        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">

            <!-- Header -->
            <div class="card-container mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('index.rekap.data.leader') }}"
                        class="w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all">
                        <i class="ri-arrow-left-line text-xl text-white"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Pengajuan Personil Keluar</h1>
                        <p class="text-slate-200 text-sm">Formulir Pengajuan Personil Keluar</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card-white p-6 sm:p-8">
                <form action="{{ route('person-is-out.store') }}" method="POST" x-data="overtimeForm()"
                    enctype="multipart/form-data">
                    @csrf

                    <!-- Name Selection -->
                    <div class="mb-6">
                        <label for="user_id" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Pegawai <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id" id="user_id"
                            class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all"
                            required>
                            <option value="">-- Pilih Nama Pegawai --</option>
                            @forelse($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->nama_lengkap ?? 'N/A' }}
                                </option>
                            @empty
                                <option value="" disabled>Tidak ada data pegawai</option>
                            @endforelse
                        </select>
                        @error('user_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Overtime -->
                    <div class="mb-6">
                        <label for="out_date" class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Keluar <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="out_date" id="out_date" value="{{ old('out_date') }}"
                            class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all"
                            required>
                        @error('out_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type Overtime -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-3">
                            Alasan Keluar <span class="text-red-500">*</span>
                        </label>

                        <div class="space-y-3">
                            <!-- Radio: Shift -->
                            <label
                                class="flex items-start gap-3 p-4 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all"
                                :class="selectedType === 'resign' ? 'border-blue-500 bg-blue-50' : ''">
                                <input type="radio" name="reason" value="resign" x-model="selectedType"
                                    class="mt-1 w-4 h-4 text-blue-600 focus:ring-blue-500"
                                    {{ old('reason') == 'resign' ? 'checked' : '' }} required>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800">Resign</p>
                                    <p class="text-xs text-slate-500 mt-1">Keluar karena resign</p>
                                </div>
                            </label>

                            <!-- Radio: Jam -->
                            <label
                                class="flex items-start gap-3 p-4 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all"
                                :class="selectedType === 'evaluasi' ? 'border-blue-500 bg-blue-50' : ''">
                                <input type="radio" name="reason" value="evaluasi" x-model="selectedType"
                                    class="mt-1 w-4 h-4 text-blue-600 focus:ring-blue-500"
                                    {{ old('reason') == 'evaluasi' ? 'checked' : '' }} required>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800">Evaluasi</p>
                                    <p class="text-xs text-slate-500 mt-1">Keluar karena di Evaluasi</p>
                                </div>
                            </label>
                            <!-- Radio: Lainnya -->
                            <label
                                class="flex items-start gap-3 p-4 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all"
                                :class="selectedType === 'lainnya' ? 'border-blue-500 bg-blue-50' : ''">
                                <input type="radio" name="reason" value="lainnya" x-model="selectedType"
                                    class="mt-1 w-4 h-4 text-blue-600 focus:ring-blue-500"
                                    {{ old('reason') == 'lainnya' ? 'checked' : '' }} required>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800">Lainnya</p>
                                    <p class="text-xs text-slate-500 mt-1">Jenis keluar lainnya (sebutkan manual)</p>
                                </div>
                            </label>

                            <!-- Manual Input (shown when Lainnya selected) -->
                            <div x-show="selectedType === 'lainnya'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2" class="mt-2">
                                <input type="text" name="reason_manual" x-model="manualType"
                                    placeholder="Masukan Alasannya..." value="{{ old('reason_manual') }}"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all"
                                    :required="selectedType === 'lainnya'">
                                <p class="text-xs text-slate-500 mt-1">Contoh: meninggal, cacat fisik permanen,
                                    dll.</p>
                            </div>
                        </div>

                        @error('reason')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                        @error('reason_manual')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- MK -->
                    <div class="mb-6">
                        <label for="total_mk" class="block text-sm font-semibold text-slate-700 mb-2">
                            Jumlah MK <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="total_mk" id="total_mk" value="{{ old('total_mk') }}"
                            class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all"
                            required placeholder="Masukan jumlah mk karyawan...">
                        @error('total_mk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6" x-data="imagePreview()">
                        <label for="img" class="block text-sm font-semibold text-slate-700 mb-2">
                            Unggah Bukti <span class="text-red-500">*</span>
                        </label>

                        <!-- Drag & Drop Zone -->
                        <div @click="$refs.fileInput.click()" @drop.prevent="handleDrop($event)"
                            @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                            :class="{ 'border-blue-500 bg-blue-50': isDragging, 'border-slate-300': !isDragging }"
                            class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-all hover:border-blue-400 hover:bg-slate-50">

                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                    <i class="ri-upload-cloud-2-line text-3xl text-slate-400"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-slate-700">
                                        <span class="text-blue-600 hover:underline">Klik untuk unggah</span> atau seret
                                        & lepas
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">PNG, JPG, JPEG hingga 2MB</p>
                                </div>
                            </div>

                            <!-- Hidden file input -->
                            <input type="file" name="img" id="img" accept="image/*" x-ref="fileInput"
                                @change="previewImage" class="hidden">
                        </div>

                        <!-- Preview -->
                        <template x-if="imageUrl">
                            <div class="mt-4 relative inline-block">
                                <p class="text-xs text-slate-500 mb-2">Preview</p>
                                <div class="relative group">
                                    <img :src="imageUrl" alt="Preview image"
                                        class="w-40 h-40 object-cover rounded-lg border border-slate-200">

                                    <!-- Remove button -->
                                    <button type="button" @click.stop="removeImage"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="ri-close-line text-sm"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-slate-600 mt-1" x-text="fileName"></p>
                            </div>
                        </template>

                        @error('img')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-200">
                        <button type="submit"
                            class="flex-1 btn-primary px-6 py-3 rounded-lg font-semibold flex items-center justify-center gap-2">
                            <i class="ri-save-line"></i>
                            <span>Simpan Pengajuan</span>
                        </button>
                        <a href="{{ route('index.rekap.data.leader') }}"
                            class="flex-1 px-6 py-3 rounded-lg font-semibold border-2 border-slate-300 text-slate-700 hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                            <i class="ri-close-line"></i>
                            <span>Batal</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="card-white p-4 mt-4 border-l-4 border-blue-500">
                <div class="flex items-start gap-3">
                    <i class="ri-information-line text-blue-500 text-xl mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">Informasi Penting</p>
                        <ul class="text-xs text-slate-600 mt-2 space-y-1 list-disc list-inside">
                            <li>Pastikan semua data yang diisi sudah benar</li>
                            <li>Pastikan sebelum akhir bulan untuk finalisasi data</li>
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
                    // Watch for changes
                    this.$watch('selectedType', value => {
                        if (value !== 'lainnya') {
                            this.manualType = '';
                        }
                    });
                }
            }
        }

        function imagePreview() {
            return {
                imageUrl: null,
                fileName: '',
                isDragging: false,

                previewImage(event) {
                    const file = event.target.files[0];
                    this.processFile(file);
                },

                handleDrop(event) {
                    this.isDragging = false;
                    const file = event.dataTransfer.files[0];

                    if (file && file.type.startsWith('image/')) {
                        // Update the file input
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.$refs.fileInput.files = dataTransfer.files;

                        this.processFile(file);
                    }
                },

                processFile(file) {
                    if (file && file.type.startsWith('image/')) {
                        // Check file size (5MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('Ukuran file terlalu besar. Maksimal 2MB');
                            return;
                        }

                        this.fileName = file.name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imageUrl = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                removeImage() {
                    this.imageUrl = null;
                    this.fileName = '';
                    this.$refs.fileInput.value = '';
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f1f5f9;
        }

        .card-container {
            background: #64748b;
            border-radius: 16px;
            padding: 24px;
        }

        .card-white {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #fbbf24;
            color: #1f2937;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #f59e0b;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(251, 191, 36, 0.3);
        }

        /* Radio button custom styling */
        input[type="radio"]:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        /* Date input styling */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(0.5);
        }

        input[type="date"]:hover::-webkit-calendar-picker-indicator {
            filter: invert(0.3);
        }

        /* Select dropdown arrow */
        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            appearance: none;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .card-container {
                padding: 16px;
            }
        }
    </style>
</x-app-layout>
