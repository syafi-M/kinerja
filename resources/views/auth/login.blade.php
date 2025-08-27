<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div>
        <!-- Login Heading -->
        <div class="text-center">
            <h2 class="text-lg md:text-2xl font-semibold text-gray-700">
                Masuk ke Sistem Kinerja
            </h2>
        </div>
        {{-- login form --}}
        <form method="POST" action="{{ route('login') }}" id="form-login" class="space-y-4">
            @csrf

            <!-- Nama Pengguna Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pengguna</label>
                <div class="relative">
                    <!-- Nama Pengguna Icon -->
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <input type="text" id="name" name="name" placeholder="Masukkan nama pengguna Anda"
                        required
                        class="block w-full rounded-xl border-2 border-orange-300 pl-10 pr-4 py-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200 shadow-sm">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Kata Sandi Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <!-- Kata Sandi Icon -->
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15a2 2 0 110-4 2 2 0 010 4z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9a7 7 0 017 7v2a1 1 0 01-1 1H6a1 1 0 01-1-1v-2a7 7 0 017-7z" />
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Masukkan kata sandi Anda"
                        required
                        class="block w-full rounded-xl border-2 border-orange-300 pl-10 pr-4 py-3 text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200 shadow-sm">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Login Button -->
            <div class="flex items-center justify-center mt-4">
                <button id="btnLogin" type="submit"
                    class="bg-teal-400 hover:bg-teal-500 rounded-lg py-2 px-10 shadow font-semibold transition-all duration-300">Log
                    In</button>
            </div>
        </form>

        <!-- "Not Chrome" message, hidden by default -->
        <div id="divNotChrome" class="hidden text-center mt-4">
            <p class="text-red-500 font-medium">Gunakan Chrome Untuk Menggunakan Website Ini</p>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#btnLogin').click(function() {
                $(this).prop('disabled', true).text('Tunggu...').css(
                    'background-color: rgb(96 165 250 / 0.5);');
                $('#form-login').submit();
            });
        })
    </script>
</x-guest-layout>
