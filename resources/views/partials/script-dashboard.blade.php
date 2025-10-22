<script defer>
    $(document).ready(function() {
        // === Cache DOM elements once ===
        const $lat = $('.lat');
        const $long = $('.long');
        const $tutor = $('#tutor');
        const $modalPulangBtn = $('#modalPulangBtn');
        const $pulangBtnText = $modalPulangBtn.find('span');
        const $jam = $('#jam');
        const $jam2 = $('#jam2');
        const $labelWaktu = $('#labelWaktu');
        const $startTime = $('#startTime');
        const $endTime = $('#endTime');
        const $btnAbsensi = $('#btnAbsensi');
        const $btnRating = $('#btnRating');
        const $btnCP = $('#btnCP');
        const $btnLaporan = $('#btnLaporan');
        const $btnRiwayat = $('.btnRiwayat');
        const $closeButtons = $('.close, .closeNews');

        // === Constants & Data ===
        const lokasiMitra = {!! json_encode($lokasiMitra ?? []) !!};
        const userCoopId = {{ Auth::user()->kerjasama_id }};
        const userData = {!! json_encode(Auth::user()) !!};
        const userJabatanCode = "{{ Auth::user()->jabatan?->code_jabatan }}";
        const userDevisiId = {{ Auth::user()->devisi_id }};
        const isSupervisorOrSpecialDept = userJabatanCode === "SPV-W" || userDevisiId === 12;
        const SHIFT_START_TIME = "{{ $absenP?->shift?->jam_start }}";
        const ABSEN_CREATED_TIME = "{{ $absenP?->created_at?->format('H:i:s') }}";
        const MINUTES_BEFORE_SHIFT_END = 120;
        const SUPERVISOR_MIN_WORK_TIME = 390; // 6.5 hours

        const startTimeAttr = $startTime.attr('startTimer');
        const endTimeAttr = $endTime.attr('endTimer');

        // === Helper: Pad zero ===
        function padZero(num) {
            return num < 10 ? `0${num}` : num;
        }

        // === Clock ===
        function startClock() {
            function updateClock() {
                const now = new Date();
                const timeString =
                    `${padZero(now.getHours())}:${padZero(now.getMinutes())}:${padZero(now.getSeconds())}`;
                $jam.text(timeString);
                setTimeout(updateClock, 1000);
            }
            updateClock();
        }

        // === Shift Timer ===
        function startShiftTimer() {
            function updateShiftTimer() {
                const now = new Date();
                let timeDiffStr = '';
                let jadiMenit = 0;
                let bedaCreatedAt = 0;

                // Supervisor: calculate from absen created time
                if (isSupervisorOrSpecialDept && ABSEN_CREATED_TIME) {
                    const [h, m, s] = ABSEN_CREATED_TIME.split(':').map(Number);
                    const startDate = new Date();
                    startDate.setHours(h, m, s, 0);
                    bedaCreatedAt = Math.floor((now - startDate) / (1000 * 60));
                }

                // Time until shift end
                if (endTimeAttr && endTimeAttr.includes(':')) {
                    const [endH, endM] = endTimeAttr.split(':').map(Number);
                    let diffH = endH - now.getHours() - 1;
                    let diffM = endM - now.getMinutes();
                    const diffS = 60 - now.getSeconds();

                    if (diffM < 0) {
                        diffH--;
                        diffM += 60;
                    }
                    jadiMenit = diffH * 60 + diffM;
                    timeDiffStr = (diffH < 0 ? '-' : '') +
                        `${Math.abs(diffH)} jam ${padZero(diffM)} menit ${padZero(diffS)} detik`;
                }

                // Update UI
                if (userData.name !== "DIREKSI" && userData.jabatan_id !== 35) {
                    if (jadiMenit <= 0) {
                        $jam2.text('~ Shift Anda Telah Selesai ~');
                        $labelWaktu.text('');
                    } else {
                        $jam2.text(timeDiffStr);
                        $labelWaktu.text('Shift Anda Masih').addClass('text-center');
                    }
                }

                // Show/hide pulang button
                const shouldShow = (isSupervisorOrSpecialDept && bedaCreatedAt >= SUPERVISOR_MIN_WORK_TIME) ||
                    jadiMenit <= MINUTES_BEFORE_SHIFT_END ||
                    userData.name === "DIREKSI";

                $modalPulangBtn.toggleClass('hidden', !shouldShow).toggleClass('flex', shouldShow);

                setTimeout(updateShiftTimer, 1000);
            }
            updateShiftTimer();
        }

        // === Geolocation ===
        function setupGeolocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            function handlePositionUpdate(position) {
                const {
                    latitude,
                    longitude
                } = position.coords;
                $lat.val(latitude);
                $long.val(longitude);
                $tutor.removeClass('hidden');

                if (userCoopId === 1) {
                    $modalPulangBtn.prop('disabled', false).removeClass('btn-disabled');
                    $pulangBtnText.text('Pulang');
                } else {
                    checkLocationRadius(latitude, longitude);
                }
            }

            function checkLocationRadius(lat, lng) {
                const userLoc = L.latLng(lat, lng);
                const within = lokasiMitra.some(loc => {
                    const center = L.latLng(loc.latitude, loc.longtitude);
                    return userLoc.distanceTo(center) <= parseFloat(loc.radius);
                });

                $modalPulangBtn.prop('disabled', !within).toggleClass('btn-disabled', !within);
                $pulangBtnText.text(within ? 'Pulang' : 'Diluar Radius!');
            }

            function handleGeolocationError(error) {
                console.error("Geolocation error:", error);
            }

            navigator.geolocation.watchPosition(
                handlePositionUpdate,
                handleGeolocationError, {
                    enableHighAccuracy: true,
                    maximumAge: 0
                }
            );
        }

        // === Izin Timer (Hide after 3 mins) ===
        const waktuIzin = $("#waktuIzin").data('waktu');
        if (waktuIzin) {
            setInterval(() => {
                const now = new Date();
                const nowSec = now.getHours() * 3600 + now.getMinutes() * 60;
                const [h, m] = waktuIzin.split(':').map(Number);
                const izinSec = h * 3600 + m * 60 + 180; // +3 mins
                $("#inpoIzin").toggleClass('hidden', nowSec >= izinSec);
            }, 1000);
        }

        // === Event Delegation for Dynamic Toggles ===
        $(document).on('click', '#btnAbsensi', function() {
            $(this).addClass('clicked');
            setTimeout(() => $(this).removeClass('clicked'), 100);
            $('.ngabsen, .ngabsenK, .ngeLembur, .ngIzin, .btnRiwayat').toggle();
        });

        $(document).on('click', '.btnRiwayat', function() {
            $('.isiAbsen, .isiLembur, .isiIzin').toggle();
        });

        $(document).on('click', '#btnRating', function() {
            $(this).addClass('clicked');
            setTimeout(() => $(this).removeClass('clicked'), 100);
            $('#cekMe, #cekRate').toggleClass('hidden');
        });

        $(document).on('click', '#btnLaporan', function() {
            $(this).addClass('clicked');
            setTimeout(() => $(this).removeClass('clicked'), 100);
            $('#cekLaporan, #tambahLaporan').toggle();
        });

        $(document).on('click', '#btnCP', function() {
            $(this).addClass('clicked');
            setTimeout(() => $(this).removeClass('clicked'), 100);
            $('#isiIndex, #tambahCP, #kirimCP').toggle();
        });

        // === Modal Handlers ===
        $(document).on('click', '#modalPulangBtn', function() {
            $('.modalp').removeClass('hidden').addClass('flex justify-center items-center opacity-100');
        });

        $(document).on('click', '.close', function() {
            $('.modalp')
                .removeClass('flex justify-center items-center opacity-100')
                .addClass('opacity-0 hidden');
        });

        $(document).on('click', '.closeNews', function() {
            $('.modalNews').hide();
        });

        // === Image Preview ===
        $('#img, #img2, #img3').on('change', function() {
            const num = this.id.replace('img', '') || '1';
            const $preview = $('.preview' + (num === '1' ? '' : num));
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    $preview.show().removeClass('hidden')
                        .find(`.img${num}`)
                        .attr('src', e.target.result)
                        .addClass('rounded-md shadow-md my-4');
                };
                reader.readAsDataURL(file);
            }
        });

        // === Mobile Menu (if exists) ===
        $('#nav-btn').on('click', function() {
            $('#mobile-menu').toggleClass('absolute').toggle();
        });

        // === Initialize ===
        startClock();
        if (startTimeAttr || isSupervisorOrSpecialDept) {
            startShiftTimer();
        }
        setupGeolocation();
    });
</script>
