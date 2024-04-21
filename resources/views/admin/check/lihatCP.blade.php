<x-app-layout>
    <style>
        
       #isiModal {
            width: 40%; /* Default width for screens wider than 640px */
        
            @media (max-width: 640px) {
                width: 100%; /* Set width to 100% for screens 640px and below */
            }
        }

    </style>
    <x-main-div>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold uppercase"> {{ $type == 'rencana' ? 'Rencana Kerja ' : 'Pekerjaan ' }} {{ $user->nama_lengkap }}</p>
            </div>
            
            <div class="flex justify-center items-center gap-2 mt-5 rounded-md">
                <div class="flex flex-col gap-2 mt-5 bg-slate-200 p-4 drop-shadow-md rounded-md w-fit">
                    <p class="text-center font-semibold text-sm"> ~>Filter<~ </p>
                    <div class="flex gap-2 justify-center sm:justify-start overflow-hidden">
                        <form action="" method="get" class="btn btn-info btn-sm overflow-hidden">
                            <input type="hidden" name="type" value="rencana" id="">
                            <button type="submit" class="overflow-hidden">Rencana</button>
                        </form>
                        {{-- <form action="" method="get" class="btn btn-warning btn-sm overflow-hidden">
                            <input type="hidden" name="type" value="" id="">
                            <button type="submit"><i class="ri-refresh-line"></i></button>
                        </form> --}}
                        <form action="" method="get" class="btn btn-info btn-sm overflow-hidden">
                            <input type="hidden" name="type" value="dikerjakan" id="">
                            <button type="submit" class="overflow-hidden">Dikerjakan</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="mx-2 sm:mx-10 my-2">
                <a href="{{ $type == 'rencana' ? route('checkpoint-user.edit', $cex2->id) : '' }}" {{ $type == 'rencana' ? '' : 'disabled' }}>
                    <button type="submit"  class="btn btn-warning btn-sm {{ $type == 'rencana' ? '' : 'btn-disabled' }}">+ Ubah Rencana Kerja</button>
                </a>
            </div>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full rounded">
                    <table class="table w-full table-xs bg-slate-50 table-zebra sm:table-md text-sm sm:text-md  md:scale-90">
                        <thead class="text-center">
                            <tr>
								<th class="bg-slate-300 rounded-tl-2xl">#</th>
								<th class="bg-slate-300 {{ $type == 'rencana' ? 'hidden' : 'table-cell' }}">Gambar Bukti</th>
								<th class="bg-slate-300">Nama CP</th>
								<th class="bg-slate-300 {{ $type == 'rencana' ? 'hidden' : 'table-cell' }}">Deskripsi</th>
								<th class="bg-slate-300">Check Point</th>
								<th class="bg-slate-300   {{ $type == 'rencana' ? 'hidden' : 'table-cell' }}">Status</th>
								{{-- <th class="bg-slate-300 displayTH" id="displayTH">Informasi</th> --}}
								<th class="bg-slate-300 rounded-tr-2xl">Action</th>
							</tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($cex2->pekerjaan_cp_id as $index => $c)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    @if ((empty($c) || $c == 'no-image.jpg') && $c != 'rencana')
                                        <td>
                                            <x-no-img class="scale-50"/>
                                        </td>
                                    @elseif ($c == 'rencana')
                                        <td class="{{ $type == 'rencana' ? 'hidden' : '' }}"></td>
                                    @else
                                        <td class="flex gap-1 {{ $type == 'rencana' ? 'hidden' : 'table-cell' }}">
                                            @if(isset($cex2->img[$index]))
                                            <img src="{{ asset('storage/images/' . $cex2->img[$index]) }}" alt="" srcset=""
                                                width="70px" class="rounded">
                                        @endif
                                        </td>
                                    @endif
                                    
                                    <td class="capitalize text-start min-w-[100px]">
                                        @php
                                            $ce = $pcp->where('id', $c)->first();
                                        @endphp
                                        @if (empty($ce))
                                            <div class="flex gap-1 ">
                                                <p>~ {{ $c }} </p>
                                                <p class="text-green-700 underline underline-offset-1 hidden sm:block"></p>
                                            </div>
                                        @else
                                            @forelse($pcp->whereIn('id', $c) as $i => $pc)
                                                @if($pc)
                                                @php
                                                    $counts = count(array_filter($cex2->pekerjaan_cp_id, function ($value) use ($pc) {
                                                        return $value == $pc->id;
                                                    }));
                                                @endphp
                                                    <div class="flex gap-1 ">
                                                        <p>~ {{ $pc->name }} </p>
                                                        <p class="text-green-700 underline underline-offset-1 hidden sm:block">@if($i < $pc->count() - 1),@endif</p>
                                                    </div>
                                                @else
                                                    <p>kosong</p>
                                                @endif
                                                
                                            @empty
                                            @endforelse
                                        @endif
                                    </td>
                                    <td class="capitalize text-start min-w-[200px] {{ $type == 'rencana' ? 'hidden' : 'table-cell' }}">
                                        @php
                                            $descriptions = $cex2->deskripsi ? $cex2->deskripsi[$index] : '';
                                        @endphp
                                        <p>~ {{ $descriptions }}</p>
                                    </td>
                                    <td class="capitalize text-start">
                                        @forelse($pcp->whereIn('id', $c) as $i => $pc)
                                            
                                            @if($pc)
                                                <p>~ {{ $pc->type_check }}</p>
                                            @endif
                                        @empty
                                        @endforelse
                                    </td>

                                    {{-- <td class="overflow-hidden">
                                        <a href="{{ Auth::user()->role_id == 2 ? route('admin-lihatMap', $cex2->id) : route('direksi-lihatMap', $cex2->id) }}"
                                            class="btn btn-sm btn-info text-xs overflow-hidden">
                                            <span id="displayText" class="displayText">Lokasi</span> <!-- Empty span to hold the text -->
                                        </a>
                                    </td> --}}

                                    
                                    <td class="min-w-[100px] {{ $type == 'rencana' ? 'hidden' : 'table-cell' }}">
                                        @if (isset($cex2->approve_status[$index]) && is_array($cex2->approve_status))
                                            @if($cex2->approve_status[$index] == 'accept')
                                                <div class="flex flex-col justify-center items-center">
                                                    <span class="badge bg-emerald-700 px-2 text-xs text-white overflow-hidden">{{ $cex2->approve_status[$index] }}</span> 
                                                    <p>Note: <br> {{ $cex2->note[$index] }}</p>
                                                </div>
                                            @elseif($cex2->approve_status[$index] == "proccess")
                                                <div class="flex flex-col justify-center items-center">
                                                    <span class="badge bg-amber-500 px-2 text-xs text-white overflow-hidden">{{ $cex2->approve_status[$index] }}</span> 
                                                </div>
                                            @else
                                                <div class="flex flex-col justify-center items-center">
                                                    <span class="badge bg-red-500 px-2 text-xs text-white overflow-hidden">{{ $cex2->approve_status[$index] }}</span>
                                                    <p>Note: <br> {{ $cex2->note[$index] }}</p>
                                                </div>
                                            @endif
                                            {{-- <p>{{ $cek->approve_status[$index] }}</p> --}}
                                        @endif
                                    </td>
                                    
                                    <td class="flex flex-col gap-2 items-center p-2 justify-center">
                                        @if ($type == 'dikerjakan')
                                        <div class="flex flex-col items-center justify-center {{ $cex2->approve_status[$index] == 'accept' ? 'hidden' : '' }}">
                                            <button id="{{ 'btn_'.$c }}" data-id="{{ $c }}" data-btn="edit" data-index-arr="{{ $index }}" class="btn btn-info btn-sm">Nilai</button>
                                        </div>
                                        
                                        @else
                                        <div class="flex flex-col items-center justify-center">
                                            @if (empty($ce))
                                                <button id="{{ 'btn_'. str_replace(' ', '_', $c) }}" data-btn="delete" data-id="{{ $cex2->id }}" data-index-arr="{{ $index }}" data-pc="{{ $c }}" class="btn btn-error btn-sm">Hapus</button>
                                            @else
                                                @forelse($pcp->whereIn('id', $c) as $i => $pc)
                                                    <button id="{{ 'btn_'. str_replace(' ', '_', $c) }}" data-btn="delete" data-id="{{ $cex2->id }}" data-index-arr="{{ $index }}" data-pc="{{ $pc->name }}" class="btn btn-error btn-sm">Hapus</button>
                                                @empty
                                                @endforelse
                                            @endif
                                        </div>
                                        
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
                <div id="div_form_nilai" class="hidden p-10 fixed overflow-hidden justify-center items-center inset-0 w-full z-[9999] bg-slate-900/50 drop-shadow-md">
                                            
                </div>
                <div id="div_form_delete" class="hidden p-10 fixed overflow-hidden justify-center items-center inset-0 w-full z-[9999] bg-slate-900/50 drop-shadow-md">
                    
                </div>
                <div class="flex justify-end gap-2  mx-5 sm:mx-10 my-2">
                    @if(Auth::user()->role_id == 2)
                        <a href="{{ route('admin.cp.index') }}" class="btn btn-error">Kembali</a>
                    @else
                        <a href="{{ route('direksi.cp.index') }}" class="btn btn-error">Kembali</a>
                    @endif
                </div>
                <div>
                        <!-- Display your modal here -->
                        <div id="modalShow" class="modalShow" style="display: none;">
                            <div
                            	style="z-index: 9000; backdrop-filter: blur(3px);" class="fixed w-full flex justify-center items-center inset-0 bg-slate-500/10 transition-all duration-300 ease-in-out h-screen">
                                <div id="isiModal" class="flex justify-center items-center">
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

                $('[id^="btn_"]').click(function() {
                    // var id = $(this).attr('id').replace('btn_', '');
                    var id = $(this).data('id');
                    var route = "{{ route('direksi.deleteRencana', '') }}/" + id; // Define route with placeholder
                    var indexArr = $(this).data('indexArr'); // Get indexArr value
                    var dataPC = $(this).data('pc'); // Get indexArr value
                    var dataBTN = $(this).data('btn');
                    var isImg = "{{ isset($cex2->img) }}";
                    var dataCex = {!! json_encode($cex2) !!};
                    var dataPcp = {!! json_encode($pcp) !!};
                    
                    // var filterData = dataPcp.filter(function(item) {
                    //     console.log(dataCex.pekerjaan_cp_id.includes(String(item.id)));
                    //     return dataCex.pekerjaan_cp_id.includes(String(item.id))
                    // });
                    var filterData = dataPcp.filter(function(item) {
                        return String(item.id) == id;
                    });
                    // console.log({!! json_encode($type) !!});
                    // Dynamically generate the HTML using template literals and insert dynamic values
                    
                    
                    // Toggle the visibility of the delete form and update its content
                    if (dataBTN == 'delete') {
                            var formHtml = `
                                <div class="flex overflow-hidden p-2 rounded-md flex-col bg-slate-200 mx-10">
                                    <div class="flex justify-end">
                                        <button id="btnClose" class="btn btn-error">&times;</button>
                                    </div>
                                    <form action="${route}" method="POST" class="flex justify-center items-center overflow-hidden p-2 flex-col">
                                        @csrf
                                        @method('DELETE')
                                        <div>
                                            <p class="text-center text-sm">Yakin ingin menghapus ${dataPC}?</p>
                                        </div>
                                        <input type="hidden" name="arrKe" value="${indexArr}" class="input input-bordered input-sm text-sm">
                                        <button type="submit" class="btn btn-info btn-sm mt-2">Submit</button>
                                    </form>
                                </div>
                            `;
                            $('#div_form_delete').toggleClass('hidden flex').html(formHtml);
                            
                        } else {
                            var formHtml2 = `
                            <div class="flex overflow-hidden p-2 px-5 rounded-md flex-col bg-slate-200 min-w-full sm:min-w-fit sm:max-w-20">
                                <div class="flex justify-end">
                                    <button id="btnClose" class="btn btn-error">&times;</button>
                                </div>
                                <form action="{{ route('direksi.uploadNilai', $cex2->id) }}" method="POST" class="flex justify-center items-center flex-col">
                                    @csrf
                                    @method('put')
                                    <div class="flex flex-col gap-2 my-2 w-full">
                                        <div>
                                            <p class="text-center font-semibold">~${filterData[0].name}~</p>
                                            </div>
                                        <div class="flex justify-center">
                                            ${isImg ? `<img src="{{ asset('storage/images/${dataCex.img[indexArr]}') }}" class="rounded" alt="" srcset="" width="70px">` : ''}
                                        </div>
                                        <div class="flex justify-start my-2">
                                            <p class="text-start font-semibold text-xs sm:text-sm line-clamp-2">${dataCex.deskripsi[indexArr]}</p>
                                        </div>
                                        <div class="">
                                            <x-input-label for="approve_status[]" class="text-center" :value="__('Status: ')" />
                                            <div class="flex gap-2 justify-around">
                                                <div>
                                                    <input type="radio" name="approve_status[]" checked placeholder="" value="accept" class="radio radio-sm radio-success">
                                                    <label for="approve_status[]" class="text-sm font-semibold">Disetujui</label>
                                                </div>
                                                <div>
                                                    <input type="radio" name="approve_status[]" placeholder="" value="denied" class="radio radio-sm radio-error">
                                                    <label for="approve_status[]" class="text-sm font-semibold">Ditolak</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full">
                                        <x-input-label for="note[]" class="" :value="__('Note: ')" />
                                        <input type="text" name="note[]" placeholder="note.." class="input input-bordered w-full input-sm text-sm">
                                    </div>
                                    <input type="hidden" name="arrKe" value="${indexArr}" class="input input-bordered input-sm text-sm">
                                    <button type="submit" class="btn btn-info btn-sm mt-2">Submit</button>
                                </form>
                            </div>`;
                            $('#div_form_nilai').toggleClass('hidden flex').html(formHtml2);
                            
                        }
                        // console.log("clicked", id, indexArr);
                    
                    $('body').addClass('overflow-hidden');
                });

                $(document).on('click', '#btnClose', function() {
                    $('#div_form_nilai').addClass('hidden').removeClass('flex').html(''); // Hide the delete form and clear its content
                    $('#div_form_delete').addClass('hidden').removeClass('flex').html(''); // Hide the delete form and clear its content
                    $('body').removeClass('overflow-hidden');
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
                        // displayText.textContent = 'Info Lokasi'; // Set text for larger screens
                    });
        
                    displayTHs.forEach(function(displayTH) {
                        // displayTH.textContent = 'Koordinat'; // Set text for larger screens
                    });
                } else {
                    displayTexts.forEach(function(displayText) {
                        displayText.textContent = 'Info Selengkapnya'; // Set text for smaller screens
                    });
        
                    displayTHs.forEach(function(displayTH) {
                        // displayTH.textContent = 'Informasi'; // Set text for smaller screens
                    });
                }
            }
        
            // Call the function initially and add an event listener for window resize
            updateTextBasedOnScreenSize();
            window.addEventListener('resize', updateTextBasedOnScreenSize);
        </script>
    </x-main-div>
</x-app-layout>