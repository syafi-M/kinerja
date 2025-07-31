<x-app-layout>
	<x-main-div>
	    	@php
    			$starte = \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString();;
    			$ende = \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth();
    		@endphp
		<div class="py-10 sm:mx-10">
			<p class="text-center text-lg sm:text-2xl font-bold mb-10 uppercase">Data Jadwal Karyawan,
				{{ Auth::user()->kerjasama->client->name }}</p>
				@if(Auth::user()->role_id == 2)
        			<div class="flex flex-col justify-center items-center gap-2 md:flex-row sm:justify-between w-full">
        			    <form action="{{ route('import-jadwal') }}" method="POST" class="flex items-center gap-2 overflow-hidden" enctype="multipart/form-data">
        				    @csrf
        				    <label for="iJDW" class="btn btn-success overflow-hidden" ><i class="ri-file-excel-2-line text-lg"></i><span id="importLabel" class="overflow-hidden">Import Jadwal</span></label>
        				    <input id="iJDW" name="file" type="file" class="hidden" accept=".csv"/>
        				    <button class="btn btn-primary hidden" type="submit" id="btnImport">Import</button>
        				</form>
        				<x-search />
        			</div>
				@endif
			@if (Auth::user()->role_id == 2)
				<div class="flex  justify-center gap-2 mx-5 py-3 ">
					<form action="{{ route('jadwal_export.admin') }}" method="get"
						class="flex flex-col justify-center gap-2 bg-slate-100 rounded px-5 py-3">
						<p class="text-center font-semibold">Export Jadwal</p>
						<div class="flex justify-end mx-10 mb-2 gap-2">
							<span class="flex flex-col">

								<div>
									<label class="label text-xs sm:text-base">Mulai</label>
									<input class="input input-bordered w-full input-xs" type="date" name="str1" id="str1" required>
								</div>
								<div>
									<label class="label text-xs sm:text-base">Selesai</label>
									<input class="input input-bordered w-full input-xs" type="date" name="end1" id="end1" required>
								</div>
							</span>
							<span class="flex items-center">
								<div>
									<label class="label text-xs sm:text-base">Pilih Client</label>
									<select name="filter" class="select select-bordered w-full text-black select-sm text-xs" required>
										<option class="disabled">~Pilih Client~</option>
										@forelse($kerj as $i)
											<option value="{{ $i->id }}">{{ $i->client->name }}</option>
										@empty
											<option class="disabled">~ Client Kosong ~</option>
										@endforelse
									</select>
								</div>
							</span>
						</div>
						<span class="flex justify-end mx-10">
							<button type="submit" class="bg-yellow-400 px-3 py-2 shadow rounded-md text-2xl flex items-center gap-2"
								style="margin-bottom: 3rem;">
								<p class="text-sm font-semibold">Print PDF</p>
								<i class="ri-file-download-line"></i>
							</button>
						</span>
					</form>
				</div>
			@elseif(Auth::user()->divisi->jabatan->code_jabatan == 'MITRA')
			@elseif(Auth::user()->divisi->jabatan->code_jabatan == 'LEADER')
				<div class="flex  justify-center gap-2 mx-5 py-3 ">
					<form action="{{ route('store.processDate') }}" method="GET"
						class="flex flex-col justify-center gap-2 bg-slate-100 rounded px-5 py-3">
						<p class="text-center font-semibold">Buat Jadwal</p>
						<div class="flex justify-between flex-col md:flex-row gap-2 ">
							<div>
								<label class="label text-xs sm:text-base">Mulai</label>
								<input class="input input-bordered w-full input-xs" type="date" name="str1" id="str1" required>
							</div>
							<div>
								<label class="label text-xs sm:text-base">Selesai</label>
								<input class="input input-bordered w-full input-xs" type="date" name="end1" id="end1" required>
							</div>
							<div>
								<label class="label text-xs sm:text-base">Pilih Divisi</label>
								<select name="divisi" class="select select-bordered w-full text-black select-sm text-xs" required>
									<option class="disabled">~Pilih Divisi~</option>
									@forelse($divisi as $i)
										<option value="{{ $i->id }}">{{ $i->jabatan->name_jabatan }}</option>
									@empty
										<option class="disabled">~Divisi Kosong~</option>
									@endforelse
								</select>
							</div>
						</div>
						<button><a class="btn btn-primary btn-sm sm:btn-md">+ Jadwal</a></button>

					</form>
				</div>
			@endif

			{{-- <div class="flex justify-center mx-2 pb-10 text-xs">
				<div class="overflow-x-auto md:overflow-hidden w-full">
					<table class="table table-zebra w-full table-sm bg-slate-50 overflow-auto shadow-md text-xs md:text-base"   
						id="searchTable">
						<thead class="text-slate-700 text-center">
    						<tr>
    							<th class="bg-slate-300 rounded-tl-2xl px-5">#</th>
    							<th class="bg-slate-300">Nama Lengkap</th>
    							@if(Auth::user()->divisi->jabatan->code_jabatan != "CO-CS")
    							    <th class="bg-slate-300">Mitra</th>
    							@endif
    							<th class="bg-slate-300 rounded-tr-2xl">Lihat Jadwal</th>
    						</tr>
    					</thead>
    					<tbody>
					    	@php
								$no = 1;
							@endphp
							@forelse($user as $us)
							        <tr>
										<td class="p-1 text-center">{{ $no++ }}</td>
										<td class="p-1">{{ $us->nama_lengkap }}</td>
										@if(Auth::user()->divisi->jabatan->code_jabatan != "CO-CS")
										    <td class="p-1 ">{{ $us->kerjasama->client->name }}</td>
										@endif
										<td class="p-1 flex justify-center">
										    <span hidden id="jadwalUserId"></span>
    										<button id="myModalBtnJadwal{{ $us->id }}" data-user-id="{{ $us->id }}"
    											data-user-name="{{ $us->nama_lengkap }}"
    											class="btn btn-sm btn-info text-xs overflow-hidden text-slate-800 border-slate-200 hover:border-slate-400/70">
    											Jadwal</button>
    										<span id="data-orang" data-user-id="{{ $us->id }}" data-user-name="{{ $us->nama_lengkap }}"
    											hidden></span>
    											<div id="myModalContent{{ $us->id }}" class="hidden fixed modalz overflow-hidden">
    											<!-- Your modal content here -->
    											<div class="flex justify-center bg-slate-500/10 backdrop-blur-sm items-center min-h-screen rounded-md overflow-auto">
    												<div class="bg-slate-200 w-full pb-10 my-10 mx-10 rounded-md shadow overflow-auto">
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
										</td>
										
									</tr>
									<div class="mx-5">
                                        <span id="formA" class="p-5 mt-4 bg-slate-50 rounded hidden min-h-screen" data-user-name="{{ $us->nama_lengkap }}" style="width: 80%;">
                                            <div class="w-full min-h-screen">
                                                <div class="flex justify-end mt-3 mb-1">
                                                    <button class="btn btn-error closeF">&times;</button>
                                                </div>
                                                <form method="POST" id="jadwalForm" class="p-5 w-full mt-4">
                                                    @csrf
                                                    <div class="rounded-lg h-full">
                                                        <div class="flex flex-col justify-center w-full">
                                                            <div class="w-full">
                                                                <x-input-label for="user_id" :value="__('Nama Karyawan')" />
                                                                <input type="text" disabled id="pre_user_id" value="" class="input input-bordered w-full disabled" />
                                                                <input type="text" name="user_id" id="user_id" value="" class="input input-bordered w-full hidden" />
                                                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                                                            </div>
                                                            <div class="w-full">
                                                                <x-input-label for="shift_id" :value="__('Nama Shift')" />
                                                                <select name="shift_id" id="shift_id" data-shift="{{ $shift }}" class="select select-bordered w-full">
                                                                    <option selected disabled>~ Pilih Shift ~</option>
                                                                </select>
                                                                <x-input-error :messages="$errors->get('shift_id')" class="mt-2" />
                                                            </div>
                                                            <div class="w-full">
                                                                <x-input-label for="tanggal" :value="__('Tanggal')" />
                                                                <input type="date" disabled id="pre_tanggal" value="" class="input input-bordered w-full"/>
                                                                <input type="date" name="tanggal" id="tanggal" value="" class="input input-bordered w-full hidden"/>
                                                                <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                                                            </div>
                                                            <div class="w-full">
                                                                <x-input-label for="area_id" :value="__('Nama Area')" />
                                                                <select name="area" id="area_id" data-shift="{{ $area }}" class="select select-bordered w-full">
                                                                    <option selected disabled>~ Pilih Area ~</option>
                                                                </select>
                                                                <x-input-error :messages="$errors->get('area_id')" class="mt-2" />
                                                            </div>
                                                            <div class="w-full">
                                                                <x-input-label for="status" :value="__('Status')" />
                                                                <select name="status" id="status" class="select select-bordered w-full">
                                                                    <option selected disabled>~ Pilih Status ~</option>
                                                                    <option value="M">Masuk</option>
                                                                    <option value="OFF">Off</option>
                                                                </select>
                                                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                                            </div>
                                                        </div>
                                                        <div class="flex justify-center sm:justify-end mt-5">
                                                            <button id="submitJadwal" type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </span>
                                    </div>

							@empty
							    <tr>
							        <td class="text-center" colspan="5">Kosong</td>
							    </tr>
							@endforelse
    					</tbody>
				</table>
			</div>
		</div> --}}
		
		<div>
		    <a href="{{ route('leader-jadwal.create', ['hari' => 'senin']) }}">Senin</a>
		</div>
		
		<div class="flex justify-center sm:justify-end my-5">
		    @if(Auth::user()->divisi->code_jabatan == "CO-CS")
			    <a href="{{ route('leaderView') }}" class="btn btn-error">Kembali</a>
		    @elseif(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR")
			    <a href="{{ route('danruView') }}" class="btn btn-error">Kembali</a>
		    @else
			    <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
		    @endif
		</div>
	</div>
</x-main-div>
    <script>
	$(document).ready(function() {
	    
	    $('#jadwalForm').on('submit', function(e) {
	        e.preventDefault(); // Prevent the default form submission

            // Serialize form data
            var formData = $(this).serialize();
            
            var roles = <?php echo json_encode(Auth::user()->role_id); ?>;
            var url;

            if (roles == 2) {
                url = '{{ route("storeJadwalAdmin") }}';
            } else {
                url = '{{ route("storeJadwalLeader") }}';
            }

            // AJAX POST request
            $.ajax({
                type: 'POST',
                url: url, // Replace this with your route URL
                data: formData,
                success: function(response) {
                    // Handle the success response
                    // console.log('Success:', response);
                	var userId = $('[id^="myModalBtnJadwal"]').data('user-id');
                    // updateJadwal(userId)
                    var modal = $('#formA');
                    modal.fadeOut();
                },
                error: function(error) {
                    // Handle the error
                    // console.log('Error:', error);
                    // You can display an error message or handle the error as needed
                }
            });
	    })
	    
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

						for (let date = new Date(starte); date <= new Date(ende); date.setDate(date.getDate() + 1)) {
							var dataFound = false;
							var formatedDate = date.toISOString().split('T')[0];
							
				// 			console.log(formatedDate);

							if (response.length > 0) {
								var scheduleFound = false;

								response.forEach(function(schedule) {
									var dateT = new Date(schedule.tanggal);
									var data = dateT.getDate();
								// 	console.log(dateT);


									if (schedule.user_id == userId && formatedDate == dateT.toISOString().split('T')[0] && schedule.status == 'M') {
								// 		console.log(schedule.user_id, userId,
								// 			formatedDate, schedule.tanggal);

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
									} else if (schedule.user_id == userId && formatedDate == dateT.toISOString().split('T')[0] && schedule.status == 'OFF') {

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
										`<button data-tanggal=${formatedDate} id="btnTambahJadwal" class="py-1 px-2 sm:py-2 sm:px-4 rounded-lg font-bold bg-slate-300 w-fit text-[8px] sm:text-xs">` +
										date.getDate() +
										'</button>' +
										'</div>';
									dataFound = false;
								}

							} else {
								if (!dataFound) {
									scheduleHtml +=
										'<div class="flex justify-center items-center">' +
										`<button data-tanggal=${formatedDate} id="btnTambahJadwal" class="py-1 px-2 sm:py-2 sm:px-4 rounded-lg font-bold bg-slate-300 w-fit text-[8px] sm:text-xs">` +
										date.getDate() +
										'</button>' +
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
			$(document).on("click", "#btnTambahJadwal", function() {
                var shifts = <?php echo json_encode($shift); ?>;
                var users = <?php echo json_encode(Auth::user()); ?>;
                var users_id = <?php echo json_encode(Auth::user()->id); ?>;
                var area = <?php echo json_encode($area); ?>;
                
                var now = new Date();
                var yearMonth = now.toISOString().slice(0, 7);
                var getDate = $(this).data('tanggal');
				
                var selectOptions = '<option selected disabled>~ Pilih Shift ~</option>';
            
                $.each(shifts, function(index, shift) {
                    if(shift.client_id == users.kerjasama.client_id && shift.jabatan_id == users.divisi.jabatan_id){
                        selectOptions += '<option value="' + shift.id + '">' + shift.jam_start + ' - ' + shift.jam_end + '|' + shift.shift_name + '</option>';
                    }
                });
                
                // console.log(area);
            
				$('#user_id').val(userId);
				$('#pre_user_id').val(userName);
                $('#shift_id').html(selectOptions);
                $('#tanggal').val(getDate);
                $('#pre_tanggal').val(getDate);
                
                var areaOptions = '<option selected disabled>~ Pilih Area ~</option>';
                $.each(area, function(index, are) {
                    if (users.kerjasama_id == are.kerjasama_id) {
                        areaOptions += '<option value="' + are.id + '">' + are.nama_area + '</option>';
                    }
                });
                
                $('#area_id').html(areaOptions);    
                $("#formA").fadeIn();
				$("#formA")
				    .removeClass('hidden')
				    .addClass('inset-0 z-[10000]')
				    .css({
                        'z-index': '10000',
                        'position': 'fixed',  // Set the position to fixed
                        'top': '50%',         // Position from top
                        'left': '50%',        // Position from left
                        'transform': 'translate(-50%, -50%)'
                    });
				
			})
		});
		var userId = $('[id^="myModalBtnJadwal"]').data('user-id');

		$(document).on("click", ".close", function() {
			var modal = $('#myModalContent' + userId);
			modal.removeClass(' inset-0 z-[99]');
			modal.addClass('hidden');
		});
		$(document).on("click", ".closeF", function() {
			var modal = $('#formA');
			modal
			    .addClass('hidden')
			    .removeClass('inset-0 z-[10000]')
			    .css({
                    'z-index': '10000',
                    'position': 'fixed',  // Set the position to fixed
                    'top': '50%',         // Position from top
                    'left': '50%',        // Position from left
                    'transform': 'translate(-50%, -50%)'
                });
		});
		
        $('#iJDW').on('change', function () {
            var fileInput = $(this);
            var importLabel = $('#importLabel');
            var submitButton = $('#btnImport');

            if (fileInput.val()) {
                importLabel.text('Klik import');
                submitButton.removeClass('hidden');
            } else {
                importLabel.text('Import Jadwal');
                submitButton.addClass('hidden');
            }
        });
	});
</script>
    
</x-app-layout>
