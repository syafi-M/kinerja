<x-app-layout>
	<x-main-div>
		<div class="px-10 py-10">
			<p class="mb-10 text-xl font-bold text-center uppercase">Tambah Kerjasama</p>
			<form action="{{ route('kerjasama.store') }}" method="post">
				<div class="px-10 py-10 mx-5 rounded-md bg-slate-100">
					@method('POST')
					@csrf
					<div class="flex items-center mt-5 mb-5">
						<label for="client_id" class="mr-[3.61rem]">Client</label>
						<select
							class="block w-3/6 px-3 py-2 placeholder-gray-600 bg-white border-2 border-gray-300 rounded-lg shadow-md text-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none"
							name="client_id">
							<option selected disabled class="disabled:bg-slate-700 disabled:text-slate-100">--Select Client--</option>
							@foreach ($client as $i)
								<option value="{{ $i->id }}">{{ $i->name }}</option>
							@endforeach
							<x-input-error class="mt-2" :messages="$errors->get('client_id')" />
						</select>
					</div>
					<div class="flex items-center mb-5">
						<label for="value" class="mr-5">Input Value</label>
						<input type="text" name="value" id="value"
							class="block w-3/6 px-3 py-2 placeholder-gray-600 bg-white border-2 border-gray-300 rounded-lg shadow-md text-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
						<x-input-error class="mt-2" :messages="$errors->get('value')" />
					</div>
					<div class="flex items-center mb-5">
						<label for="value" class="mr-[4.1rem]">Date</label>
						<input type="date" name="experied" id="experied"
							class="block w-3/6 px-3 py-2 placeholder-gray-600 bg-white border-2 border-gray-300 rounded-lg shadow-md text-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
						<x-input-error class="mt-2" :messages="$errors->get('experied')" />
					</div>
					<div class="flex items-center gap-5 mb-5 w-fit">
						<label for="approve[1,2,3]" class="mr-4">Approve By</label>
						<input type="text" name="approve1" id="approve1"
							class="block w-2/6 px-3 py-2 placeholder-gray-600 bg-white border-2 border-gray-300 rounded-lg shadow-md text-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
						<input type="text" name="approve2" id="approve2"
							class="block w-2/6 px-3 py-2 placeholder-gray-600 bg-white border-2 border-gray-300 rounded-lg shadow-md text-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
						<input type="text" name="approve3" id="approve3"
							class="block w-2/6 px-3 py-2 placeholder-gray-600 bg-white border-2 border-gray-300 rounded-lg shadow-md text-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
					</div>
					<div class="flex justify-end gap-3 mb-5">
						<a href="{{ route('kerjasama.index') }}" class="btn btn-error">Back</a>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</div>
			</form>
		</div>
	</x-main-div>
</x-app-layout>
