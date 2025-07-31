<x-app-layout>
    <x-main-div>
        <div class="py-10 sm:mx-10">
            <p class="text-center text-lg sm:text-2xl uppercase font-bold ">Riwayat Absensi, <br>{{ $filterMitra ? $absen[0]->kerjasama->client->name : 'PT. Surya Amanah Cendikia' }}</p>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="flex justify-center sm:justify-between items-center my-5">
                    <div class="flex flex-col sm:flex-row w-full gap-2 sm:gap-5 items-center justify-center sm:justify-between">
                        <div class="bg-slate-100 rounded-md p-5 mx-5 w-full">
                            @php
                                $now = Carbon\Carbon::now()->format('Y-m');
                                $defaultMonth = $filter ?? $now;
                                $isMitra = Auth::user()->divisi->jabatan->code_jabatan === 'MITRA';
                                $isCoScr = Auth::user()->divisi->jabatan->code_jabatan === 'CO-SCR';
                                $isDireksi = Auth::user()->devisi_id == 18;
                            @endphp
                
                            @if($isMitra)
                                <form action="{{ route('mitra_absensi') }}" method="GET" class="sm:flex sm:justify-start">
                                    <div class="join sm:ml-10">
                                        <input type="month" name="search" id="search" value="{{ $defaultMonth }}" max="{{ $now }}" placeholder="pilih bulan..." class="join-item input input-bordered" />
                                        <button type="submit" class="btn btn-info join-item">FILTER</button>
                                    </div>
                                </form>
                
                            @elseif($isCoScr)
                                <form action="{{ route('danru_absensi') }}" method="GET" class="sm:flex sm:justify-start">
                                    <div class="join sm:ml-10 w-full">
                                        <div style="width: 75%;" class="flex flex-col join-item w-3/4">
                                            <input type="month" name="search" id="search" value="{{ $defaultMonth }}" max="{{ $now }}" placeholder="pilih bulan..." class="input input-sm input-bordered w-full rounded-none text-sm" />
                                            @if(Auth::user()->id == 175)
                                                <select name="mitra" class="select select-bordered select-sm text-xs w-full rounded-none text-black">
                                                    <option disabled selected>~Pilih Mitra~</option>
                                                    @forelse($mitra as $i)
                                                        <option value="{{ $i->id }}" {{ $filterMitra == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
                                                    @empty
                                                        <option disabled>~Mitra Kosong~</option>
                                                    @endforelse
                                                </select>
                                            @endif
                                        </div>
                                        <div style="width: 25%;" class="join-item w-1/4">
                                            <button type="submit" class="btn btn-info w-full h-full rounded-none">FILTER</button>
                                        </div>
                                    </div>
                                </form>
                
                            @elseif($isDireksi)
                                <form action="{{ route('direksi_absensi') }}" method="GET" class="sm:flex sm:justify-start gap-2 w-full">
                                    <div class="flex flex-col items-center justify-center gap-2 w-full">
                                        <select name="mitra" class="select select-bordered text-black text-xs w-full">
                                            <option disabled selected>~Pilih Mitra~</option>
                                            @forelse($mitra as $i)
                                                <option value="{{ $i->id }}" {{ $filterMitra == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
                                            @empty
                                                <option disabled>~Mitra Kosong~</option>
                                            @endforelse
                                        </select>
                                        <input type="month" name="search" id="search" value="{{ $defaultMonth }}" max="{{ $now }}" placeholder="pilih bulan..." class="input input-bordered w-full text-sm" />
                                    </div>
                                    <div class="flex justify-end mt-2">
                                        <button type="submit" class="btn btn-info w-full h-full">FILTER</button>
                                    </div>
                                </form>
                
                            @else
                                <form action="{{ url('LEADER/leader-absensi') }}" method="GET" class="sm:flex sm:justify-start">
                                    <div class="join sm:ml-10">
                                        <input type="month" name="search" id="search" value="{{ $defaultMonth }}" max="{{ $now }}" placeholder="pilih bulan..." class="join-item input input-bordered text-sm" />
                                        <button type="submit" class="btn btn-info join-item">FILTER</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                
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
								<th class="p-1 py-2 bg-slate-300" style="padding-left: 3rem; padding-right: 3rem;">Shift</th>
								@if(auth()->user()->id == 175)
								<th class="p-1 py-2 bg-slate-300">Penempatan</th>
								@endif
								<th class="p-1 py-2 bg-slate-300" style="padding-left: 1.5rem; padding-right: 1.5rem;">Tanggal</th>
								<th class="p-1 py-2 bg-slate-300 px-5">Masuk - pulang</th>
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
                                <td class="p-1 ">{{ $n++ }}.</td>
                                @if ($i->image == 'no-image.jpg')
									<td>
										<x-no-img />
									</td>
								@elseif(Storage::disk('public')->exists('images/' . $i->image))
									<td><img  class="lazy lazy-image rounded-md" loading="lazy" src="{{ asset('storage/images/' . $i->image) }}" data-src="{{ asset('storage/images/' . $i->image) }}" alt="" srcset="" width="120px"></td>
								@else
								    <td>
										<x-no-img />
									</td>
								@endif
                                <td class="p-1  break-words whitespace-pre-wrap">{{ ucwords(strtolower($i->user->nama_lengkap)) }}</td>
                                <td class="p-1 text-center">{{ $i->shift?->shift_name }}, <br> {{ $i->shift?->jam_start }} - {{ Carbon\Carbon::parse($i->shift?->jam_end)->subHour(1)->format('H:i') }}</td>
                                @if(auth()->user()->id == 175)
								<td class="p-1">
								    @php
                                        $words = explode(' ', $i->kerjasama?->client?->name);
                                        $initials = '';
                                        foreach ($words as $word) {
                                            $initials .= substr($word, 0, 1);
                                        }
                                    @endphp
                                    {{ $initials }}
								</td>
								@endif
                                <td class="p-1 text-center"><p>{{ $i->tanggal_absen }}</p></td>
                                <td class="p-1 ">
                                    <span class="flex flex-col justify-center text-center">
                                        <span>{{ $i->absensi_type_masuk }}</span>
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
                                @php
                                    $jamAbs = $i->absensi_type_masuk ?? '00:00:00';
                                    $jamStr = $i->shift?->jam_start ?? '00:00:00';
                                
                                    // Determine time format
                                    $formatAbs = strlen($jamAbs) === 5 ? 'H:i' : 'H:i:s';
                                    $formatStr = strlen($jamStr) === 5 ? 'H:i' : 'H:i:s';
                                
                                    // Parse times using Carbon
                                    $jAbs = Carbon\Carbon::createFromFormat($formatAbs, $jamAbs);
                                    $jJad = Carbon\Carbon::createFromFormat($formatStr, $jamStr);
                                
                                    // Calculate the difference
                                    $jDiff = $jAbs->diff($jJad);
                                
                                    // Build readable difference
                                    $diffHasil = '';
                                    if ($jDiff->h > 0) {
                                        $diffHasil .= $jDiff->format('%h Jam ');
                                    }
                                    if ($jDiff->i > 0) {
                                        $diffHasil .= $jDiff->format('%i Menit ');
                                    }
                                    if ($jDiff->s > 0) {
                                        $diffHasil .= $jDiff->format('%s Detik');
                                    }
                                
                                    $diffHasil = trim($diffHasil);
                                @endphp

                                @if(Auth::user()->devisi_id == 8)
                                    <td>
                                        <div class="flex justify-center items-center">
                                            @if ($i->keterangan == 'masuk')
                                            <span class=" badge badge-success gap-2 overflow-hidden">{{ $i->keterangan }}</span> 
                                            @elseif($i->keterangan == 'telat')
                                            <span class="badge badge-error rounded-md gap-2 overflow-hidden text-center text-xs" style="height: 50pt; width: 60pt;">Terlambat,<br>{{ $diffHasil }}</span>
                                            @else
                                            <span class="badge badge-warning gap-2 overflow-hidden">{{ $i->keterangan }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="overflow-hidden">
                                        <a href="{{ route('mitra-lihatMap', $i->id) }}" class="btn btn-sm btn-info text-xs overflow-hidden">Lihat Koordinat</a>
                                    </td>
                                @else
                                <td>
                                    <div class="flex justify-center items-center">
                                        @if ($i->keterangan == 'masuk')
                                        <span class=" badge badge-success gap-2 overflow-hidden">{{ $i->keterangan }}</span> 
                                        @elseif($i->keterangan == 'telat')
                                        <span class="badge badge-error rounded-md gap-2 overflow-hidden text-center text-xs" style="height: 50pt; width: 60pt;">{{ $i->keterangan }},<br>{{ $diffHasil }}</span>
                                        @else
                                        <span class="badge badge-warning gap-2 overflow-hidden">{{ $i->keterangan }}</span>
                                        @endif
                                    </div>
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