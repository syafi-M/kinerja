<x-app-layout>
    <x-main-div>
        <div class="py-10 sm:mx-10">
            <p class="text-center text-lg sm:text-2xl uppercase font-bold ">List Gaji Karyawan, <br>{{ auth()->user()->id == 175 ? "Semua Mitra" : Auth::user()->kerjasama->client->name }}</p>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                
                <div class="flex items-center justify-center sm:justify-between gap-2 w-full mt-5">
                    <div class="w-full">
                        @if(Auth::user()->divisi->code_jabatan == "CO-CS")
                		    <a href="{{ route('leaderView') }}" class="btn btn-error">Kembali</a>
                	    @elseif(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR")
                		    <a href="{{ route('danruView') }}" class="btn btn-error">Kembali</a>
                	    @else
                		    <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
                	    @endif
                    </div>
                    <div class="mt-5 flex justify-end" style="min-width: 50%;">
                        <form action="{{ Auth::user()->kerjasama_id == 1 ? route('slip-karyawan') : route('leader-slip') }}" method="GET" class="flex flex-col gap-2 bg-slate-100 rounded p-2 mb-5" style="">
                            @if(Auth::user()->kerjasama_id == 1)
                            <div style="width: 100%">
                                <label class="label text-xs">Pilih Mitra</label>
                                <select name="penempatan" class="select select-bordered text-black select-sm text-xs w-full">
                                    <option class="disabled" disabled selected>~Pilih Mitra~</option>
                                    @forelse($mitra as $i)
                                        @if(Auth::user()->devisi_id === 26 && $i->id !== 1)
                                        <option value="{{ $i->id }}" {{ $penempatan == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
                                        @elseif(Auth::user()->devisi_id !== 26)
                                        <option value="{{ $i->id }}" {{ $penempatan == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
                                        @endif
                                    @empty
                                        <option class="disabled">~Mitra Kosong~</option>
                                    @endforelse
                                </select>
                            </div>
                            @endif
                            <div class="join" style="Width: 100%;">
                                <input type="month" name="bulan" value="{{ $bulan ? $bulan : Carbon\Carbon::now()->subMonth()->format('Y-m') }}" max="{{ Carbon\Carbon::now()->addMonth()->format('Y-m') }}" class="input input-sm input-bordered join-item" style="width: 75%;" />
                                <button type="submit" class="btn btn-sm btn-info join-item" style="width: 25%;" ><i class="ri-search-2-line"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="overflow-x-scroll w-full md:overflow-hidden mx-2 sm:mx-10  ">
                    <table id="searchTable" class="table table-xs table-zebra sm:table-md text-xs bg-slate-50 font-semibold sm:text-md ">
                        <thead>
							<tr class="text-center">
                                <th class="p-1 py-2 bg-slate-300 rounded-tl-2xl">#</th>
                                <th class="p-1 py-2 bg-slate-300 text-start">Nama Lengkap</th>
                                <th class="p-1 py-2 bg-slate-300 text-start">Penempatan</th>
                                <th class="p-1 py-2 bg-slate-300">Bulan Tahun</th>
                                <th class="p-1 py-2 bg-slate-300">Penghasilan</th>
                                <th class="p-1 py-2 bg-slate-300">Potongan</th>
                                <th class="p-1 py-2 bg-slate-300 rounded-tr-2xl">Total Penerimaan</th>
							</tr>
						</thead>
                        <tbody>
                            @php
                                $n = 1;
                            @endphp
                            @forelse ($slip as $i)
                                @php
                                    $totalPenghasilan = $i?->gaji_pokok + $i?->gaji_lembur + $i?->tj_jabatan + $i?->tj_kehadiran + $i?->tj_kinerja + $i?->tj_lain;
                                    $totalPotongan = $i?->bpjs + $i?->pinjaman + $i?->absen + $i?->lain_lain;
                                    $totalBersih = 0;
                                    if($totalPotongan > 0){
                                        $totalBersih = $totalPenghasilan - $totalPotongan;
                                    }else{
                                        $totalBersih = $totalPenghasilan + $totalPotongan;
                                    }
                                    
                                    $mitraName = '';
                                    $name = $i?->user->kerjasama->client->name ?? '';
                                    $words = preg_split('/\s+/', $name);
                                    $prefixes = ['PT.', 'CV', 'MTS'];
                                    $locPrefix = ['Ponorogo', 'Madiun', 'Lamongan', 'Surabaya', 'babat', 'gresik', '(rusunawa)', 'ngabar', 'ngabar)'];
                                    
                                    $firstWord = $words[0] ?? '';
                                    $endWord = end($words) ?? '';
                                    
                                    $hasPrefix = in_array(strtoupper(rtrim($firstWord, '.')), array_map(fn($p) => rtrim($p, '.'), $prefixes));
                                    $hasEndPrefix = in_array(strtolower(rtrim($endWord, '.')), array_map(fn($p) => strtolower(rtrim($p, '.')), $locPrefix));
                                    
                                    $initials = collect($words)
                                        ->skip($hasPrefix ? 1 : 0)
                                        ->map(fn($word) => strtoupper(mb_substr(preg_replace('/[^A-Za-z]/u', '', $word), 0, 1)))
                                        ->implode('');
                                    $closing = collect($words)
                                        ->take($hasEndPrefix ? count($words) - 1 : 0)
                                        ->map(fn($word) => strtoupper(mb_substr(preg_replace('/[^A-Za-z]/u', '', $word), 0, 1)))
                                        ->implode('');
                                    $both = collect($words)
                                        ->skip(1)
                                        ->take(1)
                                        ->map(fn($word) => strtoupper(mb_substr(preg_replace('/[^A-Za-z]/u', '', $word), 0, 1)))
                                        ->implode('');
                                        
                                    if ($hasPrefix && $hasEndPrefix && count($words) > 2) {
                                        $mitraName = "$firstWord $both $endWord";
                                    } else if ($hasPrefix && !$hasEndPrefix && count($words) > 2) {
                                        $mitraName = "$firstWord $initials";
                                    } else if (!$hasPrefix && $hasEndPrefix && count($words) > 2) {
                                        $mitraName = "$closing $endWord";
                                    } else if (count($words) > 2) {
                                        $mitraName = $initials;
                                    } else {
                                        $mitraName = $name;
                                    }
                                @endphp
                                <tr class="text-center">
                                    <td>{{ $n++ }}.</td>
                                    <td class="text-start">{{ \Illuminate\Support\Str::title(Str::lower($i?->karyawan)) }}</td>
                                    <td class="text-start">{{ $mitraName }}</td>
                                    <td>{{ Carbon\Carbon::createFromFormat('Y-m', $i?->bulan_tahun)->isoFormat('MMMM Y') }}</td>
                                    <td>{{ toRupiah($i?->gaji_pokok + $i?->gaji_lembur + $i?->tj_jabatan + $i?->tj_kehadiran + $i?->tj_kinerja + $i?->tj_lain) }}</td>
                                    <td>{{ toRupiah($i?->bpjs + $i?->pinjaman + $i?->absen + $i?->lain_lain) }}</td>
                                    <td>{{ toRupiah($totalBersih) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        ~ Data Kosong ~
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-main-div>
</x-app-layout>