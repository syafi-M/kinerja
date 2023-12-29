<x-app-layout>
	<x-main-div>
		<div class="px-5 py-10">
			<div>
                <p class="text-center text-lg sm:text-2xl font-bold py-5 uppercase">Check Point</p>
            </div>
			<form method="POST" action="{{ route('checkpoint-user.store') }}" class=" my-10" id="form-cp" enctype="multipart/form-data">
			@csrf
			<div class="bg-slate-100 px-10 py-5 rounded shadow">
				<div class="flex flex-col justify-between">
					<label>Nama: </label>
					<input type="text" id="user_id" name="user_id" value="{{ Auth::user()->id }}" hidden>
					<input type="text" value="{{ Auth::user()->nama_lengkap }}" disabled class="input input-bordered">
				</div>
				<div class="flex flex-col  justify-between mt-3">
					<label>Bermitra Dengan: </label>
					<input type="text" name="divisi_id" id="divisi_id" hidden value="{{ Auth::user()->divisi->id }}"> 
					<input type="text" value="{{ Auth::user()->kerjasama->client->name }}" disabled
						class="input input-bordered">
				</div>
				<div class="mt-3">
					<x-input-label for="type_check " :value="__('Check Point')" class="required"/>
					<select name="type_check" id="type_check" class="select select-bordered w-full mt-1" required>
						<option selected disabled value="0">~ Pilih Check Point ~</option>
						
					    <option name="type_check" value="harian" data-typecheck="harian" class="py-2">Harian</option>
						<option name="type_check" value="mingguan" data-typecheck="mingguan" class="py-2">Mingguan</option>
						<option name="type_check" value="bulanan" data-typecheck="bulanan" class="py-2">Bulanan</option>
						<option name="type_check" value="isidental" data-typecheck="isidental" class="py-2">Isidental</option>
						
					</select>
					<x-input-error :messages="$errors->get('type_check')" class="mt-2" />
				</div>
				<div class="flex flex-col mt-3">
                    <x-input-label for="pekerjaan_id" :value="__('Pilih Pekerjaan')"/>
                    <!--<p>{{ $pcp }} / {{ $cheli }}</p>-->
                    <select name="pekerjaan_id" id="pekerjaan_id" class="select-bordered select">
                        <option selected disabled value="0">~ Pilih pekerjaan ~</option>
                        @forelse ($pcp as $p)
                            @php
                                $isDisabled = false;
                            @endphp
                            @forelse ($pch as $che)
                                @if ($p->type_check == $che->type_check && $p->id == $che->pekerjaanCp->id)
                                    @php
                                        $isDisabled = true;
                                    @endphp
                                    @break
                                @endif
                            @empty
                            @endforelse
                
                            @if ($isDisabled)
                                @if ($p->kerjasama_id == Auth::user()->kerjasama_id)
                                    <option disabled value="{{ $p->id }}" data-pekerjaan="{{ $p->type_check }}">{{ $p->name }} (sudah di isi)</option>
                                @endif
                            @else
                                @if ($p->kerjasama_id == Auth::user()->kerjasama_id)
                                    <option value="{{ $p->id }}" data-name="{{ $p->type_check }}" data-pekerjaan="{{ $p->type_check }}">{{ $p->name }}</option>
                                @endif
                            @endif
                        @empty
                            <option disabled>~ Data Kosong ~</option>
                        @endforelse
                    </select>
                </div>

				<div class="my-5">
					<x-input-label for="deskripsi" :value="__('Deskripsi')"/>
					<textarea name="deskripsi" id="deskripsi" rows="3" class="textarea textarea-bordered w-full" placeholder="deskripsi check point.."></textarea>
					<x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
				</div>
				{{-- img --}}
				<div class="my-5 p-1">
					<x-input-label for="img" :value="__('Foto Bukti')" class="required"/>
					<div class="preview hidden w-full">
						<span class="flex justify-center items-center">
							<label for="img" class="p-1">
								<img class="img1 ring-2 ring-slate-500/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s"
									src="" alt="" srcset="" height="120px" width="120px">
								
							</label>
						</span>
					</div>
					<label for="img"
						class="w-full iImage1 flex flex-col items-center justify-center rounded-md bg-slate-300/70  ring-2 ring-slate-400/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s">
						<span class="p-3 flex justify-center flex-col items-center">
							<i class="ri-image-add-line text-xl text-slate-700/90"></i>
							<span class="text-xs font-semibold text-slate-700/70">+ Bukti</span>
							<input id="img" class="hidden mt-1 w-full file-input file-input-sm file-input-bordered shadow-none"
								type="file" name="img" :value="old('img')" autofocus autocomplete="img" accept="image/*"/>
						</span>
					</label>
					<label for="img" class="text-red-500 text-xs error-message hidden">File gambar terlalu besar. max( 3MB )</label>
				</div>
				<x-input-error :messages="$errors->get('img')" class="mt-2" />
				<span>
				    <p class="text-center">~ Lokasi ~</p>
    				<span class="flex justify-center join">
        				<input type="text" value="" id="latitude" name="latitude" class="join-item w-fit input input-disabled text-xs text-center" readonly/>
        				<input type="text" value="" id="longitude" name="longtitude" class="join-item w-fit input input-disabled text-xs text-center" readonly/>
    				</span>
				</span>
			</div>
			<div class="flex justify-center sm:justify-end gap-2 mt-10">
				<button type="submit" id="btnSubmit" class="btn btn-primary">Simpan</button>
				<a href="{{ route('dashboard.index') }}" class="btn btn-error hover:bg-red-500 transition-all ease-linear .2s">
					Kembali
				</a>
			</div>
			</form>
		</div>
	{{-- Leaflet --}}
	<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
		integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
		integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
		
		
		
	<script>
		var latitudeInput = $('#latitude');
        var longitudeInput = $('#longitude');
    
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position){
                showPosition(position);
            });
        } else {
            alert('Geo Location Not Supported By This Browser !!');
        }
    
            function showPosition(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
        
                latitudeInput.val(latitude);
                longitudeInput.val(longitude);
            }
	</script>
	<script>
	$(document).ready(function() {
	    $('#btnSubmit').click(function(){
    		    $(this).prop('disabled', true);
    		    $(this).text('Tunggu..');
    		    $(this).css('background-color: rgb(255, 204, 0 / 0.5);');
    		    $('#form-cp').submit();
    		});
        
	});
	
