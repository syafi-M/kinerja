<x-app-layout>
    @push('styles')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

            * {
                font-family: 'Inter', sans-serif;
            }

            body {
                background: #f1f5f9;
            }

            .card-container {
                background: #64748b;
                border-radius: 16px;
                padding: 24px;
            }

            .card-white {
                background: white;
                border-radius: 12px;
                transition: all 0.2s ease;
            }

            .card-white:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            .btn-primary {
                background: #fbbf24;
                color: #1f2937;
                font-weight: 600;
                transition: all 0.2s ease;
            }

            .btn-primary:hover {
                background: #f59e0b;
                transform: translateY(-1px);
            }

            .profile-circle {
                width: 48px;
                height: 48px;
                background: #94a3b8;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .icon-blue {
                color: #3b82f6;
            }

            .icon-red {
                color: #ef4444;
            }

            .icon-green {
                color: #10b981;
            }

            .icon-yellow {
                color: #f59e0b;
            }

            .icon-indigo {
                color: #6366f1;
            }

            .icon-pink {
                color: #ec4899;
            }

            .expand-btn {
                overflow: hidden !important;
                transition: transform 0.2s ease;
            }

            .expand-btn.rotated {
                overflow: hidden !important;
                transform: rotate(90deg);
            }

            .menu-item {
                transition: all 0.2s ease;
            }

            .menu-item:hover {
                background: #f9fafb;
            }

            .stat-card {
                background: #f8fafc;
                border-radius: 8px;
                padding: 12px;
            }

            /* Responsive adjustments */
            @media (max-width: 640px) {
                .card-container {
                    padding: 16px;
                }
            }
        </style>
    @endpush
    <x-main-div>
        <div class="max-w-6xl m-5">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-white">Rekapitulasi</h1>
                <p class="text-white/60 text-sm mt-1">Rekap Data & Pengajuan Rekap</p>
            </div>

            <!-- Parent wrapper with shared state -->
            <div x-data="{ activeAccordion: null }" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <!-- Pengajuan Lembur -->
                <div class="card-white p-5 sm:p-6">
                    <button @click="activeAccordion = activeAccordion === 'lembur' ? null : 'lembur'"
                        class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-3">
                            <i class="ri-time-line text-xl icon-blue"></i>
                            <span class="font-semibold text-slate-800 text-base">Pengajuan Lembur</span>
                        </div>
                        <i class="ri-arrow-right-s-line text-xl text-slate-600 transition-transform duration-200"
                            :class="{ 'rotate-90': activeAccordion === 'lembur' }"></i>
                    </button>

                    <div x-show="activeAccordion === 'lembur'" x-collapse class="space-y-2 mt-4">
                        <a href="{{ route('overtime-application.create') }}"
                            class="menu-item flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                            <i class="ri-file-add-line icon-blue text-lg"></i>
                            <span class="text-sm font-medium text-slate-700 flex-1">Pengajuan</span>
                        </a>
                        <a href="{{ route('overtime-application.show', 1) }}"
                            class="menu-item flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                            <i class="ri-history-line icon-blue text-lg"></i>
                            <span class="text-sm font-medium text-slate-700 flex-1">Riwayat</span>
                        </a>
                    </div>
                </div>

                <!-- Personil Keluar -->
                <div class="card-white p-5 sm:p-6">
                    <button @click="activeAccordion = activeAccordion === 'personil' ? null : 'personil'"
                        class="flex items-center justify-between w-full">
                        <div class="flex items-center gap-3">
                            <i class="ri-user-unfollow-line text-xl icon-red"></i>
                            <span class="font-semibold text-slate-800 text-base">Personil Keluar</span>
                        </div>
                        <i class="ri-arrow-right-s-line text-xl text-slate-600 transition-transform duration-200"
                            :class="{ 'rotate-90': activeAccordion === 'personil' }"></i>
                    </button>

                    <div x-show="activeAccordion === 'personil'" x-collapse class="space-y-2 mt-4">
                        <a href="{{ route('person-is-out.create') }}"
                            class="menu-item flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                            <i class="ri-file-add-line icon-red text-lg"></i>
                            <span class="text-sm font-medium text-slate-700 flex-1">Pengajuan</span>
                        </a>
                        <a href="{{ route('person-is-out.show', 1) }}"
                            class="menu-item flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                            <i class="ri-history-line icon-red text-lg"></i>
                            <span class="text-sm font-medium text-slate-700 flex-1">Riwayat</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </x-main-div>
</x-app-layout>
