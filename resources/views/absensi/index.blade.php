<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>{{ env('APP_NAME', 'KINERJA SAC-PONOROGO') }}</title>
		<link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon">

		<link rel="preload" href="https://fonts.bunny.net">
		<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
			integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
	    <script src="{{ URL::asset('src/js/jquery-min.js') }}"></script>
		<!-- Scripts -->
		@vite(['resources/css/app.css', 'resources/js/app.js'])

		{{-- Leaflet --}}
		<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
			integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
			integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

		<link href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css" rel="stylesheet">
		<style>
			#map {
				height: 180px;
			}
			
		</style>

	</head>

<body class="font-sans antialiased  bg-slate-400">
	<div class="min-h-screen pb-[12.5rem]">
		@include('../layouts/navbar')
		<div class="sm:mx-10 mx-5 bg-slate-500 rounded-md shadow-md">
			<main>
				<div class="px-5 py-5">
			     {{-- @if ($errors->any())
                    <div class="text-red-500 bg-slate-200 rounded-md">
                        <p>{{ $errors->shift_id }}</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li> - {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}
					<form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data" id="form-absen">
						@method('POST')
						@csrf
						@if(Auth::user()->kerjasama_id != 1)
    						<div class="flex flex-col  sm:m-0 items-center  justify-center">
    						    <div class="relative">
    							    <video id="video" style="scale: 70%;" class="bg-slate-200 p-5 rounded-md square-video" autoplay></video>
    						        <svg style="position: absolute; display: none; padding: 3vw; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 55vw; height: auto; opacity: 30%; z-index: 1;" class="svg-icon-foto" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M42.666667 217.6c-12.8 0-21.333333-8.533333-21.333334-21.333333V87.466667C21.333333 51.2 51.2 21.333333 87.466667 21.333333H149.333333c12.8 0 21.333333 8.533333 21.333334 21.333334s-10.666667 21.333333-21.333334 21.333333H87.466667C74.666667 64 64 74.666667 64 87.466667v108.8c0 12.8-8.533333 21.333333-21.333333 21.333333zM149.333333 1002.666667H87.466667C51.2 1002.666667 21.333333 972.8 21.333333 936.533333v-98.133333c0-12.8 8.533333-21.333333 21.333334-21.333333s21.333333 8.533333 21.333333 21.333333v98.133333c0 12.8 10.666667 23.466667 23.466667 23.466667H149.333333c12.8 0 21.333333 8.533333 21.333334 21.333333s-10.666667 21.333333-21.333334 21.333334zM936.533333 1002.666667H874.666667c-12.8 0-21.333333-8.533333-21.333334-21.333334s8.533333-21.333333 21.333334-21.333333h61.866666c12.8 0 23.466667-10.666667 23.466667-23.466667v-98.133333c0-12.8 8.533333-21.333333 21.333333-21.333333s21.333333 8.533333 21.333334 21.333333v98.133333c0 36.266667-29.866667 66.133333-66.133334 66.133334zM981.333333 217.6c-12.8 0-21.333333-8.533333-21.333333-21.333333V87.466667c0-12.8-10.666667-23.466667-23.466667-23.466667H874.666667c-12.8 0-21.333333-8.533333-21.333334-21.333333s8.533333-21.333333 21.333334-21.333334h61.866666C972.8 21.333333 1002.666667 51.2 1002.666667 87.466667v108.8c0 12.8-8.533333 21.333333-21.333334 21.333333zM512 537.6c-106.666667 0-192-87.466667-192-192 0-106.666667 87.466667-192 192-192s192 87.466667 192 192c0 106.666667-87.466667 192-192 192z m0-341.333333c-83.2 0-149.333333 68.266667-149.333333 149.333333s68.266667 149.333333 149.333333 149.333333c83.2 0 149.333333-68.266667 149.333333-149.333333s-68.266667-149.333333-149.333333-149.333333zM759.466667 832H262.4c-21.333333 0-40.533333-10.666667-51.2-27.733333-12.8-17.066667-12.8-40.533333-6.4-59.733334C245.333333 650.666667 362.666667 554.666667 512 554.666667s266.666667 96 305.066667 189.866666c8.533333 19.2 6.4 42.666667-6.4 59.733334-10.666667 17.066667-29.866667 27.733333-51.2 27.733333zM512 597.333333c-128 0-232.533333 85.333333-266.666667 164.266667-2.133333 6.4-2.133333 12.8 2.133334 19.2 2.133333 6.4 8.533333 8.533333 14.933333 8.533333h497.066667c6.4 0 12.8-2.133333 17.066666-8.533333 4.266667-6.4 4.266667-12.8 2.133334-19.2C744.533333 682.666667 640 597.333333 512 597.333333z"  /></svg>
    						    </div>
    							<canvas id="canvas"  style="display:none;"></canvas>
    							<div id="results" class=" sm:mt-0 rounded mb-3"></div>
    							
        						@if($errors->image)
    							    <!--<p class=" font-bold bg-white text-start p-1 rounded-lg" style="color: red">Foto Tidak Boleh Kosong</p>-->
    							@endif
    						</div>
    						
    
    						<div class="flex justify-center">
    							<button type=button id="snapButton" class="p-2 my-2 px-3 mb-5 text-white bg-blue-400 rounded-full"><i
    									class="ri-camera-fill"></i></button>
    						</div>
    						
						@endif
						@if($cekTukar)
    						@php
    						    $isNow = Carbon\Carbon::now()->format("H:i");
    						    $tesx = Carbon\Carbon::createFromFormat('H:i', $cekTukar->shift?->jam_end)->subHour(2)->format('H:i');
    						@endphp
						@endif
						@if($cekTukar && $isNow <= $tesx)
    						@if($cekTukar?->tukar_id == Auth::user()->id)
    						    <div class="p-1 rounded my-3 bg-slate-50 text-center">
    						        <p class="font-medium">Shift Anda Telah Tergantikan Oleh <span style="text-decoration: underline; text-transform: capitalize; font-weight: 600;">{{ $cekTukar->user->nama_lengkap }}</span></p>
    						    </div>
    						@endif
						@endif
						<div class="p-1 rounded my-3 ">
						    <label class="required text-white">Map : </label>
						    <span id="labelMap" class="text-white text-center flex flex-col justify-center">
						        <p>Pastikan map sudah muncul !!</p>
						        <p id="resolver">coba refresh browser beberapa kali jika map belum muncul</p>
						    </span>
							<div id="map" class="rounded"></div>
							<span id="tutor" class="text-white hidden text-center flex flex-col justify-center text-sm capitalize" style="font-style: italic;">
							    <p id="latlongLabel" style="font-size: 8px;"></p>
							    <p>Pastikan tanda biru berada dilingkaran</p>
							</span>
						</div>
						<div class="flex flex-col gap-2">
							<div class="flex flex-col justify-between">
								<label for"name" class="text-white">{{Route::currentRouteName() == 'absensi-karyawan-co-cs.index' || Route::currentRouteName() == 'absensi-karyawan-co-scr.index' ? "Pilih Nama: " : "Nama: "}}</label>
								@if(Route::currentRouteName() == 'absensi-karyawan-co-cs.index' || Route::currentRouteName() == 'absensi-karyawan-co-scr.index')
								    <select name="user_id" id="selectUser" class="select select-bordered">
								        <option selected value="{{ Auth::user()->id }}" class="op" data-clien="{{ Auth::user()->kerjasama->client_id }}">{{ Auth::user()->nama_lengkap }}</option>
								        @foreach($userL as $us)
								            <option value="{{ $us->id }}" class="op" data-divisi="{{ $us->devisi_id }}" data-clien="{{ $us->kerjasama->client_id }}" data-jab="{{ $us->divisi?->jabatan_id }}">{{ $us->nama_lengkap }}</option>
								        @endforeach
								    </select>
								@else
    								<input type="text" id="user_id" name="user_id" value="{{ Auth::user()->id }}" hidden>
    								<input type="text" id="name" name="{{ Auth::user()->name }}" value="{{ Auth::user()->nama_lengkap }}" disabled class="input input-bordered">
								@endif
							</div>
							<div class="flex flex-col  justify-between">
								<label for="kerjasama" class="text-white">Bermitra Dengan: </label>
								<input type="text" name="kerjasama_id" id="kerjasama_id" hidden value="{{ Auth::user()->kerjasama_id }}">
								<input type="text" id="kerjasama" name="{{ Auth::user()->kerjasama->client->name }}" value="{{ Auth::user()->kerjasama->client->name }}" disabled
									class="input input-bordered">
							</div>
							@if(Auth::user()->kerjasama_id == 1)
							<input type="text" name="masuk" value="1" class="hidden"/>
							@endif
							@if(Auth::user()->kerjasama_id != 1)
    							<div class="flex flex-col justify-start">
        							<x-input-label for="jenis_abs" class="text-white text-lg" :value="__('Jenis Absen: ')" />
        							<div class="flex flex-col justify-start bg-slate-50 rounded-lg">
                						<div>
                                            <input type="radio" id="type_absen" data-pilih="masuk" name="jenis_abs" value="1"
                                                class="radio radio-sm m-2" 
                                                {{ (isset($cekAbsen) && count($cekAbsen) > 0 && count($cekAbsen) <= 2 && isset($cekPulang) && $cekPulang->absensi_type_pulang != null) ? 'disabled' : 'checked="checked"' }}
                                                >
                                            <label for="masuk" class="overflow-hidden">Masuk</label>
                                        </div>
                						<div>
                                            <input type="radio" id="type_absen" data-pilih="tukar" name="jenis_abs" value="1"
                                                class="radio radio-sm m-2">
                                            <label for="tukar" class="overflow-hidden">Tukar Shift</label>
                                        </div>
                						<div>
                                            <input type="radio" id="type_absen" data-pilih="lembur" name="jenis_abs" value="1"
                                                class="radio radio-sm m-2" disabled>
                                            <label for="lembur" class="overflow-hidden">Lembur (maintenance)</label>
                                        </div>
                                        @if(count($cekAbsen) > 0 && count($cekAbsen) <= 2 && $cekPulang->absensi_type_pulang != null )
                    						<div>
                                                <input type="radio" id="type_absen" data-pilih="terus" name="jenis_abs" value="1"
                                                    class="radio radio-sm m-2" >
                                                <label for="terus" class="overflow-hidden">Meneruskan Shift</label>
                                            </div>
                                        @endif
                                        
                                        <div id="type_absen_div" class="hidden">
                                            <input type="text" name="masuk" value="1" class="hidden"/>
                                        </div>
        							</div>
                                </div>
                                <div class="w-full" id="divPengganti" style="display: none;">
                                    <x-input-label for="pengganti" class="text-white text-lg" :value="__('Pengganti: ')" />
                                    <select name="pengganti" id="pengganti" required style="{{$errors->any() && $errors->pengganti ? 'border: 2px solid red;' : ''}}" class="select select-bordered font-thin w-full">
                                        @if($errors->any() && $errors->pengganti)
    								        <option selected disabled class="p-1 my-1 font-bold" style="color: red">Pengganti Tidak Boleh Kosong</option>
        								@endif
    									<option disabled {{ $errors->any() && $errors->pengganti ? '' : 'selected' }}>-- Pilih Pengganti --</option>
    									@forelse($pengganti as $peng)
    									    <option value="{{ $peng->id }}">{{ $peng->nama_lengkap }}</option>
    									@empty
    									    <option>Belum Ada Karyawan</option>
    									@endforelse
                                    </select>
                                </div>
							@endif
							@if(Auth::user()->divisi->jabatan->name_jabatan != "DIREKSI")
    							<div class="flex flex-col  justify-between">
    								<label class="required text-white" for="shift_id">Shift: </label>
    								<select name="shift_id" id="shift_id" {{ Auth::user()->name == "DIREKSI" ? '' : 'required' }}  style="{{$errors->any() && $errors->shift_id ? 'border: 2px solid red;' : ''}}" class="select select-bordered font-thin">
        							    @if($errors->any() && $errors->shift_id)
                            		        <option selected disabled class="p-1 my-1 font-bold" style="color: red">Shift Tidak Boleh Kosong</option>
                            			@endif
                            			<option disabled {{ $errors->any() && $errors->shift_id ? '' : 'selected' }}>-- Pilih Shift --</option>
                            			@php
                            			
                            			@endphp
                            			@forelse ($shift as $i)
                            				@php
                            				    $endA = Carbon\Carbon::parse($i->jam_end)->subHour(1)->format('H:i');
                            				@endphp
                            				@if($cekPulang && $cekPulang->shift_id != $i->id)
                            					<option value="{{ $i->id }}" data-shift="{{ $i->jam_start }}">{{ $i->jam_start }} - {{ $endA }} | {{ $i->jabatan->name_jabatan }} |
                            						{{ $i->shift_name }}
                            						</option>
                            				@elseif(!$cekPulang && count($cekAbsen) == 0)
                            				    <option value="{{ $i->id }}" data-shift="{{ $i->jam_start }}">{{ $i->jam_start }} - {{ $endA }} | {{ $i->jabatan->name_jabatan }} |
                            						{{ $i->shift_name }}
                            						</option>
                            				@endif
                            			@empty
                            				<option readonly disabled>~ Tidak ada Shift ! ~</option>
                            			@endforelse
    								</select>
    								
    								@if(Auth::user()->kerjasama->client_id == 1)
    								    <span id="absen-kantor" data-absen-kantor="{{ Auth::user()->kerjasama->client_id }}" hidden></span>
    								@endif
    							</div>
    						@else
    						    <input type="hidden" name="shift_id" value="145" />
							@endif

							<div>
								<div>
									<label class="required text-white">Perlengkapan: </label>
								</div>
								<div class="p-2 bg-white rounded-lg " style="{{$errors->any() && $errors->shift_id ? 'border: 2px solid red;' : ''}}">
							    	@if($errors->any() && $errors->perlengkapan)
    								    <p  class="p-1 my-1 font-bold" style="color: red">Perlengkapan Tidak Boleh Kosong</p>
    								@endif
									<div id="divPerlengkapan" class="grid grid-cols-1">
										@forelse ($dev as $arr)
											@if (Auth::user()->devisi_id == $arr->id)
												@foreach ($arr->perlengkapan as $i)
													<div>
														<input type="checkbox" name="perlengkapan[]" id="perlengkapan {{ $i->id}}" value="{{ $i->name }}"
															class="checkbox checkbox-sm m-2 perle">
														<label for="perlengkapan {{ $i->id}}">{{ $i->name }}</label>
													</div>
												@endforeach
											@else
											@endif
										@empty
											<p>~ Kosong ~</p>
										@endforelse
									</div>
								</div>
							
							</div>
							<div>
								<label class="text-white" for="deskripsi">Deskripsi (opsional) : </label>
								<textarea name="deskripsi" id="deskripsi" value="" placeholder="deskripsi..."
								 class="w-full textarea textarea-bordered"></textarea>
							</div>
							<div class="flex flex-col">
                                @php
                                    $today = Carbon\Carbon::now()->format('Y-m-d');
                                    $hasJadwal = false;
                                @endphp
                            
                                @forelse ($jadwal as $jad)
                                    @php
                                        $tanggalJDW = Carbon\Carbon::createFromFormat('Y-m-d', $jad->tanggal)->isoFormat('dddd, D MMMM YYYY');
                                        $jadiTGL = Carbon\Carbon::createFromFormat('Y-m-d', $jad->tanggal)->format('Y-m-d');
                                    @endphp

                                    @if (!$hasJadwal)
                                        @if (Carbon\Carbon::now()->format('Y-m-d') == $jadiTGL)
                                            <label>Jadwal Hari ini: </label>
                                            @if ($jad->status == 'OFF')
                                                <span style="height: auto;" class="input input-bordered" disabled>
                                                    Tanggal:    {{ $tanggalJDW }}, <br/>
                                                    Shift:      {{ $jad->shift->shift_name }}, <br/> 
                                                    Area:       <span class="text-red-500">{{ $jad->area->nama_area }}</span>
                                                </span>
                                                @php
                                                    $hasJadwal = true;
                                                @endphp
                                            @else
                                                <span style="height: auto;" class="input input-bordered" disabled>
                                                    Tanggal:    {{ $tanggalJDW }}, <br/>
                                                    Shift:      {{ $jad->shift->shift_name }}, <br/> 
                                                    Area:       {{ $jad->area->nama_area }}
                                                </span>
                                                @php
                                                    $hasJadwal = true;
                                                @endphp
                                            @endif
                                        @endif
                                    @endif
                            
                                @empty
                                @endforelse
                            
                                @if (!$hasJadwal)
                                <label hidden>Jadwal Hari ini: </label>
                                <span class="input input-bordered flex items-center justify-center hidden">
                                    <span class=" text-center" disabled>Belum Ada Jadwal</span>
                                </span>
                                @endif
                            </div>
                            @if(Auth::user()->kerjasama_id != 1)
							    <input type="text" id="image" name="image" class="image-tag" hidden>
                            @endif
							<input type="text" id="keterangan" name="keterangan" value="masuk" data-authName="{{ Auth::user()->name }}" hidden>
						</div>
						<input type="text" class="hidden" name="absensi_type_masuk" value="1">
						@php
							$key = Auth::user()->id;
							$cekRoute = Route::currentRouteName() == 'absensi-karyawan-co-cs.index' || Route::currentRouteName() == 'absensi-karyawan-co-scr.index';
						@endphp
						<div class="flex flex-col justify-center sm:justify-end gap-3 mt-2 mr-2">
							<span id="labelWaktuStart" class="text-center text-[10px] capitalize font-semibold hidden py-2 px-4 rounded-md bg-slate-50"></span>
							<span class="flex justify-center gap-3">
								@forelse ($absensi as $abs)
									{{-- sudah --}}
									@if (!$cekRoute && $abs->tanggal_absen == Carbon\Carbon::now()->format('Y-m-d') && !$cekTukar && $abs->absensi_type_pulang == null && $abs->tukar == null && !$afaLib)
										<button
											class="p-2 my-2 px-4 text-slate-100 bg-blue-300  rounded transition-all ease-linear .2s disabled cursor-not-allowed"
											disabled>Sudah Absen</button>
									@elseif(!$cekRoute && count($cekAbsen) >= 2)
									    <button
											class="p-2 my-2 px-4 text-slate-100 bg-blue-300  rounded transition-all ease-linear .2s disabled cursor-not-allowed"
											disabled>Sudah Absen 2x</button>
									{{-- belum --}}
									@elseif(!$cekRoute && $cekTukar && $isNow <= $tesx && !$afaLib)
									    @if($cekTukar->tukar_id == Auth::user()->id)
									        <button
    											class="p-2 my-2 px-4 text-slate-100 bg-blue-300  rounded transition-all ease-linear .2s disabled cursor-not-allowed"
    											disabled>Shift Tergantikan</button>
									    @endif
									@elseif($afaLib)
									    <button
    											class="p-2 my-2 px-4 text-slate-100 bg-blue-300  rounded transition-all ease-linear .2s disabled cursor-not-allowed"
    											disabled>Jadwal Tidak Ada</button>
									@else
										<button type="button" class="p-2 my-2 px-4 text-white bg-blue-500 hover:bg-blue-600 rounded transition-all ease-linear .2s"
											id="btnAbsen">Absen</button>
									@endif
								@break

								@empty
									@if($cekTukar)
									    @if($cekTukar->tukar_id == Auth::user()->id)
									        <button
    											class="p-2 my-2 px-4 text-slate-100 bg-blue-300  rounded transition-all ease-linear .2s disabled cursor-not-allowed"
    											disabled>Shift Tergantikan</button>
									    @endif
									@else
										<button type="button" class="p-2 my-2 px-4 text-white bg-blue-500 hover:bg-blue-600 rounded transition-all ease-linear .2s"
											id="btnAbsen">Absen</button>
									@endif
								@endforelse
								<a href="{{ route('dashboard.index') }}"
									class="p-2 my-2 px-4 text-white bg-red-500 hover:bg-red-600 rounded transition-all ease-linear .2s">
									Kembali
								</a>
							</span>
						</div>
						<input class="hidden" id="thisId" value="{{ Auth::user()->id }}">
						@php
							$mytime = Carbon\Carbon::now()->format('H:m:s');
							$mytime2 = '10:00:00';
							$uID = Auth::user()->divisi->jabatan->code_jabatan;
						@endphp
						<input class="hidden" id="thisTime" value="{{ $mytime }}">
						<input class="hidden" id="thisTime2" value="{{ $mytime2 }}">
						<input class="hidden" id="isi" name="absensi_type_pulang">
						<input type="hidden" id="lat" name="lat_user" value="" class="hidden lat_user" />
						<input type="hidden" id="long" name="long_user" value="" class="hidden long_user" />
						<span class="hidden" id="dataUser" data-userId="{{ $uID }}"></span>
					</form>
				</div>
			</main>
		</div>
	</div>
	<div class="flex justify-center">
		<div class="fixed bottom-0 z-[999]">
			<x-menu-mobile />
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

	@if(Auth::user()->kerjasama_id != 1)
    	<!-- Configure a few settings and attach camera -->
        <script>
        	$(document).ready(function() {
            // Mendapatkan elemen video
        	var video = document.getElementById('video');
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d', { willReadFrequently: true });
            
            // Mengatur ukuran canvas sesuai opsi
            canvas.width = 320;
            canvas.height = 240;
        
            // Mengonfigurasi constraints untuk mendapatkan akses kamera
            var constraints = {
                audio: false,
                video: { facingMode: 'user', width: 450, height: 450 }
            };
        
            // Mengambil akses kamera
            navigator.mediaDevices.getUserMedia(constraints)
            .then(function(mediaStream) {
                // Menampilkan video dari kamera ke elemen video
                video.srcObject = mediaStream;
                video.onloadedmetadata = function(e) {
                    $('.svg-icon-foto').show();
        			canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
        			//console.log(canvas.width)
                    video.play();
                    checkVideoStatus();
                    // Memeriksa status video setiap beberapa detik
                    setInterval(function() {
                        checkVideoStatus();
                    }, 1); // Memeriksa setiap 2 detik, sesuaikan jika diperlukan
                };
        
            })
            .catch(function(err) {
                console.log('Gagal mengambil akses kamera: ' + err);
            });
        	
        	 function detectColor(data, colorThreshold) {
                var colorPixels = 0;
                for (var i = 0; i < data.length; i += 4) {
                    var red = data[i];
                    var green = data[i + 1];
                    var blue = data[i + 2];
        
                    // Periksa apakah warna piksel sesuai dengan warna yang ditetapkan
                    if (red > colorThreshold.red && green < colorThreshold.green && blue < colorThreshold.blue) {
                        colorPixels++;
                    }
                }
                return colorPixels;
            }
        	
        	// Fungsi untuk mengambil snapshot
            function takeSnapshot() {
        
                // Menggunakan ukuran yang sama dengan elemen video
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
        
                // Menggambar video pada canvas
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
                // Mengubah gambar menjadi URL data
                var dataURL = canvas.toDataURL('image/jpeg', 0.8);
                $('.image-tag').val(dataURL)
        
                // Mengirim dataURL ke backend atau melakukan hal lain sesuai kebutuhan Anda
                //console.log(dataURL);
        		document.getElementById('results').innerHTML = '<img id="imgprev" width="200" height="200" class="rounded-md" src="' + dataURL + '"/>';
            }
        	
        	$('#snapButton').click(function() {
        		takeSnapshot();
        	});
        	 
        
            // Fungsi untuk memeriksa status video
            function checkVideoStatus() {
                // Membuat elemen canvas untuk memproses gambar dari video
                    
                    // canvas.width = video.videoWidth;
                    // canvas.height = video.videoHeight;
                    
                    canvas.width = 450;
                    canvas.height = 450;
                    
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    // console.log(video);           
            
                    // Mengambil data piksel dari gambar
                    var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    var data = imageData.data;
            
                    // Menghitung jumlah piksel yang berwarna hitam (gelap)
                    var blackPixels = 0;
            		var redPixels = 0;
                    var purplePixels = 0;
                    var darkBluePixels = 0;
                    for (var i = 0; i < data.length; i += 4) {
                        // Mengecek apakah nilai rata-rata warna piksel cukup rendah (mungkin warna hitam)
                        var avgColor = (data[i] + data[i + 1] + data[i + 2]) / 3;
                        if (avgColor < 20) { // Sesuaikan nilai ambang batas sesuai kebutuhan
                            blackPixels++;
                        }
                    }
            		
            		var redPixels = 0;
                    var purplePixels = 0;
                    var darkBluePixels = 0;
            
                    // Ambang batas warna
                    var colorThresholds = {
                        red: 150,
                        green: 100,
                        blue: 100
                    };
                    // Memanggil fungsi detectColor untuk warna merah
                    redPixels = detectColor(data, colorThresholds);
            
                    // Mengganti ambang batas warna untuk warna ungu
                    colorThresholds.red = 150;
                    colorThresholds.green = 100;
                    colorThresholds.blue = 150;
            
                    // Memanggil fungsi detectColor untuk warna ungu
                    purplePixels = detectColor(data, colorThresholds);
            
                    // Mengganti ambang batas warna untuk warna biru tua
                    colorThresholds.red = 100;
                    colorThresholds.green = 100;
                    colorThresholds.blue = 150;
                    // Memanggil fungsi detectColor untuk warna biru tua
                    darkBluePixels = detectColor(data, colorThresholds);
            
                    // Memeriksa apakah terlalu banyak warna yang terdeteksi
                    if (redPixels / (canvas.width * canvas.height) > 0.2 ||
                        purplePixels / (canvas.width * canvas.height) > 0.2 ||
                        darkBluePixels / (canvas.width * canvas.height) > 0.2) {
                        alert('Terlalu banyak warna terdeteksi!');
            			$('#snapButton').hide()
                    }else{
            			$('#snapButton').show()
            		}
                    // Jika sebagian besar piksel adalah hitam, mungkin output kamera hitam
                    if (blackPixels > (canvas.width * canvas.height * 0.9)) { // 90% piksel hitam, sesuaikan jika diperlukan
                        alert('Output kamera hitam!');
            			$('#snapButton').hide();
            // 			$('#snapButton').prop('disabled', true);
                    }else{
            			$('#snapButton').show()
            		}
            		
            
                    // Menutup elemen canvas
                    canvas.remove();
            }
        });
        
        </script>
    	<!--Camera-->
	@endif
	
    <script>
        $(document).ready(function () {
            
        var selectedClientID = $('#selectUser').find(':selected').data('clien');
        var selectedJabID = $('#selectUser').find(':selected').data('jab');
        var selectedDivID = $('#selectUser').find(':selected').data('divisi');
        
        $('#selectUser').change(function(){
            selectedClientID = $(this).find(':selected').data('clien');
            selectedJabID = $(this).find(':selected').data('jab');
            selectedDivID = $(this).find(':selected').data('divisi');
            // console.log(selectedClientID);
            $.ajax({
                url: '/get-shifts/' + selectedClientID + '/' + selectedJabID,
                type: 'GET',
                success: function(data) {
                    var html = '';
                    var htmlP = '';
    
                        // console.log(data.dev);
                    if(data.shift.length > 0){
                        html += '<option disabled {{ $errors->any() && $errors->shift_id ? '' : 'selected' }}>-- Pilih Shift --</option>';
                        data.shift.forEach(function(shift) {
                            // var endA = moment(shift.jam_end).subtract(1, 'hour').format('HH:mm');
        
                            html += '<option value="' + shift.id + '" data-shift="' + shift.jam_start + '">' +
                                shift.jam_start + ' - ' + shift.jam_end + ' | ' + shift.jabatan.name_jabatan + ' | ' + shift.shift_name +
                                '</option>';
                        });
                        
                    }else{
                        html += '<option>~ Tidak ada Shift ! ~</option>';
                    }
    
                    $('#shift_id').html(html);
                    if(data.dev.length > 0){
                        data.dev.forEach(function(divisi) {
                            if(divisi.id == selectedDivID){
                                // console.log(divisi);
                                divisi.perlengkapan.forEach(function(perle){
                                    htmlP += '<div><input type="checkbox" name="perlengkapan[]" id="perlengkapan '+ perle.id +'" value="'+ perle.name +'" class="checkbox checkbox-sm m-2 perle"><label for="perlengkapan '+ perle.id +'">'+ perle.name +'</label></div>';
                                    // console.log(htmlP);
                                })
                            }
                        })
                    }else{
                        htmlP += '<p>~ Kosong ~</p>';
                    }
                    $('#divPerlengkapan').html(htmlP);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            })
            
        });
        
            
            
            
        // Event listener for checkbox change
        
        $('.radio').change(function () {
          if ($(this).prop('checked')) {
             var che = $(this).data('pilih');
            // console.log(`Checkbox is checked!, ${$(this).data('pilih')}`);
            if(che == "masuk"){
                $('#type_absen_div').html(
                    `<input type="text" name="masuk" value="1" class="hidden"/>`
                )
                $('#divPengganti').hide();
            }else if(che == "tukar"){
                $('#type_absen_div').html(
                    `<input type="text" name="tukar" value="1" class="hidden"/>`
                )
                $('#divPengganti').show();
            }else if(che == "lembur"){
                $('#type_absen_div').html(
                    `<input type="text" name="lembur" value="1" class="hidden"/>`
                )
                $('#divPengganti').hide();
            }else if(che == "terus"){
                $('#type_absen_div').html(
                    `<input type="text" name="terus" value="1" class="hidden"/>`
                )
                $('#divPengganti').hide();
            }
          }
        });
      });
    </script>
	<script>
	    function detectDevice() {
            var userAgent = navigator.userAgent.toLowerCase();
            
            if (/android/.test(userAgent)) {
                return 'Android';
            } else if (/iphone|ipad|ipod/.test(userAgent)) {
                return 'iPhone';
            } else {
                return 'Unknown'; // Return 'Unknown' for other devices
            }
        }
        
        // Example usage:
        var deviceType = detectDevice();
        // console.log('Device type:', deviceType, navigator.userAgent.toLowerCase());
        var latlngleng = 0;
        
        if(deviceType == 'Android'){
            latlngleng = 11;
        }else if(deviceType == 'iPhone'){
            latlngleng = 17;
        }else {
            latlngleng = 11;
        }

    	var userLocation = null;
    	var userMarker = null;
    	const MIN_DISTANCE_FOR_MOVEMENT = 0.01;
		var lat = document.getElementById('lat')
		var long = document.getElementById('long')
        var labelMap = $('#labelMap')
        var tutor = $('#tutor')
        var getNewLoc = null;
        
    		if (navigator.geolocation) {
        		navigator.geolocation.getCurrentPosition(function(position){
        			userLocation = [position.coords.latitude, position.coords.longitude];
        		  //  console.log([position.coords.longitude, position.coords.latitude], userLocation);
        			showPosition(position);
        			labelMap.addClass('hidden');
        			tutor.removeClass('hidden');
        		});
        
        		navigator.geolocation.watchPosition(function(position){
        			if (userLocation) {
        			 //   console.log(userMarker.getLatLng());
        				const markerLocation = userMarker.getLatLng();
        				
        				if(markerLocation.lat.toString().length >= latlngleng){
    		                $('#form-absen').attr('action', '#');
        				    $('#btnAbsen').text('Diluar Radius');
        				    $('#btnAbsen').prop('disabled', true);
        				    $('#btnAbsen').addClass('btn-disabled');
    		                $('#btnAbsen').css('background-color', 'rgba(96, 165, 250, 0.5)');
    		                $('#btnAbsen').attr('id', '');
        				}
        				
        				getNewLoc = markerLocation;
        				// console.log(markerLocation.lat.toString().length);
        			    $('#latlongLabel').html(`[${markerLocation.lat}, ${markerLocation.lng}]`);
        				// console.log(markerLocation);
        				if (markerLocation.lat !== userLocation[1] || markerLocation.lng !== userLocation[0]) {
        					// The marker has been moved, so the GPS is likely fake
        					userMarker.setLatLng(userLocation);
        				}
        			}
        		});
        	} else {
        		alert('Geo Location Not Supported By This Browser !!');
        		labelMap.removeClass('hidden');
        	}

		function showPosition(position) {
			lat.value = position.coords.latitude;
			long.value = position.coords.longitude;

			var lati = "{{ $harLok->latitude }}"
			var longi = "{{ $harLok->longtitude }}"
			var radi = "{{ $harLok->radius }}"
			var client = "{{ $harLok->client->name }}"

			var latitude = position.coords.latitude; // Ganti dengan latitude Anda
			var longitude = position.coords.longitude; // Ganti dengan longitude Anda


			var map = L.map('map').setView([latitude, longitude], 13); // ini adalah zoom level

			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '© OpenStreetMap contributors'
			}).addTo(map);


            // console.log("marker: ", userLocation, [latitude, longitude]);
            userMarker = L.marker(userLocation).addTo(map);
// 			var marker = L.marker([latitude, longitude]).addTo(map);
			var circle = L.circle([lati, longi], {
					color: 'crimson',
					fillColor: '#f09',
					fillOpacity: 0.5,
					radius: radi
				}).addTo(map).bindPopup("Lokasi absen: " + client).openPopup();
    		window.onload = function () {
                showPosition(position);
            };
		}
	</script>
	<script>
		$(document).ready(function() {
		    
		    var dataUserId = $("#dataUser").attr('data-userId');
		    
		    var debounceTimer;
		    var keterangan = $('#keterangan');
			function calculatedJamStart() {
				// get jam
				var currentDate = new Date();
				var jamSaiki = currentDate.getHours();
				var menitSaiki = currentDate.getMinutes();
				var detikSaiki = currentDate.getSeconds();

				// fungsi
				var selectedOption = $('#shift_id').find(":selected");
				var shiftStart = selectedOption.data('shift');
				
				
				
				if (typeof shiftStart != 'undefined' && shiftStart != '') {
					var startTimeParts = shiftStart.split(':');
					var startHours = parseInt(startTimeParts[0]);
					var startMinutes = parseInt(startTimeParts[1]);

					var startDiffMinutes = startHours * 60 + startMinutes;
					var nowDiffMinutes = jamSaiki * 60 + menitSaiki;

					var jadi = startDiffMinutes - nowDiffMinutes;

					var kesimH = Math.floor(jadi / 60 - 1);
					var kesimM = Math.abs(jadi % 60);
					var kesimS = Math.abs(60 - detikSaiki);
					
					if (kesimM < 0) {
        				kesimH--;
        				kesimM += 60;
        			}
        			
                // kantor
                var absenKantor = $('#absen-kantor').data('absen-kantor');
                var authName = keterangan.data('authname');
				// console.log(authName, $('#keterangan').data('authname'));
				
				
				// 	keterangan
				if (absenKantor == 1) {
                    if (jadi < -32 && authName != 'DIREKSI') {
                        console.log('telat');
                        $('#keterangan').val('telat');
                    } else {
                        console.log('masuk');
                        $('#keterangan').val('masuk');
                    }
                } else {
                    if (jadi < 0) {
                        $('#keterangan').val('telat');
                    } else {
                        $('#keterangan').val('masuk');
                    }
                }
				
				    
				    if(dataUserId == 'MCS' || dataUserId == 'SPV'){
    						$('#btnAbsen').removeClass('cursor-not-allowed bg-blue-400/50 hover:bg-blue-400/50');
    						$('#btnAbsen').prop('disabled', false);
    						$('#labelWaktuStart').addClass('hidden');
				    }else{
				        if (jadi <= 90) {
    						$('#btnAbsen').removeClass('cursor-not-allowed bg-blue-400/50 hover:bg-blue-400/50');
    						$('#btnAbsen').prop('disabled', false);
    						$('#labelWaktuStart').addClass('hidden');
    					} else {
    						$('#btnAbsen').addClass('cursor-not-allowed bg-blue-400/50 hover:bg-blue-400/50');
    						$('#labelWaktuStart').removeClass('hidden');
    						$('#btnAbsen').prop('disabled', true);
    						if (kesimH == 0) {
    							$('#labelWaktuStart').html(`tunggu ${kesimM} menit ${kesimS} detik lagi untuk absen`);
    						} else if (kesimM == 0 && kesimH == 0) {
    							$('#labelWaktuStart').html(`tunggu ${kesimS} detik lagi untuk absen`);
    						} else
    							$('#labelWaktuStart').html(
    								`tunggu ${kesimH} jam ${kesimM} menit ${kesimS} detik lagi untuk absen`);
    					}
				    }

				} else {
				}
				clearTimeout(debounceTimer);
				debounceTimer = setTimeout(calculatedJamStart, 1000)
			}

			$('#shift_id').change(function() {
			 //   console.log("shift id changed");
				calculatedJamStart();
			});
			
			toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "3500",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

			
    		$('#btnAbsen').click(function(){
    		    $(this).prop('disabled', true)
        		    .text('Tunggu...')
        		    .addClass('btn-disabled')
        		    .css('background-color', 'rgba(96, 165, 250, 0.5)');
    		    
		        $('#form-absen').submit();
    		})
    		
            var value = $('.lat_user').val();
            // console.log(document.getElementById('latlongLabel').innerHTML);
		})
		
    
      


	</script>
	
</body>

</html>
