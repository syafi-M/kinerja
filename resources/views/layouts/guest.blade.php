<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ env('APP_NAME', 'Kinerja SAC-PO') }}</title>
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<style>
    @media screen and (min-height: 576px) {
        .bg-img {
            background-image: url('{{ URL::asset('/logo/bg-versi-ramdhan.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    }

    @media screen and (max-height: 576px) {
        .bg-img {
            background-image: url('{{ URL::asset('/logo/bg-versi-ramdhan.jpg') }}');
            background-size: cover;
            background-position: left;
            background-repeat: no-repeat;
        }
    }

    @keyframes hanging-wiggle {
        0% {
            transform: rotate(-15deg);
        }

        50% {
            transform: rotate(-10deg);
        }

        100% {
            transform: rotate(-15deg);
        }
    }

    @keyframes hanging-wiggle2 {
        0% {
            transform: rotate(10deg);
        }

        50% {
            transform: rotate(15deg);
        }

        100% {
            transform: rotate(10deg);
        }
    }

    @keyframes hanging-wiggle3 {
        0% {
            transform: rotate(15deg);
        }

        50% {
            transform: rotate(10deg);
        }

        100% {
            transform: rotate(15deg);
        }
    }

    @keyframes hanging-wiggle4 {
        0% {
            transform: rotate(-10deg);
        }

        50% {
            transform: rotate(-15deg);
        }

        100% {
            transform: rotate(-10deg);
        }
    }

    @media (min-width: 48rem) {

        /* Screens wider than 48rem */
        .hanging,
        .hanging2,
        .hanging3,
        .hanging4 {
            width: 5%;
        }
    }

    @media (max-width: 48rem) {

        /* Screens smaller than 48rem */
        .hanging,
        .hanging2,
        .hanging3,
        .hanging4 {
            width: 10%;
        }
    }


    .hanging {
        position: absolute;
        top: 0;
        transform-origin: top center;
        /* Makes it swing from the top */
        animation: hanging-wiggle 2s ease-in-out infinite alternate;
    }

    .hanging2 {
        position: absolute;
        top: 0;
        transform-origin: top center;
        /* Makes it swing from the top */
        animation: hanging-wiggle2 2.5s ease-in-out infinite alternate;
    }

    .hanging3 {
        position: absolute;
        top: 0;
        transform: scaleX(-1);
        transform-origin: top center;
        /* Makes it swing from the top */
        animation: hanging-wiggle2 2.5s ease-in-out infinite alternate;
    }

    .hanging4 {
        position: absolute;
        top: 0;
        transform: scaleX(-1);
        transform-origin: top center;
        /* Makes it swing from the top */
        animation: hanging-wiggle 2s ease-in-out infinite alternate;
    }
</style>

<body class="font-sans text-gray-900 antialiased overflow-hidden">

    <div style="min-height: 100svh; position: relative; z-index: 9001;"
        class="flex flex-col px-4 bg-gradient-to-b from-gray-100 to-gray-500/70 bg-cover items-center justify-center bg-img overflow-hidden">

        @if (Carbon\Carbon::now()->lessThan(Carbon\Carbon::parse('2025-04-09')))
            <div class="overflow-hidden">
                <img src="{{ URL::asset('/logo/ketupat-3.png') }}" class="hanging"
                    style="z-index: 9000; left: 0px; padding: 0px; border-radius: 100%; filter: drop-shadow(0 3px 3px rgb(0 0 0 / 0.15));" />
                <img src="{{ URL::asset('/logo/ketupat-1.png') }}" class="hanging2"
                    style="z-index: 8999; left: 0px; padding: 0px; border-radius: 100%; filter: drop-shadow(0 3px 3px rgb(0 0 0 / 0.15));" />
                <img src="{{ URL::asset('/logo/ketupat-3.png') }}" class="hanging3"
                    style="z-index: 9000; right: 0px; padding: 0px; border-radius: 100%; filter: drop-shadow(0 3px 3px rgb(0 0 0 / 0.15));" />
                <img src="{{ URL::asset('/logo/ketupat-1.png') }}" class="hanging4"
                    style="z-index: 8999; right: 0px; padding: 0px; border-radius: 100%; filter: drop-shadow(0 3px 3px rgb(0 0 0 / 0.15));" />
            </div>
        @endif

        <div style="font-size: 8pt; color: #404040; top: 5pt; left: 5pt; background-color: rgba(254, 243, 199, 0.75); padding: 4px; padding-left: 8px; padding-right: 12px;"
            class="absolute flex items-center gap-2 rounded-full">
            <i style="background-color: rgba(254, 243, 199, 0.85); padding-left: 6px; padding-right: 6px;"
                class="ri-information-2-line text-xl rounded-full"></i>
            <p class="font-semibold">Gunakan Google Chrome untuk pengalaman terbaik. rill cuy</p>
        </div>

        <div class="sm:flex sm:flex-col justify-center">
            <a href="{{ url('https://sac-po.com') }}">
                <div
                    class="flex flex-col justify-center items-center gap-2 sm:p-4 sm:bg-gradient-to-tr sm:from-gray-400/20 sm:to-gray-500/20  sm:rounded-md sm:shadow-inner sm:shadow-gray-400/30">
                    <img src="{{ URL::asset('/logo/sac.png') }}"
                        class="w-20  -right-2 bg-white p-3 rounded-full shadow" alt="..." width="100%"
                        height="100%">
                    <p
                        class="text-slate-800 font-black text-lg p-2 rounded-md shadow sm:shadow-lg sm:pl-4 text-center sm:pr-2  bg-white">
                        PT. Surya Amanah Cendikia</p>
                </div>
            </a>
        </div>
        <div
            class="w-full drop-shadow-2xl sm:max-w-md mt-6 px-6 py-4 bg-gradient-to-br from-yellow-400 to-amber-500 shadow-lg overflow-hidden rounded-lg h-fit mx-4 sm:mx-0 ">
            <div class="mt-4">
                @auth
                    <p class="text-center font-black text-xl text-slate-800">Anda Sudah Login</p>
                @endauth
                @guest
                    <p class="text-center sm:hidden font-black text-xl text-slate-800">Silahkan Login<br>Terlebih Dahulu</p>
                @endguest
            </div>
            @auth
                <div>
                    <div class="flex items-center justify-center mt-4">
                        <a href="{{ Auth::user()->role_id == 2 ? route('admin.index') : '/dashboard' }}"
                            class="bg-teal-400 hover:bg-teal-500 rounded-lg py-2 px-10 shadow" id="btnDashboard">Klik
                            Disini</a>
                    </div>
                </div>
            @endauth
            @guest
                <div>
                    {{ $slot }}
                </div>
            @endguest
        </div>
    </div>


    <script>
        // console.log("---", navigator.userAgentData.brands.length == 2)
        // console.log("---", navigator, "---")
        var OtherBrowser = navigator.userAgentData.brands[2] === undefined
        var ResLength = navigator.userAgentData.brands.length == 3

        if (ResLength) {
            var Chrome = navigator.userAgentData.brands[0].brand == 'Chromium'
            var ChromePC = navigator.userAgentData.brands[2].brand == 'Google Chrome'
            var EdgePC = navigator.userAgentData.brands[2].brand == 'Microsoft Edge'


            var MobileChrome = navigator.userAgentData.brands[1].brand == 'Google Chrome'
            var MobileChromium = navigator.userAgentData.brands[2].brand == 'Chromium'
        }

        // alert(Chrome == false || MobileChromium == false ||  MobileChrome == false || e == false && c == false)
        // console.log(MobileChromium, MobileChrome)

        if (!ResLength) {
            alert('Gunakan Google Chrome Dan Update Ke Versi Terbaru !!');
            window.location.reload();
        } else if (!MobileChrome && EdgePC && ChromePC) {
            alert('Browser Tidak Support Atau Bukan Google Chrome !!');
            window.location.reload();
        }

        // Preview Script
        $(document).ready(function() {
            $('#img').change(function() {
                const input = $(this)[0];
                const preview = $('.preview');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.show();
                        preview.find('img').attr('src', e.target.result).removeClass('hidden').addClass(
                            'rounded-md shadow-md my-4');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });

            $('#btnDashboard').click(function() {
                $(this).prop('disabled', true).text('Tunggu...').css('background-color',
                    'rgba(96, 165, 250, 0.5)');
            });
        });
    </script>
</body>

</html>
