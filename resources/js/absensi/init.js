import { initMap, createLayerGroup, ensureTileLayer, drawLocationCircle as drawLocationCircleHelper, prefetchLocationTiles as prefetchLocationTilesHelper, adjustMapViewForBothPoints as adjustMapViewForBothPointsHelper } from "./map";

export function initAbsensiPage(config) {
    const {
        loc,
        mitra,
        defaultLocationId,
        lati,
        longi,
        radi,
        client,
        canBypassRadius,
        isSpvW,
        isDivisi12,
        authUserId,
        routes
    } = config;

        let userLocation = null;
        let userMarker = null;
        const MAX_GPS_ACCURACY_METERS = 100;
        const GPS_OPTIONS = {
            enableHighAccuracy: true,
            maximumAge: 5000,
            timeout: 15000
        };
        const FAST_GPS_OPTIONS = {
            enableHighAccuracy: false,
            maximumAge: 10000,
            timeout: 8000
        };
        var lat = document.getElementById('lat')
        var long = document.getElementById('long')
        var labelMap = $('#labelMap')
        var tutor = $('#tutor')
        var getNewLoc = null;
        var map = initMap("map"); // ini adalah zoom level
        var tileLayer = null;
        var locationLayerGroup = createLayerGroup(map);
        var absenButton = $('.btnAbsen');
        var gpsDebug = false;
        var prefetchedTiles = new Set();
        var absenGateState = {
            gpsReady: false,
            gpsText: 'Mengambil GPS...',
            timeReady: false,
            timeText: 'Tunggu'
        };

        function hasRequiredAbsenFields() {
            const kerjasamaId = ($('[name="kerjasama_id"]').first().val() || '').toString().trim();
            const shiftValue = ($('[name="shift_id"]').first().val() || '').toString().trim();
            const hasPerlengkapan = $('[name="perlengkapan[]"]:checked').length > 0;
            const keteranganValue = ($('#keterangan').val() || '').toString().trim();
            const latValue = ($('#lat').val() || '').toString().trim();
            const longValue = ($('#long').val() || '').toString().trim();
            const imageField = document.getElementById('image');
            const hasImage = !imageField || (imageField.files && imageField.files.length > 0);

            return Boolean(kerjasamaId) && Boolean(shiftValue) && hasPerlengkapan && Boolean(keteranganValue) && Boolean(latValue) && Boolean(longValue) && hasImage;
        }

        function refreshAbsenButtonState() {
            const formReady = hasRequiredAbsenFields();
            const canEnable = absenGateState.gpsReady && absenGateState.timeReady && formReady;

            let text = 'Absen';
            if (!absenGateState.timeReady) {
                text = absenGateState.timeText;
            } else if (!absenGateState.gpsReady) {
                text = absenGateState.gpsText;
            } else if (!formReady) {
                text = 'Lengkapi Form';
            }

            absenButton
                .text(text)
                .prop('disabled', !canEnable)
                .toggleClass('btn-disabled', !canEnable)
                .toggleClass('bg-blue-500 hover:bg-blue-600', canEnable)
                .toggleClass('cursor-not-allowed bg-blue-400/50 hover:bg-blue-400/50', !canEnable)
                .css('background-color', canEnable ? '' : 'rgba(96, 165, 250, 0.5)');
        }

        window.refreshAbsenButtonState = refreshAbsenButtonState;

        const tileLayerRef = { current: null };

        function drawLocationCircle(latitude, longitude, radius) {
            return drawLocationCircleHelper(locationLayerGroup, latitude, longitude, radius);
        }

        function logGpsDebug(label, data = {}) {
            if (!gpsDebug) {
                return;
            }

            console.log(`[GPS DEBUG] ${label}`, data);
        }

        function setGpsGateState(enabled, text) {
            logGpsDebug('button state', {
                enabled,
                text
            });

            absenGateState.gpsReady = enabled;
            absenGateState.gpsText = text;
            refreshAbsenButtonState();
        }

        function setTimeGateState(enabled, label = "Absen") {
            absenGateState.timeReady = enabled;
            absenGateState.timeText = label;

            if (enabled) {
                $('#labelWaktuStart').addClass('hidden');
            } else {
                $('#labelWaktuStart').removeClass('hidden');
            }

            refreshAbsenButtonState();
        }

        window.setBtnAbsen = setTimeGateState;
        window.setTimeGate = setTimeGateState;
        window.setGpsGate = setGpsGateState;

        function showGeolocationError(error) {
            const messages = {
                1: 'Izinkan akses lokasi untuk absen.',
                2: 'Lokasi belum tersedia. Pastikan GPS aktif.',
                3: 'Mengambil lokasi terlalu lama. Coba nyalakan GPS lalu refresh.'
            };

            logGpsDebug('geolocation error', {
                code: error.code,
                message: error.message,
                label: messages[error.code] || 'Unknown error'
            });

            $('#resolver').text(messages[error.code] || 'Gagal mengambil lokasi. Coba refresh browser.');
            labelMap.removeClass('hidden');
            tutor.addClass('hidden');
            setGpsGateState(false, 'GPS Tidak Tersedia');
        }

        if (navigator.geolocation) {
            setGpsGateState(false, 'Mengambil GPS...');
            logGpsDebug('geolocation supported', FAST_GPS_OPTIONS);

            navigator.geolocation.getCurrentPosition(function(position) {
                userLocation = [position.coords.latitude, position.coords.longitude];
                logGpsDebug('initial position', {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: position.timestamp
                });
                showPosition(position);
                setupNearbyLocations(userLocation);
                prefetchLocationTilesHelper(prefetchedTiles, $('#lat_mitra').val() || lati, $('#long_mitra').val() || longi);
            }, showGeolocationError, FAST_GPS_OPTIONS);

            const watchUser = navigator.geolocation.watchPosition(
                (position) => {
                    const {
                        latitude,
                        longitude,
                        accuracy
                    } = position.coords;
                    labelMap.addClass('hidden');
                    tutor.removeClass('hidden');

                    userLocation = [latitude, longitude];
                    lat.value = latitude;
                    long.value = longitude;
                    getNewLoc = L.latLng([latitude, longitude]);

                    var userLatLng = L.latLng([latitude, longitude]);
                    var circleLatLng = L.latLng([$('#lat_mitra').val(), $('#long_mitra').val()]);
                    var distanceFromCenter = userLatLng.distanceTo(circleLatLng); // in meters
                    var distanceFromBorder = distanceFromCenter - parseFloat($('#radius_mitra').val() || radi || 0);
                    var hasAccurateGps = accuracy <= MAX_GPS_ACCURACY_METERS;
                    var isInsideRadius = distanceFromBorder <= 1;
                    logGpsDebug('watch position', {
                        latitude,
                        longitude,
                        accuracy,
                        maxAccuracy: MAX_GPS_ACCURACY_METERS,
                        mitraLatitude: $('#lat_mitra').val(),
                        mitraLongitude: $('#long_mitra').val(),
                        mitraRadius: $('#radius_mitra').val(),
                        distanceFromCenter: Number(distanceFromCenter.toFixed(2)),
                        distanceFromBorder: Number(distanceFromBorder.toFixed(2)),
                        hasAccurateGps,
                        isInsideRadius,
                        canBypassRadius
                    });

                    if (!hasAccurateGps) {
                        $('#form-absen').attr('action', routes.store);
                        setGpsGateState(false, 'GPS Belum Akurat');
                    } else if (canBypassRadius || isInsideRadius) {
                        $('#form-absen').attr('action', routes.store);
                        setGpsGateState(true, 'Absen');
                    } else {
                        $('#form-absen').attr('action', '#');
                        setGpsGateState(false, `Diluar Radius ${Math.ceil(distanceFromBorder)}m`);
                    }

                    $('#latlongLabel').html(
                        `[${latitude}, ${longitude}, jarak batas: ${distanceFromBorder.toFixed(2)}m, akurasi: ${accuracy.toFixed(0)}m]`
                    );

                    adjustMapViewForBothPointsHelper(
                        map,
                        latitude,
                        longitude,
                        parseFloat($('#lat_mitra').val()),
                        parseFloat($('#long_mitra').val()),
                        isInsideRadius
                    );

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
                    showGeolocationError(error);
                },
                GPS_OPTIONS
            );
        } else {
            logGpsDebug('geolocation unsupported');
            alert('Geo Location Not Supported By This Browser !!');
            labelMap.removeClass('hidden');
        }

        function showPosition(position) {
            var latitude = position.coords.latitude; // Ganti dengan latitude Anda
            var longitude = position.coords.longitude; // Ganti dengan longitude Anda
            ensureTileLayer(map, tileLayerRef);
            logGpsDebug('render map', {
                latitude,
                longitude,
                mitraLatitude: $('#lat_mitra').val(),
                mitraLongitude: $('#long_mitra').val(),
                radius: radi,
                client
            });

            map.setView([latitude, longitude], 14); // ini adalah zoom level
            locationLayerGroup.clearLayers();

            var circle = drawLocationCircle($('#lat_mitra').val(), $('#long_mitra').val(), radi)
                .bindPopup("Lokasi absen: <br>" + client);

            prefetchLocationTilesHelper(prefetchedTiles, $('#lat_mitra').val(), $('#long_mitra').val());
        }

        function getDistanceFromLatLng(lat1, lng1, lat2, lng2) {
            var pointA = L.latLng(lat1, lng1);
            var pointB = L.latLng(lat2, lng2);
            // console.log(pointA.distanceTo(pointB));
            return pointA.distanceTo(pointB);
        }

        function findClosestLocation(userLatLng, locations, threshold) {
            var closestLocations = [];
            locations.forEach(function(location) {
                var distance = getDistanceFromLatLng(userLatLng[0], userLatLng[1], location.latitude, location
                    .longtitude);
                logGpsDebug('location distance', {
                    locationId: location.id,
                    client: location.client?.name,
                    distance: Number(distance.toFixed(2)),
                    radius: location.radius,
                    threshold
                });
                // Add location to closestLocations array if it's within the threshold
                // console.log(location.client.name + ' distance: ' + distance + ' meters' + 'with threshold: ' + threshold);
                if (distance <= (parseInt(location.radius, 10) + threshold)) {
                    location.distance = distance; // Optionally store the distance
                    closestLocations.push(location);
                }
            });

            closestLocations.sort(function(a, b) {
                return a.distance - b.distance;
            });

            return closestLocations;
        }
        function setupNearbyLocations(userLatLng) {
            var threshold = 50;

            // Find the closest locations within the threshold distance
            var closestLocations = findClosestLocation(userLatLng, loc, threshold);
            logGpsDebug('closest locations', closestLocations.map(location => ({
                id: location.id,
                clientId: location.client_id,
                client: location.client?.name,
                distance: Number(location.distance.toFixed(2)),
                radius: location.radius
            })));

            // Get the select element
            var selectMitra = $('.selectMitra');

            // Clear existing options
            selectMitra.html('');

            if (authUserId == 10) {
                // Add the default location to the select dropdown
                loc.forEach(function(location) {
                    if (location.id == defaultLocationId) {
                        const option = document.createElement('option');
                        var selectedMit = mitra.find(mit => mit.client_id == location.client_id);
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
                        var selectedMit = mitra.find(mit => mit.client_id == location.client_id);
                        option.textContent = selectedMit.client.name;
                        option.value = selectedMit.id;
                        selectMitra.append(option);

                        drawLocationCircle(location.latitude, location.longtitude, location.radius);
                    }
                });
                selectMitra.off('change.gpsLocation').on('change.gpsLocation', function() {
                    var selectedKerjasamaId = Number($(this).val());
                    var selectedMit = mitra.find(mit => Number(mit.id) === selectedKerjasamaId);

                    if (!selectedMit) {
                        console.log("Mitra not found for kerjasama ID:", selectedKerjasamaId);
                        return;
                    }

                    var selectedLocation = loc.find(location => Number(location.client_id) === Number(
                        selectedMit.client_id));
                    logGpsDebug('mitra selected', {
                        selectedKerjasamaId,
                        selectedClientId: selectedMit.client_id,
                        selectedLocation
                    });

                    if (selectedLocation) {
                        $('#lat_mitra').val(selectedLocation.latitude);
                        $('#long_mitra').val(selectedLocation.longtitude);
                        $('#radius_mitra').val(selectedLocation.radius);
                        prefetchLocationTilesHelper(prefetchedTiles, selectedLocation.latitude, selectedLocation.longtitude);

                        L.popup()
                            .setLatLng([selectedLocation.latitude, selectedLocation.longtitude])
                            .setContent("Lokasi absen: <br>" + selectedLocation.client.name)
                            .openOn(map);
                    } else {
                        console.log("Location not found for client ID:", selectedMit.client_id);
                    }
                });
            } else if (config.authClientId == 28) {
                closestLocations.forEach(function(location) {
                    if (location.id == 25 || location.id == 28) {
                        // console.log("aku: ", location);
                        $('#lat_mitra').val(location.latitude);
                        $('#long_mitra').val(location.longtitude);
                        $('#radius_mitra').val(location.radius);
                        prefetchLocationTilesHelper(prefetchedTiles, location.latitude, location.longtitude);

                        var selectMitra = mitra.find(mit => mit.client_id == location.client_id);
                        $('#kerjasama_id').val(selectMitra.id);
                        $('.viewKerjasama').val(selectMitra.client.name);

                        drawLocationCircle(location.latitude, location.longtitude, location.radius);
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
            } else if (authUserId == 7 || isSpvW) {
                if (closestLocations.length > 0) {
                    // Get the absolute closest one (the first item in the sorted array)
                    var location = closestLocations[0];

                    // Update form values
                    $('#lat_mitra').val(location.latitude);
                    $('#long_mitra').val(location.longtitude);
                    $('#radius_mitra').val(location.radius);
                    prefetchLocationTilesHelper(prefetchedTiles, location.latitude, location.longtitude);

                    // Find and update Mitra info
                    selectMitra = mitra.find(mit => mit.client_id == location.client_id);
                    if (selectMitra) {
                        $('#kerjasama_id').val(selectMitra.id);
                        $('.viewKerjasama').val(selectMitra.client.name);
                    }

                    // Add visual circle to map for the closest location
                    drawLocationCircle(location.latitude, location.longtitude, location.radius);

                    // Set map view and open popup
                    map.setView([location.latitude, location.longtitude], 16);

                    L.popup()
                        .setLatLng([location.latitude, location.longtitude])
                        .setContent("Lokasi terdekat: <br>" + location.client.name)
                        .openOn(map);
                } else {
                    console.log("No locations found within the threshold.");
                }
            }
        }
}
