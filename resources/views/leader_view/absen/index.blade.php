<x-app-layout>
    <x-main-div>
        <div class="py-10 sm:mx-10">
            <p class="text-center text-lg sm:text-2xl uppercase font-bold ">Riwayat Absensi, {{ Auth::user()->kerjasama->client->name }}</p>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="flex justify-center sm:justify-between  items-center my-5">
                        <div class="flex flex-col sm:flex-row w-full gap-2 sm:gap-5 items-center justify-center sm:justify-between">
                            @if(Auth::user()->divisi->jabatan->code_jabatan == 'MITRA')
                            <form action="{{ route('mitra_absensi') }}" method="GET" class="sm:flex sm:justify-start">
                			    <div class="join sm:ml-10">
                			        <input type="month" placeholder="pilih bulan..." class="join-item input input-bordered" name="search" id="search" max="{{ Carbon\Carbon::now()->format('Y-m') }}"/>
                			        <button type="submit" class="btn btn-info join-item">FILTER</button>
                			    </div>
        		              </form>
        		            @elseif(Auth::user()->divisi->jabatan->code_jabatan == 'DIREKSI')
        		            <form action="{{ route('direksi_absensi') }}" method="GET" class="sm:flex sm:justify-start">
                			    <div class="join sm:ml-10">
                			        <input type="month" placeholder="pilih bulan..." class="join-item input input-bordered" name="search" id="search" max="{{ Carbon\Carbon::now()->format('Y-m') }}"/>
                			        <button type="submit" class="btn btn-info join-item">FILTER</button>
                			    </div>
        		              </form>
                            @else
                              <form action="{{ url("LEADER/leader-absensi")}}" method="GET" class="sm:flex sm:justify-start">
                			    <div class="join sm:ml-10">
                			        <input type="month" placeholder="pilih bulan..." class="join-item input input-bordered" name="search" id="search" max="{{ Carbon\Carbon::now()->format('Y-m') }}"/>
                			        <button type="submit" class="btn btn-info join-item">FILTER</button>
                			    </div>
        		              </form>
                            @endif
        		              <div class="flex items-center mt-5">
        		                <x-search />
        		              </div>
                        </div>
                </div>
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-10">
                    <table id="searchTable" class="table table-xs table-zebra sm:table-md text-xs bg-slate-50 font-semibold sm:text-md ">
                        <thead>
							<tr class="text-center">
								<th class="p-1 py-2 bg-slate-300 rounded-tl-2xl">#</th>
								<th class="p-1 py-2 bg-slate-300" style="padding-left: 2rem; padding-right: 2rem;">Foto</th>
                                <th class="p-1 py-2 bg-slate-300">Nama</th>
								<th class="p-1 py-2 bg-slate-300">Shift</th>
								<th class="p-1 py-2 bg-slate-300" style="padding-left: 1.5rem; padding-right: 1.5rem;">Tanggal</th>
								<th class="p-1 py-2 bg-slate-300 px-10">Masuk - pulang</th>
								@if(Auth::user()->devisi_id == 8)
								    <th class="p-1 py-2 bg-slate-300">Keterangan</th>
								    <th class="p-1 py-2 bg-slate-300 rounded-tr-2xl">Lokasi Absen</th>
								@else
								    <th class="p-1 py-2 bg-slate-300 rounded-tr-2xl">Keterangan</th>
								@endif
							</tr>
						</thead>
                        <tbody>
                            @php
                                $n = 1;
                            @endphp
                            @forelse ($absen as $i)
                            <tr>
                                <td class="p-1 ">{{ $n++ }}</td>
                                @if ($i->image == 'no-image.jpg')
									<td>
										<x-no-img />
									</td>
								@elseif(Storage::disk('public')->exists('images/' . $i->image))
									<td><img  class="lazy lazy-image" loading="lazy" src="{{ asset('storage/images/' . $i->image) }}" data-src="{{ asset('storage/images/' . $i->image) }}" alt="" srcset="" width="120px"></td>
								@else
								    <td>
										<x-no-img />
									</td>
								@endif
                                <td class="p-1  break-words whitespace-pre-wrap">{{ $i->user->nama_lengkap }}</td>
                                <td class="p-1 ">{{ $i->shift?->shift_name }}</td>
                                <td class="p-1 text-center"><p>{{ $i->tanggal_absen }}</p></td>
                                <td class="p-1 ">
                                    <span class="flex flex-col justify-center text-center">
                                        @php
                                            $khus = Carbon\Carbon::createFromFormat('H:i:s', $i->created_at->format('H:i:s'));
                                            $khusKurang = $khus->copy()->subMinutes(31)->subSeconds(59);
                                        @endphp
                                        
                                        @if($i->user->kerjasama_id == 1 && $i->absensi_type_masuk >= '07:30:00')
                                            <span>{{ $khusKurang->format('H:i:s') }}</span>
                                        @else
                                            <span>{{ $i->absensi_type_masuk }}</span>
                                        @endif
                                        <span> - </span>
                                        @if($i->absensi_type_pulang == null)
                                            <span class="text-red-500 font-semibold capitalize">kosong</span>
                                        @elseif($i->absensi_type_pulang == 'Tidak Absen Pulang')
                                            <span class="text-yellow-500 font-semibold capitalize">{{ $i->absensi_type_pulang }}</span>
                                        @else
                                            {{ $i->absensi_type_pulang }}
                                        @endif
                                    </span>
                                </td>
                                @if(Auth::user()->devisi_id == 8)
                                    <td>
                                        @if ($i->keterangan == 'masuk')
                                        <span class=" badge badge-success gap-2 overflow-hidden">{{ $i->keterangan }}</span> 
                                        @elseif($i->keterangan == 'telat')
                                        <span class="badge badge-error gap-2 overflow-hidden">{{ $i->keterangan }}</span>
                                        @else
                                        <span class="badge badge-warning gap-2 overflow-hidden">{{ $i->keterangan }}</span>
                                        @endif
                                    </td>
                                    <td class="overflow-hidden">
                                        <a href="{{ route('mitra-lihatMap', $i->id) }}" class="btn btn-sm btn-info text-xs overflow-hidden">Lihat Koordinat</a>
                                    </td>
                                @else
                                <td>
                                    @if ($i->keterangan == 'masuk')
                                    <span class=" badge badge-success gap-2 overflow-hidden">{{ $i->keterangan }}</span> 
                                    @elseif($i->keterangan == 'telat')
                                    <span class="badge badge-error gap-2 overflow-hidden">{{ $i->keterangan }}</span>
                                    @else
                                    <span class="badge badge-warning gap-2 overflow-hidden">{{ $i->keterangan }}</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="7">
                                        ~ Data Kosong ~
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                    <div id="pag-1" class="mt-5 mb-5 mx-10">
                        {{ $absen->links() }}
                    </div>
                    <div class="flex">
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