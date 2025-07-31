<x-app-layout>
    <x-main-div>
        <p class="text-center text-2xl font-bold py-5 uppercase">absensi izin</p>
        <div class="flex justify-between my-5 mx-10">
            <a href="{{ route('admin.index') }}" class="btn btn-error">Back</a>
            <div class="flex items-start gap-2">
                <x-search class=""/>
                <form action="{{ route('admin.export-izin') }}" method="GET" class="flex justify-end items-center">
                    @csrf
                    <select name="kerjasama_id" id="filterKerjasama" style="width: 16rem;" class="select  select-bordered text-md active:border-none border-none">
        				<option selected disabled>~ Nama Klien ~</option>
        				@foreach ($kerja as $i)
        					<option value="{{ $i->id }}">{{ $i->client->name }}</option>
        				@endforeach
        			</select>
                    <button class="flex justify-end mx-10 btn btn-warning"><i class="ri-file-download-line"></i></button>
                </form>
            </div>
        </div>
        <div class="flex items-center justify-center flex-col mx-10 pb-10">
            <table class="table table-sm w-full bg-slate-50" id="searchTable">
            <thead>
                <tr class="text-center">
                    <th class="bg-slate-300 rounded-tl-xl">#</th>
                    <th class="bg-slate-300">Nama lengkap</th>
                    <th class="bg-slate-300">Shift</th>
                    <th class="bg-slate-300">Mitra</th>
                    <th class="bg-slate-300 ">alasan izin</th>
                    <th class="bg-slate-300 ">Tanggal</th>
                    <th class="bg-slate-300">status</th>
                    <th class="bg-slate-300 rounded-tr-xl text-center">action</th>
                </tr>
            </thead>
            <tbody>
            @php
                $no = 1;
            @endphp
                @php
                $no = 1;
            @endphp
            @forelse ($izin as $i)
                <tr>
                    <td>{{ $no++ }}.</td>
                    <td style="color: {{ $i->user ? 'inherit' : 'red' }}">{{ $i->user ? $i->user->nama_lengkap : "User Tidak Ditemukan" }}</td>
                    <td>{{ $i->shift?->shift_name }}</td>
                    <td style="color: {{ $i->kerjasama ? 'inherit' : 'red' }}; width: 200pt;">{{ $i->kerjasama ? $i->kerjasama->client->name : "KOSONG"}}</td>
                    <td style="width: 200pt;" class="text-start line-clamp-2">{{ $i->alasan_izin }}</td>
                    <td class="text-start">{{ $i->created_at->format('Y-m-d') }}</td>
                    <td>
                        @if ($i->approve_status == 'process')
                            <span class="badge bg-amber-500 px-2 text-xs overflow-hidden font-semibold">{{ $i->approve_status }}</span>    
                        @elseif($i->approve_status == 'accept')
                            <span class="badge bg-emerald-700 px-2 text-xs text-white overflow-hidden">{{ $i->approve_status }}</span>    
                        @else
                            <span class="badge bg-red-500 px-2 text-xs overflow-hidden font-semibold text-white">{{ $i->approve_status }}</span>    
                        @endif
                    </td>
                    <td>
                        @if ($i->approve_status == 'process')
                        <div class="flex justify-center gap-1 items-center text-center">
                            <div>
                                <form action="{{ route('admin_acc', $i->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-xs rounded-btn"><i class="ri-check-double-line"></i></button>
                                </form>
                            </div>
                            <div>
                                <form action="{{ route('admin_denied', $i->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-error btn-xs rounded-btn"><i class="ri-close-line"></i></button>
                                </form>
                            </div>
                            <div class="overflow-hidden ">
                                <a href="{{ route('izin.show', $i->id) }}" class="text-sky-400 hover:text-sky-500 text-xl transition-all ease-linear .2s"><i class="ri-eye-fill"></i></a>
                            </div>
                            <form action="{{ route('admin.deletedIzin', $i->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="overflow-hidden ">
                                    <button  class="text-red-400 hover:text-red-500 text-xl transition-all ease-linear .2s"><i class="ri-delete-bin-5-line"></i></button>
                                </div>
                            </form>
                        </div>
                        @else
                        <div class="flex gap-2">
                            <div class="overflow-hidden ">
                                <a href="{{ route('izin.show', $i->id) }}" class="text-sky-400 hover:text-sky-500 text-xl transition-all ease-linear .2s"><i class="ri-eye-fill"></i></a>
                            </div>
                            <form action="{{ route('admin.deletedIzin', $i->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="overflow-hidden ">
                                    <button  class="text-red-400 hover:text-red-500 text-xl transition-all ease-linear .2s"><i class="ri-delete-bin-5-line"></i></button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">~ Kosong ~</td>
                </tr>
            @endforelse
            </tbody>
            </table>
            <div class="mt-5">
                {{ $izin->links()}}
            </div>
        </div>
    </x-main-div>
</x-app-layout>
