<x-app-layout>
    <x-main-div>
        <div class="max-w-6xl px-4 py-8 mx-auto">
            <!-- Page Header -->
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold tracking-wide uppercase sm:text-3xl text-slate-800">Riwayat Izin Saya</h1>
                <div class="w-24 h-1 mx-auto mt-2 rounded-full bg-amber-500"></div>
            </div>

            <!-- Table Container -->
            <div class="mb-6 overflow-hidden bg-white shadow-md rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200" id="searchTable">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">#</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Nama Lengkap</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Mitra</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Shift</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Alasan Izin</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left uppercase text-slate-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($izin as $i)
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap text-slate-900">{{ $no++ }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-slate-700">{{ $i->user->nama_lengkap }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-slate-700">{{ $i->kerjasama->client->name }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-slate-700">{{ $i->shift->shift_name }}</td>
                                    <td class="max-w-xs px-6 py-4 text-sm text-slate-700">{{ $i->alasan_izin }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($i->approve_status == 'process')
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                                <i class="mr-1 ri-time-line"></i>
                                                Proses
                                            </span>
                                        @elseif($i->approve_status == 'accept')
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                                                <i class="mr-1 ri-checkbox-circle-line"></i>
                                                Diterima
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                                                <i class="mr-1 ri-close-circle-line"></i>
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="mb-2 text-4xl ri-inbox-line text-slate-300"></i>
                                            <p class="text-slate-500">Tidak ada data riwayat izin</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col items-center justify-between gap-4 mb-6 sm:flex-row">
                <div class="text-sm text-slate-700">
                    Menampilkan {{ $izin->firstItem() }}-{{ $izin->lastItem() }} dari {{ $izin->total() }} data
                </div>
                <div id="pag-1" class="flex justify-center">
                    {{ $izin->links() }}
                </div>
            </div>

            <!-- Back Button -->
            <div class="flex justify-center">
                <a href="{{ route('dashboard.index') }}" class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <i class="mr-2 ri-arrow-left-line"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </x-main-div>
</x-app-layout>
