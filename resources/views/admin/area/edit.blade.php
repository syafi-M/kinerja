<x-app-layout>
    <x-main-div>
        <div>
			<p class="text-center text-2xl font-bold py-10 uppercase">Edit Area, {{ $area->nama_area }}</p>
        </div>
        
        <form method="POST" action="{{ route('area.update', $area->id) }}" class="mx-[25%] my-10" id="form">
			@csrf
			@method('PATCH')
            <div class="bg-slate-100 px-10 py-5 rounded shadow">
                <div>
                    <x-input-label for="kerjasama_id" :value="__('Nama Client')" />
                    <select name="kerjasama_id" id="kerjasama_id" class="select select-bordered w-full mt-1">
                        <option selected disabled>~ Pilih Client ~</option>
                        @forelse ($kerjasama as $i)
                            <option value="{{ $i->id }}"{{ $area->kerjasama_id == $i->id ? 'selected' : '' }} name='kerjasama_id'>{{ $i->client->name }}</option>
                        @empty
                            <option disabled>~ Kosong ~</option>
                        @endforelse
                    </select>
					<x-input-error :messages="$errors->get('kerjasama_id')" class="mt-2" />
                </div>
                <div>
					<x-input-label for="nama_area" :value="__('Nama Area')" />
					<x-text-input id="nama_area" class="block mt-1 w-full" type="text" name="nama_area" value="{{ $area->nama_area }}" required
						 autocomplete="nama_area" placeholder="Masukkan nama area..."/>
					<x-input-error :messages="$errors->get('nama_area')" class="mt-2" />
				</div>
				<div  id="inputContainer" class="flex flex-col pb-10">
                <label class="label pl-2">Sub Area</label>
				<div class="p-1 mt-4 grid gap-2">
        				    @foreach ($subArea as $i)
                                @php
                                    $isChecked = $sub->contains('subarea_id', $i->id);
                                @endphp
                                <span class="flex gap-2">
                                <div class="bg-orange-200 p-1 rounded w-full">
                                    <input type="checkbox" {{ $isChecked ? '' : 'name=subarea_id[]' }} value="{{ $i->id }}" class="checkbox" {{ $isChecked ? 'checked' : '' }} />
                                    <label>{{ $i->name }}</label>
                                </div>
                                    @if($isChecked)
                                    <div class="bg-red-500 p-1 rounded w-full">
                                        <input type="checkbox" name=delete_sub[] value="{{ $i->id }}" class="checkbox" />
                                        <label class="text-white">Delete dari Divisi</label>
                                    </div>
                                    @endif
                                </span>
                            
                            @endforeach
    				</div>
                </div>
                <div class="flex gap-2 my-5 justify-end">
					<button><a href="{{ route('area.index') }}" class="btn btn-error">Back</a></button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
            </div>
        </form>
    </x-main-div>
</x-app-layout>