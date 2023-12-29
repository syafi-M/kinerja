<x-app-layout>
    <x-main-div>
		<div class="py-10 px-5">
			<p class="text-center text-2xl font-bold  uppercase">Index Pekerjaan CP</p>
			<div class="flex flex-col justify-end ">
				<div class=" flex flex-col justify-end items-end ">
				<x-search />
				
			</div>
				<div class="flex justify-between gap-2 mx-16 py-3">
    				<form action="{{ route('import-pekerjaan') }}" method="POST" class="flex items-center gap-2 overflow-hidden" enctype="multipart/form-data">
    				    @csrf
    				    <label for="iCP" class="btn btn-success overflow-hidden" ><i class="ri-file-excel-2-line text-lg"></i><span id="importLabel" class="overflow-hidden">Import Pekerjaan</span></label>
    				    <input id="iCP" name="file" type="file" class="hidden" accept=".csv"/>
    				    <button class="btn btn-primary hidden" type="submit" id="btnImport">Import</button>
    				</form>
    				<a href="{{ route('pekerjaanCp.create') }}" class="btn btn-primary">+ Pekerjaan</a>
    			</div>
			</div>
			<!--Handle error-->
			    @if(session('failures'))
			    <div class="mx-10 mb-5">
                    <div class="alert alert-error text-white border-none" style="background-color: #b50404">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div class="mx-10 flex">
                            <strong>Duplicate Data : </strong>
                            <ul class="ml-2">
                                <li>{{ session('failures')}}</li>
                            </ul>
                        </div>
                    </div>
			    </div>
                @endif
			<!--End Handle-->
			<div class="flex justify-center overflow-x-auto mx-10 pb-10">
				<table class="table table-fixed w-full shadow-md bg-slate-50" id="searchTable">
					<thead>
						<tr>
							<th class="bg-slate-300 rounded-tl-2xl">#</th>
							<th class="bg-slate-300 ">Nama User</th>
							<th class="bg-slate-300 ">Nama Devisi</th>
							<th class="bg-slate-300 ">Nama CLient</th>
							<th class="bg-slate-300 ">Nama Pekerjaan</th>
							<th class="bg-slate-300 ">Jenis Pekerjaan</th>
							<th class="bg-slate-300 rounded-tr-2xl">Action</th>
						</tr>
					</thead>
					<tbody class="text-sm my-10">
						@php
							$no = 1;
						@endphp
						@forelse ($pcp as $i)
							<tr>
								<td>{{ $no++ }}</td>
								<td>{{ $i->user?->nama_lengkap ?$i->user?->nama_lengkap : 'Kosong' }}</td>
								<td>{{ $i->divisi?->jabatan?->name_jabatan ?  $i->divisi?->jabatan?->name_jabatan : 'Kosong' }}</td>
								<td>{{ $i->kerjasama->client->name }}</td>
								<td>{{ $i->name }}</td>
								<td>{{ $i->type_check }}</td>
								<td class="space-y-2">
									<x-btn-edit>{{ route('pekerjaanCp.edit', [$i->id]) }}</x-btn-edit>
									@if(Auth::user()->role_id == 2)
    									<form action="{{ route('pekerjaanCp.destroy', [$i->id]) }}" method="POST">
    										@csrf
    										@method('DELETE')
    										<x-btn-submit/>
    									</form>
									@endif
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" class="text-center">~ Data Kosong ~</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
				<div class="mt-5 mx-10">
					{{ $pcp->links() }}
				</div>
			<div class="flex justify-end gap-2 mx-16 py-3">
				<a href="{{ route('admin.index') }}" class="btn btn-error">Back</a>
			</div>
		</div>
    <script>
    $(document).ready(function () {
        $('#iCP').on('change', function () {
            var fileInput = $(this);
            var importLabel = $('#importLabel');
            var submitButton = $('#btnImport');

            if (fileInput.val()) {
                importLabel.text('Klik import');
                submitButton.removeClass('hidden');
            } else {
                importLabel.text('Import Pekerjaan');
                submitButton.addClass('hidden');
            }
        });
    });
</script>
	</x-main-div>
</x-app-layout>