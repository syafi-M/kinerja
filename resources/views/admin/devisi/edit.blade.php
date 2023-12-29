<x-app-layout>
    <form action="{{ url('devisi/'. $data->id) }}" method="POST">
    @method('PUT')
    @csrf
    <x-main-div>
    <p class="text-2xl text-center font-bold uppercase my-10">Edit Divisi</p>
    <div class="flex justify-center items-center pb-10">
        <div class="w-[25rem] px-2 py-2 p-2 bg-white max-w-md shadow-md rounded-md">
            <div id="inputContainer" class="pb-2 flex flex-col">
                <label>Nama Divisi</label>
                <input type="text" value="{{ $data->name }}" name="name" class="input input-bordered">
            </div>
            <div class="pb-10">
			    <x-input-label for="jabatan_id" :value="__('Jabatan')" />
			    <select name="jabatan_id" id="" class="select select-bordered w-full mt-1">
					<option selected disabled>~ Pilih Jabatan ~</option>
					@foreach ($jabatan as $i)
						<option name="jabatan_id" {{$data->jabatan_id == $i->id ? 'selected' : '' }} value="{{ $i->id }}" class="py-2">{{ $i->code_jabatan }} | {{ $i->name_jabatan }}</option>
					@endforeach
				</select>
			</div>
			<div  id="inputContainer" class="flex flex-col pb-10">
                <label class="label pl-2">Perlengkapan</label>
				<div class="p-1 mt-4 grid gap-2">
    				    @foreach ($alat as $i)
                            @php
                            $isChecked = $lengkapan->contains('perlengkapan_id', $i->id);
                            @endphp
                            <span class="flex gap-2">
                            <div class="bg-orange-200 p-1 rounded w-full">
                                <input type="checkbox" {{ $isChecked ? '' : 'name=perlengkapan_id[]' }} value="{{ $i->id }}" class="checkbox" {{ $isChecked ? 'checked' : '' }} />
                                <label>{{ $i->name }}</label>
                            </div>
                                @if($isChecked)
                                <div class="bg-red-500 p-1 rounded w-full">
                                    <input type="checkbox" name=delete_alat[] value="{{ $i->id }}" class="checkbox" />
                                    <label class="text-white">Delete dari Divisi</label>
                                </div>
                                @endif
                            </span>
                        
                        @endforeach
				</div>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ url('devisi') }}" class="btn btn-error w-fit">cancel</a>
                <button type="submit" class="btn btn-primary w-fit">Save</button>
            </div>
        </div>
    </div>
</x-main-div>
    </form>
</x-app-layout>