// 	$(document).ready(function() {
//         $('#pekerjaan_id').on('change', function() {
//             var selectedValue = $(this).val(); // Get the selected value

//             console.log(selectedValue); // Log the selected value to the console
//         });
//     });
    
    $(document).ready(function() {
        $('#pekerjaan_id').on('change', function() {
            var selectedDivisi = $(this).find(':selected').data('pekerjaan'); // Get the data-divisi attribute value

            // Set the selected value in the 'devisi_id' dropdown
            $('#type_check').val(selectedDivisi? selectedDivisi : 0);
        });
        
        $('#pekerjaan_id option:not([value="0"])').hide();
        
        $('#type_check').change(function () {
            var selectedTypeCheck = $(this).val();
            $('#pekerjaan_id').val(0);
    
            if (selectedTypeCheck == 0) {
                $('#pekerjaan_id option').show();
            } else {
                $('#pekerjaan_id option').each(function () {
                    if ($(this).data('pekerjaan') == selectedTypeCheck) {
                        $(this).show();
                    } else if ($(this).data('pekerjaan') == null) {
                        $('#pekerjaan_id').show();
                    } else if ($(this).data('pekerjaan') == undefined) {
                        $('#pekerjaan_id').val(0);
                    } else {
                        $(this).hide();
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        
        
        
        
        $('#img').change(function() {
            var maxSizeInBytes = 3 * 1024 * 1024; // 5MB (change this value to your desired max size)
            var fileSize = this.files[0].size;
            var errorMessageLabel = $('.error-message');

            if (fileSize > maxSizeInBytes) {
                // Clear the input field if the file exceeds the maximum size
                // $(this).val('');
                // errorMessageLabel.removeClass('hidden');
                
            } else {
                errorMessageLabel.addClass('hidden');
            }
        });
        
        
        
    });
    



	</script>
	</x-main-div>
</x-app-layout>