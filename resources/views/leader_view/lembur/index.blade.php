<x-app-layout>
    <x-main-div>
        <div class="py-10 sm:mx-10">
            <p class="text-center text-lg sm:text-2xl uppercase font-bold ">Riwayat Lembur, {{ Auth::user()->kerjasama->client->name }}</p>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="flex items-center w-full justify-center sm:justify-end my-5">
                    <x-search />
                </div>
                <div class="overflow-x-scroll w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full ">
                    <table id="searchTable" class="table w-full table-xs table-zebra sm:table-md text-xs bg-slate-50 font-semibold sm:text-md ">
                        <thead>
							<tr >
								<th class="p-1 py-2 bg-slate-300 rounded-tl-2xl">#</th>
                                <th class="p-1 py-2 bg-slate-300">Foto</th>
                                <th class="p-1 py-2 bg-slate-300">Name</th>
								<th class="p-1 py-2 bg-slate-300">Tanggal</th>
								<th class="p-1 py-2 bg-slate-300 rounded-tr-2xl">Lama Lembur</th>
							</tr>
						</thead>
                        <tbody>
                            @php
                                $n = 1;
                            @endphp
                            @forelse ($lembur as $i)
                            	<tr>
        							<td class="py-1">{{ $n++ }}</td>
        							<td><img class="lazy lazy-image" loading="lazy" src="{{asset('storage/images/'.$i->image)}}" data-src="{{asset('storage/images/'.$i->image)}}" alt="data-absensi-image" width="120px"/></td>
        							<td class="py-1">{{ $i->user->nama_lengkap }}</td>
        							<td class="py-1">{{ $i->created_at->format('Y-m-d') }}</td>
        							@if ($i->jam_selesai == null)
        								<td class="py-1">Belum Selesai Lembur</td>
        							@else
        							@php
        								$masuk = strtotime($i->jam_mulai);
        								$keluar = strtotime($i->jam_selesai);
        
        								$msk = date('H', $masuk);
        								$klr = date('H' ,$keluar);
        								
        								
        								
        								$tot =  $klr - $msk;
        
        							@endphp
                                        @if($tot <= 0)
                                        <td class="py-1 text-red-500">0 Jam</td>
                                        @else
        								<td class="py-1">{{ $tot . ' Jam'  }}</td>
        								@endif
    							@endif
						    </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        ~ Data Kosong ~
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                    <div id="pag-1" class="mt-5 mb-5 mx-10">
                        {{ $lembur->links() }}
                    </div>
                    <div class="flex justify-center sm:justify-end w-full">
                        @if(Auth::user()->divisi->code_jabatan == "CO-CS")
            			    <a href="{{ route('leaderView') }}" class="btn btn-error">Kembali</a>
            		    @elseif(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR")
            			    <a href="{{ route('danruView') }}" class="btn btn-error">Kembali</a>
            		    @else
            			    <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
            		    @endif
                    </div>
            </div>
        </div>
    </x-main-div>
</x-app-layout>