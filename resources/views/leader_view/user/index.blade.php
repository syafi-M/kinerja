<x-app-layout>
    <x-main-div>
        <div class="py-10 sm:mx-10">
            <p class="text-center text-lg sm:text-2xl uppercase font-bold ">List Karyawan, {{ Auth::user()->kerjasama->client->name }}</p>
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
                    <div class="mt-5 w-full">
                        <x-search />
                    </div>
                </div>
                <form action="{{ route('lead_user') }}" method="GET" class="flex flex-col justify-center gap-2 bg-slate-100 rounded px-5 py-3 mb-5 hidden">
                    <div>
                        <label class="label text-xs sm:text-base">Pilih Divisi</label>
                        <select name="divisi" class="select select-bordered w-full text-black select-sm text-xs">
                            <option class="disabled" disabled selected>~Pilih Divisi~</option>
                            @forelse($divisi as $i)
                                <option value="{{ $i->id }}">{{ $i->name }}</option>
                            @empty
                                <option class="disabled">~Divisi Kosong~</option>
                            @endforelse
                        </select>
                    </div>
                    <button><a class="btn btn-primary btn-sm sm:btn-md">Filter</a></button>
                </form>
                <div class="overflow-x-scroll w-full md:overflow-hidden mx-2 sm:mx-10  ">
                    <table id="searchTable" class="table table-xs table-zebra sm:table-md text-xs bg-slate-50 font-semibold sm:text-md ">
                        <thead>
							<tr class="text-center">
                                <th class="p-1 py-2 bg-slate-300 rounded-tl-2xl">#</th>
                                <th class="p-1 py-2 bg-slate-300">Image</th>
                                <th class="p-1 py-2 bg-slate-300">Name</th>
								<th class="p-1 py-2 bg-slate-300">Nama Lengkap</th>
								<th class="p-1 py-2 bg-slate-300">Jabatan</th>
								<th class="p-1 py-2 bg-slate-300">Email</th>
								<th class="p-1 py-2 bg-slate-300 rounded-tr-2xl">Kerjasama</th>
							</tr>
						</thead>
                        <tbody>
                            @php
                                $n = 1;
                            @endphp
                            @forelse ($user as $i)
                                @if($i->nama_lengkap != 'admin' && $i->nama_lengkap != 'user' && $i->nama_lengkap != 'MITRA SAC')
                                    <tr>
                                        <td class="p-1 ">{{ $n++ }}</td>
                                        @if ($i->image == 'no-image.jpg')
        									<td>
        										<x-no-img />
        									</td>
        								@elseif(Storage::disk('public')->exists('images/' . $i->image))
        									<td><img class="lazy lazy-image" loading="lazy" src="{{ asset('storage/images/' . $i->image) }}" data-src="{{ asset('storage/images/' . $i->image) }}" alt="" srcset="" width="120px"></td>
        								@else
        								    <td>
        										<x-no-img />
        									</td>
        								@endif
                                        <td class="p-1 ">{{ $i->name }}</td>
                                        <td class="p-1  break-words whitespace-pre-wrap">{{ $i->nama_lengkap }}</td>
                                        <td class="p-1  break-words whitespace-pre-wrap" style="width: 90px">{{ $i->divisi->jabatan->code_jabatan }}</td>
                                        <td class="p-1 break-words whitespace-pre-line">{{ $i->email}}</td>
                                        @if ($i->kerjasama == null)
        									<td>kosong</td>
        								@elseif($i->kerjasama->client == null)
        									<td>kosong</td>
        								@else
        									<td class="break-words whitespace-pre-line p-1">{{ $i->kerjasama->client->name }}</td>
        								@endif
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7">
                                        ~ Data Kosong ~
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                    <div id="pag-1" class="mt-5 mb-5">
                        {{ $user->links() }}
                    </div>
                    
            </div>
        </div>
    </x-main-div>
</x-app-layout>