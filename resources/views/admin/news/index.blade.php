<x-app-layout>
	<x-main-div>
		<div>
			<p class="text-center text-lg sm:text-2xl font-bold py-10 uppercase">Data News SAC</p>
		</div>
		<div class="flex justify-end">
			<div class="input flex w-fit mx-10 items-center justify-end mb-5 input-bordered">
				<i class="ri-search-2-line"></i>
				<input type="search" id="searchInput" class="border-none rounded ml-1" placeholder="Search..." required>
			</div>
		</div>

		<div class="overflow-x-auto mx-5">
			<table class="table table-zebra table-xs sm:table-md bg-slate-50 w-full" id="searchTable">
				<thead>
					<tr>
						<th class="bg-slate-300 rounded-tl-2xl">#</th>
						<th class="bg-slate-300 text-center">Foto Berita</th>
						<th class="bg-slate-300 ">Tanggal Berlaku</th>
						<th class="bg-slate-300 rounded-tr-2xl">Keterangan</th>
					</tr>
				</thead>
				<tbody>
					@php 
					    $no = 1; 
					@endphp
					@forelse ($news as $n)
						<tr>
							<td>{{ $no++ }}</td>
							@if ($n->image == 'no-image.jpg')
								<td><x-no-img /></td>
							@else
								<td class="scale-75"><img src="{{ asset('storage/images/' . $n->image) }}" alt=""
										srcset="{{ asset('storage/images/' . $n->image) }}" width="100px" class="lazy lazy-image" loading="lazy">
								</td>
							@endif
							<td>{{ $n->tanggal_lihat }} - {{ $n->tanggal_tutup }}</td>
							<td class="flex flex-col gap-2 items-center justify-center">
							    <x-btn-edit>{{ route('news.edit', $n->id) }}</x-btn-edit>
								<form action="{{ url('news/' . $n->id) }}" method="POST">
									@csrf
									@method('DELETE')
									<x-btn-submit></x-btn-submit>
								</form>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="8" class="text-center">Berita Saat Ini Kosong</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<div>
			<div class="flex justify-center sm:justify-end my-3 gap-2 mr-0 sm:mr-9">
				<a href="{{ route('news.create') }}"
					class="btn btn-warning hover:bg-yellow-600 border-none transition-all ease-in-out .2s">+ Berita</a>
				<a href="{{ route('admin.index') }}"
					class="btn btn-error border-none hover:bg-red-500 transition-all ease-in-out .2s">Kembali</a>
			</div>
		</div>
	</x-main-div>
</x-app-layout>
