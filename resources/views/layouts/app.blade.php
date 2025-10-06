<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

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
    <!-- Webcam CDN -->
    <script src="{{ URL::asset('js/webcam.min.js') }}"></script>
    <script src="{{ URL::asset('js/jqueryNew.min.js') }}"></script>


    <style>
        *,
        body,
        html {
            overflow-x: hidden;
        }
    </style>

</head>

<body class="font-sans antialiased bg-slate-400">
    <div class="min-h-screen">
        @include('layouts.navbar')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>

            {{ $slot }}

        </main>

    </div>
    <div class="flex justify-center">
        <div class="fixed bottom-0 z-[999]">
            <x-menu-mobile />
        </div>
    </div>
    <!-- cdnjs -->
    <script type="text/javascript" src="{{ URL::asset('js/jquery.lazy.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/jquery.lazy.plugins.min.js') }}">
    </script>
    <script>
        $(document).ready(function() {
            $("#searchInput").on("keyup", function() {
                let value = $(this).val().toLowerCase();
                $("#searchTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            $('#nav-btn').click(function() {
                $('#mobile-menu').addClass('absolute');
                $('#mobile-menu').toggle();
            });

            $('.lazy').lazy({
                // your configuration goes here
                scrollDirection: 'vertical',
                effect: 'fadeIn',
                visibleOnly: true,
                onError: function(element) {
                    console.log('error loading ' + element.data('src'));
                }
            });
        });
        //input ++

        $(document).ready(function() {
            var count = 1
            $('#add').click(function() {
                var input = $(
                    '<input class="my-2 input input-bordered" placeholder="Add Name ...." name="name[]" type="text"/>'
                );
                $('#inputContainer').append(input);

                count++
            });
        });


        //End input ++

        // Preview Script
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



                // handle rate

                var $tableRows = $('#searchTable tbody tr');

                $('#searchInput').on('keyup', function() {
                    var value = $(this).val().toLowerCase();

                    // Show all rows initially
                    $tableRows.show();

                    // Filter rows based on the search text
                    $tableRows.filter(function() {
                        return $(this).text().toLowerCase().indexOf(value) == -1;
                    }).hide();
                });

            });
            $('#img2').change(function() {
                const input = $(this)[0];
                const preview = $('.preview2');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.show();
                        preview.find('.img2').attr('src', e.target.result);
                        preview.removeClass('hidden');
                        preview.find('.img2').addClass('rounded-md shadow-md my-4');
                        $('.iImage2').removeClass('flex').addClass('hidden');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
            $('#img3').change(function() {
                const input = $(this)[0];
                const preview = $('.preview3');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.show();
                        preview.find('.img3').attr('src', e.target.result);
                        preview.removeClass('hidden');
                        preview.find('.img3').addClass('rounded-md shadow-md my-4');
                        $('.iImage3').removeClass('flex').addClass('hidden');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
            $('#img4').change(function() {
                const input = $(this)[0];
                const preview = $('.preview4');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.show();
                        preview.find('.img4').attr('src', e.target.result);
                        preview.removeClass('hidden');
                        preview.find('.img4').addClass('rounded-md shadow-md my-4');
                        $('.iImage4').removeClass('flex').addClass('hidden');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
            $('#img5').change(function() {
                const input = $(this)[0];
                const preview = $('.preview5');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.show();
                        preview.find('.img5').attr('src', e.target.result);
                        preview.removeClass('hidden');
                        preview.find('.img5').addClass('rounded-md shadow-md my-4');
                        $('.iImage5').removeClass('flex').addClass('hidden');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });

            var btnAbsensi = $("#btnAbsensi");
            var btnRating = $("#btnRating");

            var table = $("#table");
            var table2 = $("#table2");
            var btn2 = $('#btnShow2');
            var menuUser = $('#menuUser');
            var user = $('#user');
            var menu1 = $('.menu1');
            var menu2 = $('.menu2');
            var menu3 = $('.menu3');
            var menu4 = $('.menu4');
            var menu5 = $('.menu5');
            var menu6 = $('.menu6');
            var menu7 = $('.menu7');
            var menu8 = $('.menu8');
            var menu9 = $('.menu9');
            var menu10 = $('.menu10');
            var menu11 = $('.menu11');
            var menu12 = $('.menu12');
            var menu13 = $('.menu13');
            var menu14 = $('.menu14');
            var absen = $('#absen');
            var iPulang = $('.iPulang');
            var iAbsensi = $('.iAbsensi');

            btnAbsensi.click(function() {
                btnRating.toggle();
                $('#isiAbsen').toggle();
                $('#ngabsen').toggle();
                $('#ngeLembur').toggle();
                $('#isiLembur').toggle();
            });

            btnRating.click(function() {
                $('#cekMe').toggle();
                $('#cekRate').toggle();
            });

            // $('#isiAbsen').click(function() {
            // 	table.toggle();
            // 	table.addClass('my-0 sm:my-5 mx-5 shadow-md');
            // });

            $('#btnShow').click(function() {
                $('#pag-1').toggle();
                btn2.toggle();
                table.toggle();
                table.addClass('my-0 sm:my-5 mx-5 shadow-md');
                iPulang.toggle();

            });

            btn2.click(function() {
                table2.toggle();
                table2.addClass('my-0 sm:my-5 mx-0 sm:mx-5 shadow-md');
                iAbsensi.toggle();
            });

            $('#btnUser').click(function() {
                user.toggle();
                $('.btnRuangan').toggle();
                $('#btnAbsen').toggle();
            });

            $('#btnClient').click(function() {
                $('#client').toggle();
                $('.btnArea').toggle();
                $('#btnLembur').toggle();

            });

            $('#btnKerjasama').click(function() {
                $('#kerjasama').toggle();
                $('#btnJabatan').toggle();
                $('.menuCP').toggle();

            });

            $('#btnAbsen').click(function() {
                $('#absen').toggle();
                $('#btnLokasi').toggle();


            });
            $('#btnDevisi').click(function() {
                $('#devisi').toggle();
                $('.btnPoint').toggle();
                $('#btnPerlengkapan').toggle();

            });
            $('#btnPerlengkapan').click(function() {
                $('#perlengkapan').toggle();
                $('#btnJadwal').toggle();

            });
            $('#btnLembur').click(function() {
                $('#lembur').toggle();
                $('#btnLaporan').toggle();


            });
            $('#btnJabatan').click(function() {
                $('#jabatan').toggle();
                $('.menuCP').toggle();
            });
            $('#btnShift').click(function() {
                $('#shift').toggle();
                $('#btnKerjasama').toggle();
                $('#btnJabatan').toggle();
            });
            $('#btnCP').click(function() {
                $('#CP').toggle();
            });
            $('#btnPCP').click(function() {
                $('#PCP').toggle();
            });

            $('#btnLokasi').click(function() {
                $('#lokasi').toggle();
                $('#btnPCP').toggle();
            });

            $('#btnJadwal').click(function() {
                $('#jadwal').toggle();
            });
            $('#btnLaporan').click(function() {
                $('#laporan').toggle();
            });
            $('#btnNews').click(function() {
                $('#News').toggle();
            });
            $('#btnSubArea').click(function() {
                $('#SubArea').toggle();
            });
            $('#btnChecklist').click(function() {
                $('#Checklist').toggle();
            });
            $('#btnListPekerjaan').click(function() {
                $('#listPekerjaan').toggle();
            });

        });
        $(document).ready(function() {
            $(document).on("click", ".myModalBtn", function() {
                var modalId = $(this).attr('id').replace('myModalBtn', '');
                var modal = $('#myModal' + modalId);
                modal.removeClass('hidden ');
                modal.addClass(' inset-0 z-[999]');
            });

            $(document).on("click", ".close", function() {
                var modalId = $(this).closest('.modalz').attr('id').replace('myModal', '');
                var modal = $('#myModal' + modalId);
                modal.removeClass(' inset-0 z-[99]');
                modal.addClass('hidden ');
            });
        })
    </script>
</body>

</html>
