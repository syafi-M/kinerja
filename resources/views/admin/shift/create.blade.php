<x-app-layout>
    <x-main-div>
        <form method="POST" action="{{ route('shift.store') }}" class="mx-20" id="form">
            @csrf
            <div>
                <p class="text-center text-2xl font-bold my-10">Tambah Shift</p>
                <div class="bg-slate-100 mx-10 my-10 px-10 py-5 rounded shadow">
                    <div class="grid grid-cols-2 gap-5">
                        <!-- Jabatan -->
                        <div class="flex flex-col">
                            <label for="jabatan_id" class="label">Jabatan</label>
                            <select name="jabatan_id" id="jabatan_id" class="select-bordered select">
                                <option selected disabled>~ Pilih Jabatan ~</option>
                                @forelse ($jabatan as $jab)
                                    <option value="{{ $jab->id }}">{{ $jab->name_jabatan }}</option>
                                @empty
                                    <option disabled>~ Data Kosong ~</option>
                                @endforelse
                            </select>
                        </div>
                        <!-- client -->
                        <div class="flex flex-col">
                            <label for="client_id" class="label">Client</label>
                            <select name="client_id" id="client_id" class="select-bordered select">
                                <option selected disabled>~ Pilih Client ~</option>
                                @forelse ($client as $cli)
                                    <option value="{{ $cli->id }}">{{ $cli->name }}</option>
                                @empty
                                    <option disabled>~ Data Kosong ~</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <!-- client -->
                    <div class="flex flex-col">
                        <label for="shift_name" class="label">Nama Shift</label>
                        <input type="text" name="shift_name" id="shift_name" class="input input-bordered"
                            placeholder="masukkan nama shift...">
                    </div>
                    <!-- start -->
                    <div class="flex flex-col">
                        <label for="jam_start" class="label">Jam Mulai</label>
                        <input type="time" name="jam_start" id="jam_start" class="input input-bordered">
                    </div>
                    <!-- end -->
                    <div class="flex flex-col">
                        <label for="jam_end" class="label">Jam Selesai</label>
                        <input type="time" name="jam_end" id="jam_end" class="input input-bordered">
                    </div>

                    {{-- day --}}
                    <div class="">
                        <label class="label">
                            <span class="label-text font-semibold">Hari</span>
                        </label>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                            @php
                                $days = [
                                    'Senin' => 'Sen',
                                    'Selasa' => 'Sel',
                                    'Rabu' => 'Rab',
                                    'Kamis' => 'Kam',
                                    'Jumat' => 'Jum',
                                    'Sabtu' => 'Sab',
                                    'Minggu' => 'Min',
                                ];
                            @endphp
                            @foreach ($days as $fullDay => $shortDay)
                                <label for="{{ strtolower($fullDay) }}"
                                    class="day-checkbox has-[:checked]:border-[#10b981] has-[:checked]:hover:border-[#059669] relative flex items-center justify-center p-3 rounded-lg border-2 border-base-300 cursor-pointer transition-all hover:border-primary">
                                    <input type="checkbox" name="hari[]" value="{{ $fullDay }}"
                                        class="absolute opacity-0 day-input peer" id="{{ strtolower($fullDay) }}">
                                    <span class="text-sm font-medium peer-checked:text-emerald-600">
                                        {{ $shortDay }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <button type="button" onclick="toggleAllDays()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors mx-auto block mt-2">
                            Pilih Semua
                        </button>
                    </div>


                    <div class="flex gap-2 my-5 justify-end">
                        <button><a href="{{ route('shift.index') }}" class="btn btn-error">Kembali</a></button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </x-main-div>
</x-app-layout>
<script>
    function toggleAllDays() {
        const checkboxes = document.querySelectorAll('.day-input');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });

        // Update button text
        const btn = event.target;
        btn.textContent = allChecked ? 'Pilih Semua' : 'Batalkan Semua';
    }
</script>
