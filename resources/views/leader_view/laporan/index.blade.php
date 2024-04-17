<x-app-layout>
    <x-main-div>
        
        <div id="mDiv" class="py-10 sm:mx-10">
            <p class="text-center text-lg sm:text-2xl uppercase font-bold ">Riwayat Laporan, {{ Auth::user()->kerjasama->client->name }}</p>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="flex items-center w-full justify-center sm:justify-end my-5">
                    <x-search />
                </div>
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full ">
                    <table id="searchTable" class="table w-full table-xs table-zebra sm:table-md bg-slate-50 text-xs font-semibold sm:text-md ">
                        <thead>
							<tr >
								<th class="p-1 py-2 bg-slate-300 rounded-tl-2xl">#</th>
                                <th class="p-1 py-2 bg-slate-300 text-center" style="min-width: 230px;" colspan="5">Foto Progres</th>
                                @if(Auth::user()->divisi->jabatan->code_jabatan == 'MITRA')
								    <th class="p-1 py-2 bg-slate-300">Area</th>
								    <th class="p-1 py-2 bg-slate-300">Keterangan</th>
								    <th class="p-1 py-2 bg-slate-300 text-center">Tanggal</th>
								    <th class="p-1 py-2 bg-slate-300 rounded-tr-2xl">Action</th>
                                @else
								    <th class="p-1 py-2 bg-slate-300">Area</th>
								    <th class="p-1 py-2 bg-slate-300">Keterangan</th>
								    <th class="p-1 py-2 bg-slate-300 rounded-tr-2xl text-center">Tanggal</th>
                                @endif
							</tr>
						</thead>
                        <tbody>
                            @php
                                $n = 1;
                            @endphp
                            @forelse ($laporan as $i)
                            <tr>
                                <td class="p-1">{{ $n++ }}</td>
                                @if ($i->image1 == 'no-image.jpg')
                                <td>
                                    <x-no-img style="width: 90px;"/>
                                </td>
                                @elseif (Storage::disk('public')->exists('images/' . $i->image1) && $i->image1 != null)
                                    <td><img id="image1" src="{{ asset('storage/images/' . $i->image1) }}" alt="{{ asset('storage/images/' . $i->image1) }}" srcset="{{ asset('storage/images/' . $i->image1) }}" width="90px" data-index="{{ $loop->index }}" data-image="{{ $i->image1 }}" data-imgName="image1" class="rounded imeg"></td>
                                @else
                                 <td>
                                    <x-no-img style="width: 90px;" />
                                 </td>
                                @endif
                                
                                
                                @if ($i->image2 == 'no-image.jpg')
                                <td>
                                    <x-no-img style="width: 90px;" />
                                </td>
                                @elseif (Storage::disk('public')->exists('images/' . $i->image2) && $i->image2 != null)
                                    <td><img id="image2" src="{{ asset('storage/images/' . $i->image2) }}" alt="{{ asset('storage/images/' . $i->image2) }}" srcset="{{ asset('storage/images/' . $i->image2) }}" width="90px" data-index="{{ $loop->index }}" data-image="{{ $i->image2 }}" data-imgName="image2" class="rounded imeg"></td>
                                @else
                                    <td>
                                        <x-no-img style="width: 90px;" />
                                    </td>
                                @endif
                                
                                
                                @if ($i->image3 == 'no-image.jpg')
                                <td>
                                    <x-no-img style="width: 90px;" />
                                </td>
                                @elseif (Storage::disk('public')->exists('images/' . $i->image3) && $i->image3 != null)
                                    <td><img id="image3" src="{{ asset('storage/images/' . $i->image3) }}" alt="{{ asset('storage/images/' . $i->image3) }}" srcset="{{ asset('storage/images/' . $i->image3) }}" width="90px" data-index="{{ $loop->index }}" data-image="{{ $i->image3 }}" data-imgName="image3" class="rounded imeg"></td>
                                @else
                                    <td>
                                        <x-no-img style="width: 90px;" />
                                    </td>
                                @endif
                                
                                @if ($i->image4 == 'no-image.jpg')
                                <td>
                                    <x-no-img style="width: 90px;" />
                                </td>
                                @elseif (Storage::disk('public')->exists('images/' . $i->image4) && $i->image4 != null)
                                    <td><img id="image4" src="{{ asset('storage/images/' . $i->image4) }}" alt="{{ asset('storage/images/' . $i->image4) }}" srcset="{{ asset('storage/images/' . $i->image4) }}" width="90px" data-index="{{ $loop->index }}" data-image="{{ $i->image4 }}" data-imgName="image4" class="rounded imeg"></td>
                                @else
                                    <td>
                                        <x-no-img style="width: 90px;" />
                                    </td>
                                @endif
                                
                                @if ($i->image5 == 'no-image.jpg')
                                <td>
                                    <x-no-img style="width: 90px;" />
                                </td>
                                @elseif (Storage::disk('public')->exists('images/' . $i->image5) && $i->image5 != null)
                                    <td><img id="image5" src="{{ asset('storage/images/' . $i->image5) }}" alt="{{ asset('storage/images/' . $i->image5) }}" srcset="{{ asset('storage/images/' . $i->image5) }}" width="90px" data-index="{{ $loop->index }}" data-image="{{ $i->image5 }}" data-imgName="image5" class="rounded imeg"></td>
                                @else
                                    <td>
                                        <x-no-img style="width: 90px;" />
                                    </td>
                                @endif
                                
                                @if(Auth::user()->devisi_id == 8)
                                    <td class="break-words whitespace-pre-wrap" style="width: 270px;">{{ $i->ruangan ? $i->ruangan->nama_ruangan : "Belum Ada" }}</td>
                                    <td>{{ $i->keterangan }} <br>~{{ $i->user->nama_lengkap }}</td>
                                    <td style="width: 140px; text-align: center;">{{ $i->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="overflow-hidden ">
                                            <a href="{{ route('mitra_laporan.show', $i->id) }}" class="text-sky-400 hover:text-sky-500 text-xl transition-all ease-linear .2s"><i class="ri-eye-fill"></i></a>
                                        </div>
                                    </td>
                                @else
                                    <td class="break-words whitespace-pre-wrap" style="width: 270px;">{{ $i->ruangan ? $i->ruangan->nama_ruangan : "Belum Ada" }}</td>
                                    <td>{{ $i->keterangan }} <br>~{{ $i->user->nama_lengkap }}</td>
                                    <td style="width: 140px; text-align: center;">{{ $i->created_at->format('Y-m-d') }}</td>
                                @endif
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        ~ Data Kosong ~
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                    <div id="pag-1" class="mt-5 mb-5 mx-10">
                        {{ $laporan->links() }}
                    </div>
                    <div class="flex justify-center sm:justify-end w-full">
		                @if(Auth::user()->divisi->code_jabatan == "CO-CS")
            			    <a href="{{ route('leaderView') }}" class="btn btn-error">Kembali</a>
            		    @elseif(Auth::user()->divisi->jabatan->code_jabatan == "CO-SCR")
            			    <a href="{{ route('danruView') }}" class="btn btn-error">Kembali</a>
            		    @else
            			    <a href="{{ route('dashboard.index') }}" class="btn btn-error">Kembali</a>
            		    @endif
                    </div>
                    
                    <div id="divImeg" style="z-index: 9000; gap: 1rem; display: none; backdrop-filter: blur(8px); " class="fixed top-0 w-full bg-slate-500/10 min-h-screen flex flex-col justify-center items-center overflow-hidden">
                        <div  style="z-index: 9001; inset: 0px; padding: 20px;" class=" top-0 bg-slate-100 rounded-lg flex flex-col justify-center items-center">
                            <div id="showImage" class="flex justify-center items-center">
                                
                            </div>
                        </div>
                        <div class="flex justify-center items-center">
                            <button id="closeMod" class="btn btn-error">&times;</button>
                        </div>
                    </div>
            </div>
        </div>
    <script>
        $(document).ready(function() {
            var getData = {!! json_encode($laporan) !!};
            $('.imeg').click(function() {
                var imegId = $(this).attr('id');
                var index = $(this).data('index');
                var imageName = $(this).data('imgname');
                var imegUri = '{{ asset('storage/images/') }}/' + getData.data[index][imageName];
                
                $('#showImage').html(
                    `<img src="${imegUri}" width="360">`
                )
                $('#divImeg').fadeIn('fast');
                
                $(window).on('scroll.disableScroll', function() {
                    $(this).scrollTop(0).scrollLeft(0);
                });
            
                // Disable scrolling on the document
                $(document).on('scroll.disableScroll', function() {
                    $(this).scrollTop(0).scrollLeft(0);
                });
            
                // Disable touchmove events
                $(document).on('touchmove.disableScroll', function(event) {
                    event.preventDefault();
                });
            
                // Disable mousewheel events
                $(document).on('mousewheel.disableScroll', function(event) {
                    event.preventDefault();
                });
                
                
                // console.log(imegId, getData.data[index][imageName], imageName);
            })
            $('#closeMod').click(function(){
                $('#divImeg').fadeOut('fast');
                
                $(window).off('scroll.disableScroll');
                $(document).off('scroll.disableScroll');
                $(document).off('touchmove.disableScroll');
                $(document).off('mousewheel.disableScroll');
            })
        })
    </script>
    </x-main-div>
</x-app-layout>