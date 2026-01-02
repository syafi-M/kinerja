<x-app-layout>
    <x-main-div>
        <div class="px-5 py-10">
            <p class="text-2xl font-bold text-center uppercase">Data Klien</p>
            <div class="flex justify-end ">
                <div class="flex items-center my-10 input w-fit input-bordered">
                    <i class="ri-search-2-line"></i>
                    <input type="search" id="searchInput" class="ml-1 border-none rounded" placeholder="Search..."
                        required>
                </div>
            </div>
            <div class="flex justify-end gap-2 py-3 mx-16">
                <a href="{{ route('admin.index') }}" class="btn btn-error">Kembali</a>
                <a href="{{ route('data-client.create') }}" class="btn btn-primary">+ Client</a>
            </div>
            <div class="flex justify-center pb-10 mx-10 overflow-x-auto">
                <table class="table w-full shadow-md table-fixed table-zebra table-sm bg-slate-50" id="searchTable">
                    <thead>
                        <tr>
                            <th class="bg-slate-300 rounded-tl-2xl">#</th>
                            <th class="bg-slate-300 ">Logo</th>
                            <th class="bg-slate-300 ">Name Client</th>
                            <th class="bg-slate-300 ">Alamat</th>
                            <th class="bg-slate-300 ">Provinsi</th>
                            <th class="bg-slate-300 ">Kabupaten</th>
                            <th class="bg-slate-300 ">Kode Pos</th>
                            <th class="bg-slate-300 ">Email</th>
                            <th class="bg-slate-300 ">No. Telepon</th>
                            <th class="bg-slate-300 ">No. Fax</th>
                            <th class="bg-slate-300 rounded-tr-2xl">Action</th>
                        </tr>
                    </thead>
                    <tbody class="my-10 text-sm ">
                        @php
                            $no = 1;
                        @endphp
                        @forelse ($client as $i)
                            <tr>
                                <td>{{ $no++ }}</td>
                                @if ($i->logo == 'no-image.jpg')
                                    <td>
                                        <x-no-img />
                                    </td>
                                @elseif(Storage::disk('public')->exists('images/' . $i->logo))
                                    <td><img class="lazy lazy-image" loading="lazy"
                                            src="{{ asset('storage/images/' . $i->logo) }}"
                                            data-src="{{ asset('storage/images/' . $i->logo) }}" alt=""
                                            srcset="" width="120px"></td>
                                @else
                                    <td>
                                        <x-no-img />
                                    </td>
                                @endif
                                <td class="whitespace-pre-wrap hyphens-auto ">{{ $i->name }}</td>
                                <td class="whitespace-pre-wrap hyphens-auto">{{ $i->address }}</td>
                                <td>{{ $i->province }}</td>
                                <td>{{ $i->kabupaten }}</td>
                                <td>{{ $i->zipcode }}</td>
                                <td class="break-words whitespace-pre-line">{{ $i->email }}</td>
                                <td class="break-words whitespace-pre-line">{{ $i->phone }}</td>
                                <td>{{ $i->fax }}</td>
                                <td class="space-y-2">
                                    <x-btn-edit>{{ url('client/data-client/' . $i->id . '/edit') }}</x-btn-edit>
                                    @php
                                        $getDat = $i;
                                    @endphp
                                    <x-btn-submit type="button" id="deleteUser" class="deleteUser"
                                        data="{{ $i }}" data-dataId="{{ $i->id }}" />
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
                {{ $client->links() }}
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

                    $('.formDelet').attr('action', `{{ url('client/data-client/') }}/${jsonObj.id}`);
                    $('.textModalDelet').text(`Apakah Anda Yakin Ingin Menghapus Client ${jsonObj.name}?`);
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
