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
						<button type="submit" class="btn btn-info join-item">search</button>
					</div>
				</span>
			</form>
			<div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
				<div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full">
					<table class="table w-full table-xs bg-slate-50 table-zebra sm:table-md text-sm sm:text-md scale-90 md:scale-90">
						<thead>
							<tr class="text-center">
								<th class="bg-slate-300 rounded-tl-2xl">#</th>
								<th class="bg-slate-300 px-7">Shift</th>
								<th class="bg-slate-300 px-7">Tanggal</th>
								<th class="bg-slate-300">Absen Masuk</th>
								<th class="bg-slate-300">Absen Siang(dzuhur)</th>
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
								<tr>
									<td>{{ $no++ }}</td>
									<td>{{ $arr->shift->shift_name }}</td>
									<td>{{ $arr->tanggal_absen }}</td>
									<td class="text-center">{{ $arr->absensi_type_masuk }}</td>
								    <td class="text-center">{!! $arr->dzuhur ? "Sudah Absen" : '<span class="text-red-500 font-semibold uppercase">Belum Absen</span>' !!}</td>
									{{-- Handle Absensi Type Pulang --}}
									<td class="text-center">
									    {!!$arr->absensi_type_pulang == null ? '<span class="text-red-500 underline font-bold">Belum Absen Pulang</span>' : $arr->absensi_type_pulang  !!}
									</td>
									{{-- End Handle Absensi Type Pulang --}}
									
									@php
									    $jAbs = Carbon\Carbon::createFromFormat('H:i:s', $arr->created_at->format('H:i:s'));
                                        $jJad = Carbon\Carbon::createFromFormat('H:i', $arr->shift->jam_start)->format('H:i:s');
                                    
                                        // Convert $jJad to Carbon instance to perform the diff operation
                                        $jJad = Carbon\Carbon::createFromFormat('H:i:s', $jJad);
                                        if(Auth::user()->kerjasama_id == 1){
                                            $jJad->addMinutes(31);
                                            $jJad->addSeconds(59);
                                        }
                                    
                                        // Check if both $jAbs and $jJad are Carbon instances before using diff()
                                        if ($jAbs instanceof Carbon\Carbon && $jJad instanceof Carbon\Carbon) {
                                            $jDiff = $jAbs->diff($jJad);
                                            
                                            $hours = $jDiff->h == 0 ? '' : ($jDiff->h < 10 ? $jDiff->h : (string)$jDiff->h) . ':';
                                            $minutes = $jDiff->i == 0 ? '' : ($jDiff->i < 10 ? $jDiff->i : (string)$jDiff->i) . ':';
                                            $seconds = $jDiff->s == 0 ? '' : ($jDiff->s < 10 ? $jDiff->s : (string)$jDiff->s) . '';
                                            
                                            $diffHasil = trim("$hours$minutes$seconds");
                                            
                                            if($jDiff->h != 0){
                                            
								                $diffHasil = $diffHasil." Jam";
                                            }
								            else if($jDiff->i != 0){
								            
								                $diffHasil = $diffHasil. " Menit";
								            }
								            else if($jDiff->s != 0){
								                $diffHasil = $diffHasil." Detik";
								            }
                                        } else {
                                            $diffHasil = '0';
                                        }
									@endphp

									{{-- Handle Keterangan --}}
									<td class="flex flex-col justify-center items-center" style="width: 160px;">
									   {!! $arr->keterangan == 'masuk' ? '<div class="badge badge-success gap-2 overflow-hidden">' . $arr->keterangan . '</div>' 
									   : ($arr->keterangan == 'izin' ? '<div class="badge badge-warning gap-2 overflow-hidden">' . $arr->keterangan . '</div>' 
									   : '<div class="badge badge-error gap-2 overflow-hidden">' . '<p>' . $arr->keterangan . '</p>' . '<p>' . $diffHasil . '</p>' . '</div>') !!}
									   
									</td>
								</tr>
									{{-- EndHandle Keterangan --}}
							@endif
							{{-- EndHandle Point Samping --}}
						@endforeach
					</tbody>
				</table>
			</div>
			
				<div class="flex items-center justify-center px-5 py-2 m-2 rounded-md shadow-md" style="background-color: #00670A;">
    				@if(Auth::user()->kerjasama_id == 1)
    				<span class ="text-center text-white font-semibold">
    				    {{!empty($totalPointsPerUser) ? "Point Anda Sekarang" . toRupiah(array_sum($totalPointsPerUser)) : "~ Point Belum Di Peroleh ~" }}
    				</span>
    				@endif
				</div>
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
                                    				    Persentase Kehadiran {{ $persentase . "%"}}
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
                                    				    Persentase Kehadiran {{ $persentase . "%"}}
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
                                    				    Persentase Kehadiran {{ $persentase . "%"}}
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
