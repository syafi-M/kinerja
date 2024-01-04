<x-app-layout>
    <x-main-div>
        <div class="py-10 sm:mx-10">
            <p class="text-center text-lg sm:text-2xl uppercase font-bold ">Hasil Laporan, {{ $laporan->user->nama_lengkap }}</p>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full">
                    <div class="bg-slate-50 rounded-lg shadow-md mx-5">
                        <div class="my-5">
                            @if($laporan->image1 != 'no-image.jpg' && $laporan->image1 != null && Storage::disk('public')->exists('images/' . $laporan->image1)
                            || $laporan->image2 != 'no-image.jpg' && $laporan->image2 != null && Storage::disk('public')->exists('images/' . $laporan->image2)
                            || $laporan->image3 != 'no-image.jpg' && $laporan->image3 != null && Storage::disk('public')->exists('images/' . $laporan->image3))
                                @if($laporan->image1 != 'no-image.jpg' && $laporan->image1 != null && Storage::disk('public')->exists('images/' . $laporan->image1))
                                    <p class="text-center font-semibold">~ Sebelum ~</p>
                                    <span class="flex justify-center"><img src="{{ asset('storage/images/' . $laporan->image1) }}" alt="{{ asset('storage/images/' . $laporan->image1) }}" srcset="{{ asset('storage/images/' . $laporan->image1) }}" width="150px" class="rounded"></span>
                                @endif
                                
                                @if($laporan->image2 != 'no-image.jpg' && $laporan->image2 != null && Storage::disk('public')->exists('images/' . $laporan->image2))
                                    <p class="text-center font-semibold">~ Proses ~</p>
                                    <span class="flex justify-center"><img src="{{ asset('storage/images/' . $laporan->image2) }}" alt="{{ asset('storage/images/' . $laporan->image2) }}" srcset="{{ asset('storage/images/' . $laporan->image2) }}" width="150px" class="rounded"></span>
                                @endif
                                @if($laporan->image3 != 'no-image.jpg' && $laporan->image3 != null && Storage::disk('public')->exists('images/' . $laporan->image3))
                                    <p class="text-center font-semibold">~ Sesudah ~</p>
                                    <span class="flex justify-center"><img src="{{ asset('storage/images/' . $laporan->image3) }}" alt="{{ asset('storage/images/' . $laporan->image3) }}" srcset="{{ asset('storage/images/' . $laporan->image3) }}" width="150px" class="rounded"></span>
                                @endif
                            @else
                                <p class="text-center font-semibold my-10">Gambar Tidak Ditemukan</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-center sm:justify-end w-full mt-5">
            			 <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
        
    </x-main-div>
</x-app-layout>