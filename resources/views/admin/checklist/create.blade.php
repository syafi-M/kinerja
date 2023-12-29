<x-app-layout>
    <x-main-div>
        <div>
			<p class="text-center text-2xl font-bold py-10 uppercase">Create Checklist</p>
        </div>
        
        <form method="POST" action="{{ route('admin-checklist.store') }}" class="mx-[25%] my-10" id="form">
			@csrf
            <div class="bg-slate-100 px-10 py-5 rounded shadow">
                <div class="mt-3">
                    <x-input-label for="area" :value="__('Nama Area')" />
                   <select name="area" id="area" class="select select-bordered w-full mt-1">
                        <option selected disabled>~ Pilih Area ~</option>
                        @php
                            $renderedAreas = []; // Track rendered areas
                        @endphp
                        @forelse($areasub as $ar)
                            @if (!in_array($ar->area->nama_area, $renderedAreas))
                                <option value="{{ $ar->area->nama_area }}">{{ $ar->area?->nama_area }}</option>
                                @php
                                    $renderedAreas[] = $ar->area->nama_area; // Add rendered area to the list
                                @endphp
                            @endif
                        @empty
                            <option disabled>~ Kosong ~</option>
                        @endforelse
                    </select>
					<x-input-error :messages="$errors->get('area')" class="mt-2" />
                </div>
                <div class="mt-3">
					<x-input-label for="subarea" :value="__('Nama Sub Area')" />
					<select name="sub_area" id="subarea" class="select select-bordered w-full mt-1">
                        <option selected disabled>~ Pilih Area ~</option>
                        @forelse($areasub as $ar)
                            <option value="{{ $ar->subarea?->name }}" data-name="{{ $ar->area->nama_area }}">{{ $ar->subarea?->name }}</option>
                        @empty
                            <option disabled>~ Kosong ~</option>
                        @endforelse
                    </select>
					<x-input-error :messages="$errors->get('subarea')" class="mt-2" />
				</div>
				<div class="mt-3">
				    <x-input-label for="tingkat_kebersihan" :value="__('Tingkat Kebersihan')" />
                    <span style="height: 10rem;" class="flex flex-col input input-bordered gap-2">
                        <p>~ Pilih Tingkat Kebersihan ~</p>
                        <span class="flex gap-2 p-1">
                            <input type="radio" name="tingkat_bersih" value="bersih" class="radio p-1"/>
                            <label>Bersih</label>
                        </span>
                        <span class="flex gap-2 p-1">
                            <input type="radio" name="tingkat_bersih" value="cukup" class="radio p-1"/>
                            <label>Cukup</label>
                        </span>
                        <span class="flex gap-2 p-1">
                            <input type="radio" name="tingkat_bersih" value="kurang" class="radio p-1"/>
                            <label>Kurang</label>
                        </span>
                    </span>
                    
					<x-input-error :messages="$errors->get('tingkat_kebersihan')" class="mt-2" />
				</div>
                <div class="flex gap-2 my-5 justify-end">
					<button><a href="{{ route('admin-checklist.index') }}" class="btn btn-error">Kembali</a></button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
            </div>
        </form>
    </x-main-div>
        <script>
            $(document).ready(function() {
    function handlePekerjaanVisibility() {
        $('#area').change(function() {
            var selectedTypeCheck = $(this).val();

            if (selectedTypeCheck === '0') { // Compare against '0' string
                $('#subarea option').hide();
            } else {
                $('#subarea option').each(function() {
                    var dataName = $(this).data('name');

                    if (dataName && dataName.toString() === selectedTypeCheck) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
    }

    window.onload = function() {
        $('#subarea option').hide(); // Hide all initially
        handlePekerjaanVisibility();
    };
});

    
        </script>
</x-app-layout>