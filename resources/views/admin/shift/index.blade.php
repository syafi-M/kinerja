<x-app-layout>
    <x-main-div>
        <div class="py-10 px-5">
            <p class="text-center text-2xl font-bold  uppercase">Data Shift</p>
            <div class="flex justify-end mr-10">
                <x-search />
            </div>
            <div class="flex justify-end gap-2 mx-16 py-3">
                <a href="{{ route('admin.index') }}" class="btn btn-error">Kembali</a>
                <a href="{{ route('shift.create') }}" class="btn btn-primary">+ Shift</a>
            </div>
            <div class="flex justify-center overflow-x-auto mx-10 pb-10">
                <table class="table table-fixed table-sm w-full shadow-md bg-slate-50" id="searchTable">
                    <thead>
                        <tr>
                            <th class="bg-slate-300 rounded-tl-2xl">#</th>
                            <th class="bg-slate-300 ">Jabatan</th>
                            <th class="bg-slate-300 ">Name Client</th>
                            <th class="bg-slate-300 ">Nama Shift</th>
                            <th class="bg-slate-300 ">Jam Mulai</th>
                            <th class="bg-slate-300 ">Jam Selesai</th>
                            <th class="bg-slate-300 text-center">Hari</th>
                            <th class="bg-slate-300 rounded-tr-2xl">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm my-10">
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
                                    <td class="break-words whitespace-pre-wrap text-red-500">Kosong</td>
                                @endif
                                <td>{{ $i->shift_name }}</td>
                                <td>{{ $i->jam_start }}</td>
                                <td>{{ $i->jam_end }}</td>
                                <td class="grid grid-cols-3 gap-1 text-center">
                                    {{-- menampilkan hari dalam bentuk badge --}}
                                    @php
                                        $days = '';
                                        if ($i->hari != null) {
                                            $daysArray = json_decode($i->hari, true);

                                            if (count($daysArray) == 7) {
                                                $days =
                                                    '<span class="px-2 py-1 col-span-3 mx-auto bg-green-500 text-white rounded text-sm">Setiap Hari</span>';
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
                                                '<span class="px-2 py-1 col-span-3 mx-auto bg-gray-400 text-white rounded text-sm">Kosong</span>';
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
            <div class="mt-5 mx-10">
                {{ $shift->links() }}
            </div>

        </div>
        <div
            class="fixed inset-0 modalDeleteUser hidden bg-slate-500/10 backdrop-blur-sm transition-all duration-300 ease-in-out">
            <div class="bg-slate-200 w-fit p-5 mx-2 rounded-md shadow">
                <div class="flex justify-end mb-3">
                    <button id="close" class="btn btn-error scale-90">&times;</button>
                </div>
                <form id="formDelet" action="{{ url('client/data-client/' . $i->id) }}" method="POST"
                    class="flex justify-center items-center formDelet ">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center flex-col gap-2">
                        <div class="flex flex-col gap-2">
                            <p id="textModalDelet" class="textModalDelet text-center text-lg font-semibold"></p>
                        </div>
                        <div class="flex justify-center items-center overflow-hidden">
                            <button type="submit" class="btn btn-error overflow-hidden"><span
                                    class="font-bold overflow-hidden">Hapus Data</span>
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
