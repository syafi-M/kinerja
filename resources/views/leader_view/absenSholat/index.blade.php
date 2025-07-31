<x-app-layout>
    <x-main-div>
        <div class="p-5 py-10">
			<p class="text-center font-bold text-xl sm:text-2xl uppercase">Laporan Absen Sholat</p>
			<form action="{{ route('leader-absenSholat-store') }}" method="POST" enctype="multipart/form-data" id="form-absen">
			    @method('POST')
				@csrf
				<div class="flex flex-col  sm:m-0 items-center  justify-center">
				    <input type="hidden" class="image-tag" id="image-tag" name="fotoSholat" />
				    <div class="relative">
					    <video id="video" style="scale: 90%; padding: 0.75rem;" class="bg-slate-200 rounded-md square-video" autoplay></video>
				    </div>
					<canvas id="canvas"  style="display:none;"></canvas>
					<div id="results" class=" sm:mt-0 rounded mb-3"></div>
				</div>
				<div class="flex justify-center">
					<button type=button id="snapButton" class="p-2 px-3 mb-5 text-white bg-blue-400 rounded-full"><i
							class="ri-camera-fill"></i></button>
				</div>
				<div style="padding: 0.75rem;" class="bg-slate-200 rounded-md">
				    <p class="text-center font-semibold text-sm p-2">~:Nama Karyawan Yang Hadir:~</p>
				    @forelse($user as $us)
    			        @php
    			            $abs = $absen->where('user_id', $us->id)->where('dzuhur', '1')->first();
    			        @endphp
    				    <div>
    						<input type="checkbox" {{ $abs ? 'disabled' : '' }} name="user[]" id="user_{{ $us->id }}" value="{{ $us->id }}"
    							class="checkbox checkbox-sm m-2">
    						<label for="user_{{ $us->id }}" style="{{ $abs ? 'text-decoration-line: line-through;' : '' }}" class="break-words whitespace-pre-line font-medium text-sm">{{ $us->nama_lengkap }}</label>
    					</div>
    				@empty
				    @endforelse
				</div>
				@php
				    $wancine = (Carbon\Carbon::now()->format('H:i:s') >= '11:20:00' && Carbon\Carbon::now()->format('H:i:s') <= '14:10:00') || (Carbon\Carbon::now()->format('H:i:s') >= '17:20:00' && Carbon\Carbon::now()->format('H:i:s') <= '18:45:00');
				@endphp
				<div class="flex justify-center items-center gap-2 mt-5">
				    <button type="submit" {{ $wancine ? '' : 'disabled'}} class="p-2 btnAbsen {{ $wancine ? '' : 'btn-disabled'}} my-2 px-4 text-white bg-blue-500 hover:bg-blue-600 rounded transition-all ease-linear .2s"
						id="btnSholat">Simpan</button>
					<a href="{{ route('dashboard.index') }}"
						class="p-2 my-2 px-4 text-white bg-red-500 hover:bg-red-600 rounded transition-all ease-linear .2s">
						Kembali
					</a>
				</div>
			</form>
		</div>
		<script>
		    $(document).ready(function() {
            // Mendapatkan elemen video
        	var video = document.getElementById('video');
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d', { willReadFrequently: true });
            
            // Mengatur ukuran canvas sesuai opsi
            canvas.width = 640;
            canvas.height = 480;
        
            // Mengonfigurasi constraints untuk mendapatkan akses kamera
            var constraints = {
                audio: false,
                video: { facingMode: 'user', width: 1280, height: 720 }
            };
        	   //console.log(navigator.mediaDevices.getUserMedia(constraints));
        
            // Mengambil akses kamera
            navigator.mediaDevices.getUserMedia(constraints)
            .then(function(mediaStream) {
                // Menampilkan video dari kamera ke elemen video
                video.srcObject = mediaStream;
                video.onloadedmetadata = function(e) {
                    // $('.svg-icon-foto').show();
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
        		document.getElementById('results').innerHTML = '<img id="imgprev" width="640" height="480" style="scale: 85%;" class="rounded-md" src="' + dataURL + '"/>';
            }
        	
        	$('#snapButton').click(function() {
        		takeSnapshot();
        	});
        	 
        
            // Fungsi untuk memeriksa status video
            function checkVideoStatus() {
                // Membuat elemen canvas untuk memproses gambar dari video
                    
                    // canvas.width = video.videoWidth;
                    // canvas.height = video.videoHeight;
                    
                    canvas.width = 640;
                    canvas.height = 480;
                    
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
    </x-main-div>
</x-app-layout>