<x-app-layout>
	<x-main-div class="ydis">
		<div class="bg-slate-500 p-4  shadow-md rounded-md">
			<p class="text-center text-2xl uppercase font-bold my-10">Jadwal Hari {{ $hari }}</p>
			@if (Auth::user()->role_id == 2)
				<form action="{{ route('jadwal_export.admin') }}" method="get">
					<div class="flex justify-end mx-10 mb-2 ">
						<input type="text" name="str1"  class="hidden">
						<input type="text" name="end1" class="hidden">
						<div class="flex flex-col items-center gap-x-2">
							<select name="filter" class="input input-bordered">
								<option class="disabled" disabled>~Pilih Mitra~</option>
								@forelse($kerj as $i)
									<option value="{{ $i->id }}" {{ $i->id == $filter ? 'selected' : '' }}>{{ $i->client->name }}</option>
								@empty
									<option class="disabled">~Mitra Kosong~</option>
								@endforelse
							</select>
						</div>
					</div>
					<span class=" justify-end mx-10">    
						<button type="submit" class="bg-yellow-400 px-3 py-2 shadow rounded-md text-2xl flex items-center gap-2"
							style="margin-bottom: 3rem;">
							<p class="text-sm font-semibold">Print PDF</p>
							<i class="ri-file-download-line"></i>
						</button>
					</span>
				</form>
			@else
				<form action="{{ route('lead_jadwal_export') }}" method="get" class="hidden">
					<div class="flex justify-end mx-10 mb-2 ">
						<button type="submit" class="bg-yellow-400 px-3 mt-4 py-2 shadow rounded-md text-2xl flex items-center gap-2">
							<p class="text-sm font-semibold">Print PDF</p>
							<i class="ri-file-download-line"></i>
						</button>
						<input type="text" name="str1" hidden>
						<input type="text" name="end1"  hidden>
					</div>
				</form>
			@endif

			{{-- 2 --}}
			<div class="overflow-x-scroll sm:overflow-x-auto pb-10 text-xs">
			    <form id="jadwalForm" action="{{ route('storeJadwalLeader') }}" method="POST">
			        @csrf
    				<table class="table table-xs table-zebra bg-slate-50 w-full">
    					<thead>
    						<tr>
    							<th class="bg-slate-300 rounded-tl-2xl">#</th>
    							<th class="bg-slate-300">Nama Lengkap</th>
    							<th class="bg-slate-300">Centang</th>
    							<th class="bg-slate-300 rounded-tr-2xl">Shift</th>
    						</tr>
    					</thead>
    					<tbody>
    						@php
    							$no = 1;
    						@endphp
    						@forelse ($user as $us)
    							@if ($us->nama_lengkap != 'admin' && $us->nama_lengkap != 'user')
    								<tr>
    									<td>{{ $no++ }}</td>
    									<td>{{ $us->nama_lengkap }}</td>
    									<td><input name="userID[]" value="{{ $us->id }}" type="checkbox" class="checkbox checkbox-sm"/></td>
    									<td>
    									    <select name="shift[]" class="input input-bordered">
    									        <option selected disabled>Pilih Shift</option>
    									        @forelse($shift as $i)
    									        <option value="{{ $i->id }}">{{ $i->shift_name }} || {{ $i->jam_start }} - {{ $i->jam_end }}</option>
    									        @empty
    									        @endforelse
    									    </select>
    									</td>
    							</tr>
    						@endif
        					@empty
        
        					@endforelse
        				</tbody>
        			</table>
        			<input type="hidden" name="hari" value="{{ $hari }}"/>
        			<button type="submit">Simpan</button>
			    </form>
    		</div>
		<div class="flex justify-center sm:justify-end my-5">
		    @if(Auth::user()->divisi->code_jabatan == "CO-CS")
			    <a href="{{ route('leaderView') }}" class="btn btn-error">Kembali</a>
		    @else
			    <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
		    @endif
		</div>
	</div>
</x-main-div>

<script>

</script>

</x-app-layout>
