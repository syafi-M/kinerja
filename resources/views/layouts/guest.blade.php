<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'Login - ' . env('APP_NAME', 'Login - Kinerja SAC-PO') }}</title>
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Fonts -->
    {{-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<style>
    body {
        font-family: 'Inter', sans-serif;
    }

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

<body class="text-gray-900 antialiased max-h-svh overflow-hidden transition-colors duration-300">

    <div class="min-h-screen flex items-center justify-center p-4 bg-cover bg-img overflow-hidden relative">

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

        <div id="browser-alert"
            class="hidden absolute top-0 left-0 right-0 z-20 flex items-center justify-between
         bg-orange-100 border-b border-orange-300 text-orange-800
         px-3 py-2 text-sm">
            <div class="flex items-center gap-2">
                <i class="ri-information-2-line text-lg"></i>
                <span>Gunakan <strong>Google Chrome</strong> untuk pengalaman terbaik.</span>
            </div>
            <button onclick="document.getElementById('browser-alert').remove()"
                class="text-orange-600 hover:text-orange-800">
                ✕
            </button>
        </div>

        <div class="absolute z-[2] min-h-full min-w-full bg-gradient-to-br from-transparent to-amber-500/50 "></div>

        <div
            class="relative z-10 w-full max-w-sm bg-white/90 rounded-3xl shadow-2xl p-8 space-y-4 transition-transform duration-300 hover:scale-[1.01] backdrop-blur-sm overflow-hidden">
            <!-- Logo & Company Name -->
            <div class="flex flex-col items-center">
                <!-- Placeholder for Logo -->
                <a href="{{ url('https://sac-po.com') }}">
                    <img src="{{ URL::asset('/logo/sac.png') }}" alt="SAC Logo"
                        class="w-24 h-24 rounded-full drop-shadow-md p-1">
                </a>
                <h1 class="mt-4 text-2xl md:text-3xl font-bold text-gray-800 text-center">
                    Kinerja - SAC
                </h1>
            </div>
            <!-- Page Content -->
            @auth
                <div>
                    <div class="flex flex-col items-center justify-center mt-4 space-y-4">
                        <div class="text-center">
                            <h2 class="text-lg md:text-2xl font-semibold text-gray-700">
                                Masuk ke Sistem Kinerja
                            </h2>
                        </div>
                        <a href="{{ Auth::user()->role_id == 2 ? route('admin.index') : '/dashboard' }}"
                            class="bg-teal-400 hover:bg-teal-500 rounded-lg py-2 px-10 shadow font-semibold"
                            id="btnDashboard">Klik
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
        document.addEventListener("DOMContentLoaded", () => {
            const alertBar = document.getElementById("browser-alert");
            const ua = navigator.userAgent;

            // Deteksi Chrome (pastikan bukan Edge/Opera)
            const isChrome = /Chrome/.test(ua) && !/Edg/.test(ua) && !/OPR/.test(ua);

            if (!isChrome) {
                alertBar.classList.remove("hidden");
            }
        });
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
