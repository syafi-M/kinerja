<x-app-layout>
    <x-main-div>
    <p class="my-10 text-2xl font-bold text-center uppercase">Perlengkapan</p>

    <div class="flex justify-center mb-10 ">
        <form action="{{ url('divisi/'.$data->id.'/add-equipment')}}" method="POST">
        @csrf
		{{-- @method("PATCH") --}}
        <div class="w-[25rem] px-2 py-2 p-2 bg-white max-w-md shadow-md rounded-md">
            <div  id="inputContainer" class="flex flex-col pb-10">
                <label class="pl-2 label">Perlengkapan</label>
				<div class="grid gap-2 p-1 mt-4">
				@foreach ($alat as $i)
				<div class="py-1 bg-orange-200 rounded">
					<input type="checkbox" name="perlengkapan_id[]" value="{{ $i->id }}" class="checkbox"/>
					<label>{{ $i->name }}</label>
				</div>
				@endforeach
				</div>
            </div>
            <div class="flex justify-end gap-2 mx-2">
                <div>
                    <button type="submit" class="btn btn-info w-fit">Save</button>
                    <a class="overflow-hidden btn btn-error" href="{{ route('divisi.index') }}">Back</a>
                </div>
            </div>
        </div>
        </form>
    </div>
</x-main-div>
</x-app-layout>
