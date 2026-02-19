<x-app-layout>
    <div class="mx-auto w-full max-w-xl px-4 py-8 sm:px-6">
        <section class="rounded-2xl border border-red-100 bg-white p-6 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-red-600">Point Summary</p>
            <h1 class="mt-1 text-xl font-bold tracking-tight text-gray-900">Point Saya</h1>
            <p class="mt-3 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                Kamu belum mempunyai point.
            </p>
            <div class="mt-5 flex justify-end">
                <a href="{{ url('dashboard') }}" class="inline-flex h-10 items-center rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
