<x-app-layout>
	<x-main-div class="ydis">
		@php
			$starte = \Carbon\Carbon::createFromFormat('Y-m-d', $str1);
			$ende = \Carbon\Carbon::createFromFormat('Y-m-d', $end1);
		@endphp
		<div class="bg-slate-500 p-4  shadow-md rounded-md">
			<p class="text-center text-2xl uppercase font-bold my-10">Tambah Jadwal</p>
			@if (Auth::user()->role_id == 2)
				<form action="{{ route('jadwal_export.admin') }}" method="get">
					<div class="flex justify-end mx-10 mb-2 ">
						<input type="text" name="str1" value="{{ $starte->format('Y-m-d') }}" class="hidden">
						<input type="text" name="end1" value="{{ $ende->format('Y-m-d') }}" class="hidden">
						<div class="flex flex-col items-center gap-x-2">
							<select name="filter" class="input input-bordered">
								<option class="disabled" disabled>~Pilih Mitra~</option>
								@forelse($kerj as $i)
									<option value="{{ $i->id }}" {{ $i->id == $filter ? 'selected' : '' }}>{{ $i->client->name }}</option>
								@empty
									<option class="disabled">~Mitra Kosong~</option>
								@endforelse
							</select>
						</div>
					</div>
					<span class=" justify-end mx-10">    
						<button type="submit" class="bg-yellow-400 px-3 py-2 shadow rounded-md text-2xl flex items-center gap-2"
							style="margin-bottom: 3rem;">
							<p class="text-sm font-semibold">Print PDF</p>
							<i class="ri-file-download-line"></i>
						</button>
					</span>
				</form>
			@else
				<form action="{{ route('lead_jadwal_export') }}" method="get" class="hidden">
					<div class="flex justify-end mx-10 mb-2 ">
						<button type="submit" class="bg-yellow-400 px-3 mt-4 py-2 shadow rounded-md text-2xl flex items-center gap-2">
							<p class="text-sm font-semibold">Print PDF</p>
							<i class="ri-file-download-line"></i>
						</button>
						<input type="text" name="str1" value="{{ $starte->format('Y-m-d') }}" hidden>
						<input type="text" name="end1" value="{{ $ende->format('Y-m-d') }}" hidden>
					</div>
				</form>
			@endif

			{{-- 2 --}}
			<div class="overflow-x-scroll sm:overflow-x-auto pb-10 text-xs">
				<table class="table table-xs table-zebra bg-slate-50 w-full">
					<thead>
						<tr>
							<th class="bg-slate-300 rounded-tl-2xl">#</th>
							<th class="bg-slate-300">Nama Lengkap</th>
							<th class="bg-slate-300 rounded-tr-2xl">lihat Jadwal create</th>
						</tr>
					</thead>
					<tbody>
						@php
							$no = 1;
						@endphp
						@forelse ($user as $us)
							@if ($us->nama_lengkap != 'admin' && $us->nama_lengkap != 'user')
								<tr>
									<td>{{ $no++ }}</td>
									<td>{{ $us->nama_lengkap }}</td>
									<td>
										<span hidden id="jadwalUserId"></span>
										<button id="myModalBtnJadwal{{ $us->id }}" data-user-id="{{ $us->id }}"
											data-user-name="{{ $us->nama_lengkap }}"
											class="btn btn-sm text-xs overflow-hidden bg-amber-400 text-slate-800 border-slate-200 hover:border-slate-400/70 hover:bg-amber-500/70">
											Jadwal</button>
										<span id="data-orang" data-user-id="{{ $us->id }}" data-user-name="{{ $us->nama_lengkap }}"
											hidden></span>

										<div id="myModalContent{{ $us->id }}" class="hidden fixed modalz">
											<!-- Your modal content here -->
											<div class="flex justify-center bg-slate-500/10 backdrop-blur-sm items-center min-h-screen rounded-md">
												<div class="bg-slate-200 w-full mb-20 mt-10 mx-10 rounded-md shadow">
													<div class="flex justify-end m-5">
														<button class="btn btn-error close">&times;</button>
													</div>
													<div class="flex justify-center items-center flex-col ">
														<p id="judulNamaUser" class="text-center text-xl font-semibold mb-3"></p>
														<span class="text-center my-2 font-semibold">Tanggal : </span>
														<span hidden>
															<span id="starte" data-starte="{{ $starte }}"></span>
															<span id="ende" data-ende="{{ $ende }}"></span>
														</span>
														<span class="grid grid-cols-7 gap-2 mx-5" id="jadwalContainer{{ $us->id }}">

														</span>
														<div class="w-full px-5 pt-5">
															<p class="my-2 font-semibold">Ket: </p>
															<span class="flex flex-col gap-2">
																<div class="rounded-lg flex gap-2 items-center text-[10px] sm:text-xs">
																	<span class="py-2 px-4 rounded-lg font-bold bg-slate-300 w-fit"></span>
																	<span>: <span class="underline text-bold">Kosong/belum diisi</span></span>
																</div>
																<div class="rounded-lg flex gap-2 items-center text-[10px] sm:text-xs">
																	<span class="py-2 px-4 rounded-lg font-bold bg-orange-400 w-fit"></span>
																	<span>: <span class="underline text-bold">Off/Libur</span></span>
																</div>
																<div class="rounded-lg flex gap-2 items-center text-[10px] sm:text-xs">
																	<span class="py-2 px-4 rounded-lg font-bold bg-sky-400 w-fit"></span>
																	<span>: <span class="underline text-bold">Masuk</span></span>
																</div>
															</span>

														</div>
													</div>
													@php
														$awal = strtotime($str1);
														$akhir = strtotime($end1);

														$dataAwal = date('Y-m-d', $awal);
														$dataAkhir = date('Y-m-d', $akhir);
													@endphp
													<span hidden>
														<span id="adminId" data-admin="{{ Auth::user()->role_id }}"></span>
													</span>
													@if (Auth::user()->role_id == 2)
														<form method="POST" action="{{ route("storeJadwalAdmin") }}" class="p-5 w-full mt-4" id="formA">
															@csrf
															<div class="w-full ">
															<p class="text-center text-xl font-semibold mb-3">Form Jadwal</p>  
															<div class="w-full">
																<div class="mt-4">
																	<x-input-label for="name" :value="__('Nama Lengkap')" />
																	<input type="text" name="user_id" id="dataUserId" value="{{ $us->id }}"
																		class="input input-bordered hidden" required>
																	<input type="text" id="namaUser" disabled value="{{ $us->nama_lengkap }}"
																		class="input input-bordered w-full">
																	<x-input-error :messages="$errors->get('name')" class="mt-2" />
																</div>
																<div class="mt-4">
																	<x-input-label for="shift_id" :value="__('Shift')" />
																	<select name="shift_id" id="shift_id" class="select select-bordered font-thin w-full" required>
																		<option disabled selected>~ Pilih Shift ~</option>
																		@forelse ($shift as $shi)
																			@if ($us->kerjasama->client_id == $shi->client_id && $us->divisi->jabatan_id == $shi->jabatan_id)
																				<option value="{{ $shi->id }}">{{ $shi->jam_start }}|{{ $shi->shift_name }}</option>
																			@endif
																		@empty
																		@endforelse
																	</select>
																	<x-input-error :messages="$errors->get('shift_id')" class="mt-2" />
																</div>
																<div class="mt-4">
																	<x-input-label for="tanggal" :value="__('Tanggal')" />
																	<input type="date" id="tanggal" name="tanggal" min="{{ $dataAwal }}"
																		max="{{ $dataAkhir }}" class="input input-bordered w-full" required>

																	<x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
																</div>
																<div class="mt-4">
																	<x-input-label for="area" :value="__('Nama Area')" />
																	<select name="area" id="area" class="select select-bordered font-thin w-full" required>
																		<option disabled selected>~ Pilih Area ~</option>
																		@forelse ($area as $ar)
																			<option value="{{ $ar->id }}">{{ $ar->nama_area }}</option>
																		@empty
																			<option class="text-center" disabled>Area Masih Kosong</option>
																		@endforelse
																	</select>
																	<x-input-error :messages="$errors->get('area')" class="mt-2" />
																</div>
																<div class="mt-4">
																	<x-input-label for="status" :value="__('Status')" />
																	<select name="status" id="status" class="select select-bordered font-thin w-full" required>
																		<option disabled selected>~ Pilih Status ~</option>
																		<option value="M">M</option>
																		<option value="OFF">Libur</option>
																	</select>
																	<x-input-error :messages="$errors->get('status')" class="mt-2" />
																</div>
															</div>
														</div>
														<div class="flex justify-center sm:justify-end mt-5">
															<button id="submitJadwal" type="submit" class="btn btn-primary">Simpan</button>
														</div>
													</form>
												@else
													<form method="POST" action="{{ route("storeJadwalLeader") }}" class="p-5 w-full mt-4" id="formA">    
														@csrf
														<div class="w-full ">
															<p class="text-center text-xl font-semibold mb-3">Form Jadwal</p>  
															<div class="w-full">
																<div class="mt-4">
																	<x-input-label for="name" :value="__('Nama Lengkap')" />
																	<input type="text" name="user_id" id="dataUserId" value="{{ $us->id }}"
																		class="input input-bordered hidden">
																	<input type="text" id="namaUser" disabled value="{{ $us->nama_lengkap }}"
																		class="input input-bordered w-full">
																	<x-input-error :messages="$errors->get('name')" class="mt-2" />
																</div>
																<div class="mt-4">
																	<x-input-label for="shift_id" :value="__('Shift')" />
																	<select name="shift_id" id="shift_id" class="select select-bordered font-thin w-full">
																		<option disabled selected>~ Pilih Shift ~</option>
																		@forelse ($shift as $shi)
																			@if ($us->kerjasama->client_id == $shi->client_id && $us->divisi->jabatan_id == $shi->jabatan_id)
																				<option value="{{ $shi->id }}">{{ $shi->jam_start }}|{{ $shi->shift_name }}</option>
																			@endif
																		@empty
																		@endforelse
																	</select>
																	<x-input-error :messages="$errors->get('shift_id')" class="mt-2" />
																</div>
																<div class="mt-4">
																	<x-input-label for="tanggal" :value="__('Tanggal')" />
																	<input type="date" id="tanggal" name="tanggal" min="{{ $dataAwal }}"
																		max="{{ $dataAkhir }}" class="input input-bordered w-full">

																	<x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
																</div>
																<div class="mt-4">
																	<x-input-label for="area" :value="__('Nama Area')" />
																	<select name="area" id="area" class="select select-bordered font-thin w-full">
																		<option disabled selected>~ Pilih Area ~</option>
																		@forelse ($area as $ar)
																			<option value="{{ $ar->id }}">{{ $ar->nama_area }}</option>
																		@empty
																			<option class="text-center" disabled>Area Masih Kosong</option>
																		@endforelse
																	</select>
																	<x-input-error :messages="$errors->get('area')" class="mt-2" />
																</div>
																<div class="mt-4">
																	<x-input-label for="status" :value="__('Status')" />
																	<select name="status" id="status" class="select select-bordered font-thin w-full">
																		<option disabled selected>~ Pilih Status ~</option>
																		<option value="M">M</option>
																		<option value="OFF">Libur</option>
																	</select>
																	<x-input-error :messages="$errors->get('status')" class="mt-2" />
																</div>
															</div>
														</div>
														<div class="flex justify-center sm:justify-end mt-5">
															<button id="submitJadwal" type="submit" class="btn btn-primary">Simpan</button>
														</div>
													</form>
												@endif
											</div>
										</div>
								</td>
							</tr>
						@endif
					@empty

					@endforelse
				</tbody>
			</table>
		</div>
		<div class="flex justify-center sm:justify-end my-5">
		    @if(Auth::user()->divisi->code_jabatan == "CO-CS")
			    <a href="{{ route('leaderView') }}" class="btn btn-error">Kembali</a>
		    @else
			    <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
		    @endif
		</div>
	</div>
