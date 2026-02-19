<x-app-layout>
    <x-main-div>
        <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">

            <!-- Header -->
            <div class="card-container mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('index.rekap.data.leader') }}"
                        class="w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center">
                        <i class="ri-arrow-left-line text-xl text-white"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Edit Pengajuan Personil Keluar</h1>
                        <p class="text-slate-200 text-sm">Perbarui data pengajuan</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="card-white p-6 sm:p-8">
                <form action="{{ route('person-is-out.update', $personOut->id) }}" method="POST"
                    enctype="multipart/form-data" x-data="overtimeForm()">
                    @csrf
                    @method('PUT')

                    <!-- USER (LOCKED) -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Pegawai
                        </label>

                        <select class="w-full px-4 py-2 rounded-lg border bg-slate-100" disabled>
                            @foreach ($users as $user)
                                <option {{ $personOut->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>

                        <input type="hidden" name="user_id" value="{{ $personOut->user_id }}">
                    </div>

                    <!-- DATE -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 required">Tanggal Keluar</label>
                        <input type="date" name="out_date"
                            value="{{ old('out_date', \Carbon\Carbon::parse($personOut->out_date)->format('Y-m-d')) }}"
                            class="w-full px-4 py-2 rounded-lg border" required>
                    </div>

                    <!-- REASON -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-3 required">Alasan Keluar</label>

                        @foreach (['resign' => 'Resign', 'evaluasi' => 'Evaluasi', 'lainnya' => 'Lainnya'] as $key => $label)
                            <label class="flex gap-3 p-4 border rounded-lg cursor-pointer mb-2"
                                :class="selectedType === '{{ $key }}' ? 'border-blue-500 bg-blue-50' : ''">
                                <input type="radio" name="reason" value="{{ $key }}" x-model="selectedType"
                                    required>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach

                        <div x-show="selectedType === 'lainnya'" class="mt-2">
                            <input type="text" name="reason_manual" x-model="manualType"
                                class="w-full px-4 py-2 border rounded-lg" placeholder="Masukkan alasan">
                        </div>
                    </div>

                    <!-- MK -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 required">Jumlah MK</label>
                        <input type="text" name="total_mk" value="{{ old('total_mk', $personOut->total_mk) }}"
                            class="w-full px-4 py-2 border rounded-lg" required>
                    </div>

                    <!-- IMAGE -->
                    <div class="mb-6" x-data="imagePreview()">
                        <label class="block text-sm font-semibold mb-2 required">Bukti</label>

                        <input type="file" name="img" accept="image/*" x-ref="fileInput" @change="previewImage"
                            class="hidden">

                        <div @click="$refs.fileInput.click()"
                            class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer">
                            Klik untuk ganti gambar
                        </div>

                        <template x-if="imageUrl">
                            <div class="mt-4">
                                <img :src="imageUrl" class="w-40 h-40 object-cover rounded border">
                                <p class="text-xs mt-1" x-text="fileName"></p>
                            </div>
                        </template>
                    </div>

                    <!-- ACTION -->
                    <div class="flex gap-3 pt-4 border-t w-fit">
                        <button type="submit" class="btn btn-primary py-3 rounded-lg">
                            Update Data
                        </button>
                        <a href="{{ route('index.rekap.data.leader') }}" class="btn py-3 border rounded-lg">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </x-main-div>

    <!-- ALPINE -->
    <script>
        function overtimeForm() {
            return {
                selectedType: '{{ old('reason', $personOut->reason) }}',
                manualType: '{{ old('reason_manual', $personOut->reason_manual) }}'
            }
        }

        function imagePreview() {
            return {
                imageUrl: '{{ $personOut->img ? asset('storage/images/' . $personOut->img) : '' }}',
                fileName: '{{ $personOut->img ?? '' }}',

                previewImage(e) {
                    const file = e.target.files[0]
                    if (!file) return
                    this.fileName = file.name
                    this.imageUrl = URL.createObjectURL(file)
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
