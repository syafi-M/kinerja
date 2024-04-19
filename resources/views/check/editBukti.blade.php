<x-app-layout>
	<x-main-div>
		<div class="px-5 py-10">
			<div>
                <p class="text-center text-lg sm:text-2xl font-bold py-5 uppercase">{{ "Kirim Bukti Pekerjaan" }}</p>
            </div>
			<form method="POST" action="{{ route('uploadBukti-checkpoint-user') }}" class=" my-10" id="form-cp" enctype="multipart/form-data">
			@csrf
			<div class="bg-slate-100 px-5 py-5 rounded shadow">
				<div class="flex flex-col justify-between">
					<label class="font-semibold">Nama: </label>
					<input type="text" id="user_id" name="user_id" value="{{ Auth::user()->id }}" hidden>
					<input type="text" value="{{  Auth::user()->nama_lengkap }}" disabled class="input input-bordered">
				</div>
				<div class="flex flex-col  justify-between mt-3">
					<label class="font-semibold">Bermitra Dengan: </label>
					<input type="text" name="divisi_id" id="divisi_id" hidden value="{{ Auth::user()->divisi->id }}"> 
					<input type="text" value="{{ Auth::user()->kerjasama->client->name }}" disabled
						class="input input-bordered">
				</div>
				<div class="mt-3 flex flex-col gap-2">
					<x-input-label for="type_check " :value="__('Check Point')" class="required text-center font-semibold"/>
					@foreach(['harian', 'mingguan', 'bulanan', 'isidental'] as $type)
                        @php
                            $pcpType = $pcp->whereIn('id', $cex->pekerjaan_cp_id)->where('type_check', $type);
                        @endphp
                        <span class="flex flex-col gap-1">
							@if ($pcpType->count() >= 1)
                            	<label for="example_checkbox" class="label font-semibold">~{{ ucfirst($type) }}</label>
							@endif
                            <div class="flex flex-col">
                                @forelse ($pcpType as $p)
                                    <span class="flex flex-col justify-center gap-2 p-1 overflow-hidden">
                                        <label for="checkbox" style="padding-left: 10px;" class="lab" data-id="{{ $p->id }}" data-loop="{{ $loop->index }}">{{ $loop->index + 1 }}. {{ $p->name }}</label>
                                        <!---->
                                        <div class="p-1">
                    						<div class="preview_{{$p->id}} hidden w-full">
                    							<span class="flex justify-center items-center">
                    								<label for="img_{{$p->id}}" class="p-1">
                    									<img class="img_{{$p->id}} ring-2 ring-slate-400/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s"
                    										src="" alt="" srcset="" height="120px" width="120px">
                    								</label>
                    							</span>
                    						</div>
                    						<label for="img_{{$p->id}}"
                    							class="w-full iImage_{{$p->id}} flex flex-col items-center justify-center rounded-md bg-slate-300/70 ring-2 ring-slate-400/70 hover:ring-0 hover:bg-slate-300 transition ease-in-out .2s">
                    							<span class="p-2 flex justify-center items-center">
                    								<i class="ri-image-add-line text-xl text-slate-700/90"></i>
                    								<span class="text-xs font-semibold text-slate-700/70">+ Gambar</span>
                    								<input id="img_{{$p->id}}" data-pcp_id="{{ $p->id }}" class="input_img_{{ $p->id }} hidden mt-1 w-full file-input file-input-sm file-input-bordered shadow-none"
                    									type="file" name="img[]" value="null" autofocus autocomplete="img2" accept="image/*"/>
                    							</span>
                    						</label>
                    					</div>
                    					<!---->
                    					<div class="my-2">
                    						<textarea name="deskripsi[]" id="deskripsi" rows="1" class="textarea textarea-bordered w-full" placeholder="Deskripsi laporan..."></textarea>
                    						<textarea name="note[]" id="note" rows="1" class="textarea textarea-bordered w-full hidden" placeholder="Deskripsi laporan..."></textarea>
                    						<x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                    					</div>
                                    </span>
                                @empty
                                    {{-- <span><p class="text-center">~Pekerjaan Tidak Tersedia~</p></span> --}}
                                @endforelse
                            </div>
                        </span>
                    @endforeach
					<x-input-error :messages="$errors->get('type_check')" class="mt-2" />
				</div>
				<span class="hidden">
				    <p class="text-center">~ Lokasi ~</p>
    				<span class="flex justify-center join">
        				<input type="text" value="" id="latitude" name="latitude" class="join-item w-fit input input-disabled text-xs text-center" readonly/>
        				<input type="text" value="" id="longitude" name="longtitude" class="join-item w-fit input input-disabled text-xs text-center" readonly/>
    				</span>
				</span>
				<div class="hidden" id="pcp_container">

				</div>
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
        let checkedCount = 0;
            var checkedCheckboxes = $('.lab');
            checkedCount = checkedCheckboxes.length;
            var pcp = {!! json_encode($pcp) !!};
            // console.log(pcp);
            
            $('.lab').each(function(index, element) {
                var dataId = $(element).data('id');
                var matchedPcp = Object.values(pcp).find(item => item.id == dataId);
                // console.log(dataId, matchedPcp);
                if (matchedPcp) {
                    $(`#img_${dataId}`).change(function() {
                        const input = $(this)[0];
        				const preview = $(`.preview_${dataId}`);
						console.log($(this).data('pcp_id'), $(this).val());

						if (input.files) {
							const valueExists = $('.input_pcp').filter(function() {
                            return $(this).val() == $(`.input_img_${dataId}`).data('pcp_id');
                        }).length > 0;
							console.log(valueExists);
							if (!valueExists) {
								$('#pcp_container').append(
									$(`<input class="input_pcp" name="pekerjaan_cp_id[]" value="${$(this).data('pcp_id')}"/>
									<input class="status" name="approve_status[]" value="proccess"/>`)
								)
							}
						}
        
        				if (input.files && input.files[0]) {
        					const reader = new FileReader();
        
        					reader.onload = function(e) {
        						preview.show();
        						preview.find(`.img_${dataId}`).attr('src', e.target.result);
        						preview.removeClass('hidden');
        						preview.find(`.img_${dataId}`).addClass('rounded-md shadow-md my-4');
        						$(`.iImage_${dataId}`).removeClass('flex').addClass('hidden');
        					};
        
        					reader.readAsDataURL(input.files[0]);
        				}
                    });
        
                }
        $(`#img_${dataId}`).change(function() {
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
        
        
        
        
        
    });
    



	</script>
	</x-main-div>
</x-app-layout>