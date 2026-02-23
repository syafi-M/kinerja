@props([
    'fullWidth' => false,
    'headerTitle' => null,
    'online' => null,
    'ip' => null,
])

<!-- Sticky Header -->
<header class="sticky top-0 z-40 border-b shadow-sm backdrop-blur-xl bg-white/70 border-gray-200/50">
    <div class="{{ $fullWidth ? 'px-4 sm:px-6 lg:px-8' : 'px-4 mx-auto max-w-7xl sm:px-6 lg:px-8' }}">
        <div class="flex items-center justify-between h-16 mx-10">
            <div class="flex items-center gap-4">

                <div>
                    <h1 class="text-lg font-bold text-gray-900">{{ $headerTitle ?? 'Dashboard Admin' }}
                    </h1>
                    <p class="hidden text-xs text-gray-500 sm:block">Welcome back, Admin</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div
                    class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 rounded-lg border border-emerald-200">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-xs font-medium text-emerald-700">{{ $online ?? '0' }} Online</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-lg border border-gray-200">
                    <i class="text-sm text-gray-600 ri-global-line"></i>
                    <span class="font-mono text-xs text-gray-700">{{ $ip ?? '127.0.0.1' }}</span>
                </div>
            </div>
        </div>
    </div>
</header>
