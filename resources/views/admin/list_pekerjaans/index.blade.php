<x-app-layout>
    <x-main-div>
        <div class="py-10 px-5">
            <p class="text-center font-bold text-2xl uppercase">
                Data List Pekerjaan
            </p>
            <div class="flex flex-col justify-end my-5">
                <div class=" flex flex-col justify-end items-end">
                    <x-search/>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('listPekerjaan.create') }}" class="btn btn-warning">+ Pekerjaan</a>
                </div>
            </div>
            <div class="flex justify-between gap-2 mx-16 py-3">
    				<form action="{{ route('listPekerjaan-excell') }}" method="POST" class="flex items-center gap-2 overflow-hidden" enctype="multipart/form-data">
    				    @csrf
    				    <label for="iCP" class="btn btn-success overflow-hidden" ><i class="ri-file-excel-2-line text-lg"></i><span id="importLabel" class="overflow-hidden">Import Pekerjaan</span></label>
    				    <input id="iCP" name="file" type="file" class="hidden" accept=".csv"/>
    				    <button class="btn btn-primary hidden" type="submit" id="btnImport">Import</button>
    				</form>
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
            <div class="flex justify-center overflow-x-auto mx-10 pb-10">
                <table class="table w-full shadow-md bg-slate-50" id="searchTable">
                    <thead>
						<tr>
							<th class="bg-slate-300 rounded-tl-2xl">#</th>
							<th class="bg-slate-300 " >Nama Ruangan</th>
							<th class="bg-slate-300 ">Nama Pekerjaan</th>
							<th class="bg-slate-300 rounded-tr-2xl">Action</th>
						</tr>
					</thead>
					<tbody>
					    @php
					        $no = 1;
					    @endphp
					    @forelse($listPekerjaans as $li)
					        <tr>
					            <td style="max-width: 4px;">{{ $no++ }}</td>
					            <td style="max-width: 100px;">{{ $li->ruangan->nama_ruangan }}</td>
			                    <td style="max-width: 200px;">
			                        @php
                                        $array = json_decode($li->name);
                                        $formattedString = $array != null ? implode(', ', $array) : "";
                                    @endphp
                                    {{$formattedString}}
			                        
			                   </td>
					            <td class="flex gap-2">
                                <x-btn-edit>{{ route('listPekerjaan.edit', [$li->id]) }}</x-btn-edit>
                                <form action="{{ route('listPekerjaan.destroy', [$li->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-btn-submit/>
                                </form>
                                    
								</td>
					        </tr>
					    @empty
					    <tr>
					        <td>
					            Kosong
					        </td>
					    </tr>
					    @endforelse
					</tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $listPekerjaans->links()}}
            </div>
        </div>
    </x-main-div>
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
</x-app-layout>