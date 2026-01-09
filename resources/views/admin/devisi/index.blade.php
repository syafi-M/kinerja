<x-app-layout>
    <div class="mx-10 rounded bg-slate-500">
        <div>
            <p class="py-10 text-2xl font-bold text-center uppercase">Data Divisi</p>
        </div>
        <div class="flex justify-end">
            <div class="flex items-center justify-end mx-10 mb-5 input w-fit input-bordered">
                <i class="ri-search-2-line"></i>
                <input type="search" id="searchInput" class="ml-1 border-none rounded" placeholder="Search..." required>
            </div>
        </div>
        <div class="flex justify-end gap-2 my-3">
            <a href="{{ route('admin.index') }}"
                class="btn btn-error border-none hover:bg-red-500 transition-all ease-in-out .2s">Kembali</a>
            <a href="{{ route('divisi.create') }}"
                class="btn btn-warning hover:bg-yellow-600 border-none transition-all ease-in-out .2s">+ Divisi</a>

        </div>

        <div class="mx-10 overflow-x-auto">
            <table class="table w-full table-fixed table-sm table-zebra bg-slate-50" id="searchTable">
                <!-- head -->
                <thead>
                    <tr>
                        <th class="bg-slate-300 rounded-tl-2xl">#</th>
                        <th class="bg-slate-300 ">Nama Devisi</th>
                        <th class="bg-slate-300 ">Jabatan</th>
                        <th class="bg-slate-300 ">Perlengkapan</th>
                        <th class="bg-slate-300 rounded-tr-2xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($data as $i)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $i->name }}</td>
                            @if ($i->jabatan != null)
                                <td>{{ $i->jabatan->name_jabatan }}</td>
                            @else
                                <td>~ Jabatan Kosong ~</td>
                            @endif
                            <td>
                                @forelse ($i->perlengkapan as $value)
                                    <span class="capitalize break-words whitespace-pre-wrap">{{ $value->name }},</span>
                                @empty
                                    <a href="{{ url('/divisi/' . $i->id . '/add-equipment') }}"
                                        class="text-2xl text-yellow-500 hover:text-yellow-600 transition-all ease-in-out .2s"><i
                                            class="ri-add-circle-fill"></i></a>
                                @endforelse
                            </td>
                            <td class="overflow-hidden">
                                <x-btn-edit>{{ url('divisi/' . $i->id . '/edit') }}</x-btn-edit>
                                <x-btn-submit type="button" id="deleteUser" class="deleteUser"
                                    data="{{ $i }}" data-dataId="{{ $i->id }}" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Data Kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $data->links() }}
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

                    $('.formDelet').attr('action', `{{ url('divisi/') }}/${jsonObj.id}`);
                    $('.textModalDelet').text(`Apakah Anda Yakin Ingin Menghapus Divisi ${jsonObj.name}?`);
                    $('.modalDeleteUser').removeClass('hidden')
                        .addClass('flex justify-center items-center opacity-100');
                });

                $('#close').click(function() {
                    $('.modalDeleteUser').addClass('hidden').removeClass(
                        'flex justify-center items-center opacity-100');
                });
            })
        </script>
</x-app-layout>
