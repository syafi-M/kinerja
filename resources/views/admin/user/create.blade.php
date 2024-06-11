<x-app-layout>
	<x-main-div>
		<div class="py-10 px-10">
			<p class="text-center font-bold text-2xl uppercase">Tambah User</p>
			<form method="POST" action="{{ route('users.store') }}" class=" my-10" id="form" enctype="multipart/form-data">
				@csrf
				<div class="bg-slate-100 px-10 py-5 rounded shadow">
					
					<!-- Name -->
					<div>
				        
						<x-input-label for="name" :value="__('Nama')" />
						<x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
							autofocus autocomplete="name" placeholder="Masukkan username.. ( Username terakhir, {{ $lastUser->name }} )"/>
						<x-input-error :messages="$errors->get('name')" class="mt-2" />
					</div>

					<!-- Name Lengkap -->
					<div>
						<x-input-label for="nama_lengkap" :value="__('Nama Lengkap')" />
						<x-text-input id="nama_lengkap" class="block mt-1 w-full" type="text" name="nama_lengkap" :value="old('nama_lengkap')" required
							autofocus autocomplete="nama_lengkap" placeholder="Masukkan nama lengkap user..."/>
						<x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
					</div>

					<!-- Email Address -->
					<div class="mt-4">
						<x-input-label for="email" :value="__('Email')" />
						<x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
							autocomplete="username" placeholder="Masukkan email user..."/>
						<x-input-error :messages="$errors->get('email')" class="mt-2" />
					</div>
					
					<!-- NIK -->
        			<div class="mt-4">
        				<x-input-label for="NIK" :value="__('NIK')" />
        				<x-text-input id="NIK" class="block mt-1 w-full" type="text" name="nik"
        					value="" placeholder="Nik..." maxlength="16"  pattern="[0-9]*" autocomplete="nik" />
        				<x-input-error :messages="$errors->get('nik')" class="mt-2" />
        			</div>
        			<!-- No HP -->
        			<div class="mt-4">
        				<x-input-label for="no_hp" :value="__('No. HP Aktif')" />
        				<x-text-input id="no_hp" class="block mt-1 w-full" type="text" name="no_hp"
        					value="" placeholder="No hp aktif..." maxlength="14" autocomplete="no_hp" />
        				<x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        			</div>

					<!-- Password -->
					<div class="mt-4">
						<x-input-label for="password" :value="__('Password')" />

						<x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
							autocomplete="new-password" placeholder="Masukkan password user..."/>

						<x-input-error :messages="$errors->get('password')" class="mt-2" />
					</div>

					<!-- Confirm Password -->
					<div class="mt-4">
						<x-input-label for="password_confirmation" :value="__('Confirm Password')" />

						<x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation"
							required autocomplete="new-password" placeholder="Konfirmasi password..."/>

						<x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
					</div>
					<!-- client -->
					<div class="mt-4">
						<x-input-label for="client" :value="__('Client')" />
						<select name="kerjasama_id" id="" class="select select-bordered w-full mt-1">
							<option selected disabled>~ Pilih Client ~</option>
							@foreach ($data as $i)
								<option name="kerjasama_id" value="{{ $i->id }}" class="py-2">{{ $i->client->name }}</option>
							@endforeach
						</select>
					</div>
					<!-- divisi -->
					<div class="mt-4">
						<x-input-label for="divisi" :value="__('Divisi')" />
						<select name="devisi_id" id="" class="select select-bordered w-full mt-1">
							<option selected disabled>~ Pilih Devisi ~</option>
							@foreach ($dev as $i)
								<option name="devisi_id" value="{{ $i->id }}" class="py-2">{{ $i->name }}</option>
							@endforeach
						</select>
					</div>
					<!-- jabatan -->
					<div class="mt-4">
						<x-input-label for="jabatan_id" :value="__('Jabatan')" />
						<select name="jabatan_id" id="" class="select select-bordered w-full mt-1">
							<option selected disabled>~ Pilih Jabatan ~</option>
							@foreach ($jabatan as $i)
								<option name="jabatan_id" value="{{ $i->id }}" class="py-2">{{ $i->name_jabatan }}</option>
							@endforeach
						</select>
					</div>
					{{-- foto Profile --}}
					
					<div class="md:mt-4 p-1">
                        <x-input-label for="foto Profil" :value="__('foto Profil')" />
                        <div class="preview hidden w-full">
                            <span class="flex justify-center items-center">
                                <label for="img" class="p-1">
                                    <img class="img1 ring-2 ring-slate-500/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s"
                                        src="" alt="" srcset="" height="120px" width="120px">
                                    
                                </label>
                            </span>
                        </div>
                        <label for="img"
                            class="w-full iImage1 flex flex-col items-center justify-center rounded-md bg-slate-300/70  ring-2 ring-slate-400/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s">
                            <span class="p-3 flex justify-center flex-col items-center">
                                <i class="ri-image-add-line text-xl text-slate-700/90"></i>
                                <span class="text-xs font-semibold text-slate-700/70">+ Gambar</span>
                                <input id="img" class="hidden mt-1 w-full file-input file-input-sm file-input-bordered shadow-none"
                                    type="file" name="image" :value="old('image')" autofocus autocomplete="img" />
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('image1')" class="mt-2" />
    				</div>
					<div class="flex justify-end mt-10 gap-2">
						<a href="{{ route('users.index') }}" class="btn btn-error hover:bg-red-500 transition-all ease-linear .2s">
							Back
						</a>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</div>
			</form>
		</div>
	</x-main-div>
</x-app-layout>
