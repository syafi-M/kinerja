<x-app-layout>
    <x-main-div>
        <div class="px-5 py-10">
            <p class="text-2xl font-bold text-center uppercase">Data Shift</p>
            <div class="flex justify-end mr-10">
                <x-search />
            </div>
            <div class="flex justify-end gap-2 py-3 mx-16">
                <a href="{{ route('admin.index') }}" class="btn btn-error">Kembali</a>
                <a href="{{ route('shift.create') }}" class="btn btn-primary">+ Shift</a>
            </div>
            <div class="flex justify-center pb-10 mx-10 overflow-x-auto">
                <table class="table w-full shadow-md table-fixed table-sm bg-slate-50" id="searchTable">
                    <thead>
                        <tr>
                            <th class="bg-slate-300 rounded-tl-2xl">#</th>
                            <th class="bg-slate-300 ">Jabatan</th>
                            <th class="bg-slate-300 ">Name Client</th>
                            <th class="bg-slate-300 ">Nama Shift</th>
                            <th class="bg-slate-300 ">Jam Mulai</th>
                            <th class="bg-slate-300 ">Jam Selesai</th>
                            <th class="bg-slate-300 ">Pergantian Hari</th>
                            <th class="text-center bg-slate-300">Hari</th>
                            <th class="bg-slate-300 rounded-tr-2xl">Action</th>
                        </tr>
                    </thead>
                    <tbody class="my-10 text-sm">
                        @php
                            $no = 1;
                        @endphp
                        @forelse ($shift as $i)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $i->jabatan->name_jabatan }}</td>
                                @if ($i->client != null)
                                    <td class="break-words whitespace-pre-wrap">{{ $i->client->name }}</td>
                                @else
                                    <td class="text-red-500 break-words whitespace-pre-wrap">Kosong</td>
                                @endif
                                <td>{{ $i->shift_name }}</td>
                                <td>{{ $i->jam_start }}</td>
                                <td>{{ $i->jam_end }}</td>
                                <td>
                                    @if ($i->is_overnight == 1)
                                        Ya
                                    @else
                                        Tidak
                                    @endif
                                </td>
                                <td class="grid grid-cols-3 gap-1 text-center">
                                    {{-- menampilkan hari dalam bentuk badge --}}
                                    @php
                                        $days = '';
                                        if ($i->hari != null) {
                                            $daysArray = json_decode($i->hari, true);

                                            if (count($daysArray) == 7) {
                                                $days =
                                                    '<span class="col-span-3 px-2 py-1 mx-auto text-sm text-white bg-green-500 rounded">Setiap Hari</span>';
                                            } else {
                                                $dayMap = [
                                                    'Senin' => 'Sen',
                                                    'Selasa' => 'Sel',
                                                    'Rabu' => 'Rab',
                                                    'Kamis' => 'Kam',
                                                    'Jumat' => 'Jum',
                                                    'Sabtu' => 'Sab',
                                                    'Minggu' => 'Min',
                                                ];

                                                $colors = [
                                                    'Senin' => 'bg-blue-500',
                                                    'Selasa' => 'bg-indigo-500',
                                                    'Rabu' => 'bg-purple-500',
                                                    'Kamis' => 'bg-pink-500',
                                                    'Jumat' => 'bg-yellow-500',
                                                    'Sabtu' => 'bg-orange-500',
                                                    'Minggu' => 'bg-red-500',
                                                ];

                                                $badges = array_map(function ($d) use ($dayMap, $colors) {
                                                    $short = $dayMap[$d] ?? $d;
                                                    $color = $colors[$d] ?? 'bg-gray-500';
                                                    return "<span class='px-2 py-1 {$color} text-white rounded text-xs font-semibold'>$short</span>";
                                                }, $daysArray);

                                                $days = implode(' ', $badges);
                                            }
                                        } else {
                                            $days =
                                                '<span class="col-span-3 px-2 py-1 mx-auto text-sm text-white bg-gray-400 rounded">Kosong</span>';
                                        }
                                    @endphp
                                    {!! $days !!}
                                </td>

                                <td class="space-y-2">
                                    <x-btn-edit>{{ route('shift.edit', [$i->id]) }}</x-btn-edit>
                                    @if (Auth::user()->role_id == 2)
                                        <form action="{{ route('shift.destroy', [$i->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-btn-submit type="button" id="deleteUser" class="deleteUser"
                                                data="{{ $i }}" data-dataId="{{ $i->id }}" />
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">~ Data Kosong ~</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mx-10 mt-5">
                {{ $shift->links() }}
            </div>

        </div>
        <div
            class="fixed inset-0 hidden transition-all duration-300 ease-in-out modalDeleteUser bg-slate-500/10 backdrop-blur-sm">
            <div class="p-5 mx-2 rounded-md shadow bg-slate-200 w-fit">
                <div class="flex justify-end mb-3">
                    <button id="close" class="scale-90 btn btn-error">&times;</button>
                </div>
                <form id="formDelet" action="{{ url('client/data-client/' . $i->id) }}" method="POST"
                    class="flex items-center justify-center formDelet ">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col justify-center gap-2">
                        <div class="flex flex-col gap-2">
                            <p id="textModalDelet" class="text-lg font-semibold text-center textModalDelet"></p>
                        </div>
                        <div class="flex items-center justify-center overflow-hidden">
                            <button type="submit" class="overflow-hidden btn btn-error"><span
                                    class="overflow-hidden font-bold">Hapus Data</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('.deleteUser').click(function() {
                    var data = $(this).data('data');
                    var dataId = $(this).data('dataId');

                    var decodedJsonStr = data.replace(/&quot;/g, '"');

                    var jsonObj = JSON.parse(decodedJsonStr);
                    // console.log(jsonObj);

                    $('.formDelet').attr('action', `{{ url('shift/') }}/${jsonObj.id}`);
                    $('.textModalDelet').text(
                        `Apakah Anda Yakin Ingin Menghapus Shift ${jsonObj.shift_name} - ${jsonObj.jabatan.name_jabatan}?`
                    );
                    $('.modalDeleteUser').removeClass('hidden')
                        .addClass('flex justify-center items-center opacity-100');
                });

                $('#close').click(function() {
                    $('.modalDeleteUser').addClass('hidden').removeClass(
                        'flex justify-center items-center opacity-100');
                });
            })
        </script>
    </x-main-div>
</x-app-layout>
