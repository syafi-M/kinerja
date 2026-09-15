<script>
    $(function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                $('#latitude').val(position.coords.latitude);
                $('#longtitude').val(position.coords.longitude);
            });
        }

        const jobs = $('#jobs');
        const isEdit = @json($isEdit ?? false);
        let rowIndex = jobs.find('.job-row').length;

        function applyIndex(row, index) {
            row.attr('data-index', index);
            row.find('.job-number').text(index + 1);
            row.find('.photo-input').attr('name', `img[${index}][]`);
            row.find('.existing-images').attr('name', `existing_img[${index}][]`);
            row.find('.deskripsi-input').attr('name', `deskripsi[${index}]`);
            row.find('.tanggal-input').attr('name', `tanggal[${index}]`);
            row.find('.approve-input').attr('name', `approve_status[${index}]`);
            row.find('.job-value').attr('name', `pekerjaan_id[${index}]`);
            row.find('.manual-name').attr('name', `input_manual[${index}]`);
        }
        function renumber() {
            jobs.find('.job-row').each(function (i) { applyIndex($(this), i); });
            rowIndex = jobs.find('.job-row').length;
        }
        function syncRow(row) {
            const manual = row.find('.job-select').val() === 'manual';
            const selected = row.find('.job-select option:selected');
            const label = manual ? row.find('.manual-name').val() || 'Ketik manual' : (selected.text() || 'Pilih dari daftar pekerjaan');
            row.find('.job-label').text(label).toggleClass('text-slate-400', !row.find('.job-select').val()).toggleClass('text-slate-700', !!row.find('.job-select').val());
        }
        function closeDropdowns() {
            $('.job-menu').addClass('hidden');
            $('.job-chevron').removeClass('rotate-180');
        }

        function bindDropdown(row) {
            const trigger = row.find('.job-trigger');
            const menu = row.find('.job-menu');

            trigger.on('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                const isClosed = menu.hasClass('hidden');

                closeDropdowns();

                if (isClosed) {
                    menu.removeClass('hidden');
                    trigger.find('.job-chevron').addClass('rotate-180');
                }
            });

            row.find('.job-option').on('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                const value = $(this).data('value');

                row.find('.job-select')
                    .val(value)
                    .trigger('change');

                closeDropdowns();
            });
        }
        function syncRowLegacy(row) {
            const manual = row.find('.job-select').val() === 'manual';
            row.find('.manual-name').toggleClass('hidden', !manual).prop('required', manual);
            row.find('.job-value').val(manual ? 'manual' : row.find('.job-select').val());
        }
        function bind(row) {
            row.find('.job-select').on('change', function () { syncRowLegacy(row); syncRow(row); });
            row.find('.manual-name').on('input', function () {
                row.find('.job-select').val('manual');
                row.find('.job-value').val('manual');
                syncRow(row);
            });
            bindDropdown(row);
            row.find('.photo-input').on('change', function () {
                const preview = row.find('.photo-preview').empty();
                row.find('.photo-placeholder').toggleClass('hidden', this.files.length > 0);
                [...this.files].forEach(file => {
                    if (!file.type.startsWith('image/')) return;
                    preview.append(`<img src="${URL.createObjectURL(file)}" alt="Preview ${file.name}" class="h-20 w-full rounded-lg object-cover ring-1 ring-slate-200">`);
                });
                row.find('.photo-names').text([...this.files].map(file => file.name).join(', '));
            });
            row.find('.remove-job').on('click', function () {
                if (jobs.find('.job-row').length > 1) { row.remove(); renumber(); }
            });
            syncRowLegacy(row);
            syncRow(row);
        }

        jobs.find('.job-row').each(function () { bind($(this)); });
        renumber();
        $(document).on('click', function (event) {
            if (!$(event.target).closest('.job-dropdown').length) closeDropdowns();
        });
        $('#add-job').on('click', function () {
            const row = $($('#job-template').html());
            jobs.append(row);
            bind(row);
            renumber();
        });
        $('#form-cp').on('submit', function () { $('#submit-job').prop('disabled', true).text('Menyimpan...'); });
    });
</script>
