<x-app-layout>
	<x-main-div>
		<div class="py-10 px-5">
			<p class="text-center text-2xl font-bold  uppercase">Index Laporan Mitra</p>
			<div class="flex justify-end ">
				<div class="input flex items-center w-fit input-bordered my-10">
					<i class="ri-search-2-line"></i>
					<input type="search" id="searchInput" class="border-none rounded ml-1" placeholder="Search..." required>
				</div>
			</div>
			<div class="flex justify-center overflow-x-auto mx-10 pb-10">
				<table class="table table-fixed table-zebra w-full shadow-md bg-slate-50" id="searchTable">
					<thead>
						<tr>
							<th class="bg-slate-300 rounded-tl-2xl">#</th>
							<th class="bg-slate-300 ">Nama Mitra</th>
							<th class="bg-slate-300 {{ Auth::user()->id == 2 ? '' : 'rounded-tr-2xl'}}">File PDF</th>
							@if(Auth::user()->id == 2)
							<th class="bg-slate-300 rounded-tr-2xl">Action</th>
							@endif
						</tr>
					</thead>
					<tbody class=" text-sm my-10">
						@php
							$no = 1;
						@endphp
						@forelse ($laporanMitra as $index => $i)
							<tr>
								<td>{{ $no++ }}</td>
								<td class="hyphens-auto whitespace-pre-wrap ">{{ $i->kerjasama->client->name }}</td>
								<td>
								     <div id="pdf-container-{{$index}}" class="pdf-container"></div>
								</td>
								@if(Auth::user()->id == 2)
								<td class="space-y-2">
									<x-btn-edit>{{ route('laporanMitra.edit', $i->id) }}</x-btn-edit>
									@php
									    $getDat = $i;
									@endphp
									<x-btn-submit type="button" id="deleteUser" class="deleteUser" data="{{ $i }}" data-dataId="{{ $i->id }}"/>
								</td>
								@endif
							</tr>
						@empty
							<tr>
								<td colspan="{{Auth::user()->id == 2 ? 4 : 3}}" class="text-center">~ Data Kosong ~</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
			<div class="flex justify-end gap-2 mx-16 py-3">
				<a href="{{ route('admin.index') }}" class="btn btn-error">Back</a>
				@if(Auth::user()->id == 2)
				    <a href="{{ route('laporanMitra.create') }}" class="btn btn-primary">+ Laporan Mitra</a>
				@endif
			</div>
		</div>
        <div
        	class="fixed inset-0 modalDeleteUser hidden bg-slate-500/10 backdrop-blur-sm transition-all duration-300 ease-in-out">
        	<div class="bg-slate-200 w-fit p-5 mx-2 rounded-md shadow">
        		<div class="flex justify-end mb-3">
        			<button id="close" class="btn btn-error scale-90">&times;</button>
        		</div>
        		<form id="formDelet" action="#" method="POST"
        			class="flex justify-center items-center formDelet ">
        			@csrf
        			@method('DELETE')
        			<div class="flex justify-center flex-col gap-2">
        				<div class="flex flex-col gap-2">
        					<p id="textModalDelet" class="textModalDelet text-center text-lg font-semibold"></p>
        				</div>
        				<div class="flex justify-center items-center overflow-hidden">
        					<button type="submit"
        						class="btn btn-error overflow-hidden"><span class="font-bold overflow-hidden">Hapus Data</span>
        					</button>
        				</div>
        			</div>
        		</form>
        	</div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
        <script>
            // Function to load and render PDF
            function renderPDF(url, container) {
                pdfjsLib.getDocument(url).promise.then(function(pdf) {
                    pdf.getPage(1).then(function(page) {
                        var scale = 0.45;
                        var viewport = page.getViewport({ scale: scale });
        
                        var canvas = document.createElement("canvas");
                        var context = canvas.getContext("2d");
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        
                        canvas.style.maxWidth = "250px";
                        canvas.style.height = "auto";
        
                        var renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
        
                        container.appendChild(canvas);
                        page.render(renderContext);
                    });
                });
            }
        
            $(document).ready(function() {
                $('.deleteUser').click(function(){
                    var data = $(this).data('data');
                    var dataId = $(this).data('dataId');
                    
                    var decodedJsonStr = data.replace(/&quot;/g, '"');
        
                    var jsonObj = JSON.parse(decodedJsonStr);
                    // console.log(jsonObj);
                    
                    $('.formDelet').attr('action', `{{ url('laporanMitra/') }}/${jsonObj.id}`);
                    $('.textModalDelet').text(`Apakah Anda Yakin Ingin Menghapus Laporan Ini ?`);
                    $('.modalDeleteUser').removeClass('hidden').addClass('flex justify-center items-center opacity-100');
                });
                
                $('#close').click(function() {
                    $('.modalDeleteUser').addClass('hidden').removeClass('flex justify-center items-center opacity-100');
                });
        
                // Render PDFs
                @foreach ($laporanMitra as $index => $i)
                    var pdfContainer{{$index}} = document.getElementById("pdf-container-{{$index}}");
                    var pdfUrl{{$index}} = "{{ asset('storage/pdf/'. $i->file_pdf) }}";
                    renderPDF(pdfUrl{{$index}}, pdfContainer{{$index}});
                @endforeach
            });
        </script>
        

	</x-main-div>
</x-app-layout>
