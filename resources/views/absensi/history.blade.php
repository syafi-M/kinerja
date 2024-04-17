<x-app-layout>
	<x-main-div>
		<div class="py-10">
			<p class="text-center text-lg sm:text-2xl uppercase pb-10 font-bold ">Riwayat kehadiran Saya</p>
			<form action="{{ url('historyAbsensi') }}" method="GET" class="flex justify-center mx-2 sm:mx-10">
				<span class="p-4 rounded-md bg-slate-300">
					<label class="sm:mx-10 mx-5 label label-text font-semibold text-xs sm:text-base">Pilih Bulan</label>
					<div class="join  sm:mx-10 scale-[80%] sm:scale-100">
						<input type="month" placeholder="pilih bulan..." class="join-item input input-bordered" name="search"
							id="search" />
						<button type="submit" class="btn btn-info join-item">Filter</button>
					</div>
				</span>
			</form>
					
    		<div class="{{ Auth::user()->kerjasama_id == 1 ? 'flex text-sm bg-slate-100 sm:w-fit p-2 rounded-md font-semibold': 'hidden' }}" style="place-items: start; margin-left: 12vw; margin-right: 12vw; margin-top: 10px;">
    		    Note : 
    		    <span class="flex flex-col gap-2 mx-5 text-xs" style="font-style: italic">
    		        <div class="flex items-center gap-2">
    		               <span class="px-3 text-white rounded-md" style="background-color: rgb(51, 153, 25);">Hijau</span> <span>Point Di Klaim</span>
    		            
    		        </div>
    		        <div class="flex items-center gap-2">
    		               <span class="px-2 text-white rounded-md" style="background-color: rgba(178, 37, 37);">Merah</span> <span>Point Tidak Di Klaim</span>
    		            
    		        </div>
    		    </span>
    		</div>
    		
			<div class="flex flex-col items-center mx-2 sm:justify-center justify-start" >
				<div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full">
				<table class="table w-full table-xs bg-slate-50 sm:table-md text-sm sm:text-md scale-90 md:scale-90 {{ Auth::user()->kerjasama_id != 1 ? 'table-zebra' : "" }}">
						<thead>
							<tr class="text-center">
								<th class="bg-slate-300 rounded-tl-2xl">#</th>
								@if(Auth::user()->name != "DIREKSI")
								    <th class="bg-slate-300 px-7">Shift</th>
								@endif
								<th class="bg-slate-300 px-7">Tanggal</th>
								<th class="bg-slate-300">Absen Masuk</th>
								@if(Auth::user()->kerjasama_id == 1)
								    <th class="bg-slate-300">Absen Siang(dzuhur)</th>
								@endif
								<th class="bg-slate-300 px-5">Absen Keluar</th>
								<th class="bg-slate-300 rounded-tr-2xl" >Status</th>
							</tr>
						</thead>
						<tbody>
							@php
								$no = 1;
								$totalPointsPerUser = [];
							    $userId = Auth::user()->id;
							@endphp
							@foreach ($absen as $arr)
    							@php
    							    if($arr->user_id == $userId && $arr->point_id != null && $arr->point->client_id == Auth::user()->kerjasama->client_id){
                                        $point = intval($arr->point->sac_point);
        							    if (isset($totalPointsPerUser[$userId])) {
                                            $totalPointsPerUser[$userId] += $point;
                                        } else {
                                            $totalPointsPerUser[$userId] = $point;
                                        }
    							    }
    							@endphp
    							
    							    
								@if (Auth::user()->id != $arr->user_id)
									@continue
									<tr>
										<td colspan="6" class="text-center">
											<div class="my-10 mx-3">
												<h2>Tidak Ada History Absen</h2>
											</div>
										</td>
									</tr>
								@break

							@else
								<tr style="{{ Auth::user()->kerjasama_id == 1 ? (($arr->point_id == 1) ? 'background-color: rgba(37, 178, 79, 0.2);' : (($arr->point_id == 2) ? 'background-color: rgba(254, 153, 0, 0.2)' : 'background-color: rgba(178, 37, 37, 0.2)') ) : '' }}">
									<td>{{ $no++ }}</td>
									@if(Auth::user()->name != "DIREKSI")
									<td>{{ $arr->shift?->shift_name }}</td>
									@endif
									<td>{{ $arr->tanggal_absen }}</td>
									<td class="text-center">{{ $arr->absensi_type_masuk }}</td>
									@if(Auth::user()->kerjasama_id == 1)
								        <td class="text-center">{!! $arr->dzuhur ? "Sudah Absen Dzuhur" : '<span class="text-red-500 font-semibold uppercase">Belum Absen Dzuhur</span>' !!}</td>
									@endif
									{{-- Handle Absensi Type Pulang --}}
									<td class="text-center">
									    {!!$arr->absensi_type_pulang == null ? '<span class="text-red-500 underline font-bold">Belum Absen Pulang</span>' : $arr->absensi_type_pulang  !!}
									</td>
									{{-- End Handle Absensi Type Pulang --}}
									@if(Auth::user()->name != "DIREKSI")
    									@php
    									    $jam_abs = $arr->created_at->format('H:i:s');
    									    $jam_abslen = strlen($jam_abs);
    									    
    									    $jam_str = $arr->shift->jam_start;
    									    $jam_strlen = strlen($jam_str);
    									    
    									    $jAbs = Carbon\Carbon::createFromFormat($jam_abslen == 5 ? 'H:i' : 'H:i:s', $jam_abs);
    									    $jJad = Carbon\Carbon::createFromFormat($jam_strlen == 5 ? 'H:i' : 'H:i:s', $jam_str);
    									    
                                            if(Auth::user()->kerjasama_id == 1){
                                                $jam_strlen == 5 ? $jJad->addMinutes(31): $jJad->addMinutes(31)->addSeconds(59);
                                            }
                                            $jDiff = $jAbs->diff($jJad);
                                        
                                            $diffHasil = '';
                                            if ($jDiff->h > 0) {
                                                $diffHasil .= $jDiff->format('%h Jam ');
                                            }
                                            if ($jDiff->i > 0) {
                                                $diffHasil .= $jDiff->format('%i Menit ');
                                            }
                                            if ($jDiff->s > 0 && $jDiff->h == 0 && $jDiff->i == 0) {
                                                $diffHasil .= $jDiff->format('%s Detik');
                                            }
                                            
                                            // Trim and display the result
                                            $diffHasil = trim($diffHasil);
                                           
    									@endphp
    									<span data-jad="{{ $jam_str }} {{ $jam_strlen }} {{ $jJad }}" data-abs="{{ $jam_abs }} {{ $jam_abslen }} {{ $jAbs }}" data-diff="{{ $diffHasil }}" id="test" class="hidden test"></span>
									@endif

									{{-- Handle Keterangan --}}
									@if(Auth::user()->name != 'DIREKSI')
									<td class="flex flex-col justify-center items-center" style="width: 180px;">
									   {!! $arr->keterangan == 'masuk' ? '<div class="badge badge-success gap-2 overflow-hidden">' . $arr->keterangan . '</div>' 
									   : ($arr->keterangan == 'izin' ? '<div class="badge badge-warning gap-2 overflow-hidden">' . $arr->keterangan . '</div>' 
									   : '<div class="badge badge-error gap-1 overflow-hidden">' . '<p>' . $arr->keterangan . '</p>' . '<p style="">' . $diffHasil . '</p>' . '</div>') !!}
									   
									</td>
									@else
									    <td></td>
									@endif
								</tr>
									{{-- EndHandle Keterangan  . '<p>' . $diffHasil . '</p>' --}}
							@endif
							{{-- EndHandle Point Samping --}}
						@endforeach
					</tbody>
				</table>
			</div>
    				
			
    				@if(Auth::user()->kerjasama_id == 1)
        				<div class="flex items-center justify-center px-5 py-2 m-2 rounded-md shadow-md" style="background-color: #00670A;">
            				<span class ="text-center text-white font-semibold">
            				    {{!empty($totalPointsPerUser) ? "Point Anda Sekarang" . toRupiah(array_sum($totalPointsPerUser)) : "~ Point Belum Di Peroleh ~" }}
            				</span>
        				</div>
    				@endif
			<!--MODAL-->
                <div>
                    <button id="btnShow" class="btn">Lihat Persentase Kehadiran</button>
                </div>
                <div>
                        <!-- Display your modal here -->
                        <div id="modalShow" class="modalShow" style="display: none;">
                            <div
                            	style="z-index: 9000; backdrop-filter: blur(1px);" class="fixed w-full flex justify-center items-center inset-0 bg-slate-500/10 transition-all duration-300 ease-in-out h-screen">
                                <div class="flex justify-center items-center">
                                	<div class="bg-slate-200 inset-0 w-fit p-3 mx-10 my-10 rounded-md shadow">
                                		<div class="flex justify-end mb-3">
                                			<button id="closeButton" class="btn btn-error scale-90 closeButton">&times;</button>
                                		</div>
                                		<div>
                                		    @if($status == "BAIK")
                                			    <div class="flex items-center justify-center px-5 py-2 m-2 rounded-md shadow-md" style="background-color: #00670A;">
                                    				<span class ="text-center text-white font-semibold">
                                    				    Persentase Kehadiran {{ $persentase > 100 ? "100%" : round($persentase) . "%"}}
                                    				    <br />
                                    				    <span class="bg-white px-2 rounded-md text-red-500">
                                    				        Telat {{ $telat }} kali
                                    				    </span>
                                    				    <br />
                                    				    Status Kehadiran {{ $status}}
                                    				</span>
                                				</div>
                                				@elseif($status == "CUKUP")
                                			    <div class="flex items-center justify-center px-5 py-2 m-2 rounded-md shadow-md" style="background-color: #663300;">
                                					<span class ="text-center text-white font-semibold">
                                    				    Persentase Kehadiran {{ round($persentase) . "%"}}
                                    				    <br />
                                    				    <span class="bg-white px-2 rounded-md text-red-500">
                                    				        Telat {{ $telat }} kali
                                    				    </span>
                                    				    <br />
                                    				    Status Kehadiran {{ $status}}
                                    				</span>
                                				</div>
                                				@else
                                			    <div class="flex items-center justify-center px-5 py-2 m-2 rounded-md shadow-md" style="background-color: #660000;">
                                					<span class ="text-center text-white font-semibold">
                                    				    Persentase Kehadiran {{ round($persentase) . "%"}}
                                    				    <br />
                                    				    <span class="bg-white px-2 rounded-md text-red-500">
                                    				        Telat {{ $telat }} kali
                                    				    </span>
                                    				    <br />
                                    				    Status Kehadiran {{ $status}}
                                    				</span>
                                				</div>
                                				
                                				@endif
                                		</div>
                                	</div>
                                </div>
                            </div>
                        </div>
				</div>
			<!--END MODAL-->
				
				
		</div>
		<div id="pag-1" class="mt-5 mb-5 mx-10">
			{{ $absen->links() }}
		</div>
		<div class="flex justify-center sm:justify-end">
			<a href="{{ route('dashboard.index') }}" class="btn btn-error mx-2 sm:mx-10">Kembali</a>
		</div>
		<script>
		    $(document).ready(function() {
                $('#btnShow').click(function() {
                    $('#modalShow').toggle(); // Show/hide content
                });
                $('#closeButton').click(function() {
                    $('#modalShow').toggle(); // Show/hide content
                });
            });
		</script>
</x-main-div>
</x-app-layout>
