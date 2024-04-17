<x-app-layout>
	<x-main-div>
		<div>
			<p class="text-center text-lg sm:text-2xl font-bold py-10 uppercase">Data Laporan</p>
		</div>
        <span class="flex justify-between items-center">
    		@if (Auth::user()->role_id == 2)
    			<div class="ml-5">
    				<form action="{{ route('export.laporans') }}" method="get" class="w-full">
    					<div class="flex items-center justify-start gap-2">
    						<div class="flex justify-between items-center gap-2">
    							<div style="width: 60%" class="flex flex-col mb-5 gap-2">
    								<label for="client_id">Pilih Mitra</label>
    								<span class="flex gap-2 w-full">
        								<select name="client_id" id="client_id" style="width: 66.66%" class="select select-bordered  ">
        									<option selected disabled>~Pilih Mitra~</option>
        									@forelse ($mitra as $i)
        										<option value="{{ $i->client_id }}">{{ $i->client->name }}</option>
        									@empty
        										<option>~Kosong~</option>
        									@endforelse
        								</select>
        								<select name="ruangan_id" style="width: 33.33%" class="select select-bordered">
        								    <option selected disabled>~Pilih Ruangan (opsional)~</option>
        								    @forelse($ruangan as $ru)
        								        <option value="{{ $ru->id }}">{{ $ru->nama_ruangan }}</option>
        								    @empty
        								        <option>~Kosong~</option>
        								    @endforelse
        								</select>
    								</span>
    							</div>
    							<span style="width: 36%" class="flex gap-1 items-center bg-red-500">
        							<div class="mr-2" style="width: 50%">
        								<input type="date" name="str1" id="str1" placeholder="Tanggal Mulai"
        									class="text-md block py-2 text-sm rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
        							</div>
        							<div class="ml-2" style="width: 50%">
        								<input type="date" name="end1" id="end1"
        									class="text-md block py-2 text-sm rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
        							</div>
    							</span>
    						</div>
    						<div class="flex">
    						    <input type="hidden" name="action" value="download"/>
    							<button type="submit" class="btn btn-warning text-sm sm:btn-sm btn-xs">Print PDF</button>
    						</div>
    					</div>
    				</form>
    			</div>
    		@endif
    
    		<div class="flex justify-end items-center">
    			<div class="input flex w-fit mx-5 items-center justify-end mb-5 input-bordered text-sm">
    				<i class="ri-search-2-line"></i>
    				<input type="search" id="searchInput" class="border-none rounded ml-1" placeholder="Search..." required>
    			</div>
    		</div>
        </span>

		<div class="overflow-x-auto mx-5">
			<table class="table table-zebra table-xs sm:table-md bg-slate-50 w-full" id="searchTable">
				<thead>
					<tr>
						<th class="bg-slate-300 rounded-tl-2xl">#</th>
						<th class="bg-slate-300 text-center" colspan="5">Foto Progres</th>

						@if (Auth::user()->role_id == 2 ||
								Auth::user()->divisi->jabatan->code_jabatan == 'LEADER' ||
								Auth::user()->divisi->jabatan->code_jabatan == 'MITRA')
							<th class="bg-slate-300 ">Nama</th>
						@endif

						<th class="bg-slate-300 ">Mitra</th>
						<th class="bg-slate-300 ">Ruangan</th>
						<th class="bg-slate-300 ">Pekerjaan</th>
						<th class="bg-slate-300 ">Nilai</th>

						@if (Auth::user()->role_id == 2)
							<th class="bg-slate-300 ">Keterangan</th>
							<th class="bg-slate-300 ">Tanggal</th>
							<th class="bg-slate-300 rounded-tr-2xl">Action</th>
						@else
							<th class="bg-slate-300">Keterangan</th>
							<th class="bg-slate-300 rounded-tr-2xl">Tanggal</th>
						@endif
					</tr>
				</thead>
				<tbody>
					@php $no = 1; @endphp
					@forelse ($laporan as $i)
						<tr>
							<td>{{ $no++ }}</td>
							@if ($i->image1 != 'no-image.jpg' && file_exists(public_path('storage/images/' . $i->image1)))
								<td class="scale-75" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><img src="{{ asset('storage/images/' . $i->image1) }}" alt=""
										srcset="{{ asset('storage/images/' . $i->image1) }}" width="100px" class="lazy lazy-image" loading="lazy" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
								</td>
							@else
								<td class="" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><x-no-img /></td>
							@endif
							@if ($i->image2 != 'no-image.jpg' && $i->image2 != null && file_exists(public_path('storage/images/' . $i->image2)))
								<td class="scale-75" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><img src="{{ asset('storage/images/' . $i->image2) }}" alt=""
										srcset="{{ asset('storage/images/' . $i->image2) }}" width="100px" class="lazy lazy-image" loading="lazy" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
								</td>
							@else
								<td class="" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><x-no-img /></td>
							@endif
							@if ($i->image3 != 'no-image.jpg' && $i->image3 != null && file_exists(public_path('storage/images/' . $i->image3)))
								<td class="scale-75" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><img src="{{ asset('storage/images/' . $i->image3) }}" alt=""
										srcset="{{ asset('storage/images/' . $i->image3) }}" width="100px" class="lazy lazy-image" loading="lazy" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
								</td>
							@else
								<td class="" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><x-no-img /></td>
							@endif
							@if ($i->image4 != 'no-image.jpg' && $i->image4 != null && file_exists(public_path('storage/images/' . $i->image4)))
								<td class="scale-75" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><img src="{{ asset('storage/images/' . $i->image4) }}" alt=""
										srcset="{{ asset('storage/images/' . $i->image4) }}" width="100px" class="lazy lazy-image" loading="lazy" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
								</td>
							@else
								<td class="" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><x-no-img /></td>
							@endif
							@if ($i->image5 != 'no-image.jpg' && $i->image5 != null && file_exists(public_path('storage/images/' . $i->image5)))
								<td class="scale-75" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><img src="{{ asset('storage/images/' . $i->image5) }}" alt=""
										srcset="{{ asset('storage/images/' . $i->image5) }}" width="100px" class="lazy lazy-image" loading="lazy" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
								</td>
							@else
								<td class="" style="position: relative; width: 100px; height: calc(133.33px); overflow: hidden;"><x-no-img /></td>
							@endif
							
							<!--@if ($i->image2 == null)-->
							<!--	<td><x-no-img /></td>-->
							<!--@else-->
							<!--	<td class="scale-75"><img src="{{ asset('storage/images/' . $i->image2) }}" alt=""-->
							<!--			srcset="{{ asset('storage/images/' . $i->image2) }}" width="100px" class="lazy lazy-image" loading="lazy">-->
							<!--	</td>-->
							<!--@endif-->
							
							<!--<td class="scale-75"><img src="{{ asset('storage/images/' . $i->image3) }}" alt=""-->
							<!--		srcset="{{ asset('storage/images/' . $i->image3) }}" width="100px" class="lazy lazy-image" loading="lazy">-->
							<!--</td>-->

							@if (Auth::user()->role_id == 2 ||
									Auth::user()->divisi->jabatan->code_jabatan == 'LEADER' ||
									Auth::user()->divisi->jabatan->code_jabatan == 'MITRA')
								<td>{{ $i->user->nama_lengkap }}</td>
							@endif
								<!--<td>{{ $i->user->nama_lengkap }}</td>-->

							<td>{{ $i->client->name }}</td>
							<td>{{ $i->ruangan?->nama_ruangan }}</td>
							<td>
    							@php
                                    // Decode the JSON string to get an array
                                    $pekerjaanArray = json_decode($i->pekerjaan);
                                @endphp
                                @if (!empty($pekerjaanArray))
                                    @foreach ($pekerjaanArray as $value)
                                        {{ $value }}
                                        @if(!$loop->last)
                                            , 
                                        @endif
                                    @endforeach
                                @endif
							</td>
							<td>
							    @if($i->nilai)
    							    @if($i->nilai == "baik")
    							        <span class="badge badge-success overflow-hidden">Baik</span>
    							    @elseif($i->nilai == "cukup")
    							        <span class="badge badge-info overflow-hidden">Cukup</span>
    							    @else
    							        <span class="badge badge-error overflow-hidden">Kurang</span>
    							    @endif
							    @endif
							</td>
							<td>{{ $i->keterangan }}</td>
							<td>{{ $i->created_at->format('Y-m-d') }}</td>

							@if (Auth::user()->role_id == 2)
								<td>
									<form action="{{ url('laporans/' . $i->id) }}" method="POST">
										@csrf
										@method('DELETE')
										<x-btn-submit></x-btn-submit>
									</form>
								</td>
							@else
							@endif
						</tr>
					@empty
						<tr>
							<td colspan="8" class="text-center">Laporan Saat Ini Kosong</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<div class="mt-5 mx-10">
			{{ $laporan->links() }}
		</div>
		<div>
			<div class="flex justify-center sm:justify-end my-3 gap-2 mr-0 sm:mr-9">
				<a href="{{ url('/scan') }}"
					class="btn btn-warning hover:bg-yellow-600 border-none transition-all ease-in-out .2s">+ Laporan</a>
				<a href="{{ route('dashboard.index') }}"
					class="btn btn-error border-none hover:bg-red-500 transition-all ease-in-out .2s">Kembali</a>
			</div>
		</div>
	</x-main-div>
</x-app-layout>
