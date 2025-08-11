<x-app-layout>
    <x-main-div>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold pb-5 uppercase">Data Rencana Kerja (
                    {{ empty($type) ? 'SEMUA' : $type }} )</p>
            </div>
            <div class="flex justify-center items-center gap-2 mt-5 rounded-md">
                <div class="flex flex-col gap-2 mt-5 bg-slate-200 p-4 drop-shadow-md rounded-md w-fit">
                    <p class="text-center font-semibold text-sm"> ~>Filter<~ </p>
                            <div class="flex gap-2 justify-center sm:justify-start overflow-hidden">
                                <form action="" method="get" class="btn btn-info btn-sm overflow-hidden">
                                    <input type="hidden" name="type" value="rencana" id="">
                                    <button type="submit" class="overflow-hidden">Rencana</button>
                                </form>

                                <form action="" method="get" class="btn btn-info btn-sm overflow-hidden">
                                    <input type="hidden" name="type" value="dikerjakan" id="">
                                    <button type="submit" class="overflow-hidden">Dikerjakan</button>
                                </form>
                            </div>
                </div>
            </div>
            <div class="flex flex-col items-center m-5 md:m-10 sm:justify-center justify-start ">
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full">
                    <table
                        class="table table-zebra table-auto w-full table-xs bg-slate-50  sm:table-md text-sm sm:text-md">
                        <thead class="text-center">
                            <tr>
                                <th class="bg-slate-300 rounded-tl-2xl">#</th>
                                <th class="bg-slate-300  px-10 {{ $type == 'rencana' ? 'hidden' : '' }}">Bukti</th>
                                <th class="bg-slate-300 px-10">Pekerjaan</th>
                                <th
                                    class="bg-slate-300 px-5 {{ $type == 'rencana' ? 'hidden' : 'hidden sm:table-cell' }}">
                                    Deskripsi</th>
                                <th class="bg-slate-300 hidden sm:table-cell">Check Point</th>
                                <th class="bg-slate-300 px-10 min-w-[100pt]">Tanggal</th>
                                <th class="bg-slate-300 rounded-tr-2xl px-10 {{ $type == 'rencana' ? 'hidden' : '' }}">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @php $no = 1; @endphp

                            @forelse ($cek2 as $c)
                                @if ($type == 'dikerjakan')
                                    <tr>
                                        <td class="text-center font-semibold" colspan="8">
                                            ~ {{ $c->created_at->isoFormat('dddd, D-MMMM-Y') }} -
                                            {{ $c->updated_at->isoFormat('dddd, D-MMMM-Y') }} ~
                                        </td>
                                    </tr>
                                @endif

                                @foreach ($c->pekerjaan_cp_id as $j => $cp)
                                    <tr>
                                        <td>{{ $no++ }}</td>

                                        {{-- Bukti / Image --}}
                                        @if ((empty($cp) || $cp == 'no-image.jpg') && $type != 'rencana')
                                            <td><x-no-img class="scale-50" /></td>
                                        @elseif ($cp == 'rencana')
                                            <td class="{{ $type == 'rencana' ? 'hidden' : '' }}"></td>
                                        @else
                                            <td class="flex gap-1 {{ $type == 'rencana' ? 'hidden' : 'table-cell' }}">
                                                @if (isset($c->img[$j]))
                                                    <img src="{{ asset('storage/images/' . $c->img[$j]) }}"
                                                        alt="" width="70px">
                                                @endif
                                            </td>
                                        @endif

                                        {{-- Pekerjaan --}}
                                        <td class="capitalize text-start">
                                            @php
                                                $pc = $c->related_pcp->firstWhere('id', $cp);
                                            @endphp
                                            @if ($pc)
                                                <div class="flex gap-1">
                                                    <p>~ {{ $pc->name }}</p>
                                                </div>
                                            @else
                                                <div class="flex gap-1">
                                                    <p>~ {{ $cp }}</p>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Deskripsi --}}
                                        <td
                                            class="capitalize text-start {{ $type == 'rencana' ? 'hidden' : 'hidden sm:table-cell' }}">
                                            <p>~ {{ $c->deskripsi[$j] ?? '' }}</p>
                                        </td>

                                        {{-- Check Point Type --}}
                                        <td class="capitalize text-start hidden sm:table-cell">
                                            @if ($pc)
                                                <p>~ {{ $pc->type_check }}</p>
                                            @endif
                                        </td>

                                        {{-- Tanggal --}}
                                        <td class="text-center text-sm font-semibold">
                                            <p>{{ $c->created_at->format('Y-m-d') }}</p>
                                        </td>

                                        {{-- Status --}}
                                        <td class="{{ $type == 'rencana' ? 'hidden' : '' }}">
                                            @if (isset($c->approve_status[$j]))
                                                @php $status = $c->approve_status[$j]; @endphp

                                                @if ($status == 'accept')
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span
                                                            class="badge bg-emerald-700 px-2 text-xs text-white">{{ $status }}</span>
                                                        <p>Note: {{ $c->note[$j] ?? '' }}</p>
                                                    </div>
                                                @elseif ($status == 'proccess')
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span
                                                            class="badge bg-amber-500 px-2 text-xs text-white">{{ $status }}</span>
                                                    </div>
                                                @else
                                                    <div class="flex flex-col justify-center items-center">
                                                        <span
                                                            class="badge bg-red-500 px-2 text-xs text-white">{{ $status }}</span>
                                                        <p>Note: {{ $c->note[$j] ?? '' }}</p>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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
