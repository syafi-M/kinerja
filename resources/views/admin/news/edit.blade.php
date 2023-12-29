<x-app-layout>
	<x-main-div>
		<div class="p-5 py-10">
			<p class="text-center font-bold text-xl sm:text-2xl uppercase">Edit Berita</p>
			<form method="POST" action="{{ route('news.update', $newsId->id) }}" class=" my-10" id="form" enctype="multipart/form-data">
				@csrf
				@method('PATCH')
				<div class="bg-slate-100 px-10 py-5 rounded shadow">
					{{-- image --}}
					<div class="my-5 p-1">
						<x-input-label for="image" :value="__('Foto Berita')" />
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
									type="file" name="image" :value="old('image')" autofocus autocomplete="img" accept="image/*"/>
							</span>
						</label>
						<x-input-error :messages="$errors->get('image1')" class="mt-2" />
					</div>
					{{-- tanggal berlaku --}}
					<div class="mt-4">
						<x-input-label for="tanggal_lihat" :value="__('Tanggal Berlaku')" />
						<input type="date" name="tanggal_lihat" class="input input-bordered w-full" value="{{ $newsId->tanggal_lihat }}" required/>
						<x-input-error :messages="$errors->get('tanggal_lihat')" class="mt-2" />
					</div>
					{{-- tanggal akhir --}}
					<div class="mt-4">
						<x-input-label for="tanggal_tutup" :value="__('Tanggal Berakhir')" />
						<input type="date" name="tanggal_tutup" class="input input-bordered w-full" value="{{ $newsId->tanggal_tutup }}" required/>
						<x-input-error :messages="$errors->get('tanggal_tutup')" class="mt-2" />
					</div>

					<div class="flex justify-center sm:justify-end gap-2 mt-5">
						<button type="submit" class="btn btn-primary">Simpan</button>
						<a href="{{ route('news.index') }}" class="btn btn-error hover:bg-red-500 transition-all ease-linear .2s">
							Kembali
						</a>
					</div>
				</div>
			</form>
		</div>
	</x-main-div>
</x-app-layout>
