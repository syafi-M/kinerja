<x-admin-layout :fullWidth="true">
        <div class="px-5 py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold py-5 uppercase">Qr Code</p>
            </div>
                <form action="{{ route('qrcode.update', $qr->id) }}" method="post">
                    @csrf
                    @method('PATCH')
                    <div class="flex flex-col gap-2">
                        <x-input-label for="pekerjaan_id" :value="__('Pilih Mitra')" class="text-white"/>
                        <select name="kerjasama_id" id="kerjasama_id" class="select-bordered select">
                            <option selected disabled>~ Select Kerjasama~</option>
                            @forelse ($kerjasama as $ke)
                             <option name="kerjasama_id" {{ $qr->kerjasama_id == $ke->id ? 'selected' : "" }} value="{{ $ke->id }}">{{ $ke->client->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        <x-input-error :messages="$errors->get('kerjasama_id')" class="mt-2" />
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <x-input-label for="pekerjaan_id" :value="__('Pilih Ruangan')" class="text-white"/>
                        <select name="ruangan_id" id="ruangan_id" class="select-bordered select">
                            <option selected disabled value="0">~ Select Ruangan~</option>
                            @forelse ($ruangan as $i)
                             <option name="ruangan_id" data-kerjasama_id="{{ $i->kerjasama_id }}" {{ $qr->ruangan_id == $i->id ? 'selected' : "" }} value="{{ $i->id }}">{{ $i->nama_ruangan }}</option>
                            @empty
                                <option>~ Ruangan Kosong~</option>
                            @endforelse
                        </select>
                        <x-input-error :messages="$errors->get('ruangan_id')" class="mt-2" />
                    </div>
                    <div class="flex justify-center sm:justify-end gap-2 mt-10">
        				<button type="submit" id="btnSubmit" class="btn btn-primary">Simpan</button>
        				<a href="{{ route('qrcode.index') }}" class="btn btn-error hover:bg-red-500 transition-all ease-linear .2s">
        					Kembali
        				</a>
        			</div>
                </form>
            </div>
    
    <script>
        $(document).ready(function () {
                // Jalankan fungsi hanya sekali saat dokumen dimuat
                const selectedKerjasama = $('#kerjasama_id').val();
            
                $('#ruangan_id option').each(function () {
                    const ruanganKerjasamaId = $(this).data('kerjasama_id') || ""; // Handle undefined case
                    if (ruanganKerjasamaId == selectedKerjasama || ruanganKerjasamaId == "") {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            
                $('#ruangan_id').val({{ $qr->ruangan_id ? $qr->ruangan_id : '0' }});
            });
    
        $(document).ready(function () {
            
               $('#kerjasama_id').change(function () {
                const selectedKerjasama = $(this).val();
                
                $('#ruangan_id option').each(function () {
                    const ruanganKerjasamaId = $(this).data('kerjasama_id') || ""; // Handle undefined case
                    if (ruanganKerjasamaId == selectedKerjasama || ruanganKerjasamaId == "") {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
    
                $('#ruangan_id').val('0');
            });
        });
    </script>
</x-admin-layout>