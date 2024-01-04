<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>{{ env('APP_NAME', 'KINERJA SAC-PONOROGO') }}</title>
		<link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon">

		<link rel="preconnect" href="https://fonts.bunny.net">
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

		<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
		<link href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        #map {
            height: 240px;
        }
    </style>
</head>

<body class="font-sans antialiased  bg-slate-400">
    <div class="min-h-screen pb-[12.5rem]">
        @include('../layouts/navbar')
        <div class="sm:mx-10 mx-5 bg-slate-500 rounded-md shadow-md">
            <main>
                <div class="px-5 py-5">
                    <p class="p-2 rounded-full bg-white text-center mx-10 my-5 font-semibold">Lokasi Absen Masuk {{ $absen->user->nama_lengkap }}</p>
                    @if($absen->msk_lat && $absen->msk_long)
                        <div id="map" class="rounded-lg"></div>
                    @else
                        <div class="rounded-lg bg-white flex items-center justify-center font-semibold" style="height: 240px;"><span>~ Tidak Ada Koordinat ~</span></div>
                    @endif
                    
                    
                    <p class="p-2 rounded-full bg-white text-center mx-10 my-5 font-semibold">Lokasi Absen Pulang {{ $absen->user->nama_lengkap }}</p>
                    @if($absen->plg_lat && $absen->plg_long)
                        <div id="mapPulang" class="rounded-lg" style="height: 240px;"></div>
                    @else
                        <div class="rounded-lg bg-white flex items-center justify-center font-semibold" style="height: 240px;"><span>~ Tidak Ada Koordinat ~</span></div>
                    @endif
                    <div class="flex justify-center gap-2 sm:justify-end mx-10 my-5">
                        
                            <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <span id="data" data-latitude="{{ $absen->msk_lat }}" data-lat-pulang="{{ $absen->plg_lat }}" data-long-pulang="{{ $absen->plg_long }}" data-longtitude="{{ $absen->msk_long }}" data-user="{{ $absen->user->nama_lengkap }}"></span>



    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
            function initMap() {
                var lat = document.getElementById("data").getAttribute('data-latitude');
                var long = document.getElementById("data").getAttribute('data-longtitude');
                var user = document.getElementById("data").getAttribute('data-user');

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                }

                function showPosition(position) {
                    var map = L.map('map').setView([lat, long], 17);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                    var marker = L.marker([lat, long]).addTo(map);
                    var circle = L.circle([lat, long], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: 50
                    }).addTo(map).bindPopup("Lokasi " + user).openPopup();
                }
            }

            function initMapPulang() {
                var latPulang = document.getElementById("data").getAttribute('data-lat-pulang');
                var longPulang = document.getElementById("data").getAttribute('data-long-pulang');
                var user = document.getElementById("data").getAttribute('data-user');
    
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPositionPulang);
                }
    
                function showPositionPulang(position) {
                    var mapPulang = L.map('mapPulang').setView([latPulang, longPulang], 17);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(mapPulang);
                    var markerPulang = L.marker([latPulang, longPulang]).addTo(mapPulang);
                    var circlePulang = L.circle([latPulang, longPulang], {
                        color: 'red',
                        fillColor: '#f03',
                        fillOpacity: 0.5,
                        radius: 50
                    }).addTo(mapPulang).bindPopup("Lokasi Pulang " + user).openPopup();
                }
            }


            initMap();
            initMapPulang();
        </script>
</body>

</html>
