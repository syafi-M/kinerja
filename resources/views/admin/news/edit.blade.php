<x-admin-layout :fullWidth="true">
    @section('title', 'Edit Berita')

    <div class="mx-auto w-full max-w-screen-lg space-y-4 px-2 sm:px-3 lg:px-4">
        <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-blue-600">News Management</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">Edit Berita</h1>
                    <p class="mt-1 text-sm text-gray-600">Perbarui periode tayang dan gambar berita.</p>
                </div>
                <a href="{{ route('news.index') }}" class="inline-flex h-10 items-center rounded-xl border border-red-200 bg-red-50 px-4 text-sm font-semibold text-red-700 transition hover:bg-red-100">Kembali</a>
            </div>
        </section>

        <form method="POST" action="{{ route('news.update', $newsId->id) }}" class="space-y-4" id="form" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                <div class="space-y-4">
                    <div>
                        <label for="img" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600">Foto Berita</label>
                        <div class="mb-2"><img id="newsPreview" class="h-24 w-40 rounded-lg border border-gray-200 object-cover" src="{{ asset('storage/images/' . $newsId->image) }}" alt="Preview"></div>
                        <input id="img" class="file-input file-input-bordered w-full" type="file" name="image" accept="image/*"/>
                        <x-input-error :messages="$errors->get('image1')" class="mt-2" />
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="tanggal_lihat" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600">Tanggal Berlaku</label>
                            <input type="date" name="tanggal_lihat" id="tanggal_lihat" value="{{ $newsId->tanggal_lihat }}" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-800 focus:border-blue-300 focus:bg-white focus:outline-none" required/>
                            <x-input-error :messages="$errors->get('tanggal_lihat')" class="mt-2" />
                        </div>
                        <div>
                            <label for="tanggal_tutup" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-600">Tanggal Berakhir</label>
                            <input type="date" name="tanggal_tutup" id="tanggal_tutup" value="{{ $newsId->tanggal_tutup }}" class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 text-sm text-gray-800 focus:border-blue-300 focus:bg-white focus:outline-none" required/>
                            <x-input-error :messages="$errors->get('tanggal_tutup')" class="mt-2" />
                        </div>
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <a href="{{ route('news.index') }}" class="inline-flex h-10 items-center rounded-xl border border-red-200 bg-red-50 px-4 text-sm font-semibold text-red-700 transition hover:bg-red-100">Batal</a>
                    <button type="submit" class="inline-flex h-10 items-center rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </section>
        </form>
    </div>

    @push('scripts')
        <script>
            $(function() {
                $('#img').on('change', function(e) {
                    const file = e.target.files && e.target.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        $('#newsPreview').attr('src', ev.target.result);
                    };
                    reader.readAsDataURL(file);
                });
            });
        </script>
    @endpush
</x-admin-layout>