</x-main-div>

<script>
	$(document).ready(function() {
		$('[id^="myModalBtnJadwal"]').on('click', function() {
			var userId = $(this).data('user-id');
			var userName = $(this).data('user-name');
			var modalContent = $('#myModalContent' + userId);
			var adminId = $("#adminId").data('admin');
			// console.log(userId, userName, modalContent);
			function updateJadwal(userId) {
				$.ajax({
					type: 'GET',
					url: '/getJadwal/' + userId,
					success: function(response) {
						var scheduleHtml = '';
						var starte = $("#starte").data("starte");
						var ende = $("#ende").data("ende");
						var uniqueDates = [];
						// console.log(response);

						for (let date = new Date(starte); date.toISOString().split('T')[0] <=
							new Date(ende).toISOString().split('T')[0]; date.setDate(
								date
								.getDate() + 1)) {
							var dataFound = false;
							var formatedDate = date.toISOString().split('T')[0];
							// console.log(new Date(starte).toISOString().split('T')[0], new Date(
							// 	ende).toISOString().split('T')[0], response.length);

							if (response.length > 0) {
								var scheduleFound = false;

								response.forEach(function(schedule) {
									var dateT = new Date(schedule.tanggal);
									var data = dateT.getDate();


									if (schedule.user_id == userId && formatedDate ==
										schedule.tanggal && schedule.status == 'M') {
										// console.log(schedule.user_id, userId,
										// 	formatedDate, schedule.tanggal);

										if (!uniqueDates.includes(formatedDate)) {
											uniqueDates.push(
												formatedDate);
											scheduleHtml +=
												'<div class="flex justify-center items-center">' +
												'<span class="py-1 px-2 sm:py-2 sm:px-4 rounded-lg font-bold bg-sky-400 w-fit text-[8px] sm:text-xs">' +
												date.getDate() +
												'</span>' +
												'</div>';
											dataFound = true;
											scheduleFound = true;
										}
									} else if (schedule.user_id == userId &&
										formatedDate == schedule.tanggal && schedule
										.status == 'OFF') {

										if (!uniqueDates.includes(formatedDate)) {
											uniqueDates.push(
												formatedDate);
											scheduleHtml +=
												'<div class="flex justify-center items-center">' +
												'<span class="py-1 px-2 sm:py-2 sm:px-4 rounded-lg font-bold bg-orange-400 w-fit text-[8px] sm:text-xs">' +
												date.getDate() +
												'</span>' +
												'</div>';
											dataFound = true;
											scheduleFound = true;
										}
									} else if (!schedule){
										scheduleHtml += "kosong bg"
									}
								});
								if (!scheduleFound) {
									// Handle the case where there is no schedule for the current date
									scheduleHtml +=
										'<div class="flex justify-center items-center ">' +
										'<span class="py-1 px-2 sm:py-2 sm:px-4 rounded-lg font-bold bg-slate-300 w-fit text-[8px] sm:text-xs">' +
										date.getDate() +
										'</span>' +
										'</div>';
									dataFound = false;
								}

							} else {
								if (!dataFound) {
									scheduleHtml +=
										'<div class="flex justify-center items-center">' +
										'<span class="py-1 px-2 sm:py-2 sm:px-4 rounded-lg font-bold bg-slate-300 w-fit text-[8px] sm:text-xs">' +
										date.getDate() +
										'</span>' +
										'</div>';
								}
							}
						}
						// console.log(scheduleHtml);

						$('#jadwalContainer' + userId).html(scheduleHtml);
					},

					error: function() {
						// Handle errors if the request fails
					}
				});
			}
			updateJadwal(userId)
			// Populate the modal with user-specific data (e.g., user's name)
			modalContent.find('#judulNamaUser').html(userName);
			modalContent.find('#judulNamaUserTd').html(
				userName);

			// TODO: Populate the table with the user's schedule data

			// Show the modal
			modalContent.removeClass('hidden').addClass('inset-0 z-[9999]');
			
			$(document).on("click", "#submitJadwal", function() {
				// console.log("CLICK", $("#formA")[0]);
				updateJadwal(userId);
				updateJadwal(userId);
				// $("#formA")[0].reset();
				
			})
		});
		var userId = $('[id^="myModalBtnJadwal"]').data('user-id');

		$(document).on("click", ".close", function() {
			var modal = $('#myModalContent' + userId);
			modal.removeClass(' inset-0 z-[99]');
			modal.addClass('hidden');
		});
	});
</script>

</x-app-layout>
