<x-app-layout>
    <x-main-div>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

            <!-- Header -->
            <div class="card-container mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('index.rekap.data.leader') }}"
                            class="w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all">
                            <i class="ri-arrow-left-line text-xl text-white"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Riwayat Lembur</h1>
                            <p class="text-slate-200 text-sm">History Pengajuan Lembur</p>
                        </div>
                    </div>
                    <a href="{{ route('overtime-application.create') }}"
                        class="btn-primary px-4 py-2 rounded-lg flex items-center gap-2 whitespace-nowrap">
                        <i class="ri-add-line"></i>
                        <span>Pengajuan Baru</span>
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card-white p-4 mb-6">
                <form action="{{ route('overtime-application.show', 1) }}" method="GET"
                    class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <select name="status"
                            class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="Di Ajukan" {{ request('status') == 'Di Ajukan' ? 'selected' : '' }}>Di Ajukan
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                            </option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <input type="month" name="month" value="{{ request('month') }}"
                            class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm">
                    </div>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all flex items-center gap-2 justify-center">
                        <i class="ri-filter-3-line"></i>
                        <span>Filter</span>
                    </button>
                    <a href="#"
                        class="px-6 py-2 border-2 border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg font-medium transition-all flex items-center gap-2 justify-center">
                        <i class="ri-refresh-line"></i>
                        <span>Reset</span>
                    </a>
                </form>
            </div>

            <!-- Table Card -->
            <div class="card-white overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">
                                    No
                                </th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">
                                    Nama Pegawai
                                </th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">
                                    Tanggal Pengisian
                                </th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">
                                    Tanggal Lembur
                                </th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">
                                    Jenis Lembur
                                </th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">
                                    Keterangan
                                </th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">
                                    Status
                                </th>
                                <th
                                    class="px-4 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider whitespace-nowrap">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($overtimes ?? [] as $index => $overtime)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-4 text-sm text-slate-700">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-blue-600 font-semibold text-sm">
                                                    {{ substr($overtime->user->nama_lengkap ?? 'N', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-800 whitespace-nowrap">
                                                    {{ $overtime->user->name ?? 'N/A' }}</p>
                                                <p class="text-xs text-slate-500">
                                                    {{ $overtime->user->nama_lengkap ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($overtime->created_at ?? now())->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($overtime->date_overtime ?? now())->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @if (($overtime->type_overtime ?? 'shift') == 'shift')
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium whitespace-nowrap">
                                                <i class="ri-time-line"></i>
                                                1 Shift
                                            </span>
                                        @elseif(($overtime->type_overtime ?? '') == 'jam')
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-medium whitespace-nowrap">
                                                <i class="ri-timer-line"></i>
                                                {{ $overtime->type_overtime_manual }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-medium whitespace-nowrap">
                                                <i class="ri-more-line"></i>
                                                {{ $overtime->type_overtime_manual }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700 max-w-xs">
                                        <div class="line-clamp-2"
                                            title="{{ $overtime->desc ?? 'Tidak ada keterangan' }}">
                                            {{ $overtime->desc ?? 'Tidak ada keterangan' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if (($overtime->status ?? 'pending') == 'Di Ajukan')
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium whitespace-nowrap">
                                                <i class="ri-checkbox-circle-fill"></i>
                                                Di Ajukan
                                            </span>
                                        @elseif(($overtime->status ?? 'pending') == 'rejected')
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-medium whitespace-nowrap">
                                                <i class="ri-close-circle-fill"></i>
                                                Ditolak
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-medium whitespace-nowrap">
                                                <i class="ri-time-fill"></i>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="viewDetail({{ $overtime->id ?? 0 }})"
                                                class="w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 flex items-center justify-center transition-all"
                                                title="Lihat Detail">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            @if (($overtime->status ?? 'pending') == 'pending')
                                                <a href="{{ route('overtime-application.edit', $overtime->id) }}"
                                                    class="w-8 h-8 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-600 flex items-center justify-center transition-all"
                                                    title="Edit">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <button onclick="deleteOvertime({{ $overtime->id ?? 0 }})"
                                                    class="w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center transition-all"
                                                    title="Hapus">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                                <button onclick="sendOvertime({{ $overtime->id ?? 0 }})" type="button"
                                                    title="Send"
                                                    class="w-8 h-8 rounded-lg bg-green-100 hover:bg-green-200 text-green-600 flex items-center justify-center transition-all">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24" fill="currentColor">
                                                        <path
                                                            d="M19.4999 2C20.0945 1.99965 20.6988 2.15061 21.2499 2.46875C22.924 3.43525 23.4977 5.57598 22.5312 7.25L15.0312 20.2402C14.0647 21.9142 11.924 22.488 10.2499 21.5215C9.41368 21.0386 8.85171 20.2606 8.62005 19.3975L6.85345 12.8037L2.02533 7.97461C0.65837 6.60765 0.658729 4.39208 2.02533 3.02539C2.65755 2.39311 3.53385 2.00011 4.49994 2H19.4999ZM4.49994 4C4.08555 4.00011 3.71182 4.167 3.43939 4.43945C2.85354 5.0254 2.85378 5.97494 3.43939 6.56055L7.914 11.0352L14.8906 7.00684C15.3688 6.7308 15.9806 6.89487 16.2568 7.37305C16.5327 7.85124 16.3687 8.46312 15.8906 8.73926L8.914 12.7676L10.5517 18.8789C10.6515 19.2509 10.8913 19.5819 11.2499 19.7891C11.9673 20.2032 12.8845 19.9575 13.2988 19.2402L20.7988 6.25C21.213 5.53256 20.9674 4.61539 20.2499 4.20117C20.0128 4.06427 19.7555 3.99982 19.5009 4H4.49994Z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div
                                                class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                                <i class="ri-inbox-line text-3xl text-slate-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-slate-600 font-medium">Tidak ada data</p>
                                                <p class="text-slate-500 text-sm">Belum ada riwayat pengajuan lembur
                                                </p>
                                            </div>
                                            <a href="{{ route('overtime-application.create') }}"
                                                class="btn-primary px-6 py-2 rounded-lg flex items-center gap-2 mt-2">
                                                <i class="ri-add-line"></i>
                                                <span>Buat Pengajuan</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if (isset($overtimes) && $overtimes->hasPages())
                    <div class="px-4 py-4 border-t border-slate-200">
                        {{ $overtimes->links() }}
                    </div>
                @endif
            </div>
            <div class="w-full flex justify-end items-center">
                <form action="{{ route('overtime-bulk.status') }}" method="post">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="m-2 btn btn-sm rounded-sm bg-green-100 hover:bg-green-200 text-green-600 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="currentColor">
                            <path
                                d="M19.4999 2C20.0945 1.99965 20.6988 2.15061 21.2499 2.46875C22.924 3.43525 23.4977 5.57598 22.5312 7.25L15.0312 20.2402C14.0647 21.9142 11.924 22.488 10.2499 21.5215C9.41368 21.0386 8.85171 20.2606 8.62005 19.3975L6.85345 12.8037L2.02533 7.97461C0.65837 6.60765 0.658729 4.39208 2.02533 3.02539C2.65755 2.39311 3.53385 2.00011 4.49994 2H19.4999ZM4.49994 4C4.08555 4.00011 3.71182 4.167 3.43939 4.43945C2.85354 5.0254 2.85378 5.97494 3.43939 6.56055L7.914 11.0352L14.8906 7.00684C15.3688 6.7308 15.9806 6.89487 16.2568 7.37305C16.5327 7.85124 16.3687 8.46312 15.8906 8.73926L8.914 12.7676L10.5517 18.8789C10.6515 19.2509 10.8913 19.5819 11.2499 19.7891C11.9673 20.2032 12.8845 19.9575 13.2988 19.2402L20.7988 6.25C21.213 5.53256 20.9674 4.61539 20.2499 4.20117C20.0128 4.06427 19.7555 3.99982 19.5009 4H4.49994Z">
                            </path>
                        </svg>Ajukan Semua</button>
                </form>
            </div>
        </div>
    </x-main-div>

    <!-- Detail Modal -->
    <div x-data="{ open: false, detail: {} }" x-show="open" @detail-modal.window="open = true; detail = $event.detail" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="open = false"></div>

        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6" @click.away="open = false"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Detail Pengajuan Lembur</h3>
                    <button @click="open = false"
                        class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center transition-all">
                        <i class="ri-close-line text-xl text-slate-600"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-4 pb-4 border-b border-slate-200">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Nama Pegawai</p>
                            <p class="font-semibold text-slate-800" x-text="detail.user?.nama_lengkap || '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Tanggal Lembur</p>
                            <p class="font-semibold text-slate-800" x-text="detail.date_overtime || '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Jenis Lembur</p>
                            <p class="font-semibold text-slate-800"
                                x-text="detail.type_overtime == 'shift' ? '1 Shift' : detail.type_overtime"></p>
                            <p class="font-semibold text-xs text-slate-800" x-text="detail.type_overtime_manual">
                            </p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-2">Keterangan</p>
                        <p class="text-sm text-slate-700" x-text="detail.desc || '-'"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Status</p>
                            <span x-text="detail.status ?? '-'"
                                :class="{
                                    'bg-green-100 text-green-700': detail.status == 'Di Ajukan',
                                    'bg-yellow-100 text-yellow-700': detail.status == 'pending',
                                    'bg-red-100 text-red-700': detail.status == 'rejected'
                                }"
                                class="inline-block px-3 py-1 rounded-full text-xs font-medium"></span>

                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Tanggal Pengisian</p>
                            <p x-text="detail.created_at?.replace('T', ' ').split('.')[0]"></p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200">
                    <button @click="open = false"
                        class="px-6 py-2 border-2 border-slate-300 hover:bg-slate-50 rounded-lg font-medium transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function viewDetail(id) {
            const url = "{{ route('get-overtime-id', ':id') }}".replace(':id', id);

            try {
                const res = await fetch(url);
                const response = await res.json();

                // Dispatch event to Alpine modal
                window.dispatchEvent(new CustomEvent('detail-modal', {
                    detail: response.data
                }));
            } catch (err) {
                console.error(err);
            }
        }


        function deleteOvertime(id) {
            if (confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')) {
                // Submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/overtime-application/${id}`;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function sendOvertime(id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/overtime-change-status/${id}`;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';

            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>

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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px;
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

        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Table styling */
        table {
            border-collapse: collapse;
        }

        /* Select dropdown arrow */
        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            appearance: none;
        }

        /* Alpine.js cloak */
        [x-cloak] {
            display: none !important;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .card-container {
                padding: 16px;
            }
        }
    </style>
</x-app-layout>
