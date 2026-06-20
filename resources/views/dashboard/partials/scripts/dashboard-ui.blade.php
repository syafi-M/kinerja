    <script>
        $(document).ready(function() {
            var waktuIzin = $("#waktuIzin").data('waktu');
            if (waktuIzin) {
                setInterval(function() {
                    var sekarang = new Date();
                    var jamSekarang = sekarang.getHours();
                    var menitSekarang = sekarang.getMinutes();

                    var [jamIzin, menitIzin] = waktuIzin.split(':').map(Number);
                    var waktuIzinDetik = jamIzin * 3600 + menitIzin * 60 +
                        180; // Tambah 180 detik (3 menit)
                    var waktuSekarangDetik = jamSekarang * 3600 + menitSekarang * 60;

                    if (waktuSekarangDetik >= waktuIzinDetik) {
                        $("#inpoIzin").addClass('hidden');
                    } else {
                        $("#inpoIzin").removeClass('hidden');
                    }
                }, 1000);
            }
        });
        $(document).ready(function() {
            $('#nav-btn').click(function() {
                $('#mobile-menu').addClass('absolute');
                $('#mobile-menu').toggle();
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

        $(document).ready(function() {
            var count = 1
            $('#btnAdd').click(function() {
                var ElementAsli = $('#inputContainer').html();
                var input = $('<select class="my-2 select select-bordered">').html(ElementAsli);
                $('#inputContainer').append(input);
                count++
            });
        });

        //End input ++

        // modal pulang
        $(document).ready(function() {
            $('#modalPulangBtn').click(function() {
                $('.modalp')
                    .removeClass('hidden')
                    .addClass('flex justify-center items-center opacity-100'); // Add opacity class
            });

            $('.close').click(function() {
                $('.modalp')
                    .removeClass('flex justify-center items-center opacity-100') // Remove opacity class
                    .addClass('opacity-0') // Add opacity class for fade-out
                    .addClass('hidden')
                    .removeClass('flex justify-center items-center');
            });
        });


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
                    };

                    reader.readAsDataURL(input.files[0]);
                }

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
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });

            var btnAbsensi = $("#btnAbsensi");
            var btnRating = $("#btnRating");
            var btnMitra = $('#btnMitra');
            var btnCP = $('#btnCP');

            var table = $("#table");
            var table2 = $("#table2");
            var btn2 = $('#btnShow2');
            var menuUser = $('#menuUser');
            var user = $('#user');
            var absen = $('#absen');
            var iPulang = $('.iPulang');
            var iAbsensi = $('.iAbsensi');

            btnAbsensi.click(function(e) {
                $(this).addClass('clicked');
                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    btnAbsensi.removeClass('clicked');
                }, 100);
                btnRating.toggle();
                $('#ngabsen').toggle();
                $('#ngabsenK').toggle();
                $('#ngeLembur').toggle();
                $('#ngIzin').toggle();
                $('#btnRiwayat').toggle();
            });

            $('#btnRiwayat').click(function() {
                $('#isiAbsen').toggle();
                $('#isiLembur').toggle();
                $('#isiIzin').toggle();
            })

            btnRating.click(function() {
                $(this).addClass('clicked');

                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    btnRating.removeClass('clicked');
                }, 100);
                $('#cekMe').toggle();
                $('#cekRate').toggle();
            });

            $('#btnLaporan').click(function() {
                $(this).addClass('clicked');

                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    $('#btnLaporan').removeClass('clicked');
                }, 100);
                $('#cekLaporan').toggle();
                $('#tambahLaporan').toggle();
            });

            btnMitra.click(function() {
                $(this).addClass('clicked');

                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    btnMitra.removeClass('clicked');
                }, 100);
                $('#Labsensi').toggle();
                $('#Llaporan').toggle();
                $('#Llembur').toggle();
                $('#Luser').toggle();
                $('#Ljadwal').toggle();
                $('#lizin').toggle();
            })


            btnCP.click(function() {
                $(this).addClass('clicked');

                // Optionally, you can remove the 'clicked' class after a delay
                setTimeout(function() {
                    btnCP.removeClass('clicked');
                }, 100);
                $('#isiIndex').toggle();
                $('#tambahCP').toggle();
                $('#kirimCP').toggle();
            })

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

            $('#menuAbsen').click(function() {
                var absen = $('#absen').toggle();

            });
        });
    </script>