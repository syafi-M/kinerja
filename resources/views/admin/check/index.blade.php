<x-app-layout>
    <style>
        @media (max-width: 768px) {
            /* Example custom class */
            .custom-select {
              /* Your select styles */
              /* For demonstration purposes */
              width: 200px;
              overflow: hidden;
            }
            /* Style to clamp text in options */
            .custom-select option {
              /* Clamp text to 1 line */
              overflow: hidden;
              text-overflow: ellipsis;
              white-space: nowrap;
            }
        }

    </style>
    <x-main-div>
        <div class="py-10">
            <p class="text-center text-lg sm:text-2xl font-bold py-10 uppercase">Index Check Point</p>
            <div class="flex flex-col sm:flex-row justify-between mx-10">
                @if(Auth::user()->role_id == 2)
                    <div>
        					<form id="filterForm" action="{{ route('admin.cp.index') }}" method="GET" class="p-1 flex flex-col sm:flex-row items-center">
        						<span class="flex gap-2">
    								<select name="filterKerjasama" id="filterKerjasama" class="select select-md select-bordered text-sm active:border-none border-none">
    									<option selected disabled>~ Kerja Sama ~</option>
    									@foreach ($kerjasama as $i)
    										<option value="{{ $i->id }}" {{ $filter == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
    									@endforeach
    								</select>
    							</span>
        						<div>
        						    <button type="submit"
        							    class="bg-blue-500 px-5 py-2 rounded-md hover:bg-blue-600 transition-colors ease-in .2s font-bold uppercase ml-3">Filter</button>
        						</div>
        					</form>
        				</div>
        		@elseif(Auth::user()->name == 'DIREKSI')
            		<div>
    					<form id="filterForm" action="{{ route('direksi.cp.index') }}" method="GET" class="p-1 flex flex-col gap-2 sm:flex-row items-center">
    						<span class="flex gap-2">
    							<select name="filterKerjasama" id="filterKerjasama" class="custom-select select select-bordered text-sm active:border-none border-none">
    								<option selected disabled>~ Kerja Sama ~</option>
    								@foreach ($kerjasama as $i)
    									<option value="{{ $i->id }}" {{ $filter == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
    								@endforeach
    							</select>
    						</span>
    						<div>
    						    <button type="submit"
    							    class="bg-blue-500 px-5 py-2 rounded-md hover:bg-blue-600 transition-colors ease-in .2s font-bold uppercase ml-3">Filter</button>
    						</div>
    					</form>
    				</div>
                @endif
                <span class="py-5">
                    <x-search/>
                </span>
            </div>
            <div class="flex justify-center gap-2 sm:justify-between mx-10">
                <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
            </div>
                <div class="flex justify-center overflow-x-auto sm:mx-10 mx-3 my-5">
                    <table class="table table-fixed overflow-x-auto table-xs bg-slate-50 table-zebra sm:table-md text-sm sm:text-md " id="searchTable">
                        <thead>
							<tr>
								<th class="bg-slate-300 rounded-tl-2xl" style="width: 10px;">#</th>
								<th class="bg-slate-300">Nama Lengkap</th>
								<th class="bg-slate-300">Check Point</th>
								@if(Auth::user()->role_id == 2)
								    <th class="bg-slate-300">Mitra</th>
								    <th class="bg-slate-300 rounded-tr-2xl">Action</th>
								@else
								    <th class="bg-slate-300 rounded-tr-2xl">Mitra</th>
								@endif
							</tr>
						</thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($user as $u)
                            @if($u->nama_lengkap != 'user' && $u->nama_lengkap != 'admin')
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $u->nama_lengkap }}</td>
                                    @if(Auth::user()->role_id == 2)
                                        <td class="overflow-hidden"><a href="{{ route('admin.cp.show', $u->id) }}" class="btn btn-sm btn-info text-xs overflow-hidden">Lihat CP</a></td>
                                    @else
                                        <td class="overflow-hidden"><a href="{{ route('direksi.cp.show', $u->id) }}" class="btn btn-sm btn-info text-xs overflow-hidden">Lihat CP</a></td>
                                    @endif
                                    
                                    
                                    <td>{{ $u->kerjasama->client->name }}</td>
                                    @if(Auth::user()->role_id == 2)
                                        <td>
                                            <form action="{{ route("cp_export.admin") }}" class="flex gap-2">
                                                <input type="month" name="this_month" class="input input-bordered" required/>
                                                <input type="text" name="user_id" value="{{ $u->id }}" class="input input-bordered" hidden/>
                                                <button class="btn btn-warning">Print</button>
                                            </form>
                                        </td>
                                    @endif
                                    
                                </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">~ Kosong ~</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        </div>
    </x-main-div>
</x-app-layout>