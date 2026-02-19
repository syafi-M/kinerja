<x-admin-layout :fullWidth="true">
        <div class="px-5 py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold py-5 uppercase">Qr Code</p>
            </div>
                <form action="{{ route('qrcode.store') }}" method="post">
                    @csrf
                    <div class="flex flex-col gap-2">
                        <x-input-label for="pekerjaan_id" :value="__('Pilih Mitra')" class="text-white"/>
                        <select name="kerjasama_id" id="kerjasama_id" class="select-bordered select">
                            <option selected disabled>~ Select Kerjasama~</option>
                            @forelse ($kerjasama as $ke)
                             <option name="kerjasama_id" value="{{ $ke->id }}">{{ $ke->client->name }}</option>
                            @empty
                            @endforelse
                        </select>
                        <x-input-error :messages="$errors->get('kerjasama_id')" class="mt-2" />
                    </div>
                    
                    @forelse($qr as $i)
                        <span class="qr-item" data-id="{{ $i->id }}" data-kerjasama="{{ $i->kerjasama_id }}" data-ruangan="{{ $i->ruangan_id }}"></span>
                    @empty
                        <span></span>
                    @endforelse
                    
                    <div class="flex flex-col gap-2">
                        <x-input-label for="pekerjaan_id" :value="__('Pilih Ruangan')" class="text-white"/>
                        <select name="ruangan_id" id="ruangan_id" class="select-bordered select">
                            <option selected disabled value="0">~ Select Ruangan~</option>
                            @forelse ($ruangan as $i)
                             <option name="ruangan_id" data-kerjasama_id="{{ $i->kerjasama_id }}" data-ruang="{{ $i->id}}" value="{{ $i->id }}">{{ $i->nama_ruangan }}</option>
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
            $('#kerjasama_id').change(function () {
                const selectedKerjasama = $(this).val();
                var qr = {!! json_encode($qr) !!};
                let anyOptionVisible = false;
        
                $('#ruangan_id option').each(function () {
                    $(this).show(); // Show all options initially
                    
                    if ($(this).data('kerjasama_id') == selectedKerjasama) {
                        const ruanganId = parseInt($(this).data('ruang'));
        
                        // Check if the ruangan_id matches any of the options, then hide it
                        qr.forEach(function (item) {
                            if (ruanganId === parseInt(item.ruangan_id)) {
                                $(this).hide();
                                $('#ruangan_id').val('0');
                                // console.log(ruanganId, parseInt(item.ruangan_id));
                            }
                        }.bind(this));
                        
                        if ($(this).is(':visible')) {
                            anyOptionVisible = true; // Set the flag to true if any option is visible
                        }
                        
                    } else {
                        $(this).hide(); // Hide if the kerjasama_id doesn't match
                    }
                });
                
                if (!anyOptionVisible) {
                    $('#ruangan_id').val('0');
                    $('#ruangan_id option[value="0"]').show();
                }
        
                $('#ruangan_id').val('0');
            });
        });

    </script>
</x-admin-layout>