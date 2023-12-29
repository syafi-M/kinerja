<x-app-layout>
    <x-main-div>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold pb-5 uppercase">Data Check Point</p>
            </div>
            <form action="#" method="GET" class="flex justify-center mx-2 sm:mx-10 mb-5">
				<span class="p-4 rounded-md bg-slate-300">
					<label class="sm:mx-10 mx-5 label label-text font-semibold text-xs sm:text-base">Pilih Bulan</label>
					<div class="join  sm:mx-10 scale-[80%] sm:scale-100">
						<input type="month" placeholder="pilih bulan..." class="join-item input input-bordered" name="search"
							id="search" />
						<button type="submit" class="btn btn-info join-item">search</button>
					</div>
				</span>
			</form>
			<div class="flex justify-center gap-2 mx-10 sm:justify-end">
                <a href="{{ route('dashboard.index') }}" class="btn btn-error mx-2 sm:mx-10">Kembali</a>
                <a href="{{ route('checkpoint-user.create') }}" class="btn btn-primary mx-2 sm:mx-10">+ CP</a>
            </div>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full">
                    <table class="table w-full table-xs bg-slate-50 table-zebra sm:table-md text-sm sm:text-md scale-90 md:scale-90">
                        <thead>
                            <tr>
                                <th class="bg-slate-300 rounded-tl-2xl">#</th>
								<th class="bg-slate-300 px-7">Tanggal</th>
								<th class="bg-slate-300 ">Check Point</th>
								<th class="bg-slate-300 px-10">Pekerjaan</th>
								<th class="bg-slate-300 px-10">Deskripsi</th>
								<th class="bg-slate-300  px-10">Bukti</th>
								<th class="bg-slate-300 rounded-tr-2xl px-10">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @php
								$no = 1;
							@endphp
                            @forelse ($cek as $c)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $c->created_at->format('Y-m-d') }}</td>
                                    <td class="capitalize">{{ $c->type_check }}</td>
                                        <td class="capitalize">{{ $c->pekerjaancp ? $c->pekerjaancp->name : "~ KOSONG ~" }}</td>
                                    <td>{{ $c->deskripsi }}</td>
                                    @if ($c->img == 'no-image.jpg')
                                        <td>
                                            <x-no-img />
                                        </td>
                                    @else
                                        <td><img src="{{ asset('storage/images/' . $c->img) }}" alt="" srcset=""
                                                width="70px">
                                        </td>
                                    @endif
                                    <td class="space-y-2">
                                        <x-btn-edit>{{ route('checkpoint-user.edit', $c->id) }}</x-btn-edit>
                                        <form action="{{ route('checkpoint-user.destroy', $c->id) }}" method="POST"> 
                                            @csrf
                                            @method('DELETE')
                                            <x-btn-submit />
                                        </form>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Laporan Saat Ini Kosong</td> 
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="pag-1" class="mt-5 mb-5 mx-10">
                {{ $cek->links() }}
            </div>
            
        </div>
    </x-main-div>
</x-app-layout>