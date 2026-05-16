export function initAbsensiTime(config) {
    const { authCodeJabatan, kerId } = config;
    const debugTime = false;

    function logTimeDebug(label, data = {}) {
        if (!debugTime) {
            return;
        }

        console.log(`[ABSEN TIME] ${label}`, data);
    }

    function setBtnAbsen(enabled, label = "Absen") {
        if (typeof window.setBtnAbsen === 'function') {
            window.setTimeGate(enabled, label);
        }
    }

    function refreshButtonState() {
        if (typeof window.refreshAbsenButtonState === 'function') {
            window.refreshAbsenButtonState();
        }
    }

    function formatCountdown(minutesDiff, seconds) {
        return {
            h: Math.floor(minutesDiff / 60),
            m: Math.abs(minutesDiff % 60),
            s: Math.abs(60 - seconds)
        };
    }

    function setKeterangan(jadi, absenKantor, kerIdValue, authName) {
        let value = "masuk";
        if (absenKantor == 1) {
            if (jadi < -32 && !['DIREKTUR', 'DIRUT', 'WAHYUDI'].includes(authName)) {
                value = "telat";
            }
        } else if (kerIdValue == 11) {
            value = jadi < -15 ? "telat" : "masuk";
        } else {
            value = jadi < -30 ? "telat" : "masuk";
        }

        $('#keterangan').val(value);
    }

    $(function() {
        const keterangan = $('#keterangan');
        let debounceTimer;

        function calculatedJamStart() {
            logTimeDebug('calculatedJamStart:start', {
                authCodeJabatan,
                kerId
            });

            if (authCodeJabatan === 'SPV-W') {
                logTimeDebug('bypass:spv-w');
                setBtnAbsen(true, "Absen");
                $('#labelWaktuStart').addClass('hidden');
                return;
            }

            const now = new Date();
            const jamSaiki = now.getHours();
            const menitSaiki = now.getMinutes();
            const detikSaiki = now.getSeconds();

            const selectedOption = $('#shift_id').find(":selected");
            const shiftStart = selectedOption.data('shift');
            logTimeDebug('shift:selected', {
                shiftStart,
                selectedValue: selectedOption.val(),
                selectedText: selectedOption.text()
            });

            if (!shiftStart) {
                logTimeDebug('shift:missing');
                setBtnAbsen(true, "Absen");
                $('#labelWaktuStart').addClass('hidden');
                return;
            }

            const [startHours, startMinutes] = shiftStart.split(':').map(Number);
            const startDiffMinutes = startHours * 60 + startMinutes;
            const nowDiffMinutes = jamSaiki * 60 + menitSaiki;
            const jadi = startDiffMinutes - nowDiffMinutes;
            const bypassByJabatan = ['MCS', 'SPV', 'SPV-W'].includes((authCodeJabatan || '').toString().trim());

            const { h: kesimH, m: kesimM, s: kesimS } = formatCountdown(jadi, detikSaiki);
            const { h: kesimH2, m: kesimM2 } = formatCountdown(jadi - 30, detikSaiki);

            const absenKantor = $('#absen-kantor').data('absen-kantor');
            const authName = keterangan.data('authname');
            setKeterangan(jadi, absenKantor, kerId, authName);

            logTimeDebug('time:computed', {
                now: `${String(jamSaiki).padStart(2, '0')}:${String(menitSaiki).padStart(2, '0')}:${String(detikSaiki).padStart(2, '0')}`,
                shiftStart,
                startDiffMinutes,
                nowDiffMinutes,
                jadi,
                bypassByJabatan,
                absenKantor,
                authName
            });

            if (bypassByJabatan) {
                logTimeDebug('button:absen', {
                    reason: 'bypassByJabatan'
                });
                setBtnAbsen(true);
            } else if (jadi <= 90) {
                logTimeDebug('button:absen', {
                    reason: 'shiftWindowOpen',
                    jadi
                });
                setBtnAbsen(true, "Absen");
            } else {
                logTimeDebug('button:tunggu', {
                    reason: 'shiftNotOpenYet',
                    jadi,
                    kesimH,
                    kesimM,
                    kesimS,
                    kesimH2,
                    kesimM2
                });
                setBtnAbsen(false, "Tunggu");
                $('#labelWaktuStart').html(
                    `Shift anda dimulai ${kesimH} jam ${kesimM} menit ${kesimS} detik lagi,\nharap tunggu ${kesimH2} jam ${kesimM2} menit ${kesimS} detik lagi`
                );
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(calculatedJamStart, 1000);
        }

        $('#shift_id').change(calculatedJamStart);
        $('#form-absen').on('change input', 'select, input, textarea', refreshButtonState);
        calculatedJamStart();
        refreshButtonState();

        $(document).off('click.absenSubmit').on('click.absenSubmit', '.btnAbsen', function(e) {
            e.preventDefault();

            const form = document.getElementById('form-absen');
            if (!form) return;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (typeof window.showAbsenSubmitOverlay === 'function') {
                window.showAbsenSubmitOverlay();
            }

            $('.btnAbsen')
                .prop('disabled', true)
                .text('Memproses...')
                .addClass('btn-disabled')
                .css('background-color', 'rgba(96, 165, 250, 0.5)');

            form.requestSubmit();
        });

        $('#form-absen').off('submit.absenSubmit').on('submit.absenSubmit', function() {
            if (typeof window.showAbsenSubmitOverlay === 'function') {
                window.showAbsenSubmitOverlay();
            }
        });
    });
}
