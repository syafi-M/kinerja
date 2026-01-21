<x-app-layout>
    <x-main-div>
        <form action="{{ route('shift.update', [$shift->id]) }}" method="POST" class="mx-20" id="form">
            @method('put')
            @csrf
            <div>
                <p class="my-10 text-2xl font-bold text-center">Edit Shift</p>
                <div class="px-10 py-5 mx-10 my-10 rounded shadow bg-slate-100">
                    <div class="grid grid-cols-2 gap-5">
                        <!-- Jabatan -->
                        <div class="flex flex-col">
                            <label for="jabatan_id" class="label">Jabatan</label>
                            <select name="jabatan_id" id="jabatan_id" class="select-bordered select">
                                <option disabled>~ Pilih Jabatan ~</option>
                                @forelse ($jabatan as $jab)
                                    <option value="{{ $jab->id }}"
                                        {{ $shift->jabatan_id == $jab->id ? 'selected' : '' }}>
                                        {{ $jab->name_jabatan }}
                                    </option>
                                @empty
                                    <option disabled>~ Data Kosong ~</option>
                                @endforelse
                            </select>
                        </div>

                        <!-- Client -->
                        <div class="flex flex-col">
                            <label for="client_id" class="label">Client</label>
                            <select name="client_id" id="client_id" class="select-bordered select">
                                <option disabled>~ Pilih Client ~</option>
                                @forelse ($client as $cli)
                                    <option value="{{ $cli->id }}"
                                        {{ $shift->client_id == $cli->id ? 'selected' : '' }}>
                                        {{ $cli->name }}
                                    </option>
                                @empty
                                    <option disabled>~ Data Kosong ~</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <!-- Nama shift -->
                    <div class="flex flex-col">
                        <label for="shift_name" class="label">Nama Shift</label>
                        <input type="text" name="shift_name" value="{{ $shift->shift_name }}" id="shift_name"
                            class="input input-bordered" placeholder="masukkan nama shift...">
                    </div>

                    <!-- Jam start -->
                    <div class="flex flex-col">
                        <label for="jam_start" class="label">Jam Mulai</label>
                        <input type="time" name="jam_start" value="{{ $shift->jam_start }}" id="jam_start"
                            class="input input-bordered">
                    </div>

                    <!-- Jam end -->
                    <div class="flex flex-col">
                        <label for="jam_end" class="label">Jam Selesai</label>
                        <input type="time" name="jam_end" value="{{ $shift->jam_end }}" id="jam_end"
                            class="input input-bordered">
                    </div>

                    <!-- Overnight -->
                    <div class="flex flex-col">
                        <label for="is_overnight" class="label">Pergantian Hari</label>
                        <select name="is_overnight" id="is_overnight" class="select-bordered select">
                            <option disabled>~ Pilih Pergantian Hari ~</option>
                            <option value="0" {{ $shift->is_overnight == 0 ? 'selected' : '' }}>Tidak</option>
                            <option value="1" {{ $shift->is_overnight == 1 ? 'selected' : '' }}>Ya</option>
                        </select>
                    </div>

                    <!-- Hari -->
                    <div class="">
                        <label class="label">
                            <span class="font-semibold label-text">Hari</span>
                        </label>
                        <div class="grid grid-cols-4 gap-2 sm:grid-cols-7">
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
                                        id="{{ strtolower($fullDay) }}" class="absolute opacity-0 day-input peer"
                                        {{ in_array($fullDay, $selectedDays) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium peer-checked:text-emerald-600">
                                        {{ $shortDay }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <button type="button" onclick="toggleAllDays()"
                            class="block px-4 py-2 mx-auto mt-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400">
                            Pilih Semua
                        </button>
                    </div>

                    <div class="flex justify-end gap-2 my-5">
                        <a href="{{ route('shift.index') }}" class="btn btn-error">Kembali</a>
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
