<x-app-layout>
    <x-main-div>
        <style>
            .divImg {
                @media (min-width: 768px) { max-width: 50svw; }
            }
            
            .container-data p {
                font-size: 7%;
                 @media (min-width: 768px) {
                     font-size: 60%; 
                }
            }
            .container-data #bulan {
                top: 31.8%;
                 @media (min-width: 768px) {
                     top: 32%; 
                }
            }
            .container-data #nama {
                top: 36.4%;
                 @media (min-width: 768px) {
                     top: 36.6%; 
                }
            }
            .container-data #jabatan {
                top: 38.9%;
                 @media (min-width: 768px) {
                     top: 39.1%; 
                }
            }
            .container-data #mitra {
                top: 41.4%;
                 @media (min-width: 768px) {
                     top: 41.6%; 
                }
            }
            .container-data #status {
                top: 43.9%;
                 @media (min-width: 768px) {
                     top: 44.1%; 
                }
            }
            /*penghasilan*/
            .container-data #gaji_pokok, #bpjs_kesehatan {
                top: 54.6%;
                 @media (min-width: 768px) {
                     top: 54.8%; 
                }
            }
            .container-data #tj_jabatan, #bpjs_ketenaga {
                top: 57.1%;
                 @media (min-width: 768px) {
                     top: 57.3%; 
                }
            }
            .container-data #tj_kehadiran, #qurban {
                top: 59.6%;
                 @media (min-width: 768px) {
                     top: 59.8%; 
                }
            }
            .container-data #tj_kinerja, #lain_lain {
                top: 62.1%;
                 @media (min-width: 768px) {
                     top: 62.3%; 
                }
            }
            .container-data #total_penghasilan, #total_potongan {
                top: 67.4%;
                 @media (min-width: 768px) {
                     top: 67.6%; 
                }
            }
            .container-data #total_bersih {
                top: 73.5%;
                 @media (min-width: 768px) {
                     top: 73.8%; 
                }
            }
        </style>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold pb-5 uppercase">Data Slip Gaji</p>
            </div>
			
            <div class="flex justify-center items-center gap-2 mt-5 rounded-md">
                <div class="flex flex-col gap-2 mt-5 bg-slate-200 p-4 drop-shadow-md rounded-md w-fit">
                    <p class="text-center font-semibold text-sm"> ~>Filter Bulan<~ </p>
                    <div class="flex gap-2 justify-center sm:justify-start overflow-hidden">
                        <form action="" method="get" class="overflow-hidden">
                            <input type="month" name="bulan" value="" id="month" class="input input-sm input-bordered">
                            <button type="submit" class="overflow-hidden btn btn-info btn-sm">Cari</button>
                        </form>
                    </div>
                </div>
            </div>
            @if($slip)
                <div class="flex flex-col items-center mx-2 my-5 sm:justify-center justify-start">
                    <div class="overflow-x-auto md:overflow-hidden mx-2 sm:mx-0 bg-slate-50 p-2 rounded-md" id="divImg">
                        <div class="rounded-md relative" style="user-select: none;">
                            <img src="{{ asset('logo/desain_slip.jpg') }}" width="450" class="rounded-md" />
                            
                            <div style="z-index: 10;" class="absolute inset-0 container-data">
                                @php
                                    $totalPenghasilan = $slip?->gaji_pokok + $slip?->tj_jabatan + $slip?->tj_kehadiran + $slip?->tj_kinerja;
                                    $totalPotongan = $slip?->bpjs + $slip?->pinjaman + $slip?->absen + $slip?->lain_lain + 10000;
                                @endphp
                                <!--bulan-->
                                <p id="bulan" class="absolute font-semibold" style=" left: 51.5%; ">Mei 2024</p>
                                <!--data diri-->
                                <p id="nama" class="absolute font-semibold" style=" left: 20%; ">{{ Auth::user()->nama_lengkap }}</p>
                                <p id="jabatan" class="absolute font-semibold" style=" left: 20%; ">{{ Auth::user()->divisi->jabatan->name_jabatan }}</p>
                                <p id="mitra" class="absolute font-semibold" style=" left: 20%; ">{{ Auth::user()->kerjasama->client->name }}</p>
                                <p id="status" class="absolute font-semibold" style=" left: 20%; ">Kontrak</p>
                                <!--penghasilan-->
                                <p id="gaji_pokok" class="absolute font-semibold" style=" left: 25%; ">{{ toRupiah($slip?->gaji_pokok) }}</p>
                                <p id="tj_jabatan" class="absolute font-semibold" style=" left: 25%; ">{{ toRupiah($slip?->tj_jabatan) }}</p>
                                <p id="tj_kehadiran" class="absolute font-semibold" style=" left: 25%; ">{{ toRupiah($slip?->tj_kehadiran) }}</p>
                                <p id="tj_kinerja" class="absolute font-semibold" style=" left: 25%; ">{{ toRupiah($slip?->tj_kinerja) }}</p>
                                <p id="total_penghasilan" class="absolute" style=" left: 25%; font-weight: 700; ">{{ toRupiah($totalPenghasilan) }}</p>
                                <!--potongan-->
                                <p id="bpjs_kesehatan" class="absolute font-semibold" style=" left: 77%; ">{{ toRupiah($slip?->bpjs / 2) }}</p>
                                <p id="bpjs_ketenaga" class="absolute font-semibold" style=" left: 77%; ">{{ toRupiah($slip?->bpjs / 2) }}</p>
                                <p id="qurban" class="absolute font-semibold" style=" left: 77%; ">RP. 10.000</p>
                                <p id="lain_lain" class="absolute font-semibold" style=" left: 77%; ">{{ toRupiah($slip?->lain_lain) }}</p>
                                <p id="total_potongan" class="absolute" style=" left: 77%; font-weight: 700; ">{{ toRupiah($totalPotongan) }}</p>
                                <!--total bersih-->
                                <p id="total_bersih" class="absolute" style=" left: 39%; font-weight: 800; ">{{ toRupiah($totalPenghasilan - $totalPotongan) }}</p>
                            </div>
                        </div>
                        <div class="flex justify-center items-center" style="padding: 20px 0 10px 0;">
                            <form action="{{ route('slip-gaji.export') }}" method="get">
                                <input type="hidden" name="slip_id" value="{{ $slip->id }}" />
                                <button type="submit" class="btn btn-sm btn-warning">Download</a>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-slate-50 rounded-md mx-5 my-5 flex items-center justify-center" style="min-height: 100px;">
                    <p class="text-center font-semibold italic">~Data Masih Kosong~</p>
                </div>
            @endif
            
            <div class="flex justify-center gap-2 mx-10 sm:justify-end">
                <a href="{{ route('dashboard.index') }}" class="btn btn-error mx-2 sm:mx-10">Kembali</a>
            </div>
            
        </div>
    <script>
    </script>
    </x-main-div>
</x-app-layout>