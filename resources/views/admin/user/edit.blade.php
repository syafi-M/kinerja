<x-app-layout>
    <div class="bg-slate-500 mx-10 rounded">
		<p class="text-center text-2xl font-bold my-10">Edit User</p>
        <div class="mx-10 my-10 bg-slate-100 px-10 py-5 rounded shadow">
            <span class="py-3 flex justify-end overflow-hidden">
                <button id="deleteUser" class="btn btn-error overflow-hidden">Delete</button>
            </span>
        <form action="{{ url('users/'.$dataUser->id) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        	<!-- Name -->
			<div>
				<x-input-label for="name" :value="__('Nama')" />
				<x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ $user->name }}" required
					autofocus autocomplete="name" />
				<x-input-error :messages="$errors->get('name')" class="mt-2" />
			</div>
        	<!-- Nama lengkap -->
			<div>
				<x-input-label for="nama_lengkap" :value="__('Nama Lengkap')" />
				<x-text-input id="nama_lengkap" class="block mt-1 w-full" type="text" name="nama_lengkap" value="{{ $user->nama_lengkap }}" required
					autofocus autocomplete="nama_lengkap" />
				<x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
			</div>

			<!-- Email Address -->
			<div class="mt-4">
				<x-input-label for="email" :value="__('Email')" />
				<x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ $user->email }}" required
					autocomplete="username" />
				<x-input-error :messages="$errors->get('email')" class="mt-2" />
			</div>
			<!-- NIK -->
			<div class="mt-4">
				<x-input-label for="NIK" :value="__('NIK')" />
				<x-text-input id="NIK" class="block mt-1 w-full" type="text" name="nik"
					value="{{ $user->nik ? \Illuminate\Support\Facades\Crypt::decryptString($user->nik) : ''}}" placeholder="Nik..." maxlength="16"  pattern="[0-9]*" autocomplete="nik" />
				<x-input-error :messages="$errors->get('nik')" class="mt-2" />
			</div>
			<!-- No HP -->
			<div class="mt-4">
				<x-input-label for="no_hp" :value="__('No. HP Aktif')" />
				<x-text-input id="no_hp" class="block mt-1 w-full" type="text" name="no_hp"
					value="{{ $user->no_hp }}" placeholder="No hp aktif..." maxlength="14" autocomplete="no_hp" />
				<x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
			</div>
			<!-- client -->
			<div class="mt-4">
				<x-input-label for="client" :value="__('Client')" />
				<select name="kerjasama_id" id="kerjasama_id" class="select select-bordered w-full mt-1">
					<option disabled>~ Pilih Client ~</option>
					@foreach ($kerjasama as $i)
						<option name="kerjasama_id" {{ $dataUser->kerjasama_id == $i->id ? 'selected' : '' }}  value="{{ $i->id }}" class="py-2">{{ $i->client->name }}</option>
					@endforeach
				</select>
			</div>
			<!-- client -->
			<div class="mt-4">
				<x-input-label for="divisi" :value="__('Divisi')" />
				<select name="devisi_id" id="devisi_id" class="select select-bordered w-full mt-1">
					<option selected disabled>~ Pilih Devisi ~</option>
					@foreach ($dev as $i)
						<option name="devisi_id" {{ $dataUser->devisi_id == $i->id ? 'selected' : '' }} value="{{ $i->id }}" class="py-2">{{ $i->name }}</option>
					@endforeach
				</select>
			</div>
			{{-- foto Profile --}}
			<div class="md:mt-4">
				<div class="preview hidden">
					<img class="img1" src="" alt="" srcset="" height="120px" width="120px">
				</div>
				<x-input-label>Foto Profil</x-input-label>
				<input type="text" name="oldimage" value="{{ $user->image }}" class="hidden"/>
				<input type="file" class="file-input file-input-bordered w-full flex flex-row" id="img" name="image"/>
				<x-input-error class="mt-2" :messages="$errors->get('img')" />
			</div>
			<div class="flex justify-end mt-10 gap-2">
				<button type="submit" class="btn btn-primary">Save</button>
				<a href="{{ route('users.index')}}" class="btn btn-error transition-all ease-linear .2s">
					Back
				</a>
			</div>
        </form>
    </div>
</div>
<div
	class="fixed inset-0 modalDeleteUser hidden bg-slate-500/10 backdrop-blur-sm transition-all duration-300 ease-in-out">
	<div class="bg-slate-200 w-fit p-5 mx-2 rounded-md shadow">
		<div class="flex justify-end mb-3">
			<button id="close" class="btn btn-error scale-90">&times;</button>
		</div>
		<form action="{{ route('users.destroy', $dataUser->id) }}" method="POST"
			class="flex justify-center items-center  ">
			@csrf
			@method('DELETE')
			<div class="flex justify-center flex-col gap-2">
				<div class="flex flex-col gap-2">
					<p class="text-center text-lg font-semibold">Apakah Anda Yakin Ingin Menghapus User {{ $dataUser->nama_lengkap }}?</p>
				</div>
				<div class="flex justify-center items-center overflow-hidden">
					<button type="submit"
						class="btn btn-error overflow-hidden"><span class="font-bold overflow-hidden">Hapus User</span>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
    $(document).ready(function() {
        // console.log({!! json_encode(Auth::user()->jabatan) !!}, {!! json_encode($dev) !!});
        $('#deleteUser').click(function(){
		    $('.modalDeleteUser').removeClass('hidden')
			.addClass('flex justify-center items-center opacity-100');
		});
		
		$('#close').click(function() {
		    $('.modalDeleteUser').addClass('hidden').removeClass('flex justify-center items-center opacity-100');
		});
    })
</script>
</x-app-layout>