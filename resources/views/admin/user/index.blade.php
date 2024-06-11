<x-app-layout>
	<div class="bg-slate-500 mx-10 mb-10 shadow-md p-2 rounded-md">
		<div>
			<p class="text-center text-2xl font-bold py-10 uppercase">Data All User</p>
		</div>
		{{-- atas --}}
		<div class="flex justify-between items-center overflow-hidden mx-10 my-1">
			<div>
				<form id="filterForm" action="{{ route('users.index') }}" method="GET" class="p-1">
					<select name="filterKerjasama" id="filterKerjasama" class="select select-bordered active:border-none border-none">
						<option selected disabled>~ Kerja Sama ~</option>
						@foreach ($kerjasama as $i)
							<option value="{{ $i->id }}">{{ $i->client->name }}</option>
						@endforeach
					</select>
					<button type="submit"
						class="bg-blue-500 px-5 py-2 rounded-md hover:bg-blue-600 transition-colors ease-in .2s font-bold uppercase ml-3">Filter</button>
				</form>
			</div>
			<div class=" flex flex-col justify-end items-end ">
				<x-search />
				
			</div>
		</div>
		<div class="hidden justify-between items-center overflow-hidden mx-10 my-1">
			<div>
				<form id="massUpdateUser" action="{{ route('user.massUpdate') }}" method="POST" class="p-1 flex flex-col gap-2">
				    @csrf
				    <label>Mass Update User (jangan digunakan sembarangan)</label>
				    <div class="flex w-full gap-1">
    					<select name="kerjasama" id="filterKerjasama" class="select select-sm text-sm select-bordered active:border-none border-none w-full">
    						<option selected disabled>~ Kerja Sama ~</option>
    						@foreach ($kerjasama as $i)
    							<option value="{{ $i->id }}">{{ $i->client->name }}</option>
    						@endforeach
    					</select>
    					<select name="devisi" id="filterKerjasama" class="select select-sm text-sm select-bordered active:border-none border-none w-full">
    						<option selected disabled>~ Devisi ~</option>
    						@foreach ($dev as $i)
    							<option value="{{ $i->id }}">{{ $i->jabatan->name_jabatan }} id: {{ $i->id }}</option>
    						@endforeach
    					</select>
    					<select name="field" id="filterKerjasama" class="select select-sm text-sm select-bordered active:border-none border-none w-full">
    						<option selected disabled>~ Field (yang mau diisi/diganti) ~</option>
    						<option value="jabatan_id">Jabatan ID</option>
    						<option value="kerjasama_id">Kerjasama ID</option>
    						<!--<option value="devisi_id">Devisi ID</option>-->
    					</select>
				    </div>
					<div class="flex w-full gap-1">
    					<input name="old_value" type="text" placeholder="data lama..." class="input input-bordered input-sm w-full"/>
    					<input name="new_value" type="text" placeholder="data baru..." class="input input-bordered input-sm w-full"/>
					</div>
					<div class="flex justify-end">
    					<button type="submit"
    						class="bg-amber-500 px-5 py-2 rounded-md hover:bg-blue-600 transition-colors ease-in .2s font-bold uppercase ml-3">Update</button>
					</div>
				</form>
			</div>
		</div>

		<div class="overflow-x-auto mx-10 my-10">
			<table class="table table-xs table-zebra w-full bg-slate-50" id="searchTable">
				<!-- head -->
				<thead class="text-center">
					<tr>
						<th class="bg-slate-300 rounded-tl-2xl "></th>
						<th class="bg-slate-300 ">#</th>
						<th class="bg-slate-300 ">IMAGE</th>
						<th class="bg-slate-300 ">NAMA</th>
						<th class="bg-slate-300 ">NAMA LENGKAP</th>
						<th class="bg-slate-300 ">EMAIL</th>
						<th class="bg-slate-300 ">NIK</th>
						<th class="bg-slate-300 ">NO. HP</th>
						<th class="bg-slate-300 ">KERJASAMA</th>
						<th class="bg-slate-300 rounded-tr-2xl px-5">AKSI</th>
					</tr>
				</thead>
				<tbody>
					@php
						$no = 1;
					@endphp
					<form action="{{ route('export_checklist')}}" method="POST" id="exportUserForm">
                     @csrf
                     @method('POST')
                     <div class="flex items-center justify-between mb-2">
                         <span class="flex bg-slate-200 rounded-xl shadow p-1">
                             <span class="flex items-center ">
                                <input type="checkbox" name="check_all" id="checkbox_all" value="true" class="ml-5 checkbox"/>
                                <label for="checkbox_all" class="ml-2 font-semibold">Pilih Semua</label>
                             </span>
                            <div class="flex justify-end mx-10">
                                <button type="submit" class="btn btn-primary font-semibold" id="exportUser">export</button>
                            </div>
                         </span>
                        <div class="flex justify-end gap-2  py-3 mb-10">
                			<a href="{{ route('admin.index') }}" class="btn btn-error">Kembali</a>
                			<a href="{{ route('users.create') }}" class="btn btn-primary">+ User</a>
                		</div>
                     </div>
						@forelse ($user as $i)
							<tr>
							    <span id="data" data-value="{{ $i->id }}"></span>
								<td><input class="checkbox" type="checkbox" name="check[]" id="check_{{ $i->id }}" value="{{ $i->id}}"/></td>
								<td>{{ $no++ }}</td>
								@if ($i->image == 'no-image.jpg')
									<td>
										<x-no-img />
									</td>
								@elseif(Storage::disk('public')->exists('images/' . $i->image))
									<td class="flex justify-center"><img loading="lazy" src="{{ asset('storage/images/' . $i->image) }}" data-src="{{ asset('storage/images/' . $i->image) }}" alt="" srcset="" width="120"></td>
								@else
								    <td>
										<x-no-img />
									</td>
								@endif
								<td>{{ $i->name }}</td>
								<td class="break-words whitespace-pre-line">{{ $i->nama_lengkap }}</td>
								<td class="break-words whitespace-pre-line">{{ $i->email }}</td>
								<td class="break-words whitespace-pre-line">{{ $i->nik ? \Illuminate\Support\Facades\Crypt::decryptString($i->nik) : '' }}</td>
								<td class="break-words whitespace-pre-line">{{ $i->no_hp ? "+62".$i->no_hp : '' }}</td>
								@if ($i->kerjasama == null || $i->kerjasama->client == null)
									<td>kosong</td>
								@else
									<td class="break-words whitespace-pre-line">{{ $i->kerjasama->client->name }}</td>
								@endif
								<td>
								    <span class="flex items-center gap-1">
    									<x-btn-edit>{{ url('users/' . $i->id . '/edit') }}</x-btn-edit>
        								<button type="button" class="delete-button hidden" data-user-id="{{ $i->id }}"></button>
								    </span>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="7" class="text-center">Data Kosong</td>
							</tr>
						@endforelse
					</form>
				</tbody>
			</table>
		</div>
			<div class="mt-5">
				{{ $user->links() }}
			</div>
		
		<script>
				$(document).ready(function () {
				// Saat halaman dimuat, ambil semua elemen dengan class "lazy-image"
				var lazyImages = $('.lazy-image');
			
				// Fungsi untuk memuat gambar ketika mendekati jendela pandangan pengguna
				function lazyLoad() {
					lazyImages.each(function () {
						var image = $(this);
						if (image.is(':visible') && !image.attr('src')) {
							image.attr('src', image.attr('data-src'));
						}
					});
				}
			
				// Panggil fungsi lazyLoad saat halaman dimuat dan saat pengguna menggulir
				lazyLoad();
				$(window).on('scroll', lazyLoad);
			});
			var id = $('#data').attr('data-value');
			
		$('#checkbox_all').not('input[id^="check_"]').click(function () {
		    var checkBoxes = $('input[id^="check_"]');
            checkBoxes.prop("checked", !checkBoxes.prop("checked"));

        
        });
        
        $(document).ready(function () {
            // Handle user deletion with jQuery AJAX
            $('.delete-button').click(function (event) {
                event.preventDefault();
                    var userId = $(this).data('user-id');
                    var deleteUrl = "{{ route('users.destroy', [':id']) }}".replace(':id', userId);
                    console.log(userId, deleteUrl);
    
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function () {
                            // Handle success, e.g., remove the table row
                            // $(`#check_${userId}`).closest('tr').remove();
                        },
                        error: function (xhr, status, error) {
                            console.log(error);
                            alert('An error occurred while deleting the user.');
                        }
                    });
            });
            
            $('#exportUser').click(function(e) {
                e.preventDefault();
                $('#exportUserForm').submit();
            })
        });
		</script>
</x-app-layout>
