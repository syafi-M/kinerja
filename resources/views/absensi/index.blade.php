<x-app-layout>
    @push('scripts')
        <script src="{{ URL::asset('js/toastr.min.js') }}"></script>
        <script src="{{ URL::asset('js/moment.min.js') }}"></script>
    @endpush

    <x-main-div>
        <div class="max-w-4xl px-4 py-6 mx-auto">
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-4 mb-6 text-red-700 bg-red-100 rounded-lg">
                    <div class="flex items-start">
                        <i class="mt-0.5 mr-2 text-red-500 ri-error-warning-line"></i>
                        <div>
                            <p class="font-semibold">Terjadi kesalahan:</p>
                            <ul class="mt-1 ml-4 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Header -->
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold tracking-wide uppercase sm:text-3xl text-slate-800">Form Kehadiran</h1>
                <div class="w-24 h-1 mx-auto mt-2 rounded-full bg-amber-500"></div>
            </div>

            <!-- Form Container -->
            <div class="overflow-hidden bg-white shadow-lg rounded-xl">
                <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data" id="form-absen" class="p-6">
                    @csrf
                    @method('POST')

                    <!-- Camera Section -->
                    @if (Auth::user()->kerjasama_id != 1 || !in_array(Auth::user()->devisi_id, [2, 3, 7, 8, 12, 14, 18]))
                        <div class="mb-6">
                            <h2 class="mb-3 text-lg font-semibold text-gray-800">Ambil Foto</h2>
                            <div class="flex flex-col items-center justify-center p-4 bg-gray-100 rounded-lg">
                                <div class="relative">
                                    <video id="video" class="bg-gray-200 rounded-md max-w-[60vw]" autoplay playsinline></video>
                                </div>
                                <canvas id="canvas" style="display:none;"></canvas>
                                <div id="results" class="my-3"></div>
                                <button type="button" id="snapButton" class="flex items-center justify-center w-12 h-12 text-white bg-blue-500 rounded-full hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <i class="text-xl ri-camera-fill"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Map Section -->
                    <div class="mb-6">
                        <h2 class="mb-3 text-lg font-semibold text-gray-800">Lokasi Absen</h2>
                        <div class="p-1 bg-gray-800 rounded-lg">
                            <div id="map-loading" class="flex items-center justify-center h-64 text-white">
                                <span class="flex items-center">
                                    <i class="mr-2 ri-loader-4-line animate-spin"></i>
                                    Loading map...
                                </span>
                            </div>
                            <div id="map" class="overflow-auto rounded-md"></div>
                            <div id="tutor" class="hidden p-3 text-sm italic text-center text-gray-300">
                                <p id="latlongLabel" class="text-xs"></p>
                                <p>Pastikan tanda biru berada dilingkaran</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Name Field -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                {{ Route::currentRouteName() == 'absensi-karyawan-co-cs.index' || Route::currentRouteName() == 'absensi-karyawan-co-scr.index' ? 'Pilih Nama' : 'Nama' }}
                            </label>

                            @if (Route::currentRouteName() == 'absensi-karyawan-co-cs.index' || Route::currentRouteName() == 'absensi-karyawan-co-scr.index')
                                <select name="user_id" id="selectUser" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option selected value="{{ Auth::user()->id }}" data-client="{{ Auth::user()->kerjasama->client_id }}">
                                        {{ Auth::user()->nama_lengkap }}
                                    </option>
                                    @foreach ($userL as $us)
                                        <option value="{{ $us->id }}" data-divisi="{{ $us->devisi_id }}" data-client="{{ $us->kerjasama->client_id }}" data-jab="{{ $us->divisi?->jabatan_id }}">
                                            {{ ucwords(strtolower($us->nama_lengkap)) }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" id="user_id" name="user_id" value="{{ Auth::user()->id }}" hidden>
                                <input type="text" id="name" value="{{ Auth::user()->nama_lengkap }}" disabled class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                            @endif
                        </div>

                        <!-- Penempatan Field -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Penempatan</label>

                            @if (Auth::user()->id == 10)
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 selectMitra" name="kerjasama_id"></select>
                            @else
                                <input type="text" name="kerjasama_id" id="kerjasama_id" hidden value="{{ Auth::user()->kerjasama_id }}">
                                <input type="text" id="kerjasama" value="{{ Auth::user()->kerjasama->client->panggilan ? Auth::user()->kerjasama->client->panggilan : Auth::user()->kerjasama->client->name }}" disabled class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg viewKerjasama">
                            @endif
                        </div>

                        <!-- Jenis Absen -->
                        @if (Auth::user()->kerjasama_id != 1)
                            @php
                                $lanjutShift = isset($absensi[0]) &&
                                            $absensi[0]->absensi_type_pulang &&
                                            $absensi[0]->tanggal_absen == now()->format('Y-m-d') &&
                                            $absensi[0]->masuk;
                            @endphp

                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-700">Jenis Absen</label>
                                <div class="p-4 rounded-lg bg-gray-50">
                                    <div class="flex items-center mb-2">
                                        <input type="radio" name="jenis_abs" value="1" class="mr-2 text-blue-600 focus:ring-blue-500" {{ $lanjutShift ? 'disabled' : 'checked' }}>
                                        <label class="text-gray-700">Masuk</label>
                                    </div>

                                    @if ($lanjutShift)
                                        <div class="flex items-center mb-2">
                                            <input type="radio" name="jenis_abs" value="1" class="mr-2 text-blue-600 focus:ring-blue-500 disabled" checked>
                                            <label class="text-gray-700">Meneruskan Shift</label>
                                        </div>
                                    @endif

                                    <div class="flex items-center mb-2">
                                        <input type="radio" name="jenis_abs" value="1" class="mr-2 text-blue-600 focus:ring-blue-500" disabled>
                                        <label class="text-gray-400">Tukar Shift (maintenance)</label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="radio" name="jenis_abs" value="1" class="mr-2 text-blue-600 focus:ring-blue-500" disabled>
                                        <label class="text-gray-400">Lembur (maintenance)</label>
                                    </div>
                                </div>
                            </div>

                            <div class="hidden md:col-span-2" id="divPengganti">
                                <label class="block mb-2 text-sm font-medium text-gray-700">Pengganti</label>
                                <select name="pengganti" id="pengganti" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @if ($errors->any() && $errors->pengganti)
                                        <option selected disabled class="font-bold text-red-600">
                                            Pengganti Tidak Boleh Kosong
                                        </option>
                                    @endif
                                    <option disabled {{ $errors->any() && $errors->pengganti ? '' : 'selected' }}>
                                        -- Pilih Pengganti --
                                    </option>
                                    <option>Belum Ada Karyawan</option>
                                </select>
                            </div>
                        @endif

                        <!-- Shift Selection -->
                        @if (Auth::user()->divisi->jabatan->name_jabatan == 'DIREKSI')
                            <input type="hidden" name="shift_id" value="145" />
                        @elseif(Auth::user()->jabatan->code_jabatan == 'SPV-W')
                            <input type="hidden" name="shift_id" value="195" />
                        @elseif(Auth::user()->devisi_id == 12)
                            <input type="hidden" name="shift_id" value="200" />
                        @else
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-700">Shift</label>
                                <select name="shift_id" id="shift_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @if ($errors->any() && $errors->shift_id)
                                        <option selected disabled class="font-bold text-red-600">
                                            Shift Tidak Boleh Kosong
                                        </option>
                                    @endif
                                    <option disabled {{ $errors->any() && $errors->shift_id ? '' : 'selected' }}>
                                        -- Pilih Shift --
                                    </option>

                                    @forelse ($shift as $i)
                                        <option value="{{ $i->id }}" data-shift="{{ $i?->jam_start }}">
                                            ({{ Carbon\Carbon::parse($i?->jam_start)->format('H:i') }} - {{ Carbon\Carbon::parse($i?->jam_end)->subHour()->format('H:i') }}) {{ ucwords(strtolower($i?->shift_name)) }}
                                        </option>
                                    @empty
                                        <option readonly disabled>~ Tidak ada Shift ! ~</option>
                                    @endforelse
                                </select>

                                @if (Auth::user()->kerjasama->client_id == 1)
                                    <span id="absen-kantor" data-absen-kantor="{{ Auth::user()->kerjasama->client_id }}" hidden></span>
                                @endif
                            </div>
                        @endif

                        <!-- Perlengkapan -->
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Perlengkapan</label>
                            <div class="p-4 rounded-lg bg-gray-50">
                                @if ($errors->any() && $errors->perlengkapan)
                                    <p class="mb-2 font-bold text-red-600">Perlengkapan Tidak Boleh Kosong</p>
                                @endif

                                <div id="divPerlengkapan" class="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3">
                                    @forelse ($dev as $arr)
                                        @foreach ($arr->perlengkapan as $i)
                                            <div class="flex items-center">
                                                <input type="checkbox" name="perlengkapan[]" id="perlengkapan{{ $i->id }}" value="{{ $i->name }}" class="mr-2 text-blue-600 focus:ring-blue-500 perle">
                                                <label for="perlengkapan{{ $i->id }}" class="text-gray-700">{{ ucwords(strtolower($i->name)) }}</label>
                                            </div>
                                        @endforeach
                                    @empty
                                        <p class="text-gray-500">~ Kosong ~</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Deskripsi (opsional)</label>
                            <textarea name="deskripsi" id="deskripsi" placeholder="deskripsi..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    @if (Auth::user()->kerjasama_id != 1 || !in_array(Auth::user()->devisi_id, [2, 3, 7, 8, 12, 14, 18]))
                        <input type="text" id="image" name="image" class="image-tag" hidden>
                    @endif

                    <input type="text" id="keterangan" name="keterangan" value="masuk" hidden>
                    <input type="text" name="absensi_type_masuk" value="1" hidden>

                    <!-- Action Buttons -->
                    <div class="flex flex-col items-center justify-center gap-4 mt-8">
                        <span id="labelWaktuStart" class="hidden px-4 py-2 text-xs font-semibold text-center text-gray-700 bg-gray-100 rounded-md"></span>

                        <div class="flex flex-col justify-center w-full gap-3 sm:flex-row">
                            @forelse ($absensi as $abs)
                                @if (!$cekRoute &&
                                    $abs->tanggal_absen == now()->format('Y-m-d') &&
                                    $abs->absensi_type_pulang == null &&
                                    $abs->tukar == null &&
                                    !$afaLib)
                                    <button class="w-full px-4 py-2 text-gray-500 bg-gray-200 rounded-lg cursor-not-allowed" disabled>Sudah Absen</button>
                                @elseif($afaLib && Auth::user()->divisi->jabatan_id != 35)
                                    <button class="w-full px-4 py-2 text-gray-500 bg-gray-200 rounded-lg cursor-not-allowed" disabled>Jadwal Tidak Ada</button>
                                @elseif(!$cekRoute && $abs->tanggal_absen == now()->format('Y-m-d') && $abs->terus)
                                    <button class="w-full px-4 py-2 text-gray-500 bg-gray-200 rounded-lg cursor-not-allowed" disabled>Sudah Absen 2x</button>
                                @else
                                    <button type="button" class="w-full px-4 py-2 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 btnAbsen" id="btnAbsen">Absen</button>
                                @endif
                            @break
                            @empty
                                <button type="button" class="w-full px-4 py-2 text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 btnAbsen" id="btnAbsen">Absen</button>
                            @endforelse

                            <a href="{{ route('dashboard.index') }}" class="w-full px-4 py-2 text-center text-white transition-colors bg-red-500 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Hidden Location Data -->
                    <div id="HiddenOnes" class="hidden">
                        <input type="hidden" id="lat" name="lat_user" value="" class="lat_user" />
                        <input type="hidden" id="long" name="long_user" value="" class="long_user" />
                        <input type="hidden" id="lat_mitra" name="lat_mitra" value="{{ $harLok->latitude }}" />
                        <input type="hidden" id="long_mitra" name="long_mitra" value="{{ $harLok->longtitude }}" />
                        <input type="hidden" id="radius_mitra" name="radius_mitra" value="{{ $harLok->radius }}" />
                    </div>

                    <span id="dataUser" data-userId="{{ Auth::user()->divisi->jabatan->code_jabatan }}" hidden></span>
                </form>
            </div>
        </div>
    </x-main-div>
    <!-- Camera Script -->
    @if (auth()->user()->kerjasama_id != 1 || !in_array(auth()->user()->devisi_id, [2, 3, 7, 8, 12, 14, 18]))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // DOM Elements
                const video = document.getElementById('video');
                const canvas = document.getElementById('canvas');
                const context = canvas.getContext('2d', { willReadFrequently: true });
                const snapButton = document.getElementById('snapButton');
                const resultsDiv = document.getElementById('results');

                // State
                let isDarkEnvironment = false;
                const isLeadr = {!! json_encode(
                    Route::currentRouteName() == 'absensi-karyawan-co-cs.index' ||
                    Route::currentRouteName() == 'absensi-karyawan-co-scr.index'
                ) !!};

                // Canvas setup
                canvas.width = 320;
                canvas.height = 240;

                // Camera constraints
                const constraints = {
                    audio: false,
                    video: {
                        facingMode: isLeadr ? 'environment' : 'user',
                        width: 450,
                        height: 450
                    }
                };

                // Initialize camera
                navigator.mediaDevices.getUserMedia(constraints)
                    .then(function(mediaStream) {
                        video.srcObject = mediaStream;
                        video.onloadedmetadata = function() {
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            video.play();

                            // Check video status periodically
                            const videoCheckInterval = setInterval(checkVideoStatus, 1000);

                            // Cleanup on page unload
                            window.addEventListener('beforeunload', () => {
                                clearInterval(videoCheckInterval);
                                mediaStream.getTracks().forEach(track => track.stop());
                            });
                        };
                    })
                    .catch(function(err) {
                        console.error('Gagal mengambil akses kamera:', err);
                        alert('Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin kamera.');
                    });

                // Snapshot function
                function takeSnapshot() {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Enhance image if in dark environment
                    if (isDarkEnvironment) {
                        enhanceImage();
                    }

                    // Save image data
                    const dataURL = canvas.toDataURL('image/jpeg', 0.9);
                    document.querySelector('.image-tag').value = dataURL;

                    // Show preview
                    resultsDiv.innerHTML = `<img id="imgprev" width="200" height="200" class="rounded-md" src="${dataURL}"/>`;
                }

                // Image enhancement for dark environments
                function enhanceImage() {
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const data = imageData.data;
                    const brightnessFactor = 40;
                    const contrastFactor = 1.3;

                    for (let i = 0; i < data.length; i += 4) {
                        // Apply brightness
                        data[i] = Math.min(data[i] + brightnessFactor, 255);     // Red
                        data[i + 1] = Math.min(data[i + 1] + brightnessFactor, 255); // Green
                        data[i + 2] = Math.min(data[i + 2] + brightnessFactor, 255); // Blue

                        // Apply contrast
                        data[i] = ((data[i] - 128) * contrastFactor + 128);
                        data[i + 1] = ((data[i + 1] - 128) * contrastFactor + 128);
                        data[i + 2] = ((data[i + 2] - 128) * contrastFactor + 128);
                    }

                    context.putImageData(imageData, 0, 0);
                }

                // Check video status (lighting conditions)
                function checkVideoStatus() {
                    canvas.width = 450;
                    canvas.height = 450;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const data = imageData.data;

                    // Check for dark environment
                    let blackPixels = 0;
                    for (let i = 0; i < data.length; i += 4) {
                        const avgColor = (data[i] + data[i + 1] + data[i + 2]) / 3;
                        if (avgColor < 20) blackPixels++;
                    }

                    isDarkEnvironment = blackPixels > (canvas.width * canvas.height * 0.7);

                    // Check for color dominance
                    const colorThresholds = {
                        red: { red: 150, green: 100, blue: 100 },
                        purple: { red: 150, green: 100, blue: 150 },
                        darkBlue: { red: 100, green: 100, blue: 150 }
                    };

                    const redPixels = detectColor(data, colorThresholds.red);
                    const purplePixels = detectColor(data, colorThresholds.purple);
                    const darkBluePixels = detectColor(data, colorThresholds.darkBlue);

                    // Check if any color is too dominant
                    const totalPixels = canvas.width * canvas.height;
                    const colorDominance = Math.max(
                        redPixels / totalPixels,
                        purplePixels / totalPixels,
                        darkBluePixels / totalPixels
                    );

                    if (colorDominance > 0.2) {
                        alert('Terlalu banyak warna terdeteksi!\nTolong agak menjauh dari kamera');
                        snapButton.style.display = 'none';
                    } else {
                        snapButton.style.display = 'block';
                    }

                    // Check if environment is too dark
                    if (isDarkEnvironment) {
                        alert('Output kamera hitam!\nTolong pindah ke tempat yang lebih terang');
                        snapButton.style.display = 'none';
                    } else {
                        snapButton.style.display = 'block';
                    }
                }

                // Detect specific color in image data
                function detectColor(data, thresholds) {
                    let colorPixels = 0;
                    for (let i = 0; i < data.length; i += 4) {
                        const red = data[i];
                        const green = data[i + 1];
                        const blue = data[i + 2];

                        if (red > thresholds.red &&
                            green < thresholds.green &&
                            blue < thresholds.blue) {
                            colorPixels++;
                        }
                    }
                    return colorPixels;
                }

                // Event listeners
                snapButton.addEventListener('click', takeSnapshot);
            });
        </script>
    @endif

    <!-- Shift Selection Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectUser = document.getElementById('selectUser');
            const shiftSelect = document.getElementById('shift_id');
            const perlengkapanDiv = document.getElementById('divPerlengkapan');
            const radioButtons = document.querySelectorAll('input[name="jenis_abs"]');
            const divPengganti = document.getElementById('divPengganti');

            // Initialize
            if (selectUser) {
                selectUser.addEventListener('change', handleUserChange);
            }

            // Radio button change handler
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selected = this.dataset.pilih;
                    if (selected === "tukar") {
                        divPengganti.style.display = 'block';
                    } else {
                        divPengganti.style.display = 'none';
                    }
                });
            });

            // Trigger change for default selected radio
            const checkedRadio = document.querySelector('input[name="jenis_abs"]:checked');
            if (checkedRadio) {
                checkedRadio.dispatchEvent(new Event('change'));
            }

            // Handle user selection change
            function handleUserChange() {
                const selectedOption = selectUser.options[selectUser.selectedIndex];
                const clientId = selectedOption.dataset.client;
                const jabId = selectedOption.dataset.jab;

                if (!clientId || !jabId) return;

                // Fetch shift data
                fetch(`/get-shifts/${clientId}/${jabId}`)
                    .then(response => response.json())
                    .then(data => {
                        updateShiftOptions(data.shift);
                        updatePerlengkapanOptions(data.dev, selectedOption.dataset.divisi);
                    })
                    .catch(error => console.error('Error fetching shift data:', error));
            }

            // Update shift options
            function updateShiftOptions(shifts) {
                let html = '<option disabled selected>-- Pilih Shift --</option>';

                if (shifts.length === 0) {
                    html += '<option>~ Tidak ada Shift ! ~</option>';
                } else {
                    shifts.forEach(shift => {
                        html += `<option value="${shift.id}" data-shift="${shift.jam_start}">
                            ${shift.jam_start} - ${shift.jam_end} | ${shift.jabatan.name_jabatan} | ${shift.shift_name}
                        </option>`;
                    });
                }

                shiftSelect.innerHTML = html;
            }

            // Update perlengkapan options
            function updatePerlengkapanOptions(divisions, selectedDivId) {
                let html = '';

                const selectedDivision = divisions.find(div => div.id == selectedDivId);
                if (selectedDivision && selectedDivision.perlengkapan) {
                    selectedDivision.perlengkapan.forEach(perle => {
                        html += `
                            <div class="flex items-center">
                                <input type="checkbox" name="perlengkapan[]" id="perlengkapan${perle.id}"
                                    value="${perle.name}" class="m-2 checkbox checkbox-sm perle">
                                <label for="perlengkapan${perle.id}">${perle.name}</label>
                            </div>
                        `;
                    });
                } else {
                    html = '<p>~ Kosong ~</p>';
                }

                perlengkapanDiv.innerHTML = html;
            }
        });
    </script>

    <!-- Map Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const mapElement = document.getElementById('map');
            const mapLoading = document.getElementById('map-loading');
            const latInput = document.getElementById('lat');
            const longInput = document.getElementById('long');
            const labelMap = document.getElementById('labelMap');
            const tutor = document.getElementById('tutor');
            const latlongLabel = document.getElementById('latlongLabel');
            const formAbsen = document.getElementById('form-absen');
            const btnAbsen = document.getElementById('btnAbsen');

            // Location data
            const loc = @json($lokLok);
            const mitra = @json($penempatan);
            const defaultLocationId = "{{ auth()->user()->kerjasama_id }}";
            const lati = parseFloat("{{ $harLok->latitude }}");
            const longi = parseFloat("{{ $harLok->longtitude }}");
            const radi = parseFloat("{{ $harLok->radius }}");
            const client = "{{ $harLok->client->name }}";

            // Validate coordinates
            if (isNaN(lati) || isNaN(longi) || isNaN(radi)) {
                mapLoading.innerHTML = '<span class="text-red-500">Invalid location data</span>';
                return;
            }

            // Initialize map
            const map = L.map('map').setView([lati, longi], 15);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add location circle
            const circle = L.circle([lati, longi], {
                color: 'crimson',
                fillColor: '#f09',
                fillOpacity: 0.5,
                radius: radi
            }).addTo(map).bindPopup(`Lokasi absen: <br>${client}`);

            // Hide loading message
            mapLoading.style.display = 'none';

            // Device detection
            const deviceType = /android/i.test(navigator.userAgent) ? 'Android' :
                              /iphone|ipad|ipod/i.test(navigator.userAgent) ? 'iPhone' : 'Unknown';
            const latlngLength = deviceType === 'iPhone' ? 18 : 11;

            // Geolocation setup
            if (!navigator.geolocation) {
                labelMap.innerHTML = '<p>Geo Location Not Supported By This Browser</p>';
                labelMap.classList.remove('hidden');
                return;
            }

            let userMarker = null;
            let watchId = null;

            // Get current position
            navigator.geolocation.getCurrentPosition(
                position => {
                    const { latitude, longitude } = position.coords;

                    // Update form fields
                    latInput.value = latitude;
                    longInput.value = longitude;

                    // Create user marker
                    userMarker = L.marker([latitude, longitude])
                        .addTo(map)
                        .bindPopup("Lokasi anda");

                    // Update UI
                    labelMap.classList.add('hidden');
                    tutor.classList.remove('hidden');

                    // Calculate distance
                    const userLatLng = L.latLng([latitude, longitude]);
                    const circleLatLng = L.latLng([lati, longi]);
                    const distanceFromCenter = userLatLng.distanceTo(circleLatLng);
                    const distanceFromBorder = distanceFromCenter - radi;

                    // Update coordinates display
                    latlongLabel.innerHTML =
                        `[${latitude.toFixed(6)}, ${longitude.toFixed(6)}, ${distanceFromBorder.toFixed(2)}m]`;

                    // Update button state
                    updateButtonState(latitude, distanceFromBorder);

                    // Watch position updates
                    watchId = navigator.geolocation.watchPosition(
                        position => {
                            const { latitude, longitude } = position.coords;

                            // Update form fields
                            latInput.value = latitude;
                            longInput.value = longitude;

                            // Update marker position
                            if (userMarker) {
                                userMarker.setLatLng([latitude, longitude]);
                            }

                            // Recalculate distance
                            const newUserLatLng = L.latLng([latitude, longitude]);
                            const newDistanceFromCenter = newUserLatLng.distanceTo(circleLatLng);
                            const newDistanceFromBorder = newDistanceFromCenter - radi;

                            // Update coordinates display
                            latlongLabel.innerHTML =
                                `[${latitude.toFixed(6)}, ${longitude.toFixed(6)}, ${newDistanceFromBorder.toFixed(2)}m]`;

                            // Update button state
                            updateButtonState(latitude, newDistanceFromBorder);
                        },
                        error => {
                            console.error("Geolocation error:", error);
                        }, {
                            enableHighAccuracy: true,
                            maximumAge: 0,
                            timeout: 10000
                        }
                    );

                    // Handle form submission
                    btnAbsen.addEventListener('click', function() {
                        if (watchId) {
                            navigator.geolocation.clearWatch(watchId);
                        }
                        this.disabled = true;
                        this.textContent = 'Tunggu...';
                        this.classList.add('btn-disabled');
                        this.style.backgroundColor = 'rgba(96, 165, 250, 0.5)';
                    });
                },
                error => {
                    console.error("Geolocation error:", error);
                    labelMap.innerHTML = `<p>Error getting location: ${error.message}</p>`;
                    labelMap.classList.remove('hidden');
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );

            // Update button state based on location
            function updateButtonState(latitude, distanceFromBorder) {
                const isSupervisor = @json(auth()->user()->jabatan->code_jabatan) === "SPV-W" ||
                                    @json(auth()->user()->devisi_id) === 12;

                if (isSupervisor && latitude.toString().length <= latlngLength) {
                    formAbsen.action = '{{ route('absensi.store') }}';
                    btnAbsen.textContent = 'Absen';
                    btnAbsen.disabled = false;
                    btnAbsen.classList.remove('btn-disabled');
                    btnAbsen.classList.add('bg-blue-500', 'hover:bg-blue-600');
                } else {
                    if (latitude.toString().length > latlngLength || distanceFromBorder > 1) {
                        formAbsen.action = '#';
                        btnAbsen.textContent = 'Diluar Radius';
                        btnAbsen.disabled = true;
                        btnAbsen.classList.add('btn-disabled');
                        btnAbsen.style.backgroundColor = 'rgba(96, 165, 250, 0.5)';
                    } else {
                        formAbsen.action = '{{ route('absensi.store') }}';
                        btnAbsen.textContent = 'Absen';
                        btnAbsen.disabled = false;
                        btnAbsen.classList.remove('btn-disabled');
                        btnAbsen.classList.add('bg-blue-500', 'hover:bg-blue-600');
                    }
                }
            }

            // Handle window resize
            window.addEventListener('resize', () => {
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
            });

            // Find closest locations (if needed)
            function findClosestLocation(userLatLng, locations, threshold = 50) {
                return locations.filter(location => {
                    const distance = L.latLng(userLatLng).distanceTo([location.latitude, location.longtitude]);
                    return distance <= location.radius;
                });
            }

            // Only process closest locations if user has specific permissions
            if (@json(Auth::user()->id) === 10 ||
                @json(Auth::user()->kerjasama->client_id) === 28 ||
                @json(Auth::user()->id) === 7 ||
                @json(auth()->user()->jabatan->code_jabatan) === "SPV-W") {

                navigator.geolocation.getCurrentPosition(position => {
                    const userLatLng = [position.coords.latitude, position.coords.longitude];
                    const closestLocations = findClosestLocation(userLatLng, loc);

                    // Process closest locations based on user permissions
                    if (@json(Auth::user()->id) === 10) {
                        processLocationsForUser10(closestLocations);
                    } else if (@json(Auth::user()->kerjasama->client_id) === 28) {
                        processLocationsForClient28(closestLocations);
                    } else if (@json(Auth::user()->id) === 7 || @json(auth()->user()->jabatan->code_jabatan) === "SPV-W") {
                        processLocationsForSupervisor(closestLocations);
                    }
                });
            }

            // Process locations for user ID 10
            function processLocationsForUser10(closestLocations) {
                const selectMitra = document.querySelector('.selectMitra');
                selectMitra.innerHTML = '';

                // Add default location
                loc.forEach(location => {
                    if (location.id === defaultLocationId) {
                        const selectedMit = mitra.find(mit => mit.client_id === location.client_id);
                        const option = new Option(selectedMit.client.name, selectedMit.id, true, true);
                        selectMitra.add(option);
                    }
                });

                // Add closest locations
                closestLocations.forEach(location => {
                    if (location.id !== defaultLocationId) {
                        const selectedMit = mitra.find(mit => mit.client_id === location.client_id);
                        const option = new Option(selectedMit.client.name, selectedMit.id);
                        selectMitra.add(option);

                        // Add circle to map
                        L.circle([location.latitude, location.longtitude], {
                            color: 'crimson',
                            fillColor: '#f09',
                            fillOpacity: 0.5,
                            radius: location.radius
                        }).addTo(map);
                    }
                });

                // Handle select change
                selectMitra.addEventListener('change', function() {
                    const selectedClientId = this.value;
                    const selectedLocation = loc.find(loc => loc.client_id == selectedClientId);

                    if (selectedLocation) {
                        // Update form fields
                        document.getElementById('lat_mitra').value = selectedLocation.latitude;
                        document.getElementById('long_mitra').value = selectedLocation.longtitude;
                        document.getElementById('radius_mitra').value = selectedLocation.radius;

                        // Show popup
                        L.popup()
                            .setLatLng([selectedLocation.latitude, selectedLocation.longtitude])
                            .setContent(`Lokasi absen: <br>${selectedLocation.client.name}`)
                            .openOn(map);
                    }
                });
            }

            // Process locations for client ID 28
            function processLocationsForClient28(closestLocations) {
                closestLocations.forEach(location => {
                    if (location.id === 25 || location.id === 28) {
                        // Update form fields
                        document.getElementById('lat_mitra').value = location.latitude;
                        document.getElementById('long_mitra').value = location.longtitude;
                        document.getElementById('radius_mitra').value = location.radius;

                        const selectedMit = mitra.find(mit => mit.client_id === location.client_id);
                        document.getElementById('kerjasama_id').value = selectedMit.id;
                        document.querySelector('.viewKerjasama').value = selectedMit.client.name;

                        // Add circle to map
                        L.circle([location.latitude, location.longtitude], {
                            color: 'crimson',
                            fillColor: '#f09',
                            fillOpacity: 0.5,
                            radius: location.radius
                        }).addTo(map);

                        // Set view if location ID is 28
                        if (location.id === 28) {
                            map.setView([location.latitude, location.longtitude], 15);
                        }

                        // Show popup
                        L.popup()
                            .setLatLng([location.latitude, location.longtitude])
                            .setContent(`Lokasi absen: <br>${location.client.name}`)
                            .openOn(map);
                    }
                });
            }

            // Process locations for supervisor
            function processLocationsForSupervisor(closestLocations) {
                closestLocations.forEach(location => {
                    // Update form fields
                    document.getElementById('lat_mitra').value = location.latitude;
                    document.getElementById('long_mitra').value = location.longtitude;
                    document.getElementById('radius_mitra').value = location.radius;

                    const selectedMit = mitra.find(mit => mit.client_id === location.client_id);
                    document.getElementById('kerjasama_id').value = selectedMit.id;
                    document.querySelector('.viewKerjasama').value = selectedMit.client.name;

                    // Add circle to map
                    L.circle([location.latitude, location.longtitude], {
                        color: 'crimson',
                        fillColor: '#f09',
                        fillOpacity: 0.5,
                        radius: location.radius
                    }).addTo(map);

                    // Set view if location ID is 28
                    if (location.id === 28) {
                        map.setView([location.latitude, location.longtitude], 15);
                    }

                    // Show popup
                    L.popup()
                        .setLatLng([location.latitude, location.longitude])
                        .setContent(`Lokasi absen: <br>${location.client.name}`)
                        .openOn(map);
                });
            }
        });
    </script>

    <!-- Time Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataUserId = document.getElementById('dataUser').dataset.userid;
            const keterangan = document.getElementById('keterangan');
            const kerId = {{ Auth::user()->kerjasama_id }};
            const shiftSelect = document.getElementById('shift_id');
            const btnAbsen = document.getElementById('btnAbsen');
            const labelWaktuStart = document.getElementById('labelWaktuStart');

            let countdownInterval;

            // Initialize shift change handler
            if (shiftSelect) {
                shiftSelect.addEventListener('change', startCountdown);
                // Start countdown if shift is already selected
                if (shiftSelect.value) {
                    startCountdown();
                }
            }

            // Configure toastr
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 3500
            };

            // Handle absen button click
            btnAbsen.addEventListener('click', function() {
                this.disabled = true;
                this.textContent = 'Tunggu...';
                this.classList.add('btn-disabled');
                this.style.backgroundColor = 'rgba(96, 165, 250, 0.5)';

                // Clear countdown interval
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }

                // Submit form
                document.getElementById('form-absen').submit();
            });

            // Start countdown function
            function startCountdown() {
                // Clear existing interval
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }

                // Get selected shift
                const selectedOption = shiftSelect.options[shiftSelect.selectedIndex];
                const shiftStart = selectedOption.dataset.shift;

                if (!shiftStart) return;

                // Parse shift start time
                const [startHours, startMinutes] = shiftStart.split(':').map(Number);

                // Start countdown
                countdownInterval = setInterval(() => {
                    const now = new Date();
                    const nowHours = now.getHours();
                    const nowMinutes = now.getMinutes();
                    const nowSeconds = now.getSeconds();

                    // Calculate time difference in minutes
                    const startDiffMinutes = startHours * 60 + startMinutes;
                    const nowDiffMinutes = nowHours * 60 + nowMinutes;
                    const diffMinutes = startDiffMinutes - nowDiffMinutes;

                    // Format countdown
                    const { h: hours, m: minutes, s: seconds } = formatCountdown(diffMinutes, nowSeconds);
                    const { h: warnHours, m: warnMinutes } = formatCountdown(diffMinutes - 30, nowSeconds);

                    // Set keterangan
                    setKeterangan(diffMinutes);

                    // Update button state
                    if (['MCS', 'SPV'].includes(dataUserId)) {
                        setButtonState(true);
                    } else if (diffMinutes <= 90) {
                        setButtonState(true);
                    } else {
                        setButtonState(false);
                        labelWaktuStart.textContent =
                            `Shift anda dimulai ${hours} jam ${minutes} menit ${seconds} detik lagi, ` +
                            `harap tunggu ${warnHours} jam ${warnMinutes} menit ${seconds} detik lagi`;
                        labelWaktuStart.classList.remove('hidden');
                    }
                }, 1000);
            }

            // Format countdown
            function formatCountdown(minutesDiff, seconds) {
                return {
                    h: Math.floor(Math.abs(minutesDiff) / 60),
                    m: Math.abs(minutesDiff % 60),
                    s: Math.abs(60 - seconds)
                };
            }

            // Set keterangan value
            function setKeterangan(diffMinutes) {
                let value = "masuk";

                const absenKantor = document.getElementById('absen-kantor')?.dataset.absenKantor;
                const authName = keterangan.dataset.authname;

                if (absenKantor == 1) {
                    if (diffMinutes < -32 && !['DIREKTUR', 'DIRUT', 'WAHYUDI'].includes(authName)) {
                        value = "telat";
                    }
                } else if (kerId == 11) {
                    value = diffMinutes < -15 ? "telat" : "masuk";
                } else {
                    value = diffMinutes < 0 ? "telat" : "masuk";
                }

                keterangan.value = value;
            }

            // Set button state
            function setButtonState(enabled) {
                if (enabled) {
                    btnAbsen.disabled = false;
                    btnAbsen.textContent = 'Absen';
                    btnAbsen.classList.remove('cursor-not-allowed', 'bg-blue-400/50', 'hover:bg-blue-400/50');
                    btnAbsen.classList.add('bg-blue-500', 'hover:bg-blue-600');
                    labelWaktuStart.classList.add('hidden');
                } else {
                    btnAbsen.disabled = true;
                    btnAbsen.textContent = 'Tunggu';
                    btnAbsen.classList.add('cursor-not-allowed', 'bg-blue-400/50', 'hover:bg-blue-400/50');
                    btnAbsen.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                }
            }
        });
    </script>
</x-app-layout>
