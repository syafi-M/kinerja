<x-admin-layout :fullWidth="true">
		@section('title', 'Data Absen Sholat')

		<div class="w-full px-2 py-6 mx-auto space-y-4 max-w-screen-2xl sm:px-3 lg:px-4">
			<section class="p-4 bg-white border border-gray-100 shadow-sm rounded-2xl sm:p-5">
				<p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-blue-600">Sholat Report</p>
				<h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Data Absen Sholat</h1>
				<p class="mt-1 text-sm text-gray-600">Filter data absensi sholat, lalu unduh report berdasarkan mitra, divisi, dan rentang tanggal.</p>
			</section>

			<section class="grid gap-4 lg:grid-cols-12">
				<div class="p-4 bg-white border border-gray-100 shadow-sm rounded-2xl sm:p-5 lg:col-span-8">
					<h2 class="text-sm font-semibold text-gray-800">Filter Data</h2>
					<form id="filterForm" action="{{ route('reportSholat.index') }}" method="GET" class="grid gap-3 mt-3 md:grid-cols-2">
						<div>
							<label for="filterKerjasama" class="block mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">Nama Klien</label>
							<select
								name="filterKerjasama"
								id="filterKerjasama"
								class="w-full h-10 px-3 text-sm text-gray-700 bg-white border border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none"
							>
								<option selected disabled>~ Nama Klien ~</option>
								@foreach ($absenSi as $i)
									<option value="{{ $i->id }}" {{ $filter == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
								@endforeach
							</select>
						</div>
						<div>
							<label for="filterDevisi" class="block mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">Divisi</label>
							<select
								name="filterDevisi"
								id="filterDevisi"
								class="w-full h-10 px-3 text-sm text-gray-700 bg-white border border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none"
							>
								<option selected disabled>~ Devisi ~</option>
								@foreach ($divisi as $i)
									<option value="{{ $i->id }}" {{ $filterDivisi == $i->id ? 'selected' : '' }}>{{ $i->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="flex flex-wrap items-center gap-2 md:col-span-2">
							<button
								type="submit"
								class="inline-flex items-center h-10 px-4 text-sm font-semibold text-white transition bg-blue-600 rounded-xl hover:bg-blue-700"
							>
								Filter
							</button>
							<a
								href="{{ route('admin.index') }}"
								class="inline-flex items-center h-10 px-4 text-sm font-semibold text-red-700 transition border border-red-200 rounded-xl bg-red-50 hover:bg-red-100"
							>
								Kembali
							</a>
						</div>
					</form>
				</div>
				<div class="p-4 bg-white border border-gray-100 shadow-sm rounded-2xl sm:p-5 lg:col-span-4">
					<h2 class="text-sm font-semibold text-gray-800">Pencarian Cepat</h2>
					<label class="flex items-center w-full h-10 gap-2 px-3 border border-gray-200 rounded-xl bg-gray-50 sm:w-72">
                        <i class="text-base text-gray-500 ri-search-2-line"></i>
                        <input type="search" id="searchInput" class="w-full text-sm text-gray-700 bg-transparent border-none placeholder:text-gray-400 focus:outline-none" placeholder="Cari Sesuatu..." />
                    </label>
				</div>
			</section>

			<section class="p-4 bg-white border border-gray-100 shadow-sm rounded-2xl sm:p-5">
				<h2 class="text-sm font-semibold text-gray-800">Download Report</h2>
				<form method="GET" action="{{ route('reportSholat.download') }}" class="grid gap-3 mt-3 lg:grid-cols-12">
					<div class="lg:col-span-3">
						<label for="selectInput" class="block mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">Mitra</label>
						<select
							name="kerjasama_id"
							id="selectInput"
							class="w-full h-10 px-3 text-sm text-gray-700 bg-white border border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none"
						>
							<option disabled selected>Pilih Mitra</option>
							@forelse ($absenSi as $i)
								<option value="{{ $i->id }}">{{ $i->client->name }}</option>
							@empty
								<option>~Kosong~</option>
							@endforelse
						</select>
					</div>
					<div class="lg:col-span-3">
						<label for="str1" class="block mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">Tanggal Mulai</label>
						<input
							type="date"
							name="str1"
							id="str1"
							class="w-full h-10 px-3 text-sm text-gray-700 bg-white border border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none"
						>
					</div>
					<div class="lg:col-span-3">
						<label for="end1" class="block mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">Tanggal Selesai</label>
						<input
							type="date"
							name="end1"
							id="end1"
							class="w-full h-10 px-3 text-sm text-gray-700 bg-white border border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none"
						>
					</div>
					<div class="lg:col-span-2">
						<label for="divisi_id" class="block mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">Divisi</label>
						<select
							name="divisi_id"
							id="divisi_id"
							class="w-full h-10 px-3 text-sm text-gray-700 bg-white border border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none"
						>
							<option disabled selected>Pilih Divisi</option>
							@forelse($divisi as $i)
								<option value="{{ $i->id }}">{{ $i->name }}</option>
							@empty
								<option>~Kosong~</option>
							@endforelse
						</select>
					</div>
					<div class="flex items-end lg:col-span-1">
						<button
							type="submit"
							class="inline-flex items-center justify-center w-full h-10 px-3 text-sm font-semibold text-gray-800 transition rounded-xl bg-amber-400 hover:bg-amber-500"
							title="Download report"
						>
							<i class="text-base ri-file-download-line"></i>
						</button>
					</div>
				</form>
			</section>

			<div class="hidden">
				<form method="GET" action="{{ route('admin.export') }}">
					<div class="flex items-center">
						<div>
							<input type="text" name="libur" class="input input-bordered" placeholder="Hari libur untuk semua.."/>
						</div>
						<div class="flex justify-end mx-10 mb-2 ">
							<button type="submit" class="flex flex-col items-center px-4 py-2 bg-yellow-400 rounded-md shadow">
								<i class="text-2xl ri-file-download-line"></i>
								<span class="text-sm">All</span>
							</button>
						</div>
					</div>
				</form>
			</div>

			<section class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
				<div class="w-full overflow-x-auto">
					<table class="table w-full table-zebra bg-slate-50" id="searchTable">
					<thead>
						<tr>
							<th class="bg-slate-300 rounded-tl-2xl">#</th>
							<th class="bg-slate-300 ">Photo</th>
							<th class="bg-slate-300 ">Nama User</th>
							<th class="px-10 bg-slate-300">Tanggal</th>
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
					            <td class="font-semibold text-red-500 break-words whitespace-pre-wrap">Shift Kosong</td>
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
				<div class="px-4 py-3 border-t border-gray-100 sm:px-5">
    					{{ $absen->links()}}
				</div>
			</section>
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
	</script>
</x-admin-layout>
