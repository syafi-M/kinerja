<x-app-layout>
	<x-main-div>
		<div>
			<div>
				<p class="py-10 text-2xl font-bold text-center uppercase">Index Kerjasama</p>
			</div>
			<div class="flex justify-end mx-10 my-10">
				<div class="flex items-center input input-bordered w-fit">
					<i class="ri-search-2-line"></i>
					<input type="search" id="searchInput" class="ml-1 border-none rounded" placeholder="Search..." required>
				</div>
			</div>

			<div class="mx-10 overflow-x-auto">
				<table class="table w-full bg-slate-50" id="searchTable">
					<!-- head -->
					<thead>
						<tr>
							<th class="bg-slate-300 rounded-tl-2xl">#</th>
							<th class="bg-slate-300 ">nama CLIENT</th>
							<th class="bg-slate-300 ">VALUE</th>
							<th class="bg-slate-300 ">EXPERIED</th>
							<th class="bg-slate-300 ">APPROVED 1</th>
							<th class="bg-slate-300 rounded-tr-2xl">AKSI</th>
						</tr>
					</thead>
					<tbody>
						@php
							$no = 1;
						@endphp
						@forelse ($kerjasama as $i)
							<tr>
								<td>{{ $no++ }}</td>
								<td>{{ $i->client->name }}</td>
								<td>{{ toRupiah($i->value) }}</td>
								<td>{{ $i->experied }}</td>
								<td>{{ $i->approve1 }}</td>
								<td>
									<form action="{{ url('kerjasama/' . $i->id) }}" method="POST" class="h-9">
										@csrf
										@method('DELETE')
										<x-btn-submit />
										<x-btn-edit>{{ url('kerjasama/' . $i->id . '/edit') }}</x-btn-edit>
									</form>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="text-center ">~ Data kosong ~</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
			<div class="mx-10 mt-4">
				{{ $kerjasama->links()}}
			</div>
			<div class="flex justify-end gap-2 py-3 mx-16">
				<a href="{{ route('admin.index') }}" class="btn btn-error">Back</a>
				<a href="{{ route('kerjasama.create') }}" class="btn btn-primary">+ Kerjasama</a>
			</div>
	</x-main-div>

</x-app-layout>
