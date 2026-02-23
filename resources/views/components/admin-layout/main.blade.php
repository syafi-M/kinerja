@props([
    'fullWidth' => false,
    'headerTitle' => null,
    'online' => null,
    'ip' => null,
])

<!-- MAIN CONTENT AREA -->
<div :class="sidebarOpen ? 'ml-56' : 'ml-16'" class="flex flex-col flex-1 transition-all duration-300">

    <x-admin-layout.topbar :full-width="$fullWidth" :header-title="$headerTitle" :online="$online" :ip="$ip" />

    <!-- Page Content -->
    <main class="p-3 sm:p-4 lg:p-6">
        <div class="legacy-admin {{ $fullWidth ? 'w-full' : 'mx-auto max-w-7xl' }}">
            {{ $slot }}
        </div>
    </main>

</div>
