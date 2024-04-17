<x-app-layout>
	<x-main-div>
		<div class="px-5 py-10">
			<div>
                <p class="text-center text-lg sm:text-2xl font-bold py-5 uppercase">Buat Planning</p>
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
				<div class="mt-3 flex flex-col gap-2">
					<x-input-label for="type_check " :value="__('Check Point')" class="required text-center"/>
					@php
					    $pcpH = $pcp->where('type_check', 'harian');
					    $pcpM = $pcp->where('type_check', 'migguan');
					    $pcpB = $pcp->where('type_check', 'bulanan');
					    $pcpI = $pcp->where('type_check', 'isidental');
					@endphp
					<span class="flex flex-col gap-1">
                        <label for="example_checkbox" class="btn btn-sm btn-info" id="lHarian">Harian</label>
                        <div class="flex flex-col" id="hCont" style="display: none;">
                            @forelse ($pcpH as $p)
                                <span class="flex items-center gap-2 p-1 overflow-hidden">
                                    <input type="checkbox" name="pekerjaan_id[]" value="{{ $p->id }}" id="harian" class="checkbox">
                                    <label for="checkbox">{{ $p->name }}</label>
                                </span>
                            @empty
                                <span><p class="text-center">~Pekerjaan Tidak Tersedia~</p></span>
                            @endforelse
                        </div>
					</span>
					<span class="flex flex-col gap-1">
                        <label for="example_checkbox" class="btn btn-sm btn-info" id="lMingguan">Mingguan</label>
                        <div class="flex flex-col" id="mCont" style="display: none;">
                            @forelse ($pcpM as $p)
                                <span class="flex items-center gap-2 p-1 overflow-hidden">
                                    <input type="checkbox" name="pekerjaan_id[]" value="{{ $p->id }}" id="mingguan" class="checkbox">
                                    <label for="checkbox">{{ $p->name }}</label>
                                </span>
                            @empty
                                <span><p class="text-center">~Pekerjaan Tidak Tersedia~</p></span>
                            @endforelse
                        </div>
					</span>
					<span class="flex flex-col gap-1">
                        <label for="example_checkbox" class="btn btn-sm btn-info" id="lBulanan">Bulanan</label>
                        <div class="flex flex-col" id="bCont" style="display: none;">
                            @forelse ($pcpB as $p)
                                <span class="flex items-center gap-2 p-1 overflow-hidden">
                                    <input type="checkbox" name="pekerjaan_id[]" value="{{ $p->id }}" id="bulanan" class="checkbox">
                                    <label for="checkbox">{{ $p->name }}</label>
                                </span>
                            @empty
                                <span><p class="text-center">~Pekerjaan Tidak Tersedia~</p></span>
                            @endforelse
                        </div>
					</span>
					<span class="flex flex-col gap-1">
                        <label for="example_checkbox" class="btn btn-sm btn-info" id="lIsidental">Isidental</label>
                        <div class="flex flex-col" id="iCont" style="display: none;">
                            @forelse ($pcpI as $p)
                                <span class="flex items-center gap-2 p-1 overflow-hidden">
                                    <input type="checkbox" name="pekerjaan_id[]" value="{{ $p->id }}" id="isidental" class="checkbox">
                                    <label for="checkbox">{{ $p->name }}</label>
                                </span>
                            @empty
                                <span><p class="text-center">~Pekerjaan Tidak Tersedia~</p></span>
                            @endforelse
                        </div>
					</span>
					<x-input-error :messages="$errors->get('type_check')" class="mt-2" />
				</div>
				<span class="hidden">
				    <p class="text-center">~ Lokasi ~</p>
    				<span class="flex justify-center join">
        				<input type="text" value="" id="latitude" name="latitude" class="join-item w-fit input input-disabled text-xs text-center" readonly/>
        				<input type="text" value="" id="longitude" name="longtitude" class="join-item w-fit input input-disabled text-xs text-center" readonly/>
    				</span>
				</span>
			</div>
			<div class="flex justify-center sm:justify-end gap-2 mt-10">
				<button type="button" id="btnSubmit" class="btn btn-primary">Simpan</button>
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
        
        
        $('#lHarian').click(function(){
            $('#mCont, #bCont, #iCont').slideUp();
            $('#hCont').slideToggle();
        })
        $('#lMingguan').click(function(){
            $('#hCont, #bCont, #iCont').slideUp();
            $('#mCont').slideToggle();
        })
        $('#lBulanan').click(function(){
            $('#mCont, #hCont, #iCont').slideUp();
            $('#bCont').slideToggle();
        })
        $('#lIsidental').click(function(){
            $('#mCont, #bCont, #hCont').slideUp();
            $('#iCont').slideToggle();
        })
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