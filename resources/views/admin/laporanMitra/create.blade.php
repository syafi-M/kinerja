<x-app-layout>
	<x-main-div>
		<div class="py-10 px-10">
			<p class="text-xl font-bold uppercase text-center mb-10">Tambah Laporan Mitra</p>
			<form action="{{ route('laporanMitra.post') }}" method="post" class="flex justify-center items-center" enctype="multipart/form-data">
				<div style="max-width: 75%;" class="px-10 py-10 rounded-md mx-5 bg-slate-100">
					@method('POST')
					@csrf
					<div class="flex flex-col gap-2 mb-5 mt-5">
						<label for="client_id" class="mr-[3.61rem]">Mitra</label>
						<select
							class="select select-bordered"
							name="kerjasama_id">
							<option selected disabled class="disabled:bg-slate-700 disabled:text-slate-100">~ Pilih Mitra ~</option>
							@foreach ($kerjasama as $i)
								<option value="{{ $i->id }}">{{ $i->client->name }}</option>
							@endforeach
							<x-input-error class="mt-2" :messages="$errors->get('client_id')" />
						</select>
					</div>
					
					
					<div class="flex flex-col gap-2 mb-5">
						<label for="value" class="mr-5">File Laporan PDF</label>
						<input type="file" name="file_pdf" id="value"
							class="file-input file-input-bordered " accept=".pdf">
						<x-input-error class="mt-2" :messages="$errors->get('value')" />
					</div>
					
					<div id="pdf-preview-container" class="flex justify-center"></div>
					
					<div class="flex gap-3 justify-end my-5">
						<a href="{{ route('laporanMitra.index') }}" class="btn btn-error">Back</a>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</div>
			</form>
		</div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
        <script>
            $(document).ready(function() {
                // Function to display PDF preview
                function displayPDFPreview(pdfData) {
                    // Clear previous preview, if any
                    $("#pdf-preview-container").empty();
                    
                    // Display PDF using PDF.js
                    const loadingTask = pdfjsLib.getDocument({ data: atob(pdfData) });
                    loadingTask.promise.then(function(pdf) {
                        pdf.getPage(1).then(function(page) {
                            const viewport = page.getViewport({ scale: 0.45 });
                            const canvas = document.createElement("canvas");
                            const context = canvas.getContext("2d");
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            $("#pdf-preview-container").append(canvas);
                            page.render({ canvasContext: context, viewport: viewport });
                        });
                    });
                }
            
                // Handle file input change event
                $("#value").change(function() {
                    const file = this.files[0];
                    if (file) {
                        // Read file as data URL
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const pdfData = e.target.result.split(",")[1];
                            displayPDFPreview(pdfData);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        </script>

	</x-main-div>
</x-app-layout>
