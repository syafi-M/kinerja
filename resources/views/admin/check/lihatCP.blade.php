<x-app-layout>
    <x-main-div>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold uppercase">Check Point {{ $user->nama_lengkap }}</p>
            </div>
            <div class="flex justify-end mx-10">
                <x-search/>
            </div>
            <div class="flex justify-center gap-2 sm:justify-between mx-10">
                @if(Auth::user()->role_id == 2)
                    <a href="{{ route('admin.cp.index') }}" class="btn btn-error">Back</a>
                @else
                    <a href="{{ route('direksi.cp.index') }}" class="btn btn-error">Back</a>
                @endif
            </div>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full">
                    <table class="table w-full table-xs bg-slate-50 table-zebra sm:table-md text-sm sm:text-md  md:scale-90">
                        <thead>
                            <tr>
								<th class="bg-slate-300 rounded-tl-2xl">#</th>
								<th class="bg-slate-300" style="width: 7rem;">Tanggal</th>
								<th class="bg-slate-300">Check Point</th>
								<th class="bg-slate-300">Deskripsi</th>
								<th class="bg-slate-300">Kordinat</th>
								<th class="bg-slate-300">Gambar Bukti</th>
								<th class="bg-slate-300">Status</th>
								<th class="bg-slate-300 rounded-tr-2xl">Action</th>
							</tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($cek as $c)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $c->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $c->type_check }}</td>
                                    <td>{{ $c->deskripsi }}</td>
                                    @if(Auth::user()->role_id == 2)
                                        <td class="overflow-hidden"><a href="{{ route('admin-lihatMap', $c->id) }}" class="btn btn-sm btn-info text-xs overflow-hidden">Lihat Koordinat</a></td>
                                    @else
                                        <td class="overflow-hidden"><a href="{{ route('direksi-lihatMap', $c->id) }}" class="btn btn-sm btn-info text-xs overflow-hidden">Lihat Koordinat</a></td>
                                    @endif
                                    @if ($c->img == 'no-image.jpg')
                                        <td>
                                            <x-no-img />
                                        </td>
                                    @elseif(Storage::disk('public')->exists('images/' . $c->img))
                                        <td><img class="lazy lazy-image" loading="lazy" src="" data-src="{{ asset('storage/images/' . $c->img) }}" alt="" srcset="{{ asset('storage/images/' . $c->img) }}" width="120px"></td>
                                    @else
                                        <td>
                                            <x-no-img />
                                        </td>
                                    @endif
                                    <td>
                                        @if($c->approve_status == "proccess")
                                            <span class="badge bg-amber-500 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status }}</span> 
                                        @elseif($c->approve_status == "accept")
                                            <span class="badge bg-emerald-700 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status }}</span> 
                                        @else
                                            <span class="badge bg-red-500 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status }}</span> 
                                        @endif
                                    </td>
                                    <td>
                                        @if ($c->approve_status == 'proccess')
                                    <div class="flex justify-center gap-1 items-center text-center">
                                                <div>
                                                    <form action="{{ route('direksi.approveCP', $c->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="text" name="approve_status" value="accept" hidden/>
                                                        <button type="submit" class="btn btn-success btn-xs rounded-btn"><i class="ri-check-double-line"></i></button>
                                                    </form>
                                                </div>
                                                <div>
                                                    <form action="{{ route('direksi.approveCP', $c->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="text" name="approve_status" value="denied" hidden/>
                                                        <button type="submit" class="btn btn-error btn-xs rounded-btn"><i class="ri-close-line"></i></button>
                                                    </form>
                                                </div>
                                               {{-- <form action="{{ route('admin.deletedIzin', $i->id) }}" method="POST" hidden>
                                                @csrf
                                                @method('DELETE')
                                                <div class="overflow-hidden ">
                                                    <button  class="text-red-400 hover:text-red-500 text-xl transition-all ease-linear .2s"><i class="ri-delete-bin-5-line"></i></button>
                                                </div>
                                            </form>--}}
                                        </div>
                                        @else
                                        {{--<div class="flex gap-2 hidden">
                                            <form action="{{ route('admin.deletedIzin', $i->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="overflow-hidden ">
                                                    <button  class="text-red-400 hover:text-red-500 text-xl transition-all ease-linear .2s"><i class="ri-delete-bin-5-line"></i></button>
                                                </div>
                                            </form>
                                        </div> --}}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">~ Kosong ~</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div>
                    {{ $cek->links()}}
                </div>
            </div>
        </div>
    </x-main-div>
</x-app-layout>