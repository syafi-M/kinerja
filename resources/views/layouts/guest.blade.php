<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ 'Login - ' . config('app.name', 'Kinerja SAC-PO') }}</title>

    {{-- <!-- Quick test: Tailwind CDN (ganti ke @vite di production) --> --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.70), rgba(255, 255, 255, 0.55));
            -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
        }

        .blob {
            filter: blur(10px);
            opacity: .6;
        }
    </style>
</head>

<body
    class="min-h-screen max-h-screen bg-gradient-to-b from-amber-50 via-amber-100 to-amber-200 font-sans text-stone-800">

    <!-- top browser alert (only if not chrome) -->
    <div x-data="{ open: false }" x-init="(() => {
        const ua = navigator.userAgent;
        const isChrome = /Chrome/.test(ua) && !/Edg/.test(ua) && !/OPR/.test(ua);
        if (!isChrome) open = true;
    })()" x-show="open" x-transition class="fixed inset-x-4 top-4 z-50">
        <div class="glass border border-amber-200 px-4 py-2 rounded-full flex items-center justify-between shadow">
            <div class="flex items-center gap-3 text-sm text-amber-900">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-200">
                    <!-- info icon -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                            d="M13 16h-1v-4h-1M12 8h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div class="leading-tight">Gunakan <strong>Google Chrome</strong> untuk pengalaman terbaik.</div>
            </div>
            <button @click="open=false" class="text-amber-700 hover:text-amber-900 ml-4">✕</button>
        </div>
    </div>

    <!-- decorative blobs -->
    <svg class="absolute -top-24 -left-24 w-80 h-80 blob" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
        aria-hidden>
        <defs>
            <linearGradient id="a" x1="0" x2="1">
                <stop offset="0" stop-color="#fff7ed" />
                <stop offset="1" stop-color="#fff3c4" />
            </linearGradient>
        </defs>
        <path fill="url(#a)"
            d="M43.3,-70.4C56.9,-62.8,68.9,-56,74.1,-45.5C79.3,-35,77.8,-20.9,79.1,-7.4C80.4,6.2,84.6,19.6,80,32.3C75.4,45,62,57,47,64.6C31.9,72.3,16,75.6,1.6,73.4C-12.9,71.2,-25.8,63.6,-39.4,55.1C-53,46.6,-67.4,37.2,-74.5,23.7C-81.6,10.3,-81.3,-8.3,-73.8,-23.6C-66.3,-38.9,-51.5,-50.9,-36.2,-58.3C-21,-65.8,-5.3,-68.7,9.8,-74C24.9,-79.3,39.7,-87.9,43.3,-70.4Z"
            transform="translate(100 100)" />
    </svg>

    <main class="min-h-screen lg:min-h-full xl:min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 items-start justify-center">

            <!-- LEFT (only visible on md+): company info, socials, maps, kontak -->
            <aside class="hidden lg:flex flex-col gap-2">
                <div class="glass rounded-2xl p-4 shadow-lg border border-amber-100">
                    <div class="flex items-start gap-4">
                        <img src="{{ asset('logo/sac.png') }}" alt="SAC Logo"
                            class="w-20 h-20 object-contain rounded-lg">
                        <div>
                            <h2 class="text-lg font-extrabold text-amber-900">PT. Surya Amanah Cendekia (SAC)</h2>
                            <p class="mt-2 text-sm text-stone-700">Penyedia layanan outsourcing profesional yang
                                bergerak di bidang cleaning service, security, dan pengembangan SDM. Fokus kami:
                                kualitas, ketepatan, dan dokumentasi tugas lapangan yang rapi.</p>
                        </div>
                    </div>

                    <hr class="my-2 border-amber-100">

                    <!-- Quick facts -->
                    <div class="grid grid-cols-1 gap-4 text-sm text-stone-700">
                        <div class="flex items-center gap-3">
                            <i class="ri-star-line text-[20px] text-amber-500"></i>
                            <span><strong>Bidang:</strong> Cleaning Service, Security, SDM</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="ri-map-2-line text-[20px] text-amber-500"></i>
                            <span><strong>Wilayah layanan:</strong> Nasional</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="ri-map-pin-line text-[20px] text-amber-500"></i>
                            <span><strong>Head Office:</strong> Jl. Budi Utomo No.10, Ronowijayan, Kec. Siman, Kabupaten
                                Ponorogo, Jawa Timur 63471 — <a href="https://maps.app.goo.gl/4UZmBZG4sahM2VWP6"
                                    target="_blank" rel="noopener" class="text-amber-600 hover:underline">Buka di
                                    Maps</a></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="ri-time-line text-[20px] text-amber-500"></i>
                            <span><strong>Jam Operasional (Office):</strong> Senin - Jum'at, 08:00 - 15:30 WIB</span>
                        </div>
                    </div>

                    <hr class="my-4 border-amber-100">

                    <!-- Socials -->
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 mb-2">Akun Media Sosial</h3>
                        <div class="flex flex-wrap gap-3">
                            <a href="https://www.instagram.com/ptsacponorogo/" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/70 border border-amber-100 text-sm hover:shadow">
                                <i class="ri-instagram-line text-[18px]"></i>
                                <span>@ptsacponorogo</span>
                            </a>

                            <a href="https://www.facebook.com/profile.php?id=61571512059418" target="_blank"
                                rel="noopener"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/70 border border-amber-100 text-sm hover:shadow">
                                <i class="ri-facebook-line text-[18px]"></i>
                                <span>Sac Ponorogo</span>
                            </a>

                            <a href="https://youtube.com/@sacponorogo9355?si=UjkQtK5IHPZjfaPk" target="_blank"
                                rel="noopener"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/70 border border-amber-100 text-sm hover:shadow">
                                <i class="ri-youtube-line text-[18px]"></i>
                                <span>SAC Ponorogo</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- contact card -->
                <div class="rounded-xl px-4 py-2 bg-white/80 shadow border border-amber-100 text-sm">
                    <h4 class="font-semibold text-slate-700">Kontak</h4>
                    <div class="mt-2 space-y-2 text-slate-700 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="ri-whatsapp-line text-[18px] text-amber-500"></i>
                            <span>WhatsApp: <a href="https://wa.me/6282134360007"
                                    class="text-amber-600 hover:underline">+62
                                    821-3436-0007</a></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="ri-mail-send-line text-[18px] text-amber-500"></i>
                            <span>Email: <a href="mailto:sacponorogo@gmail.com"
                                    class="text-amber-600 hover:underline">sacponorogo@gmail.com</a></span>
                        </div>

                        <div class="mt-2 relative inline-block">
                            <a href="https://maps.app.goo.gl/4UZmBZG4sahM2VWP6" target="_blank" rel="noopener"
                                class="relative inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-50 text-amber-800
            overflow-hidden group">

                                <!-- Border gradient berputar -->
                                <span
                                    class="absolute -inset-[4px] rounded-lg p-[2px]
                  bg-gradient-to-r from-pink-500 via-yellow-500 to-purple-500
                  opacity-0 group-hover:opacity-100 animate-rotate-border transition-all duration-300"></span>

                                <!-- Background solid biar isi tetap jelas -->
                                <span class="absolute inset-[2px] rounded-lg bg-amber-50"></span>

                                <!-- Konten -->
                                <span class="relative flex items-center gap-2">
                                    <i class="ri-map-pin-fill text-[18px] text-amber-500"></i>
                                    <span>Lihat lokasi di Google Maps</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Right: login card -->
            <section class="mx-auto w-full max-w-md">
                {{ $slot }}
            </section>
        </div>
    </main>

    <footer class="text-center text-xs text-stone-500 absolute bottom-4 w-full">
        © {{ date('Y') }} {{ config('app.name', 'Kinerja SAC-PO') }}
    </footer>
</body>

</html>
