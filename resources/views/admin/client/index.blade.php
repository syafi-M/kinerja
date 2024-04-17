<x-app-layout>
	<x-main-div>
		<div class="py-10 px-5">
			<p class="text-center text-2xl font-bold  uppercase">Index Client</p>
			<div class="flex justify-end ">
				<div class="input flex items-center w-fit input-bordered my-10">
					<i class="ri-search-2-line"></i>
					<input type="search" id="searchInput" class="border-none rounded ml-1" placeholder="Search..." required>
				</div>
			</div>
			<div class="flex justify-center overflow-x-auto mx-10 pb-10">
				<table class="table table-fixed table-zebra w-full shadow-md bg-slate-50" id="searchTable">
					<thead>
						<tr>
							<th class="bg-slate-300 rounded-tl-2xl">#</th>
							<th class="bg-slate-300 ">Logo</th>
							<th class="bg-slate-300 ">Name Client</th>
							<th class="bg-slate-300 ">Alamat</th>
							<th class="bg-slate-300 ">Provinsi</th>
							<th class="bg-slate-300 ">Kabupaten</th>
							<th class="bg-slate-300 ">Kode Pos</th>
							<th class="bg-slate-300 ">Email</th>
							<th class="bg-slate-300 ">No. Telepon</th>
							<th class="bg-slate-300 ">No. Fax</th>
							<th class="bg-slate-300 rounded-tr-2xl">Action</th>
						</tr>
					</thead>
					<tbody class=" text-sm my-10">
						@php
							$no = 1;
						@endphp
						@forelse ($client as $i)
							<tr>
								<td>{{ $no++ }}</td>
							@if ($i->logo == 'no-image.jpg')
									<td>
										<x-no-img />
									</td>
								@elseif(Storage::disk('public')->exists('images/' . $i->logo))
									<td><img class="lazy lazy-image" loading="lazy" src="{{ asset('storage/images/' . $i->logo) }}" data-src="{{ asset('storage/images/' . $i->logo) }}" alt="" srcset="" width="120px"></td>
								@else
								    <td>
										<x-no-img />
									</td>
								@endif
								<td class="hyphens-auto whitespace-pre-wrap ">{{ $i->name }}</td>
								<td class="hyphens-auto whitespace-pre-wrap">{{ $i->address }}</td>
								<td>{{ $i->province }}</td>
								<td>{{ $i->kabupaten }}</td>
								<td>{{ $i->zipcode }}</td>
								<td class="break-words whitespace-pre-line">{{ $i->email }}</td>
								<td class="break-words whitespace-pre-line">{{ $i->phone }}</td>
								<td>{{ $i->fax }}</td>
									<td class="space-y-2">
										<x-btn-edit>{{ url('client/data-client/' . $i->id . '/edit') }}</x-btn-edit>
										@php
										    $getDat = $i;
										@endphp
										<x-btn-submit type="button" id="deleteUser" class="deleteUser" data="{{ $i }}" data-dataId="{{ $i->id }}"/>
									</td>
							</tr>
						@empty
							<tr>
								<td colspan="10" class="text-center">~ Data Kosong ~</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
				<div class="mt-5 mx-10">
					{{ $client->links() }}
				</div>
			<div class="flex justify-end gap-2 mx-16 py-3">
				<a href="{{ route('admin.index') }}" class="btn btn-error">Back</a>
				<a href="{{ route('data-client.create') }}" class="btn btn-primary">+ Client</a>
			</div>
		</div>
        <div
        	class="fixed inset-0 modalDeleteUser hidden bg-slate-500/10 backdrop-blur-sm transition-all duration-300 ease-in-out">
        	<div class="bg-slate-200 w-fit p-5 mx-2 rounded-md shadow">
        		<div class="flex justify-end mb-3">
        			<button id="close" class="btn btn-error scale-90">&times;</button>
        		</div>
        		<form id="formDelet" action="{{ url('client/data-client/' . $i->id) }}" method="POST"
        			class="flex justify-center items-center formDelet ">
        			@csrf
        			@method('DELETE')
        			<div class="flex justify-center flex-col gap-2">
        				<div class="flex flex-col gap-2">
        					<p id="textModalDelet" class="textModalDelet text-center text-lg font-semibold"></p>
        				</div>
        				<div class="flex justify-center items-center overflow-hidden">
        					<button type="submit"
        						class="btn btn-error overflow-hidden"><span class="font-bold overflow-hidden">Hapus Data</span>
        					</button>
        				</div>
        			</div>
        		</form>
        	</div>
        </div>
        <script>
            $(document).ready(function() {
                $('.deleteUser').click(function(){
                    var data = $(this).data('data');
                    var dataId = $(this).data('dataId');
                    
                    var decodedJsonStr = data.replace(/&quot;/g, '"');

                    var jsonObj = JSON.parse(decodedJsonStr);
                    // console.log(jsonObj);
                    
                    $('.formDelet').attr('action', `{{ url('client/data-client/') }}/${jsonObj.id}`);
                    $('.textModalDelet').text(`Apakah Anda Yakin Ingin Menghapus Client ${jsonObj.name}?`);
        		    $('.modalDeleteUser').removeClass('hidden')
        			.addClass('flex justify-center items-center opacity-100');
        		});
        		
        		$('#close').click(function() {
        		    $('.modalDeleteUser').addClass('hidden').removeClass('flex justify-center items-center opacity-100');
        		});
            })
        </script>
	</x-main-div>
</x-app-layout>
