<x-mitra-layout title="Detail Lokasi Absensi">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        #map {
            height: 360px;
        }
    </style>

    <div class="p-6 border shadow-xl bg-gradient-to-r from-slate-700 to-slate-800 border-slate-600 rounded-3xl">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-[11px] font-black uppercase tracking-[0.25em] text-blue-400">Detail Koordinat Absensi</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-white">
                    Lokasi Absen {{ $absen?->user?->nama_lengkap ?? '-' }}
                </h1>
                <p class="mt-1 text-sm text-slate-300">Pantau titik masuk dan pulang pada tanggal yang dipilih secara real-time.</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="px-3 py-1 text-xs font-semibold text-blue-300 rounded-full bg-blue-500/20">Masuk: Pin Biru</span>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-500/20 text-amber-300">Pulang: Pin Oranye</span>
                </div>
            </div>
            <form method="get" action="{{ $absen ? route('mitra-lihatMap', $absen->id) : '' }}"
                class="w-full p-3 border md:w-auto rounded-2xl border-slate-600 bg-slate-800/70">
                <p class="mb-2 text-[10px] font-black tracking-[0.2em] text-slate-400 uppercase">Filter Tanggal</p>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="date" value="{{ $tgl ?: $absen?->tanggal_absen }}" name="tgl"
                        class="text-sm input input-sm input-bordered bg-slate-200 text-slate-800 border-slate-300" />
                <input type="hidden" name="user" value="{{ $us ?: $absen?->user_id }}" />
                    <button class="text-white btn btn-sm btn-info">Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="p-5 mt-6 border shadow-sm bg-slate-700/50 rounded-2xl border-slate-600/50">
        <div class="grid grid-cols-1 gap-3 mb-4 sm:grid-cols-3">
            <div class="p-3 border rounded-xl bg-slate-800/70 border-slate-600">
                <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black">Tanggal</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $tgl ?: $absen?->tanggal_absen ?: '-' }}</p>
            </div>
            <div class="p-3 border rounded-xl bg-slate-800/70 border-slate-600">
                <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black">Latitude Masuk</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $absen?->msk_lat ?: '-' }}</p>
            </div>
            <div class="p-3 border rounded-xl bg-slate-800/70 border-slate-600">
                <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black">Longitude Masuk</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ $absen?->msk_long ?: '-' }}</p>
            </div>
        </div>

        @if($absen)
            <span id="data" data-latitude="{{ $absen->msk_lat }}" data-lat-pulang="{{ $absen->plg_lat }}"
                data-long-pulang="{{ $absen->plg_long }}" data-longtitude="{{ $absen->msk_long }}"
                data-user="{{ $absen->user?->nama_lengkap }}"></span>
        @endif

        @if($absen?->msk_lat && $absen?->msk_long)
            <div class="p-2 border rounded-2xl bg-slate-800/60 border-slate-600">
                <div id="map" class="rounded-xl"></div>
            </div>
        @else
            <div class="flex items-center justify-center font-semibold rounded-xl bg-slate-600/60 text-slate-200"
                style="height: 360px;">
                <span>~ Tidak Ada Koordinat ~</span>
            </div>
        @endif

        <div class="flex flex-wrap gap-4 mt-4 font-medium {{ $absen ? '' : 'hidden' }}">
            <div class="w-full px-4 py-3 rounded-lg sm:w-auto bg-slate-200 text-slate-800">
                <p class="text-sm font-bold">Keterangan Peta</p>
                <table class="mt-1">
                    <tbody>
                        <tr>
                            <td class="pr-2"><img src="{{ URL::asset('logo/pin-biru.png') }}" alt="Pin Masuk"></td>
                            <td><p>: Lokasi Masuk</p></td>
                        </tr>
                        <tr>
                            <td class="pr-2"><img src="{{ URL::asset('logo/pin-oren.png') }}" alt="Pin Pulang"></td>
                            <td><p>: Lokasi Pulang</p></td>
                        </tr>
                        <tr>
                            <td><p>Jarak</p></td>
                            <td><p id="jarak" class="font-bold text-blue-700">: -</p></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-end mt-5">
            <a href="{{ route('mitra_absensi') }}" class="btn btn-error">Kembali</a>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const dataEl = document.getElementById("data");
            if (!dataEl || !document.getElementById("map")) {
                return;
            }

            const lat = parseFloat(dataEl.getAttribute('data-latitude'));
            const long = parseFloat(dataEl.getAttribute('data-longtitude'));
            const latPulang = parseFloat(dataEl.getAttribute('data-lat-pulang'));
            const longPulang = parseFloat(dataEl.getAttribute('data-long-pulang'));
            const lokMitra = @json($lokMitra);

            const map = L.map('map').setView([lat, long], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            if (lokMitra && lokMitra.latitude && lokMitra.longtitude && lokMitra.radius) {
                L.circle([lokMitra.latitude, lokMitra.longtitude], {
                    color: 'crimson',
                    fillColor: '#f09',
                    fillOpacity: 0.3,
                    radius: lokMitra.radius
                }).addTo(map).bindPopup("Lokasi Mitra");
            }

            const redIcon = L.icon({
                iconUrl: @json(URL::asset('logo/pin-oren.png')),
                iconSize: [25, 27],
                iconAnchor: [14, 24],
                popupAnchor: [1, -34]
            });

            const blueIcon = L.icon({
                iconUrl: @json(URL::asset('logo/pin-biru.png')),
                iconSize: [25, 27],
                iconAnchor: [14, 24],
                popupAnchor: [1, -34]
            });

            L.marker([lat, long], {
                icon: blueIcon
            }).addTo(map).bindPopup("Lokasi Masuk");

            const validPulang = !Number.isNaN(latPulang) && !Number.isNaN(longPulang);
            if (validPulang) {
                L.marker([latPulang, longPulang], {
                    icon: redIcon
                }).addTo(map).bindPopup("Lokasi Pulang");

                const polyline = L.polyline([
                    [lat, long],
                    [latPulang, longPulang]
                ], {
                    color: 'blue',
                    weight: 5,
                    opacity: 0.7
                }).addTo(map);

                map.fitBounds(polyline.getBounds(), {
                    padding: [55, 55]
                });

                const distance = map.distance([lat, long], [latPulang, longPulang]);
                const distanceElement = document.getElementById("jarak");
                if (distanceElement) {
                    if (distance >= 1000) {
                        distanceElement.textContent = `: ${(distance / 1000).toFixed(2)} KM`;
                    } else {
                        distanceElement.textContent = `: ${distance.toFixed(2)} meter`;
                    }
                }
            }
        });
    </script>
</x-mitra-layout>
