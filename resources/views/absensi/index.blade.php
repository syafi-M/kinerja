<x-app-layout>
    @push('scripts')
        <script src="{{ URL::asset('js/toastr.min.js') }}"></script>
        <script src="{{ URL::asset('js/moment.min.js') }}"></script>
    @endpush
    <div class="px-5 py-5">
        @if ($errors->any())
            <div class="p-2 text-red-500 rounded-md bg-slate-200">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data" id="form-absen">
            @method('POST')
            @csrf
            @if (Auth::user()->kerjasama_id != 1 || !in_array(Auth::user()->devisi_id, [2, 3, 7, 8, 12, 14, 18]))
                <div class="flex flex-col items-center justify-center sm:m-0">
                    <div class="relative">
                        <video id="video" class="bg-slate-200 p-2.5 rounded-md square-video max-w-[60vw]" autoplay
                            playsinline></video>
                    </div>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <div id="results" class="my-3 rounded sm:mt-0"></div>

                    @if ($errors->image)
                        <!--<p class="p-1 font-bold bg-white rounded-lg text-start" style="color: red">Foto Tidak Boleh Kosong</p>-->
                    @endif
                </div>
                <div class="flex justify-center">
                    <button type=button id="snapButton"
                        class="p-2 px-3 my-2 mb-5 text-white bg-blue-400 rounded-full"><i
                            class="ri-camera-fill"></i></button>
                </div>

            @endif

            <div class="p-1 my-3 rounded ">
                <label class="text-white required">Map : </label>
                <span id="labelMap" class="flex flex-col justify-center text-center text-white">
                    <p>Pastikan map sudah muncul !!</p>
                    <p id="resolver">coba refresh browser beberapa kali jika map belum muncul</p>
                </span>
                <div id="map" class="rounded"></div>
                <div id="map-loading" class="flex items-center justify-center h-full text-white">
                    <span>Loading map...</span>
                </div>
                <span id="tutor"
                    class="flex flex-col justify-center hidden text-sm italic text-center text-white capitalize">
                    <p id="latlongLabel" class="text-[8px]"></p>
                    <p>Pastikan tanda biru berada dilingkaran</p>
                </span>
            </div>
            <div class="flex flex-col gap-2">
                <div class="flex flex-col justify-between">
                    <label for="name"
                        class="text-white">{{ Route::currentRouteName() == 'absensi-karyawan-co-cs.index' || Route::currentRouteName() == 'absensi-karyawan-co-scr.index' ? 'Pilih Nama: ' : 'Nama: ' }}</label>
                    @if (Route::currentRouteName() == 'absensi-karyawan-co-cs.index' ||
                            Route::currentRouteName() == 'absensi-karyawan-co-scr.index')
                        <select name="user_id" id="selectUser" class="select select-bordered">
                            <option selected value="{{ Auth::user()->id }}" class="op"
                                data-clien="{{ Auth::user()->kerjasama->client_id }}">
                                {{ Auth::user()->nama_lengkap }}</option>
                            @foreach ($userL as $us)
                                <option value="{{ $us->id }}" class="op" data-divisi="{{ $us->devisi_id }}"
                                    data-clien="{{ $us->kerjasama->client_id }}"
                                    data-jab="{{ $us->divisi?->jabatan_id }}">
                                    {{ ucwords(strtolower($us->nama_lengkap)) }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" id="user_id" name="user_id" value="{{ Auth::user()->id }}" hidden>
                        <input type="text" id="name" name="{{ Auth::user()->name }}"
                            value="{{ Auth::user()->nama_lengkap }}" disabled class="input input-bordered">
                    @endif
                </div>
                <div class="flex flex-col justify-between">
                    <label for="kerjasama" class="text-white">Penempatan: </label>
                    @if (Auth::user()->id == 10)
                        <select class="selectMitra select select-bordered" name="kerjasama_id">

                        </select>
                    @elseif(Auth::user()->id == 7)
                        <input type="text" name="kerjasama_id" id="kerjasama_id" hidden
                            value="{{ Auth::user()->kerjasama_id }}">
                        <input type="text" id="kerjasama" name="{{ Auth::user()->kerjasama->client->name }}"
                            value="{{ Auth::user()->id == 7 ? '' : Auth::user()->kerjasama->client->name }}" disabled
                            class="input input-bordered viewKerjasama">
                    @else
                        <input type="text" name="kerjasama_id" id="kerjasama_id" hidden
                            value="{{ Auth::user()->kerjasama_id }}">
                        <input type="text" id="kerjasama" name="{{ Auth::user()->kerjasama->client->name }}"
                            value="{{ Auth::user()->jabatan?->code_jabatan == 'SPV-W' ? '' : Auth::user()->kerjasama->client->name }}"
                            disabled class="input input-bordered viewKerjasama">
                    @endif
                </div>
                @if (Auth::user()->kerjasama_id == 1)
                    <input type="text" name="masuk" value="1" class="hidden" />
                @endif
                @if (Auth::user()->kerjasama_id != 1)
                    @php
                        $lanjutShift =
                            isset($absensi[0]) &&
                            $absensi[0]->absensi_type_pulang &&
                            $absensi[0]->tanggal_absen == Carbon\Carbon::now()->format('Y-m-d') &&
                            $absensi[0]->masuk;
                    @endphp
                    <div class="flex flex-col justify-start">
                        <x-input-label for="jenis_abs" class="text-lg text-white" :value="__('Jenis Absen: ')" />
                        <div class="flex flex-col justify-start rounded-lg bg-slate-50">
                            <div class="flex items-center">
                                <input type="radio" id="type_absen" data-pilih="masuk" name="jenis_abs" value="1"
                                    class="m-2 radio radio-sm" {{ $lanjutShift ? 'disabled' : 'checked' }}>
                                <label for="masuk" class="overflow-hidden"
                                    {{ $lanjutShift ? 'disabled' : '' }}>Masuk</label>
                            </div>
                            @if ($lanjutShift)
                                <div class="flex items-center">
                                    <input type="radio" id="type_absen" data-pilih="terus" name="jenis_abs"
                                        value="1" class="m-2 radio radio-sm disabled" checked>
                                    <label for="tukar" class="overflow-hidden" disabled>Meneruskan
                                        Shift</label>
                                </div>
                            @endif
                            <div class="flex items-center">
                                <input type="radio" id="type_absen" data-pilih="tukar" name="jenis_abs"
                                    value="1" class="m-2 radio radio-sm disabled" disabled>
                                <label for="tukar" class="overflow-hidden" disabled>Tukar Shift
                                    (maintenance)</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="type_absen" data-pilih="lembur" name="jenis_abs"
                                    value="1" class="m-2 radio radio-sm" disabled>
                                <label for="lembur" class="overflow-hidden">Lembur (maintenance)</label>
                            </div>

                            <div id="type_absen_div" class="hidden">
                                <input type="text" name="masuk" value="1" class="hidden" />
                            </div>
                        </div>
                    </div>
                    <div class="w-full" id="divPengganti" style="display: none;">
                        <x-input-label for="pengganti" class="text-lg text-white" :value="__('Pengganti: ')" />
                        <select name="pengganti" id="pengganti" required
                            style="{{ $errors->any() && $errors->pengganti ? 'border: 2px solid red;' : '' }}"
                            class="w-full font-thin select select-bordered">
                            @if ($errors->any() && $errors->pengganti)
                                <option selected disabled class="p-1 my-1 font-bold text-red-600">
                                    Pengganti Tidak Boleh Kosong</option>
                            @endif
                            <option disabled {{ $errors->any() && $errors->pengganti ? '' : 'selected' }}>
                                -- Pilih Pengganti --</option>

                            <option>Belum Ada Karyawan</option>

                        </select>
                    </div>
                @endif
                @if (Auth::user()->divisi->jabatan->name_jabatan == 'DIREKSI')
                    <input type="hidden" name="shift_id" value="145" />
                @elseif(Auth::user()->jabatan->code_jabatan == 'SPV-W')
                    <input type="hidden" name="shift_id" value="195" />
                @elseif(Auth::user()->devisi_id == 12)
                    <input type="hidden" name="shift_id" value="200" />
                @else
                    <div class="flex flex-col justify-between">
                        <label class="text-white required" for="shift_id">Shift: </label>
                        <select name="shift_id" id="shift_id"
                            {{ Auth::user()->name == 'DIREKSI' ? '' : 'required' }}
                            style="{{ $errors->any() && $errors->shift_id ? 'border: 2px solid red;' : '' }}"
                            class="font-thin select select-bordered ">
                            @if ($errors->any() && $errors->shift_id)
                                <option selected disabled class="p-1 my-1 font-bold text-red-600">
                                    Shift Tidak Boleh Kosong</option>
                            @endif
                            <option disabled {{ $errors->any() && $errors->shift_id ? '' : 'selected' }}>--
                                Pilih Shift --</option>

                            @forelse ($shift as $i)
                                @php
                                    $endA = Carbon\Carbon::parse($i->jam_end)->subHour(1)->format('H:i');
                                @endphp
                                <option value="{{ $i->id }}" data-shift="{{ $i?->jam_start }}">
                                    {{ ucwords(strtolower($i?->shift_name)) }} | {{ $i?->jam_start }} -
                                    {{ $endA }}
                                </option>
                            @empty
                                <option readonly disabled>~ Tidak ada Shift ! ~</option>
                            @endforelse
                        </select>
                        @if (Auth::user()->kerjasama->client_id == 1)
                            <span id="absen-kantor" data-absen-kantor="{{ Auth::user()->kerjasama->client_id }}"
                                hidden></span>
                        @endif
                    </div>
                @endif

                <div>
                    <div>
                        <label class="text-white required">Perlengkapan: </label>
                    </div>
                    <div class="p-2 bg-white rounded-lg "
                        style="{{ $errors->any() && $errors->shift_id ? 'border: 2px solid red;' : '' }}">
                        @if ($errors->any() && $errors->perlengkapan)
                            <p class="p-1 my-1 font-bold text-red-600">Perlengkapan Tidak Boleh
                                Kosong</p>
                        @endif
                        <div id="divPerlengkapan" class="grid grid-cols-1">
                            @forelse ($dev as $arr)
                                @foreach ($arr->perlengkapan as $i)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="perlengkapan[]"
                                            id="perlengkapan {{ $i->id }}" value="{{ $i->name }}"
                                            class="m-2 checkbox checkbox-sm perle">
                                        <label for="perlengkapan {{ $i->id }}">{{ $i->name }}</label>
                                    </div>
                                @endforeach
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

                @if (Auth::user()->kerjasama_id != 1 || !in_array(Auth::user()->devisi_id, [2, 3, 7, 8, 12, 14, 18]))
                    <input type="text" id="image" name="image" class="image-tag" hidden>
                @endif
                <input type="text" id="keterangan" name="keterangan" value="masuk"
                    data-authName="{{ Auth::user()->name }}" hidden>
            </div>
            <input type="text" class="hidden" name="absensi_type_masuk" value="1">
            @php
                $key = Auth::user()->id;
                $cekRoute =
                    Route::currentRouteName() == 'absensi-karyawan-co-cs.index' ||
                    Route::currentRouteName() == 'absensi-karyawan-co-scr.index';
            @endphp
            <div class="flex flex-col justify-center gap-3 mt-2 mr-2 sm:justify-end">
                <span id="labelWaktuStart"
                    class="text-center text-[10px] capitalize font-semibold hidden py-2 px-4 rounded-md bg-slate-50"></span>
                <span class="flex justify-center gap-3">
                    @forelse ($absensi as $abs)
                        {{-- sudah --}}
                        @if (
                            !$cekRoute &&
                                $abs->tanggal_absen == Carbon\Carbon::now()->format('Y-m-d') &&
                                $abs->absensi_type_pulang == null &&
                                $abs->tukar == null &&
                                !$afaLib)
                            <button
                                class="p-2 my-2 px-4 text-slate-100 bg-blue-300  rounded transition-all ease-linear .2s disabled cursor-not-allowed"
                                disabled>Sudah Absen</button>
                        @elseif($afaLib && Auth::user()->divisi->jabatan_id != 35)
                            <button
                                class="p-2 my-2 px-4 text-slate-100 bg-blue-300  rounded transition-all ease-linear .2s disabled cursor-not-allowed"
                                disabled>Jadwal Tidak Ada</button>
                        @elseif(!$cekRoute && $abs->tanggal_absen == Carbon\Carbon::now()->format('Y-m-d') && $abs->terus)
                            <button
                                class="p-2 my-2 px-4 text-slate-100 bg-blue-300  rounded transition-all ease-linear .2s disabled cursor-not-allowed"
                                disabled>Sudah Absen 2x (Mulih Lurr, ojo kerjo ae)</button>
                        @else
                            <button type="button"
                                class="p-2 btnAbsen my-2 px-4 text-white bg-blue-500 hover:bg-blue-600 rounded transition-all ease-linear .2s"
                                id="btnAbsen">Absen</button>
                        @endif
                    @break

                    @empty
                        <button type="button"
                            class="p-2 btnAbsen my-2 px-4 text-white bg-blue-500 hover:bg-blue-600 rounded transition-all ease-linear .2s"
                            id="btnAbsen">Absen</button>
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
            <div class="hidden" id="HiddenOnes">
                <input class="hidden" id="thisTime" value="{{ $mytime }}">
                <input class="hidden" id="thisTime2" value="{{ $mytime2 }}">
                <input class="hidden" id="isi" name="absensi_type_pulang">
                <input type="hidden" id="lat" name="lat_user" value="" class="hidden lat_user" />
                <input type="hidden" id="long" name="long_user" value="" class="hidden long_user" />

                <input type="hidden" id="lat_mitra" name="lat_mitra" value="{{ $harLok->latitude }}"
                    class="hidden" />
                <input type="hidden" id="long_mitra" name="long_mitra" value="{{ $harLok->longtitude }}"
                    class="hidden" />
                <input type="hidden" id="radius_mitra" name="radius_mitra" value="{{ $harLok->radius }}"
                    class="hidden" />
            </div>

            <span class="hidden" id="dataUser" data-userId="{{ $uID }}"></span>
        </form>
    </div>
    </main>
    </div>
    </div>

    @if (auth()->user()->kerjasama_id != 1 || !in_array(auth()->user()->devisi_id, [2, 3, 7, 8, 12, 14, 18]))
        <!-- Configure a few settings and attach camera -->
        <script defer>
            $(document).ready(function() {
                // Mendapatkan elemen video
                var video = document.getElementById('video');
                var canvas = document.createElement('canvas');
                var context = canvas.getContext('2d', {
                    willReadFrequently: true
                });
                var isLeadr = {!! json_encode(
                    Route::currentRouteName() == 'absensi-karyawan-co-cs.index' ||
                        Route::currentRouteName() == 'absensi-karyawan-co-scr.index',
                ) !!};
                var isDarkEnvironment = false;
                // console.log(isLeadr);

                // Mengatur ukuran canvas sesuai opsi
                canvas.width = 320;
                canvas.height = 240;

                // Mengonfigurasi constraints untuk mendapatkan akses kamera
                var constraints = {
                    audio: false,
                    video: {
                        facingMode: isLeadr ? 'environment' : 'user',
                        width: 450,
                        height: 450
                    }
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

                    // Get image data
                    var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    var data = imageData.data;

                    if (isDarkEnvironment) {
                        // Apply manual brightness/contrast enhancement
                        let brightnessFactor = 40; // increase brightness by +40
                        let contrastFactor = 1.3; // contrast multiplier

                        for (let i = 0; i < data.length; i += 4) {
                            // Brightness
                            data[i] = Math.min(data[i] + brightnessFactor, 255); // Red
                            data[i + 1] = Math.min(data[i + 1] + brightnessFactor, 255); // Green
                            data[i + 2] = Math.min(data[i + 2] + brightnessFactor, 255); // Blue

                            // Contrast
                            data[i] = ((data[i] - 128) * contrastFactor + 128);
                            data[i + 1] = ((data[i + 1] - 128) * contrastFactor + 128);
                            data[i + 2] = ((data[i + 2] - 128) * contrastFactor + 128);
                        }

                        // Put enhanced data back
                        context.putImageData(imageData, 0, 0);
                    }

                    // Mengubah gambar menjadi URL data
                    var dataURL = canvas.toDataURL('image/jpeg', 0.9);
                    $('.image-tag').val(dataURL)

                    // Mengirim dataURL ke backend atau melakukan hal lain sesuai kebutuhan Anda
                    //console.log(dataURL);
                    document.getElementById('results').innerHTML =
                        '<img id="imgprev" width="200" height="200" class="rounded-md" src="' + dataURL + '"/>';
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

                    isDarkEnvironment = blackPixels > (canvas.width * canvas.height * 0.7); // 80% black
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
                        alert('Terlalu banyak warna terdeteksi!\nTolong agak menjauh dari kamera');
                        $('#snapButton').hide()
                    } else {
                        $('#snapButton').show()
                    }
                    // Jika sebagian besar piksel adalah hitam, mungkin output kamera hitam
                    if (blackPixels > (canvas.width * canvas.height *
                            0.9)) { // 90% piksel hitam, sesuaikan jika diperlukan
                        alert('Output kamera hitam!\nTolong pindah ke tempat yang lebih terang');
                        $('#snapButton').hide();
                        // 			$('#snapButton').prop('disabled', true);
                    } else {
                        $('#snapButton').show()
                    }


                    // Menutup elemen canvas
                    canvas.remove();
                }
            });
        </script>
        <!--Camera-->
    @endif
    <!--Pershift an-->
    <script>
        $(document).ready(function() {
            var selectedClientID = $('#selectUser').find(':selected').data('clien');
            var selectedJabID = $('#selectUser').find(':selected').data('jab');
            var selectedDivID = $('#selectUser').find(':selected').data('divisi');

            $('#selectUser').change(function() {
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
                        if (data.shift.length > 0) {
                            html +=
                                '<option disabled {{ $errors->any() && $errors->shift_id ? '' : 'selected' }}>-- Pilih Shift --</option>';
                            data.shift.forEach(function(shift) {
                                // var endA = moment(shift.jam_end).subtract(1, 'hour').format('HH:mm');

                                html += '<option value="' + shift.id +
                                    '" data-shift="' + shift.jam_start + '">' +
                                    shift.jam_start + ' - ' + shift.jam_end + ' | ' +
                                    shift.jabatan.name_jabatan + ' | ' + shift
                                    .shift_name +
                                    '</option>';
                            });

                        } else {
                            html += '<option>~ Tidak ada Shift ! ~</option>';
                        }

                        $('#shift_id').html(html);
                        if (data.dev.length > 0) {
                            data.dev.forEach(function(divisi) {
                                if (divisi.id == selectedDivID) {
                                    // console.log(divisi);
                                    divisi.perlengkapan.forEach(function(perle) {
                                        htmlP +=
                                            '<div><input type="checkbox" name="perlengkapan[]" id="perlengkapan ' +
                                            perle.id + '" value="' + perle
                                            .name +
                                            '" class="m-2 checkbox checkbox-sm perle"><label for="perlengkapan ' +
                                            perle.id + '">' + perle.name +
                                            '</label></div>';
                                        // console.log(htmlP);
                                    })
                                }
                            })
                        } else {
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
            $('.radio').change(function() {
                const selected = $(this).data('pilih');

                let inputName = selected;
                let inputHtml = `<input type="text" name="${inputName}" value="1" class="hidden"/>`;
                $('#type_absen_div').html(inputHtml);

                if (selected === "tukar") {
                    $('#divPengganti').show();
                } else {
                    $('#divPengganti').hide();
                }
            });

            // Trigger change manually on page load to reflect default checked radio
            $(document).ready(function() {
                $('.radio:checked').trigger('change');
            });
        });
    </script>
    <!--Maps-->
    <script defer>
        function detectDevice() {
            const userAgent = navigator.userAgent.toLowerCase();
            if (/android/.test(userAgent)) return 'Android';
            if (/iphone|ipad|ipod/.test(userAgent)) return 'iPhone';
            return 'Unknown';
        }

        var loc = @json($lokLok);
        var mitra = @json($penempatan);
        var defaultLocationId = "{{ auth()->user()->kerjasama_id }}";
        var lati = "{{ $harLok->latitude }}";
        var longi = "{{ $harLok->longtitude }}";
        var radi = "{{ $harLok->radius }}";
        var client = "{{ $harLok->client->name }}";

        let userLocation = null;
        let userMarker = null;
        const MIN_DISTANCE_FOR_MOVEMENT = 0.01;
        var deviceType = detectDevice();
        const latlngLength = deviceType === 'iPhone' ? 18 : 11;
        // Example usage:
        var lat = document.getElementById('lat')
        var long = document.getElementById('long')
        var labelMap = $('#labelMap')
        var tutor = $('#tutor')
        var getNewLoc = null;
        $(document).ready(function() {
            var map = L.map('map').setView([0, 0], 2);
            map.on('load', function() {
                $('#map-loading').hide();
            });
            if (navigator.geolocation) {
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                navigator.geolocation.getCurrentPosition(function(position) {
                    userLocation = [position.coords.latitude, position.coords.longitude];
                    showPosition(position);
                });

                const watchUser = navigator.geolocation.watchPosition(
                    (position) => {
                        const {
                            latitude,
                            longitude
                        } = position.coords;
                        const markerLocation = L.marker(userLocation).getLatLng();
                        labelMap.addClass('hidden');
                        tutor.removeClass('hidden');


                        lat.value = latitude;
                        long.value = longitude;
                        getNewLoc = markerLocation;

                        var userLatLng = L.latLng([latitude, longitude]);
                        var circleLatLng = L.latLng([$('#lat_mitra').val(), $('#long_mitra').val()]);
                        var distanceFromCenter = userLatLng.distanceTo(circleLatLng); // in meters
                        var distanceFromBorder = distanceFromCenter - $('#radius_mitra').val();

                        if ((@json(auth()->user()->jabatan->code_jabatan) == "SPV-W" || @json(auth()->user()->devisi_id == 12)) &&
                            markerLocation.lat
                            .toString().length <=
                            latlngLength) {
                            $('#form-absen').attr('action', '{{ route('absensi.store') }}');
                            $('#btnAbsen').text('Absen').prop('disabled', false).removeClass('btn-disabled')
                                .addClass(
                                    'bg-blue-500 hover:bg-blue-600').attr('id', 'btnAbsen');
                            // console.log("iki spv");
                        } else {
                            if (markerLocation.lat.toString().length > latlngLength || distanceFromBorder
                                .toFixed() > 1) {
                                $('#form-absen').attr('action', '#');
                                $('#btnAbsen').text('Diluar Radius').prop('disabled', true).addClass(
                                    'btn-disabled').css(
                                    'background-color', 'rgba(96, 165, 250, 0.5)').attr('id', '');
                                // console.log("iki dudu spv");
                            } else {
                                $('#form-absen').attr('action', '{{ route('absensi.store') }}');
                                $('#btnAbsen').text('Absen').prop('disabled', false).removeClass('btn-disabled')
                                    .addClass(
                                        'bg-blue-500 hover:bg-blue-600').attr('id', 'btnAbsen');
                                // console.log("iki spv");
                            }
                        }

                        $('#latlongLabel').html(
                            `[${latitude}, ${longitude}, ${distanceFromBorder.toFixed(2)}]`);

                        // Check if marker exists
                        if (!userMarker) {
                            // Create marker if it doesn't exist
                            userMarker = L.marker([latitude, longitude]).addTo(map).bindPopup("Lokasi anda");
                        } else {
                            // Update the marker's position
                            userMarker.setLatLng([latitude, longitude]).openPopup();
                        }
                        // console.log(lat, long);
                    },
                    (error) => {
                        console.error("Geolocation error:", error);
                        // alert("Unable to retrieve location updates.");
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 0,
                    }
                );
                $('#btnAbsen').click(function() {
                    navigator.geolocation.clearWatch(watchUser);
                    $(this).prop('disabled', true)
                        .text('Tunggu...')
                        .addClass('btn-disabled')
                        .css('background-color', 'rgba(96, 165, 250, 0.5)');
                });
            } else {
                alert('Geo Location Not Supported By This Browser !!');
                labelMap.removeClass('hidden');
            }

            function showPosition(position) {
                var latitude = position.coords.latitude; // Ganti dengan latitude Anda
                var longitude = position.coords.longitude; // Ganti dengan longitude Anda

                map.setView([latitude, longitude], 14); // ini adalah zoom level

                var circle = L.circle([$('#lat_mitra').val(), $('#long_mitra').val()], {
                    color: 'crimson',
                    fillColor: '#f09',
                    fillOpacity: 0.5,
                    radius: radi
                }).addTo(map).bindPopup("Lokasi absen: <br>" + client);
            }

            function getDistanceFromLatLng(lat1, lng1, lat2, lng2) {
                var pointA = L.latLng(lat1, lng1);
                var pointB = L.latLng(lat2, lng2);
                // console.log(pointA.distanceTo(pointB));
                return pointA.distanceTo(pointB);
            }

            function findClosestLocation(userLatLng, locations, threshold = 5000) {
                var closestLocations = [];
                loc.forEach(function(location) {
                    var distance = getDistanceFromLatLng(userLatLng[0], userLatLng[1], location.latitude,
                        location
                        .longtitude);
                    // Add location to closestLocations array if it's within the threshold
                    // console.log(location, distance)
                    if (distance <= location.radius) {
                        closestLocations.push(location);
                    }
                });
                // console.log(closestLocations);
                return closestLocations;
            }
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var userLatLng = [position.coords.latitude, position.coords.longitude];

                    // Set a distance threshold (e.g., 5000 meters = 5 km)
                    var threshold = 50; // 5 km

                    // Find the closest locations within the threshold distance
                    var closestLocations = findClosestLocation(userLatLng, loc, threshold);

                    // Get the select element
                    const selectMitra = $('.selectMitra');

                    // Clear existing options
                    selectMitra.html('');

                    if (@json(Auth::user()->id) == 10) {
                        // Add the default location to the select dropdown
                        loc.forEach(function(location) {
                            if (location.id == defaultLocationId) {
                                const option = document.createElement('option');
                                var selectedMit = mitra.find(mit => mit.client_id == location
                                    .client_id);
                                option.textContent = selectedMit.client.name;
                                option.value = selectedMit.id;
                                option.selected = true; // Set as selected
                                // console.log(option);
                                selectMitra.append(option);
                            }
                        });

                        // Add closest locations to the select dropdown
                        closestLocations.forEach(function(location) {
                            // Avoid duplicating the default location
                            if (location.id != defaultLocationId) {
                                const option = document.createElement('option');
                                var selectedMit = mitra.find(mit => mit.client_id == location
                                    .client_id);
                                option.textContent = selectedMit.client.name;
                                option.value = selectedMit.id;
                                selectMitra.append(option);

                                selectMitra.change(function() {
                                    // Get the selected client ID from the dropdown
                                    var selectedClientId = $(this).val();

                                    var selectedMit = mitra.find(mit => mit.client_id ==
                                        location
                                        .client_id);
                                    // console.log('iki miti: ', selectedMit);
                                    option.textContent = selectedMit.client.name;
                                    option.value = selectedMit.id;


                                    // Find the location corresponding to the selected client ID
                                    var selectedLocation = loc.find(location => location
                                        .client_id ==
                                        selectedClientId);

                                    if (selectedLocation) {
                                        // Create and open the popup at the selected location
                                        $('#lat_mitra').val(selectedLocation.latitude);
                                        $('#long_mitra').val(selectedLocation.longtitude);
                                        $('#radius_mitra').val(selectedLocation.radius);

                                        L.popup()
                                            .setLatLng([selectedLocation.latitude,
                                                selectedLocation
                                                .longtitude
                                            ])
                                            .setContent("Lokasi absen: <br>" +
                                                selectedLocation.client
                                                .name) // Correct concatenation
                                            .openOn(map);
                                    } else {
                                        console.log(
                                            "Location not found for selected client ID:",
                                            selectedClientId);
                                    }
                                });

                                L.circle([location.latitude, location.longtitude], {
                                    color: 'crimson',
                                    fillColor: '#f09',
                                    fillOpacity: 0.5,
                                    radius: location.radius
                                }).addTo(map);
                            }
                        });
                    } else if (@json(Auth::user()->kerjasama->client_id) == 28) {
                        closestLocations.forEach(function(location) {
                            if (location.id == 25 || location.id == 28) {
                                // console.log("aku: ", location);
                                $('#lat_mitra').val(location.latitude);
                                $('#long_mitra').val(location.longtitude);
                                $('#radius_mitra').val(location.radius);

                                var selectMitra = mitra.find(mit => mit.client_id == location
                                    .client_id);
                                $('#kerjasama_id').val(selectMitra.id);
                                $('.viewKerjasama').val(selectMitra.client.name);

                                L.circle([location.latitude, location.longtitude], {
                                    color: 'crimson',
                                    fillColor: '#f09',
                                    fillOpacity: 0.5,
                                    radius: location.radius
                                }).addTo(map);
                                if (location.id == 28) {
                                    map.setView([location.latitude, location.longtitude], 15);
                                }

                                L.popup()
                                    .setLatLng([location.latitude, location.longtitude])
                                    .setContent("Lokasi absen: <br>" + location.client
                                        .name) // Correct concatenation
                                    .openOn(map);
                            }
                        })
                    } else if (@json(Auth::user()->id) == 7 || @json(Auth::user()->jabatan->code_jabatan) ==
                        "SPV-W") {
                        closestLocations.forEach(function(location) {
                            $('#lat_mitra').val(location.latitude);
                            $('#long_mitra').val(location.longtitude);
                            $('#radius_mitra').val(location.radius);

                            var selectMitra = mitra.find(mit => mit.client_id == location
                                .client_id);
                            $('#kerjasama_id').val(selectMitra.id);
                            $('.viewKerjasama').val(selectMitra.client.name);

                            L.circle([location.latitude, location.longtitude], {
                                color: 'crimson',
                                fillColor: '#f09',
                                fillOpacity: 0.5,
                                radius: location.radius
                            }).addTo(map);
                            if (location.id == 28) {
                                map.setView([location.latitude, location.longtitude], 15);
                            }

                            L.popup()
                                .setLatLng([location.latitude, location.longtitude])
                                .setContent("Lokasi absen: <br>" + location.client
                                    .name) // Correct concatenation
                                .openOn(map);
                        })
                    }
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
            $(window).on('load', function() {
                setTimeout(() => {
                    if (typeof map !== 'undefined') {
                        map.invalidateSize();
                    }
                }, 300);
            });
        })
    </script>
    <!--Waktu-->
    <script defer>
        $(document).ready(function() {
            const dataUserId = $("#dataUser").data('userid');
            const keterangan = $('#keterangan');
            const kerId = {{ Auth::user()->kerjasama_id }};
            let debounceTimer;

            function calculatedJamStart() {
                const now = new Date();
                const jamSaiki = now.getHours();
                const menitSaiki = now.getMinutes();
                const detikSaiki = now.getSeconds();

                const selectedOption = $('#shift_id').find(":selected");
                const shiftStart = selectedOption.data('shift');
                if (!shiftStart) return;

                const [startHours, startMinutes] = shiftStart.split(':').map(Number);
                const startDiffMinutes = startHours * 60 + startMinutes;
                const nowDiffMinutes = jamSaiki * 60 + menitSaiki;
                const jadi = startDiffMinutes - nowDiffMinutes;

                const {
                    h: kesimH,
                    m: kesimM,
                    s: kesimS
                } = formatCountdown(jadi, detikSaiki);
                const {
                    h: kesimH2,
                    m: kesimM2
                } = formatCountdown(jadi - 30, detikSaiki);

                // set keterangan
                const absenKantor = $('#absen-kantor').data('absen-kantor');
                const authName = keterangan.data('authname');
                setKeterangan(jadi, absenKantor, kerId, authName);

                // tombol absen
                if (['MCS', 'SPV'].includes(dataUserId)) {
                    setBtnAbsen(true);
                } else if (jadi <= 90) {
                    setBtnAbsen(true, "Absen");
                } else {
                    setBtnAbsen(false, "Tunggu");
                    $('#labelWaktuStart').html(
                        `Shift anda dimulai ${kesimH} jam ${kesimM} menit ${kesimS} detik lagi,
                 harap tunggu ${kesimH2} jam ${kesimM2} menit ${kesimS} detik lagi`
                    );
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(calculatedJamStart, 1000);
            }

            $('#shift_id').change(calculatedJamStart);

            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 3500
            };

            $('#btnAbsen').click(function() {
                $(this).prop('disabled', true)
                    .text('Tunggu...')
                    .addClass('btn-disabled')
                    .css('background-color', 'rgba(96, 165, 250, 0.5)');
                $('#form-absen').submit();
            });
        });

        function setBtnAbsen(enabled, label = "Absen") {
            const btn = $('#btnAbsen');
            if (enabled) {
                btn.removeClass('cursor-not-allowed bg-blue-400/50 hover:bg-blue-400/50')
                    .prop('disabled', false)
                    .text(label);
                $('#labelWaktuStart').addClass('hidden');
            } else {
                btn.addClass('cursor-not-allowed bg-blue-400/50 hover:bg-blue-400/50')
                    .prop('disabled', true)
                    .text(label);
                $('#labelWaktuStart').removeClass('hidden');
            }
        }

        function formatCountdown(minutesDiff, seconds) {
            return {
                h: Math.floor(minutesDiff / 60),
                m: Math.abs(minutesDiff % 60),
                s: Math.abs(60 - seconds)
            };
        }

        function setKeterangan(jadi, absenKantor, kerId, authName) {
            let value = "masuk";
            if (absenKantor == 1) {
                if (jadi < -32 && !['DIREKTUR', 'DIRUT', 'WAHYUDI'].includes(authName)) {
                    value = "telat";
                }
            } else if (kerId == 11) {
                value = jadi < -15 ? "telat" : "masuk";
            } else {
                value = jadi < 0 ? "telat" : "masuk";
            }
            $('#keterangan').val(value);
        }
    </script>
</x-app-layout>
