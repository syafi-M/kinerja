<x-app-layout>
	<x-main-div>
		<div class="py-10">
			<p class="text-center text-2xl font-bold py-5 uppercase">Data Absen Sholat</p>
			<div class="flex justify-between items-center mx-10">
			    <div class="flex justify-between items-center w-full">
    			    <div>
    					<form id="filterForm" action="{{ route('reportSholat.index') }}" method="GET" class="p-1 flex">
    						<span class="flex gap-2">
								<select name="filterKerjasama" id="filterKerjasama" style="width: 16rem;" class="select  select-bordered text-md active:border-none border-none">
									<option selected disabled>~ Nama Klien ~</option>
									@foreach ($absenSi as $i)
										<option value="{{ $i->id }}" {{ $filter == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
									@endforeach
								</select>
							</span>
							<span class="flex mx-2 gap-2">
								<select name="filterDevisi" id="filterDevisi" class="select select-bordered  text-md active:border-none border-none">
									<option selected disabled>~ Devisi ~</option>
									@foreach ($divisi as $i)
										<option value="{{ $i->id }}" {{ $filterDivisi == $i->id ? 'selected' : '' }}>{{ $i->name }}</option>
									@endforeach
								</select>
							</span>
    						<div>
    						    <button type="submit"
    							    class="bg-blue-500 px-5 py-2 rounded-md hover:bg-blue-600 transition-colors ease-in .2s font-bold uppercase ml-3">Filter</button>
    					            <a href="{{ route('admin.index') }}" class="btn btn-error py-1">back</a>
    						</div>
    					</form>
    				</div>
    				<div class="flex justify-end items-center mr-10 mt-5">
        				<x-search/>
        			</div>
    				<div class="hidden">
            			<form method="GET" action="{{route('admin.export')}}">
            			    <div class="flex items-center">
            			        <!--LIBUR-->
                				<div>
                				    <input type="text" name="libur" class="input input-bordered" placeholder="Hari libur untuk semua.."/>
                			    </div>
                			    <div class="flex justify-end mx-10 mb-2 ">
                			    <button type="submit"
                					class="bg-yellow-400 px-4 py-2 shadow rounded-md flex flex-col items-center">
                			        <i class="ri-file-download-line text-2xl"></i>
                			        <span class="text-sm">All</span>
                				</button>
                			    </div>
            			    </div>
            			</form>
    				</div>
			    </div>
			</div>	
			
				<form method="GET" action="{{route('reportSholat.download')}}">
			        <div class="flex items-center justify-center mx-10">
			            <div class="flex items-center gap-2">
        					    <div class="mr-5">
        						    <select name="kerjasama_id" id="selectInput" style="width: 10rem;" class="text-sm py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
        						        <option disabled selected>Pilih Mitra</option>
        						        @forelse ($absenSi as $i)
        						        <option value="{{ $i->id}}" class="break-words whitespace-pre-wrap">{{ $i->client->name }}</option>
        						        @empty
        						        <option >~Kosong~</option>
        						        @endforelse
        					        </select>
        				        </div>
    			                <div class="flex mr-2">
    					            <div class="mr-2">
    						            <input type="date" name="str1" id="str1" placeholder="Tanggal Mulai"
    							            class="text-sm block px-3 py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
    				                </div>
        					        <div class="ml-2">
        						         <input type="date" name="end1" id="end1"
        							        class="text-sm block px-3 py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
        					        </div>
    			                </div>
			                <div class="flex justify-between items-center">
    					        <div class="mr-2">
        						    <select name="divisi_id" class="text-sm block px-10 py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
        						        <option disabled selected>Pilih Divisi</option>
        						        @forelse($divisi as $i)
        						        <option value="{{$i->id}}">{{$i->name}}</option>
        						        @empty
        						        <option >~Kosong~</option>
        						        @endforelse
        						    </select>
    					        </div>
    						    <!--LIBUR-->
        <!--    				    <div>-->
        <!--    				        <input type="text" name="libur" style="height: 2.5rem;" class="input input-bordered w-fit text-xs" placeholder="Masukkan hari libur.."/>-->
        <!--    					</div>-->
        <!--    					{{-- + Jadwal --}}-->
								<!--<div class="flex justify-center items-center px-2 py-1 bg-slate-100 rounded mx-2">-->
								<!--	<input type="checkbox" name="jadwal" id="jadwal" value="1" class="checkbox ">-->
								<!--	<label for="jadwal" class="label label-text font-semibold text-xs text-slate-500">+ Jadwal</label>-->
								<!--</div>-->
        						<div class="flex mx-10 mb-2 ">
        						    <button type="submit" class="bg-yellow-400 px-3 py-2 shadow rounded-md text-2xl">
        								<i class="ri-file-download-line"></i>
        						    </button>
        						</div>
			                </div>
				        </div>
                </form>
			</div>

			
			<div class="overflow-x-auto mx-10 my-10">
				<table class="table table-zebra w-full bg-slate-50" id="searchTable">
					<thead>
						<tr>
							<th class="bg-slate-300 rounded-tl-2xl">#</th>
							<th class="bg-slate-300 ">Photo</th>
							<th class="bg-slate-300 ">Nama User</th>
							<th class="bg-slate-300 px-10">Tanggal</th>
							<th class="bg-slate-300 ">Shift</th>
							<th class="bg-slate-300 ">Client</th>
							<th class="bg-slate-300 ">Subuh</th>
							<th class="bg-slate-300 ">Dzuhur</th>
							<th class="bg-slate-300 ">Asar</th>
							<th class="bg-slate-300 ">Maghrib</th>
							<th class="bg-slate-300 ">Isya`</th>
						</tr>
					</thead>
					<tbody>
						@php
							$no = 1;
						@endphp
					@forelse ($absen as $arr)
							<tr>
								<td>{{ $no++ }}</td>
								<td>
								    @if($arr->image == "no-image.jpg")
								        <img class="lazy lazy-image" loading="lazy" srcset="{{ URL::asset('/logo/no-image.jpg') }}" data-src="{{asset('storage/images/'.$arr->image)}}" alt="data-absensi-image" width="120px"/>
								    @else
								        <img class="lazy lazy-image" loading="lazy" src="{{asset('storage/images/'.$arr->image)}}" data-src="{{asset('storage/images/'.$arr->image)}}" alt="data-absensi-image" width="120px"/>
								    @endif
								</td>
								<td class="break-words whitespace-pre-line">{{ $arr->user?$arr->user->nama_lengkap : 'user_id'. ' : '. $arr->user_id . 'AKU KOSONG' }}</td>
								<td>{{ $arr->tanggal_absen }}</td>
								@if($arr->shift != null)
								<td id="mitra">{{ $arr->shift->shift_name . " | " . $arr->shift->jam_start . ' - ' . $arr->shift->jam_end }}</td>
					            @else
					            <td class="break-words whitespace-pre-wrap text-red-500 font-semibold">Shift Kosong</td>
					            @endif
								<td class="break-words whitespace-pre-line">{{ $arr->kerjasama->client->name }}</td>
								<td class="break-words whitespace-pre-line">
								     {!! $arr->subuh == 1 ? "Sudah Absen Subuh " . $arr->subuh_lat . ',' . $arr->subuh_long : "<span style='color: red'>Belum Absen</span>" !!}
								</td>
								<td class="break-words whitespace-pre-line">
								     {!! $arr->dzuhur == 1 ? "Sudah Absen Dzuhur " . $arr->sig_lat . ',' . $arr->sig_long : "<span style='color: red'>Belum Absen</span>" !!}
								</td>
								<td class="break-words whitespace-pre-line">
								     {!! $arr->asar == 1 ? "Sudah Absen Asar " . $arr->asar_lat . ',' . $arr->asar_long : "<span style='color: red'>Belum Absen</span>" !!}
								</td>
								<td class="break-words whitespace-pre-line">
								     {!! $arr->maghrib == 1 ? "Sudah Absen Maghrib " . $arr->maghrib_lat . ',' . $arr->maghrib_long : "<span style='color: red'>Belum Absen</span>" !!}
								</td>
								<td class="break-words whitespace-pre-line">
								     {!! $arr->isya == 1 ? "Sudah Absen Isya " . $arr->isya_lat . ',' . $arr->isya_long : "<span style='color: red'>Belum Absen</span>" !!}
								</td>
							

							</tr>
							@empty
							<tr>
								<td colspan="10" class="text-center">~ Kosong ~</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
			<div class="mt-4 mx-10 ">
    				{{ $absen->links()}}
			</div>
		</div>
	</x-main-div>
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
	</script>
</x-app-layout>
