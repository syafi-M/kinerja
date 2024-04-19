<x-app-layout>
    <x-main-div>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold pb-5 uppercase">Data Rencana Kerja {{'('. $type.')' }}</p>
            </div>
            {{-- <form action="#" method="GET" class="flex justify-center mx-2 sm:mx-10 mb-5">
				<span class="p-4 rounded-md bg-slate-300">
					<label class="sm:mx-10 mx-5 label label-text font-semibold text-xs sm:text-base">Pilih type</label>
					<div class="join  sm:mx-10 scale-[80%] sm:scale-100">
						<input type="month" placeholder="pilih bulan..." class="join-item input input-bordered" name="search"
							id="search" />
						<button type="submit" class="btn btn-info join-item">search</button>
					</div>
				</span>
			</form> --}}
			{{-- <div class="flex justify-center gap-2 mx-10 sm:justify-end">
                <a href="{{ route('dashboard.index') }}" class="btn btn-error mx-2 sm:mx-10">Kembali</a>
                <a href="{{ route('checkpoint-user.create') }}" class="btn btn-primary mx-2 sm:mx-10">+ CP</a>
            </div> --}}
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
								{{-- <th class="bg-slate-300 px-7">Tanggal</th> --}}
								<th class="bg-slate-300 rounded-tr-2xl px-10 {{ $type == 'rencana' ? 'hidden' : '' }}">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @php
								$no = 1;
							@endphp
                            @forelse ($cek->pekerjaan_cp_id as $index => $c)
                                <tr>
                                    <td>
                                        {{ $no++ }}
                                    </td>
                                    @if ((empty($c) || $c == 'no-image.jpg') && $c != 'rencana')
                                        <td>
                                            <x-no-img class="scale-50"/>
                                        </td>
                                    @elseif ($c == 'rencana')
                                        <td class="{{ $type == 'rencana' ? 'hidden' : '' }}"></td>
                                    @else
                                        <td class="flex gap-1 {{ $type == 'rencana' ? 'hidden' : 'table-cell' }}">
                                            @if(isset($cek->img[$index]))
                                            <img src="{{ asset('storage/images/' . $cek->img[$index]) }}" alt="" srcset=""
                                                width="70px">
                                        @endif
                                        </td>
                                    @endif
                                    <td class="capitalize text-start">
                                        @php
                                            $ce = $pcp->where('id', $c)->first();
                                        @endphp
                                        @if (empty($ce))
                                            <div class="flex gap-1 ">
                                                <p>~ {{ $c }} </p>
                                                <p class="text-green-700 underline underline-offset-1 hidden sm:block"></p>
                                            </div>
                                        @else
                                            @forelse($pcp->whereIn('id', $c) as $i => $pc)
                                                @if($pc)
                                                @php
                                                    $counts = count(array_filter($cek->pekerjaan_cp_id, function ($value) use ($pc) {
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
                                            $descriptions = $cek->deskripsi ? $cek->deskripsi[$index] : '';
                                        @endphp
                                        <p>~ {{ $descriptions }}</p>
                                    </td>

                                    <td class="capitalize text-start hidden sm:table-cell">
                                        @forelse($pcp->whereIn('id', $c) as $i => $pc)
                                            @if($pc)
                                                <p>~ {{ $pc->type_check }}</p>
                                            @endif
                                        @empty
                                        @endforelse
                                    </td>
                                    {{-- @if ($loop->first)
                                        <td class="text-center text-sm font-semibold rotate-45 italic text-slate-500" rowspan="{{ count($cek->img) }}">
                                            <p>
                                                {{ $cek->created_at->format('Y-m-d') }} - {{ $cek->updated_at->format('Y-m-d') }}
                                            </p>
                                        </td>
                                    @endif --}}
                                    <td class="{{ $type == 'rencana' ? 'hidden' : '' }}">
                                        @if (isset($cek->approve_status[$index]) && is_array($cek->approve_status))
                                            @if($cek->approve_status[$index] == 'accept')
                                                <div class="flex flex-col justify-center items-center">
                                                    <span class="badge bg-emerald-700 px-2 text-xs text-white overflow-hidden">{{ $cek->approve_status[$index] }}</span> 
                                                    <p>Note: {{ $cek->note[$index] }}</p>
                                                </div>
                                            @elseif($cek->approve_status[$index] == "proccess")
                                                <div class="flex flex-col justify-center items-center">
                                                    <span class="badge bg-amber-500 px-2 text-xs text-white overflow-hidden">{{ $cek->approve_status[$index] }}</span> 
                                                </div>
                                            @else
                                                <div class="flex flex-col justify-center items-center">
                                                    <span class="badge bg-red-500 px-2 text-xs text-white overflow-hidden">{{ $cek->approve_status[$index] }}</span>
                                                    <p>Note: {{ $cek->note[$index] }}</p>
                                                </div>
                                            @endif
                                            {{-- <p>{{ $cek->approve_status[$index] }}</p> --}}
                                        @endif
                                    </td>
                                    
                                </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">CP Saat Ini Kosong</td> 
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="pag-1" class="mt-5 mb-5 mx-10">
                {{-- {{ $cek->links() }} --}}
            </div>
            
            <div class="flex justify-center gap-2 mx-10 sm:justify-end">
                <a href="{{ route('dashboard.index') }}" class="btn btn-error mx-2 sm:mx-10">Kembali</a>
            </div>
            
        </div>
    <script>
        console.log({!! json_encode($cek) !!});
    </script>
    </x-main-div>

</x-app-layout>