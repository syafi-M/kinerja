<x-app-layout>
    <div class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6">
        <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-blue-600">Point Summary</p>
                <h1 class="mt-1 text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">Ringkasan Poin Saya</h1>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full min-w-[520px] divide-y divide-gray-100">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Client</th>
                            <th class="px-4 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        <tr>
                            <td class="px-4 py-3 text-gray-500">1</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ Auth::user()->nama_lengkap }}</td>
                            <td class="px-4 py-3">{{ Auth::user()->kerjasama->client->name }}</td>
                            @if ($absen != null)
                                <td class="px-4 py-3 font-semibold text-blue-700">{{ count($absen) * $absen[0]['point']->sac_point }}</td>
                            @else
                                <td class="px-4 py-3 text-gray-500">Poin kosong</td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                <a href="{{ route('dashboard.index') }}" class="inline-flex h-10 items-center rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
            </div>
        </section>
    </div>
</x-app-layout>
