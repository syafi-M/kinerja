<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ env('APP_NAME', 'Kinerja SAC-PONOROGO') }}</title>
	<link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon">
	
	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
		integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
	<!-- Scripts -->
	@vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   
    
	<style>
		*,
		body,
		html {
			overflow-x: hidden;
		}
		
	</style>

</head>

    <body class="font-sans antialiased">
    	<div class="min-h-screen">
            <div class="rounded-md shadow-md" style="margin: 5%;">
            	<div style="background: #C0E8FC;">
            		<p class="text-center font-bold" style="padding: 5% 0; color: #132D4C; font-size: 34px; line-height: 1;">Daftar Sebagai Karyawan Baru <br/><span style="font-size: 18px;">PT. Surya Amanah Cendikia</span></p>
            		<form method="POST" action="{{ route('addKaryawanStore') }}" id="form" enctype="multipart/form-data" style="background: white; padding: 5% 0;">
            			@csrf
            			<div class="px-5 rounded">
            				<!-- Name Lengkap -->
            				<div>
            					<x-input-label for="nama_lengkap" class="required" :value="__('Nama Lengkap')" />
            					<x-text-input id="nama_lengkap" class="block mt-1 w-full" type="text" name="nama_lengkap" :value="old('nama_lengkap')" required
            						autofocus autocomplete="nama_lengkap" placeholder="Masukkan nama lengkap user..."/>
            					<x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
            				</div>
            
            				<!-- Email Address -->
            				<div class="mt-2">
            					<x-input-label for="email" class="required" :value="__('Email Aktif')" />
            					<x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
            						autocomplete="email" placeholder="Masukkan email user..."/>
            					<x-input-error :messages="$errors->get('email')" class="mt-2" />
            				</div>
            				
            				<!-- TTL -->
                			<div class="mt-2">
                				<x-input-label for="ttl" class="required" :value="__('Tempat Tanggal Lahir')" />
                				<div class="w-full flex justify-center items-center gap-1">
                    				<x-text-input id="tpt" class="block mt-1" style="width: 50%;" type="text" name="tpt" required
                    					value="" placeholder="TL..." autocomplete="tpt" />
                    				<x-text-input id="tgl" class="block mt-1" style="width: 50%;" type="date" name="tgl" required
                    					value="" placeholder="TL..." autocomplete="tgl" />
                				</div>
                				<x-input-error :messages="$errors->get('tpt') || $errors->get('tgl')" class="mt-2" />
                			</div>
            				<!-- NIK -->
                			<div class="mt-2">
                				<x-input-label for="NIK" class="required" :value="__('NIK')" />
                				<x-text-input id="NIK" class="block mt-1 w-full" type="text" name="nik" required
                					value="" placeholder="Nik..." maxlength="16"  pattern="[0-9]*" autocomplete="nik" />
                				<x-input-error :messages="$errors->get('nik')" class="mt-2" />
                			</div>
                			{{-- foto KTP --}}
            				<div class="mt-2 p-1">
                                <x-input-label for="img2" class="required" :value="__('Foto KTP (Depan)')" />
                                <div class="preview2 hidden w-full">
                                    <span class="flex justify-center items-center">
                                        <label for="img2" class="p-1">
                                            <img class="img2 ring-2 ring-slate-500/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s"
                                                src="" alt="" srcset="" width="200px">
                                            
                                        </label>
                                    </span>
                                </div>
                                <label for="img2"
                                    class="w-full iImage2 flex flex-col items-center justify-center rounded-md bg-slate-300/70  ring-2 ring-slate-400/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s">
                                    <span class="p-3 flex justify-center flex-col items-center">
                                        <i class="ri-image-add-line text-xl text-slate-700/90"></i>
                                        <span class="text-xs font-semibold text-slate-700/70">+ Gambar</span>
                                        <input id="img2" class="hidden mt-1 w-full file-input file-input-sm file-input-bordered shadow-none"
                                            type="file" name="ktp" :value="old('image')" accept="image/*" autofocus required autocomplete="img2" />
                                    </span>
                                </label>
                                <x-input-error :messages="$errors->get('image2')" class="mt-2" />
            				</div>
            				<!-- KK -->
                			<div class="mt-2">
                				<x-input-label for="kk" class="required" :value="__('No. KK')" />
                				<x-text-input id="kk" class="block mt-1 w-full" type="text" name="kk" required
                					value="" placeholder="No. KK..." maxlength="16"  pattern="[0-9]*" autocomplete="kk" />
                				<x-input-error :messages="$errors->get('kk')" class="mt-2" />
                			</div>
                			<!-- No HP -->
                			<div class="mt-2">
                				<x-input-label for="phone" class="required" :value="__('No. HP Aktif')" />
                				<x-text-input id="phone" class="block mt-1 w-full" type="tel" name="no_hp" required
                					value="" placeholder="No hp aktif..." maxlength="14" pattern="[0-9]{10,15}" autocomplete="off" />
                				<x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                			</div>
            
            				<!-- client -->
            				<div class="mt-2">
            					<x-input-label for="client" class="required" :value="__('Unit Kerja')" />
            					<select name="client_id" id="" required class="select select-bordered w-full mt-1">
            						<option selected disabled>~ Pilih Unit Kerja ~</option>
            						@foreach ($kerjasama as $i)
            							<option name="client_id" value="{{ $i->client_id }}" class="py-2">{{ ucwords(strtolower($i->client->name)) }}</option>
            						@endforeach
            					</select>
            				</div>
            				<!-- devisi -->
            				<div class="mt-2">
            					<x-input-label for="devisi" class="required" :value="__('Bagian')" />
            					<select name="devisi_id" id="devisi" required class="select select-bordered w-full mt-1">
            						<option selected disabled>~ Pilih Bagian ~</option>
            						@foreach ($devisi as $i)
            							<option value="{{ $i->id }}" class="py-2">{{ ucwords(strtolower($i->jabatan->name_jabatan)) }}</option>
            						@endforeach
            					</select>
            				</div>
            				{{-- foto Profile --}}
            				<div class="mt-2 p-1">
                                <x-input-label for="foto Profil" class="required" :value="__('Foto Profil')" />
                                <div class="preview hidden w-full">
                                    <span class="flex justify-center items-center">
                                        <label for="img" class="p-1">
                                            <img class="img1 ring-2 ring-slate-500/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s"
                                                src="" alt="" srcset="" width="200px">
                                            
                                        </label>
                                    </span>
                                </div>
                                <label for="img"
                                    class="w-full iImage1 flex flex-col items-center justify-center rounded-md bg-slate-300/70  ring-2 ring-slate-400/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s">
                                    <span class="p-3 flex justify-center flex-col items-center">
                                        <i class="ri-image-add-line text-xl text-slate-700/90"></i>
                                        <span class="text-xs font-semibold text-slate-700/70">+ Gambar</span>
                                        <input id="img" class="hidden mt-1 w-full file-input file-input-sm file-input-bordered shadow-none"
                                            type="file" name="image" :value="old('image')" accept="image/*" autofocus required autocomplete="img" />
                                    </span>
                                </label>
                                <x-input-error :messages="$errors->get('image1')" class="mt-2" />
            				</div>
            				
            				<!-- Password -->
            				<div class="mt-2">
            					<x-input-label for="password" class="required" :value="__('Password Aplikasi')" />
            					<x-text-input id="password" class="block mt-1 w-full" type="password" name="password" :value="old('password')" required
            						autocomplete="off" placeholder="Buat password anda..."/>
            					<x-input-error :messages="$errors->get('password')" class="mt-2" />
            				</div>
            				
            				<div id="otp-section" class="mt-6">
                                <x-input-label for="otp" class="required" :value="__('Kode OTP')" />
                                <div class="flex items-center gap-2">
                                    <x-text-input id="otp" class="w-full" type="text" name="otp" maxlength="6" placeholder="Masukkan OTP..." required />
                                    <button type="button" id="send-otp" data-jenis="send" class="btn btn-primary">
                                        Kirim OTP
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                                <p id="otp-timer" class="text-sm text-gray-600 mt-2 hidden">
                                    Kirim ulang OTP dalam <span id="otp-countdown">90</span> detik...
                                </p>
                            </div>

            				
            				<div class="flex justify-end mt-10 gap-2">
            					<a href="/" class="btn btn-error hover:bg-red-500 transition-all ease-linear .2s">
            						Kembali
            					</a>
            					<button type="submit" class="btn btn-primary">Simpan & Kirim</button>
            				</div>
            			</div>
            		</form>
            		<div id="toast"
                         style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%);
                                background:#f87171; color:white; padding:12px 20px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.2); z-index:9999; font-weight:500; text-align: center;">
                    </div>

            	</div>
            </div>
    	</div>
    	<script>
    	    $(document).ready(function() {
        	    $('#img').change(function() {
    				const input = $(this)[0];
    				const preview = $('.preview');
    
    				if (input.files && input.files[0]) {
    					const reader = new FileReader();
    
    					reader.onload = function(e) {
    						preview.show();
    						preview.find('.img1').attr('src', e.target.result);
    						preview.removeClass('hidden');
    						preview.find('.img1').addClass('rounded-md shadow-md my-4');
    						$('.iImage1').removeClass('flex').addClass('hidden');
    					};
    
    					reader.readAsDataURL(input.files[0]);
    				}
    			});
        	    $('#img2').change(function() {
    				const input2 = $(this)[0];
    				const preview2 = $('.preview2');
    
    				if (input2.files && input2.files[0]) {
    					const reader2 = new FileReader();
    
    					reader2.onload = function(e) {
    						preview2.show();
    						preview2.find('.img2').attr('src', e.target.result);
    						preview2.removeClass('hidden');
    						preview2.find('.img2').addClass('rounded-md shadow-md my-4');
    						$('.iImage2').removeClass('flex').addClass('hidden');
    					};
    
    					reader2.readAsDataURL(input2.files[0]);
    				}
    			});
    			
    // 			otp
                let countdownInterval;
                const countdownDuration = 90; // seconds
                
                function showToast(message, type = 'error') {
                    const colors = {
                        success: '#4ade80',
                        error: '#f87171'
                    };
                
                    $('#toast')
                        .css('background', colors[type] || colors.error)
                        .text(message)
                        .stop(true, true)
                        .fadeIn(300)
                        .delay(3000)
                        .fadeOut(300);
                }
                
                function sendOtpReg(email) {
                    return $.post('/send-otp-reg', {
                        email: email,
                        _token: '{{ csrf_token() }}'
                    });
                }
                
                function cekEmail(email, no_hp) {
                    return $.get('/check-email', {
                        email: email,
                        no_hp: no_hp,
                        _token: '{{ csrf_token() }}'
                    });
                }
                
                function startCountdown(duration, button, timerContainer) {
                    let countdown = duration;
                    $('#otp-countdown').text(countdown);
                    timerContainer.removeClass('hidden');
                
                    countdownInterval = setInterval(() => {
                        countdown--;
                        $('#otp-countdown').text(countdown);
                
                        if (countdown <= 0) {
                            clearInterval(countdownInterval);
                            button.prop('disabled', false).text('Kirim Ulang OTP');
                            timerContainer.addClass('hidden');
                        }
                    }, 1000);
                }
                
                $('#send-otp').on('click', function () {
                    const email = $('#email').val().trim();
                    const phone = $('#phone').val().trim();
                    const button = $(this);
                    const timerContainer = $('#otp-timer');
                
                    if (!email) {
                        showToast('Masukkan email terlebih dahulu.', 'error');
                        return;
                    }
                    
                    if (!phone) {
                        showToast('Masukkan no. telepon terlebih dahulu.', 'error');
                        return;
                    }
                
                    cekEmail(email, phone).then(function (response) {
                        if (response.email) {
                            showToast('Email sudah terdaftar.', 'error');
                            return;
                        }
                        if (response.phone) {
                            showToast('No. hp sudah terdaftar.', 'error');
                            return;
                        }
                
                        sendOtpReg(email).then(() => {
                            showToast('Kode OTP telah dikirim!', 'success');
                        });
                
                        button.prop('disabled', true).text('Tunggu...');
                        clearInterval(countdownInterval); // Reset previous timer if any
                        startCountdown(countdownDuration, button, timerContainer);
                    }).fail(() => {
                        showToast('Terjadi kesalahan saat memeriksa email.', 'error');
                    });
                });

                
                $('#form').on('submit', function (e) {
                    e.preventDefault();
            
                    const form = this;
                    const email = $('#email').val();
                    const otp = $('#otp').val();
            
                    if (!email || !otp) {
                        showToast('Mohon isi email dan OTP terlebih dahulu.', 'error');
                        return;
                    }
            
                    $.ajax({
                        url: '/verify-otp-reg',
                        method: 'POST',
                        data: {
                            email: email,
                            otp: otp,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            showToast('✅ OTP valid. Mengirim data...', 'success');
                            form.submit(); // lanjutkan submit
                        },
                        error: function (xhr) {
                            const status = xhr.responseJSON?.status;
            
                            if (status === 'otp.invalid' || status === 'otp.expired') {
                                showToast('❌ OTP salah atau kadaluarsa! Mengirim ulang OTP...', 'error');
            
                                // Kirim ulang OTP otomatis
                                sendOtpReg(email);
            
                                // Reset timer
                                clearInterval(countdownInterval);
                                $('#send-otp').prop('disabled', true).text('Tunggu...');
                                $('#otp-timer').removeClass('hidden');
                                countdown = 90;
                                $('#otp-countdown').text(countdown);
            
                                countdownInterval = setInterval(() => {
                                    countdown--;
                                    $('#otp-countdown').text(countdown);
                                    if (countdown <= 0) {
                                        clearInterval(countdownInterval);
                                        $('#send-otp').prop('disabled', false).text('Kirim Ulang OTP');
                                        $('#otp-timer').addClass('hidden');
                                    }
                                }, 1000);
                            } else {
                                showToast('Terjadi kesalahan saat verifikasi.', 'error');
                            }
                        }
                    });
                });
    	    })
    	</script>
    </body>
</html>