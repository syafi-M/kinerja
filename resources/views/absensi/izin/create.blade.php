<x-app-layout>
    <x-main-div>
        <div class="max-w-4xl p-4 mx-auto sm:p-6">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold tracking-wide uppercase sm:text-3xl text-slate-800">Halaman Izin</h1>
                <div class="w-24 h-1 mx-auto mt-2 rounded-full bg-amber-500"></div>
            </div>

            <div class="overflow-hidden bg-white shadow-md rounded-xl">
                <form action="{{ route('izin.store') }}" method="POST" enctype="multipart/form-data" id="form-izin" class="p-6">
                    @csrf
                    @method('POST')

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Nama Lengkap -->
                        <div>
                            <x-input-label for="nama_lengkap" :value="__('Nama Lengkap')" class="font-medium text-slate-700" />
                            <div class="relative mt-1">
                                <div class="flex items-center">
                                    <i class="absolute ri-user-line left-3 text-slate-400"></i>
                                    <x-text-input id="nama_lengkap" class="block w-full py-2 pl-10 pr-3 border rounded-lg border-slate-300 bg-slate-50 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                            type="text"
                                            value="{{ Auth::user()->nama_lengkap }}"
                                            readonly
                                            required autocomplete="nama_lengkap" />
                                </div>
                            </div>
                            <input type="text" name="user_id" id="user_id" value="{{ Auth::user()->id }}" hidden>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        <!-- Bermitra dengan -->
                        <div>
                            <x-input-label for="kerjasama_id" :value="__('Bermitra dengan')" class="font-medium text-slate-700" />
                            <div class="relative mt-1">
                                <div class="flex items-center">
                                    <i class="absolute ri-building-line left-3 text-slate-400"></i>
                                    <x-text-input id="kerjasama_id" class="block w-full py-2 pl-10 pr-3 border rounded-lg border-slate-300 bg-slate-50 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                            type="text"
                                            value="{{ Auth::user()->kerjasama->client->name }}"
                                            readonly
                                            required autocomplete="kerjasama_id" />
                                </div>
                            </div>
                            <input type="text" name="kerjasama_id" id="kerjasama_id" value="{{ Auth::user()->kerjasama_id }}" hidden>
                            <x-input-error :messages="$errors->get('kerjasama_id')" class="mt-2" />
                        </div>

                        <!-- Pilih Shift -->
                        <div class="md:col-span-2">
                            <x-input-label for="shift_id" :value="__('Pilih Shift')" class="font-medium text-slate-700" />
                            <div class="relative mt-1">
                                <div class="flex items-center">
                                    <i class="absolute ri-time-line left-3 text-slate-400"></i>
                                    <select name="shift_id" id="shift_id" class="block w-full py-2 pl-10 pr-10 bg-white border rounded-lg appearance-none border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                        <option disabled selected>-- Pilih Shift --</option>
                                        @forelse ($shift as $i)
                                            @if(Auth::user()->kerjasama->client_id == $i->client_id && Auth::user()->devisi_id == $i->jabatan->divisi_id)
                                                <option value="{{ $i->id }}">{{ $i->jabatan->name_jabatan }} | {{ $i->shift_name }} | {{ $i->jam_start }}</option>
                                            @endif
                                        @empty
                                            <option>~ Tidak ada Shift ! ~</option>
                                        @endforelse
                                    </select>
                                    <i class="absolute pointer-events-none ri-arrow-down-s-line right-3 text-slate-400"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('shift_id')" class="mt-2" />
                        </div>

                        <!-- Alasan Izin -->
                        <div class="md:col-span-2">
                            <x-input-label for="alasan_izin" :value="__('Alasan Izin')" class="font-medium text-slate-700" />
                            <div class="relative mt-1">
                                <div class="flex items-start">
                                    <i class="absolute ri-file-text-line left-3 top-3 text-slate-400"></i>
                                    <textarea name="alasan_izin" id="alasan_izin" rows="3" class="block w-full py-2 pl-10 pr-3 border rounded-lg border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Masukkan alasan izin..."></textarea>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('alasan_izin')" class="mt-2" />
                        </div>

                        <!-- Bukti Izin -->
                        <div class="md:col-span-2">
                            <x-input-label for="img" :value="__('Bukti Izin')" class="font-medium text-slate-700" />
                            <div class="mt-1">
                                <div class="hidden mb-4 preview">
                                    <div class="flex justify-center">
                                        <div class="relative group">
                                            <img class="object-cover w-32 h-32 border-2 rounded-lg shadow-sm img1 border-slate-300" src="" alt="Preview">
                                            <button type="button" class="absolute p-1 text-white transition-opacity bg-red-500 rounded-full opacity-0 remove-image -top-2 -right-2 group-hover:opacity-100">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <label for="img" class="flex flex-col items-center justify-center w-full h-40 transition-colors border-2 border-dashed rounded-lg cursor-pointer border-slate-300 bg-slate-50 hover:bg-slate-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="mb-2 text-3xl ri-image-add-line text-slate-400"></i>
                                        <p class="text-sm text-slate-500">Klik untuk upload gambar</p>
                                        <p class="mt-1 text-xs text-slate-400">PNG, JPG, JPEG (MAX. 2MB)</p>
                                    </div>
                                    <input id="img" class="hidden" type="file" name="img" accept=".gif,.tif,.tiff,.png,.crw,.cr2,.dng,.raf,.nef,.nrw,.orf,.rw2,.pef,.arw,.sr2,.raw,.psd,.svg,.webp,.heic" />
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('img')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col justify-end gap-3 pt-6 mt-8 border-t sm:flex-row border-slate-200">
                        <a href="{{ route('dashboard.index') }}" class="px-5 py-2.5 text-center text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <i class="ri-arrow-left-line"></i>
                            <span>Kembali</span>
                        </a>
                        <button type="button" id="btnIzin" class="px-5 py-2.5 text-center text-white bg-amber-500 hover:bg-amber-600 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <i class="ri-send-plane-line"></i>
                            <span>Kirim Permintaan Izin</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Image preview functionality
                $('#img').change(function() {
                    const file = this.files[0];
                    if (file) {
                        // Check if file type is previewable
                        if (file.type.startsWith('image/') && !file.type.includes('raw')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                $('.img1').attr('src', e.target.result);
                                $('.preview').removeClass('hidden');
                            }
                            reader.readAsDataURL(file);
                        } else {
                            // Show a generic icon for non-previewable formats
                            $('.img1').attr('src', '/path/to/generic-image-icon.png');
                            $('.preview').removeClass('hidden');
                        }
                    }
                });

                // Remove image functionality
                $('.remove-image').click(function() {
                    $('#img').val('');
                    $('.preview').addClass('hidden');
                });

                // Form submission
                $('#btnIzin').click(function(){
                    const btn = $(this);
                    const originalContent = btn.html();

                    // Disable button and show loading state
                    btn.prop('disabled', true);
                    btn.html('<i class="ri-loader-4-line animate-spin"></i> <span>Mengirim...</span>');
                    btn.addClass('opacity-75 cursor-not-allowed');

                    // Submit form
                    $('#form-izin').submit();

                    // Fallback to re-enable button after 5 seconds in case of issues
                    setTimeout(function() {
                        btn.prop('disabled', false);
                        btn.html(originalContent);
                        btn.removeClass('opacity-75 cursor-not-allowed');
                    }, 5000);
                });
            });
        </script>
    </x-main-div>
</x-app-layout>
