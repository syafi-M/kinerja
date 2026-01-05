<x-app-layout>
    <x-main-div>
        <div class="py-10 sm:mx-10">
            <p class="text-lg font-bold text-center uppercase sm:text-2xl ">Riwayat Absensi, <br>PT. Surya Amanah Cendikia</p>
            <div class="flex flex-col items-center justify-start mx-2 my-2 sm:justify-center">
                <div class="flex items-center justify-center my-5 sm:justify-between">
                    <div class="flex flex-col items-center justify-center w-full gap-2 sm:flex-row sm:gap-5 sm:justify-between">
                        <div class="w-full p-4 mx-5 rounded-md bg-slate-100">
                            @php
                                $isMitra = Auth::user()->divisi->jabatan->code_jabatan === 'MITRA';
                                $isCoScr = Auth::user()->divisi->jabatan->code_jabatan === 'CO-SCR';
                                $isDireksi = Auth::user()->devisi_id == 18;
                                if ($isDireksi) {
                                    $now = Carbon\Carbon::now()->format('Y-m-d');
                                } else {
                                    $now = Carbon\Carbon::now()->format('Y-m');
                                }
                                $defaultMonth = $filter ?? $now;
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
                                    <div class="w-full join sm:ml-10">
                                        <div style="width: 75%;" class="flex flex-col w-3/4 join-item">
                                            <input type="month" name="search" id="search" value="{{ $defaultMonth }}" max="{{ $now }}" placeholder="pilih bulan..." class="w-full text-sm rounded-none input input-sm input-bordered" />
                                            @if(Auth::user()->jabatan->code_jabatan == 'SPV-W')
                                                <select name="mitra" class="w-full text-xs text-black rounded-none select select-bordered select-sm">
                                                    <option disabled selected>~Pilih Mitra~</option>
                                                    @forelse($mitra as $i)
                                                        <option value="{{ $i->id }}" {{ $filterMitra == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
                                                    @empty
                                                        <option disabled>~Mitra Kosong~</option>
                                                    @endforelse
                                                </select>
                                            @endif
                                        </div>
                                        <div style="width: 25%;" class="w-1/4 join-item">
                                            <button type="submit" class="w-full h-full rounded-none btn btn-info">FILTER</button>
                                        </div>
                                    </div>
                                </form>

                            @elseif($isDireksi)
                                <form action="{{ route('direksi_absensi') }}" method="GET" class="w-full gap-2 sm:flex sm:justify-start">
                                    <div class="flex flex-col items-center justify-center w-full gap-2">
                                        <select name="mitra" class="w-full text-xs text-black select select-bordered">
                                            <option disabled selected>~Pilih Mitra~</option>
                                            @forelse($mitra as $i)
                                                <option value="{{ $i->id }}" {{ $filterMitra == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
                                            @empty
                                                <option disabled>~Mitra Kosong~</option>
                                            @endforelse
                                        </select>
                                        <input type="date" name="search" id="search" value="{{ $defaultMonth }}" max="{{ $now }}" placeholder="pilih bulan..." class="w-full text-sm input input-bordered" />
                                    </div>
                                    <div class="flex justify-end mt-2">
                                        <button type="submit" class="w-full h-full btn btn-info">FILTER</button>
                                    </div>
                                </form>

                            @else
                                <form action="{{ url('LEADER/leader-absensi') }}" method="GET" class="sm:flex sm:justify-start">
                                    <div class="join sm:ml-10">
                                        <input type="month" name="search" id="search" value="{{ $defaultMonth }}" max="{{ $now }}" placeholder="pilih bulan..." class="text-sm join-item input input-bordered" />
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

                <div class="w-full mx-2 overflow-x-auto md:overflow-hidden sm:mx-10">
                    <table id="searchTable" class="table text-xs font-semibold table-xs table-zebra sm:table-md bg-slate-50 sm:text-md ">
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
								<th class="p-1 px-5 py-2 bg-slate-300">Masuk - pulang</th>
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
									<td><img  class="rounded-md lazy lazy-image" loading="lazy" src="{{ asset('storage/images/' . $i->image) }}" data-src="{{ asset('storage/images/' . $i->image) }}" alt="" srcset="" width="120px"></td>
								@else
								    <td>
										<x-no-img />
									</td>
								@endif
                                <td class="p-1 break-words whitespace-pre-wrap">{{ ucwords(strtolower($i->user->nama_lengkap)) }}</td>
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
                                            <span class="font-semibold text-red-500 capitalize">kosong</span>
                                        @elseif($i->absensi_type_pulang == 'Tidak Absen Pulang')
                                            <span class="font-semibold text-yellow-500 capitalize">{{ $i->absensi_type_pulang }}</span>
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
                                        <div class="flex items-center justify-center">
                                            @if ($i->keterangan == 'masuk')
                                            <span class="gap-2 overflow-hidden badge badge-success">{{ $i->keterangan }}</span>
                                            @elseif($i->keterangan == 'telat')
                                            <span class="gap-2 overflow-hidden text-xs text-center rounded-md badge badge-error" style="height: 50pt; width: 60pt;">Terlambat,<br>{{ $diffHasil }}</span>
                                            @else
                                            <span class="gap-2 overflow-hidden badge badge-warning">{{ $i->keterangan }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="overflow-hidden">
                                        <a href="{{ route('mitra-lihatMap', $i->id) }}" class="overflow-hidden text-xs btn btn-sm btn-info">Lihat Koordinat</a>
                                    </td>
                                @else
                                <td>
                                    <div class="flex items-center justify-center">
                                        @if ($i->keterangan == 'masuk')
                                        <span class="gap-2 overflow-hidden badge badge-success">{{ $i->keterangan }}</span>
                                        @elseif($i->keterangan == 'telat')
                                        <span class="gap-2 overflow-hidden text-xs text-center rounded-md badge badge-error" style="height: 50pt; width: 60pt;">{{ $i->keterangan }},<br>{{ $diffHasil }}</span>
                                        @else
                                        <span class="gap-2 overflow-hidden badge badge-warning">{{ $i->keterangan }}</span>
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
                    <div id="pag-1" class="mx-10 mt-5 mb-5">
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
