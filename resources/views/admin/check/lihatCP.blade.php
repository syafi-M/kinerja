<x-app-layout>
    <style>
        
       

    </style>
    <x-main-div>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold uppercase">Check Point {{ $user->nama_lengkap }}</p>
            </div>
            
            <div class="flex justify-end gap-2  mx-5 sm:mx-10 my-2">
                @if(Auth::user()->role_id == 2)
                    <a href="{{ route('admin.cp.index') }}" class="btn btn-error">Kembali</a>
                @else
                    <a href="{{ route('direksi.cp.index') }}" class="btn btn-error">Kembali</a>
                @endif
            </div>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full">
                    <table class="table w-full table-xs bg-slate-50 table-zebra sm:table-md text-sm sm:text-md  md:scale-90">
                        <thead class="text-center">
                            <tr>
								<th class="bg-slate-300 rounded-tl-2xl">#</th>
								<th class="bg-slate-300">Gambar Bukti</th>
								<th class="bg-slate-300">Nama CP</th>
								<th class="bg-slate-300">Deskripsi</th>
								<th class="bg-slate-300">Harian</th>
								<th class="bg-slate-300">Mingguan</th>
								<th class="bg-slate-300">Bulanan</th>
								<th class="bg-slate-300">Isidental</th>
								<th class="bg-slate-300 displayTH" id="displayTH">Informasi</th>
								<th class="bg-slate-300 rounded-tr-2xl">Action</th>
							</tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($cek as $c)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    @if ($c->img == 'no-image.jpg')
                                        <td >
                                            <x-no-img />
                                        </td>
                                    @elseif(Storage::disk('public')->exists('images/' . $c->img))
                                        <td ><img class="lazy lazy-image" loading="lazy" src="" data-src="{{ asset('storage/images/' . $c->img) }}" alt="" srcset="{{ asset('storage/images/' . $c->img) }}" width="120px"></td>
                                    @else
                                        <td >
                                            <x-no-img />
                                        </td>
                                    @endif
                                    <td>{{ $c->pekerjaancp ? $c->pekerjaancp->name : $c->deskripsi }}</td>
                                    <td>{{ $c->deskripsi }}, {{ $c->created_at->format('Y-m-d') }}</td>
                                    @if($c->type_check == 'harian')
                                        <td class="min-h-full">
                                            <div class="flex items-center justify-center p-2 text-xl font-semibold">
                                                <i class="ri-check-line"></i>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @elseif($c->type_check == 'mingguan')
                                        <td></td>
                                        <td class="min-h-full">
                                            <div class="flex items-center justify-center p-2 text-xl font-semibold">
                                                <i class="ri-check-line"></i>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    @elseif($c->type_check == 'bulanan')
                                        <td></td>
                                        <td></td>
                                        <td class="min-h-full">
                                            <div class="flex items-center justify-center p-2 text-xl font-semibold">
                                                <i class="ri-check-line"></i>
                                            </div>
                                        </td>
                                        <td></td>
                                    @elseif($c->type_check == 'isidental')
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="min-h-full">
                                            <div class="flex items-center justify-center p-2 text-xl font-semibold">
                                                <i class="ri-check-line"></i>
                                            </div>
                                        </td>
                                    @endif
                                    <td class="overflow-hidden">
                                        <a href="{{ Auth::user()->role_id == 2 ? route('admin-lihatMap', $c->id) : route('direksi-lihatMap', $c->id) }}"
                                            class="btn btn-sm btn-info text-xs overflow-hidden">
                                            <span id="displayText" class="displayText">Lokasi</span> <!-- Empty span to hold the text -->
                                        </a>
                                    </td>

                                    
                                    <td>
                                        @if ($c->approve_status == 'proccess')
                                            <div class="flex justify-center gap-1 items-center text-center overflow-hidden"  style="width: 14rem;">
                                                <div class="overflow-hidden">
                                                    <button class="btn btn-success btn-xs rounded-btn flex items-center overflow-hidden" onclick="openModal({{ json_encode($c) }}, '{{ route('direksi.approveCP', $c->id) }}', {{ json_encode($c->user) }}, 'accept')">
                                                        <i class="ri-check-double-line"></i>
                                                        <p class="overflow-hidden">accept</p>
                                                    </button>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <button class="btn btn-error btn-xs rounded-btn flex items-center overflow-hidden" onclick="openModal({{ json_encode($c) }}, '{{ route('direksi.deniedCP', $c->id) }}', {{ json_encode($c->user) }}, 'denied')">
                                                        <i class="ri-close-line"></i>
                                                        <p class="overflow-hidden">denied</p>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            {{--<div class="flex flex-col gap-2 justify-center items-center mt-2">
                                                <button type="button" id="{{ "note" . $c->id }}" class="btn btn-warning btn-xs rounded-btn flex items-center overflow-hidden px-10" >
                                                    <i class="ri-sticky-note-line"></i>
                                                    <p class="overflow-hidden">note?</p>
                                                </button>
                                                <input id="{{ "notes" . $c->id }}" type="text" name="note" class="input input-bordered" placeholder="notes..." style="display: none; height: 40px;"/>
                                            </div>--}}
                                        @else
                                            @if($c->approve_status == "accept")
                                                <div class="flex flex-col justify-center items-center">
                                                    <span class="badge bg-emerald-700 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status }}</span> 
                                                    <p>Note: {{ $c->note }}</p>
                                                </div>
                                            @else
                                                <div class="flex flex-col justify-center items-center">
                                                    <span class="badge bg-red-500 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status }}</span>
                                                    <p>Note: {{ $c->note }}</p>
                                                </div>
                                            @endif
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
                </div>
                <div>
                        <!-- Display your modal here -->
                        <div id="modalShow" class="modalShow" style="display: none;">
                            <div
                            	style="z-index: 9000; backdrop-filter: blur(3px);" class="fixed w-full flex justify-center items-center inset-0 bg-slate-500/10 transition-all duration-300 ease-in-out h-screen">
                                <div class="flex justify-center items-center" style="width: 40%;">
                                	<div class="bg-slate-50 inset-0 w-full p-3 mx-10 my-10 rounded-md shadow">
                                		<div class="flex justify-end mb-3">
                                			<button id="closeButton" class="btn btn-error scale-90 closeButton">&times;</button>
                                		</div>
                                		<div class="flex flex-col justify-center items-center gap-4">
                                		    <span id="status" class="text-lg p-2 rounded-lg text-white font-semibold"></span>
                                		    <span>
                                		        <img id="modalImg" class="lazy lazy-image" loading="lazy" src="" alt="" srcset="" width="120px">
                                		    </span>
                                		    <p id="modalTitle" class="font-semibold whitespace-pre-wrap break-word py-5 text-center"></p>
                                		    <div class="flex flex-col w-full">
                                		        <label>Note</label>
                                		        <textarea id="notes" value="" name="note" class="textarea textarea-bordered" placeholder="notes..."></textarea>
                                		    </div>
                                		    <div>
                                		        <button id="confirmButton" class="btn btn-warning rounded-btn flex items-center overflow-hidden px-10">Confirm</button>
                                		    </div>
                                		</div>
                                	</div>
                                </div>
                            </div>
                        </div>
				</div>
                <div>
                    {{ $cek->links()}}
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('button[id^="note"]').on('click', function(event) {
                    event.preventDefault();
                    var id = $(this).attr('id').substring(4);
                    $('#notes' + id).toggle();
                    $('#note' + id).toggle();
                });
            });
            
            function openModal(data, route, dataUser, status) {
                console.log(data, dataUser, status);
                var kerjacp = data.pekerjaancp ? data.pekerjaancp.name : data.deskripsi;
                
                $('#modalTitle').html('CP ' + kerjacp);
                $('#status').html(status);
                $('#modalImg').attr('srcset', '{{ asset("storage/images") }}' + '/' + data.img);
                
                // Use the correct ID for the notes textarea
                var id = data.id; // Assuming data.id is the correct identifier for each row
                // var inputValue = $(`#notes${id}`).val();
                //  console.log(inputValue, id, $(`#notes${id}`));
                
                if (status == 'accept') {
                    $('#status').addClass('bg-green-500');
                } else {
                    $('#status').addClass('bg-red-500');
                }
                
                $('#modalShow').toggle();
                $('#confirmButton').attr('onclick', `approveRequest('${route}', '${status}', '${id}')`);
            }
            
            $('#closeButton').on('click', function() {
                $('#modalShow').toggle();
            })
            
            function approveRequest(route, status, id) {
                // var id = $('button[id^="note"]').attr('id').substring(4);
                var inputValue = $('#notes').val();
                // console.log(inputValue);
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        '_method': 'PATCH',
                        'approve_status': status,
                        'note': inputValue,
                    },
                    success: function (response) {
                        // Handle success, e.g., update UI
                        // console.log(response);
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        // Handle error, e.g., show error message
                        console.error(error);
                    }
                });
            }
            document.querySelectorAll('.checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('click', function(event) {
                    event.preventDefault();
                });
            });
        </script>
        <script>
            function updateTextBasedOnScreenSize() {
                var screenWidth = window.innerWidth;
        
                var displayTexts = document.querySelectorAll('.displayText');
                var displayTHs = document.querySelectorAll('.displayTH');
        
                if (screenWidth >= 576) {
                    displayTexts.forEach(function(displayText) {
                        displayText.textContent = 'Info Lokasi'; // Set text for larger screens
                    });
        
                    displayTHs.forEach(function(displayTH) {
                        displayTH.textContent = 'Koordinat'; // Set text for larger screens
                    });
                } else {
                    displayTexts.forEach(function(displayText) {
                        displayText.textContent = 'Info Selengkapnya'; // Set text for smaller screens
                    });
        
                    displayTHs.forEach(function(displayTH) {
                        displayTH.textContent = 'Informasi'; // Set text for smaller screens
                    });
                }
            }
        
            // Call the function initially and add an event listener for window resize
            updateTextBasedOnScreenSize();
            window.addEventListener('resize', updateTextBasedOnScreenSize);
        </script>
    </x-main-div>
</x-app-layout>