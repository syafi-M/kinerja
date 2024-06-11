<x-app-layout>
    <x-main-div>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold pb-5 uppercase">Data Rencana Kerja ( {{empty($type) ? "SEMUA" : $type }} )</p>
            </div>
            <div class="flex justify-center items-center gap-2 mt-5 rounded-md">
                <div class="flex flex-col gap-2 mt-5 bg-slate-200 p-4 drop-shadow-md rounded-md w-fit">
                    <p class="text-center font-semibold text-sm"> ~>Filter<~ </p>
                    <div class="flex gap-2 justify-center sm:justify-start overflow-hidden">
                        <form action="" method="get" class="btn btn-info btn-sm overflow-hidden">
                            <input type="hidden" name="type" value="rencana" id="">
                            <button type="submit" class="overflow-hidden">Rencana</button>
                        </form>
                        {{-- <form action="" method="get" class="btn btn-warning btn-sm overflow-hidden">
                            <input type="hidden" name="type" value="" id="">
                            <button type="submit"><i class="ri-refresh-line"></i></button>
                        </form> --}}
                        <form action="" method="get" class="btn btn-info btn-sm overflow-hidden">
                            <input type="hidden" name="type" value="dikerjakan" id="">
                            <button type="submit" class="overflow-hidden">Dikerjakan</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full">
                    <table class="table table-zebra table-auto w-full table-xs bg-slate-50  sm:table-md text-sm sm:text-md scale-90 md:scale-90">
                        <thead class="text-center">
                            <tr>
                                <th class="bg-slate-300 rounded-tl-2xl">#</th>
								<th class="bg-slate-300  px-10 {{ $type == 'rencana' ? 'hidden' : '' }}">Bukti</th>
								<th class="bg-slate-300 px-10">Pekerjaan</th>
								<th class="bg-slate-300 px-10 {{ $type == 'rencana' ? 'hidden' : 'hidden sm:table-cell' }}">Deskripsi</th>
								<th class="bg-slate-300 hidden sm:table-cell">Check Point</th>
								<th class="bg-slate-300 px-7">Tanggal</th>
								<th class="bg-slate-300 rounded-tr-2xl px-10 {{ $type == 'rencana' ? 'hidden' : '' }}">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @php
								$no = 1;
							@endphp
                            @forelse ($cek2 as $index => $c)
                                @if($type == 'dikerjakan')
                                <tr>
                                    <td class="text-center font-semibold" colspan="8">~ {{$c->created_at->isoFormat('dddd, D-MMMM-Y')}} - {{ $c->updated_at->isoFormat('dddd, D-MMMM-Y') }} ~</td>
                                </tr>
                                @endif
                                @forelse($c->pekerjaan_cp_id as $j => $cp)
                                    <tr>
                                        <td>
                                            {{ $no++ }}
                                        </td>
                                        @if ((empty($cp) || $cp == 'no-image.jpg') && $type != 'rencana')
                                            <td>
                                                <x-no-img class="scale-50"/>
                                            </td>
                                        @elseif ($cp == 'rencana')
                                            <td class="{{ $type == 'rencana' ? 'hidden' : '' }}"></td>
                                        @else
                                            <td class="flex gap-1 {{ $type == 'rencana' ? 'hidden' : 'table-cell' }}">
                                                @if(isset($c->img[$j]))
                                                <img src="{{ asset('storage/images/' . $c->img[$j]) }}" alt="" srcset=""
                                                    width="70px">
                                            @endif
                                            </td>
                                        @endif
                                        <td class="capitalize text-start">
                                            @php
                                                $ce = $pcp->where('id', $cp)->first();
                                            @endphp
                                            @if (empty($ce))
                                                <div class="flex gap-1 ">
                                                    <p>~ {{ $cp }} </p>
                                                    <p class="text-green-700 underline underline-offset-1 hidden sm:block"></p>
                                                </div>
                                            @else
                                                @forelse($pcp->whereIn('id', $cp) as $i => $pc)
                                                    @if($pc)
                                                    @php
                                                        $counts = count(array_filter($c->pekerjaan_cp_id, function ($value) use ($pc) {
                                                            return $value == $pc->id;
                                                        }));
                                                    @endphp
                                                        <div class="flex gap-1 ">
                                                            <p>~ {{ $pc->name }} </p>
                                                            <p class="text-green-700 underline underline-offset-1 hidden sm:block">@if($i < $pc->count() - 1),@endif</p>
                                                        </div>
                                                    @else
                                                        <p>kosong</p>
                                                    @endif
                                                    
                                                @empty
                                                @endforelse
                                            @endif
                                        </td>
                                        <td class="capitalize text-start  {{ $type == 'rencana' ? 'hidden' : 'hidden sm:table-cell' }}">
                                            @php
                                                $descriptions = isset($c->deskripsi) ? ($c->deskripsi[$j] ?? '') : '';
                                            @endphp
                                            <p>~ {{ $descriptions ? $descriptions : '' }}</p>
                                        </td>
    
                                        <td class="capitalize text-start hidden sm:table-cell">
                                            @forelse($pcp->whereIn('id', $c) as $i => $pc)
                                                @if($pc)
                                                    <p>~ {{ $pc->type_check }}</p>
                                                @endif
                                            @empty
                                            @endforelse
                                        </td>
                                            <td class="text-center text-sm font-semibold" >
                                                @php
                                                    $tgl = isset($c->tanggal) ? ($c->tanggal[$j] ?? '') : '';
                                                @endphp
                                                <p>{{ $tgl ? $tgl : '' }}</p>
                                            </td>
                                        <td class="{{ $type == 'rencana' ? 'hidden' : '' }}">
                                            @if (isset($c->approve_status[$j]) && is_array($c->approve_status))
                                                @if($c->approve_status[$j] == 'accept')
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="badge bg-emerald-700 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status[$j] }}</span> 
                                                        <p>Note: {{ $c->note[$j] }}</p>
                                                    </div>
                                                @elseif($c->approve_status[$j] == "proccess")
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="badge bg-amber-500 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status[$j] }}</span> 
                                                    </div>
                                                @else
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span class="badge bg-red-500 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status[$j] }}</span>
                                                        <p>Note: {{ $c->note[$j] }}</p>
                                                    </div>
                                                @endif
                                                {{-- <p>{{ $cek->approve_status[$index] }}</p> --}}
                                            @endif
                                        </td>
                                        
                                    </tr>
                                @empty
                                @endforelse
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">CP Saat Ini Kosong</td> 
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
            </div>
            
            <div class="flex justify-center gap-2 mx-10 sm:justify-end">
                <a href="{{ route('dashboard.index') }}" class="btn btn-error mx-2 sm:mx-10">Kembali</a>
            </div>
            
        </div>
    </x-main-div>

</x-app-layout>