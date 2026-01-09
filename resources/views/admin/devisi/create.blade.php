<x-app-layout>
	<div class="p-4 rounded-md shadow-md bg-slate-500 mx-36">
		<p class="text-2xl font-bold text-center uppercase">Tambah Divisi</p>
		<form method="POST" action="{{ route('divisi.store') }}" class="mx-[25%] my-10" id="form">
			@csrf
			<div class="px-10 py-5 rounded shadow bg-slate-100">
				<!-- name -->
				<div>
					<x-input-label for="name" :value="__('Name')" />
					<x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required
						autofocus autocomplete="name" />
					<x-input-error :messages="$errors->get('name')" class="mt-2" />
				</div>
				<div>
				    <x-input-label for="jabatan_id" :value="__('Jabatan')" />
				    <select name="jabatan_id" id="" class="w-full mt-1 select select-bordered">
							<option selected disabled>~ Pilih Jabatan ~</option>
							@foreach ($jabatan as $i)
								<option name="jabatan_id" value="{{ $i->id }}" class="py-2">{{ $i->code_jabatan }} | {{ $i->name_jabatan }}</option>
							@endforeach
						</select>
				</div>

				<div class="flex justify-end gap-2 my-5">
					<button><a href="{{ route('divisi.index') }}" class="btn btn-error">Back</a></button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</div>
		</form>
	</div>
</x-app-layout>
