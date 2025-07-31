<x-app-layout>
	<x-main-div>
	    <style>
	        .divCal {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 70vh;
                overflow: hidden;
            }
            .calendar-container {
                width: 350px;
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            @media (width <= 48rem) { 
                .calendar-container {
                    scale: 85%;
                }
                .divCal {
                    height: 40vh;
                }
            }
            .calendar-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
                font-weight: 500;
            }
            .calendar-header button {
                background: none;
                border: none;
                font-size: 18px;
                cursor: pointer;
            }
            .calendar-grid {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 5px;
            }
            .day-header, .day {
                padding: 8px;
                text-align: center;
                font-size: 12px;
            }
            .day-header {
                font-weight: bold;
                background: #ddd;
            }
            .day {
                background: #fff;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            .empty {
                background: transparent;
                box-shadow: none;
            }
            .faded {
                opacity: 50%;
            }
            .weekend {
                background: #ffebeb; /* Light red */
                color: #d9534f; /* Slightly darker red for text */
            }
            
            .friday {
                background: #e6ffe6; /* Light green */
                color: #28a745; /* Slightly darker green for text */
            }
            
            .cuti {
                background-color: #FFC9C9; /* Gold color */
                color: #d9534f;
                font-weight: bold;
                text-decoration: line-through;
            }
            
            .strikethrough {
                position: relative;
                color: #A65F00; /* Optional: Makes it look faded */
                background-color: #FFDF20;
            }
            
            .strikethrough::before {
                content: '';
                position: absolute;
                left: 35%;
                right: 35%;
                top: 70%; /* Adjusts to center */
                height: 1px;
                background-color: #A65F00;
            }
            
            .double-strikethrough {
                position: relative;
                color: #016630;
                background-color: #7BF1A8;
            }
            
            .double-strikethrough::before,
            .double-strikethrough::after {
                content: '';
                position: absolute;
                height: 1px;
                background: #016630;
            }
            
            .double-strikethrough::before {
                left: 35%;
                right: 35%;
                top: 70%; /* First line */
            }
            
            .double-strikethrough::after {
                left: 30%;
                right: 30%;
                top: 80%; /* Second line */
            }
            
            .cross-strikethrough {
                position: relative;
                color: #C10007;
                background-color: #FF6467 !important;
            }
            
            .cross-strikethrough::before,
            .cross-strikethrough::after {
                content: '';
                position: absolute;
                height: 1px;
                background: #C10007;
            }
            
            .cross-strikethrough::before {
                left: 35%;
                right: 35%;
                top: 80%; /* First line */
                rotate: 45deg;
            }
            
            .cross-strikethrough::after {
                left: 35%;
                right: 35%;
                top: 80%; /* Second line */
                rotate: -45deg;
            }

	    </style>
		<div class="py-10">
			<p class="text-center text-lg sm:text-2xl uppercase pb-10 font-bold ">Riwayat kehadiran Saya</p>
    		<!--kalendar-->
    		<div class="divCal">
    		    <div class="calendar-container">
                    <div class="calendar-header">
                        <form method="GET">
                            <input type="hidden" name="search" value="{{ $filter ? Carbon\Carbon::parse($filter)->subMonth(1)->format('Y-m') : Carbon\Carbon::now()->subMonth(1)->format('Y-m') }}">
                            <button type="submit"><i class="ri-arrow-drop-left-line font-bold" style="font-size: 3rem; line-height: 1;"></i></button>
                        </form>
                        
                        <h3 style="font-size: 22px;">{{ \Carbon\Carbon::create($year, $month)->isoFormat('MMMM Y') }}</h3>
                        
                        <form method="GET">
                            <input type="hidden" name="search" value="{{ $filter ? Carbon\Carbon::parse($filter)->addMonth(1)->format('Y-m') : Carbon\Carbon::now()->addMonth(1)->format('Y-m') }}">
                            <button type="submit"><i class="ri-arrow-drop-right-line font-bold" style="font-size: 3rem; line-height: 1;"></i></button>
                        </form>
                    </div>
                
                    <div class="calendar-grid">
                        <div class="day-header">Min</div>
                        <div class="day-header">Sen</div>
                        <div class="day-header">Sel</div>
                        <div class="day-header">Rab</div>
                        <div class="day-header">Kam</div>
                        <div class="day-header">Jum</div>
                        <div class="day-header">Sab</div>
                
                        @php
                            $day = 1;
                            $prevMonth = \Carbon\Carbon::create($year, $month, 1)->subMonth();
                            $nextMonth = \Carbon\Carbon::create($year, $month, 1)->addMonth();
                            $daysInPrevMonth = $prevMonth->daysInMonth;
                            $weeks = ceil(($startOfMonth + $daysInMonth) / 7);
                            
                            $datAbsen = $absen->keyBy(fn($a) => Carbon\Carbon::parse($a->created_at)->day);
                            $holidays = $harLib->keyBy(fn($h) => \Carbon\Carbon::parse($h['tanggal'])->format('Y-m-d'));
                        @endphp
                        
                        @for ($week = 0; $week < $weeks; $week++)
                            @for ($dow = 0; $dow < 7; $dow++)
                                @php 
                                    $classes = ''; 
                                    $classPrev = ''; 
                                    $classNext = ''; 
                        
                                    // Handle previous month's days correctly
                                    if ($week == 0 && $dow < $startOfMonth) {
                                        $prevDay = $daysInPrevMonth - ($startOfMonth - $dow - 1);
                                        $prevDate = Carbon\Carbon::create($prevMonth->year, $prevMonth->month, $prevDay)->format('Y-m-d');
                                        $holidayP = $holidays->get($prevDate);
                                        if ($holidayP) $classPrev .= 'cuti ';
                                    } 
                                    // Handle next month's days correctly
                                    elseif ($day > $daysInMonth) {
                                        $nextDay = $day - $daysInMonth;
                                        $nextDate = Carbon\Carbon::create($nextMonth->year, $nextMonth->month, $nextDay)->format('Y-m-d');
                                        $holidayN = $holidays->get($nextDate);
                                        if ($holidayN) $classNext .= 'cuti ';
                                    }
                                    // Handle current month's days
                                    else {
                                        $currentDate = Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
                                        $att = $datAbsen[$day] ?? null;
                        
                                        if ($dow == 0) $classes .= 'weekend '; // Sunday
                                        if ($dow == 5) $classes .= 'friday ';  // Friday (Optional)
                        
                                        if ($att) {
                                            if ((!is_null($att->absensi_type_masuk) && is_null($att->absensi_type_pulang)) || $att->keterangan == 'telat') {
                                                $classes .= 'strikethrough ';
                                            } elseif (!is_null($att->absensi_type_masuk) && !is_null($att->absensi_type_pulang)) {
                                                $classes .= 'double-strikethrough ';
                                            } elseif (!is_null($att->absensi_type_masuk) && is_null($att->absensi_type_pulang) && $att->created_at->format('Ymd') == Carbon::now()->format('Ymd')) {
                                                $classes .= 'cross-strikethrough ';
                                            }
                                        }
                        
                                        // Holiday Check
                                        $holiday = $holidays->get($currentDate);
                                        if ($holiday) $classes .= 'cuti ';
                                    }
                                @endphp
                                
                                {{-- Render Previous Month Days --}}
                                @if ($week == 0 && $dow < $startOfMonth)
                                    <div class="day faded {{ $classPrev }} {{ $dow == 0 ? 'weekend' : ($dow == 5 ? 'friday' : '') }}">
                                        {{ $prevDay }}
                                    </div>
                                
                                {{-- Render Next Month Days --}}
                                @elseif ($day > $daysInMonth)
                                    <div class="day faded {{ $classNext }} {{ $dow == 0 ? 'weekend' : ($dow == 5 ? 'friday' : '') }}">
                                        {{ $nextDay }}
                                    </div>
                                    @php $day++ @endphp
                                
                                {{-- Render Current Month Days --}}
                                @else
                                    <div class="day {{ $classes }}">
                                        {{ $day }}
                                    </div>
                                    @php $day++ @endphp
                                @endif
                            @endfor
                        @endfor

                    </div>
                </div>
    		</div>
    		<div class="flex justify-center items-center" style="margin: 2rem 2rem 0 2rem;">
                <div class="bg-white rounded-md p-2 w-full" style="max-width: 300pt;">
                    <p class="text-sm font-semibold">Keterangan:</p>
                    <div class="flex items-center">
                        <div class="day double-strikethrough" style="width: 35px; scale: 70%;">
                            <i class="ri-infinity-line"></i>
                        </div>
                        <p class="text-xs" style="margin-left: 20px;">: Berhasil Absen</p>
                    </div>
                    <div class="flex items-center">
                        <div class="day strikethrough" style="width: 35px; scale: 70%;">
                            <i class="ri-infinity-line"></i>
                        </div>
                        <p class="text-xs" style="margin-left: 20px;">: Belum Absen Pulang / Telat</p>
                    </div>
                    <div class="flex items-center">
                        <div class="day cross-strikethrough" style="width: 35px; scale: 70%;">
                            <i class="ri-infinity-line"></i>
                        </div>
                        <p class="text-xs" style="margin-left: 20px;">: Tidak Absen Pulang</p>
                    </div>
                    <div class="flex items-center">
                        <div class="day cuti" style="width: 35px; scale: 70%;">
                            <i class="ri-infinity-line"></i>
                        </div>
                        <p class="text-xs" style="margin-left: 20px;">: Tgl Merah</p>
                    </div>
                </div>
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
								<th class="bg-slate-300 {{ Auth::user()->jabatan->code_jabatan == 'SPV-W' ? 'hidden' : '' }}">Telat/Tidak</th>
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
								<tr style="{{ Auth::user()->kerjasama_id == 1 ? (($arr->point_id == 1) ? 'background-color: rgba(37, 178, 79, 0.2);' : (($arr->point_id == 2) ? 'background-color: rgba(254, 153, 0, 0.2)' : 'background-color: rgba(178, 37, 37, 0.2)') ) : '' }} ">
									<td>{{ $no++ }}.</td>
									@if(Auth::user()->name != "DIREKSI")
									<td>{{ $arr->shift?->shift_name }}</td>
									@endif
									<td>{{ $arr->tanggal_absen }}</td>
									<td class="text-center">{{ $arr->absensi_type_masuk }}</td>
									@if(Auth::user()->kerjasama_id == 1)
								        <td class="text-center">{!! $arr->dzuhur ? "Sudah Absen" : '<span class="text-red-500 font-semibold uppercase">Belum Absen</span>' !!}</td>
									@endif
									{{-- Handle Absensi Type Pulang --}}
									<td class="text-center">
									    {!!$arr->absensi_type_pulang == null ? '<span class="text-red-500 underline font-bold">Belum Absen Pulang</span>' : $arr->absensi_type_pulang  !!}
									</td>
									{{-- End Handle Absensi Type Pulang --}}
									@if(Auth::user()->name != "DIREKSI")
    									@php
    									    $jam_abs =  $arr->absensi_type_masuk;
    									    $jam_abslen = strlen($jam_abs);
    									    
    									    $jam_str = $arr?->shift?->jam_start;
    									    $jam_strlen = strlen($jam_str);
    									    
    									    $jAbs = Carbon\Carbon::createFromFormat($jam_abslen == 5 ? 'H:i' : 'H:i:s', $jam_abs);
    									    $jJad = Carbon\Carbon::createFromFormat($jam_strlen == 5 ? 'H:i' : 'H:i:s', $jam_str ? $jam_str : '00:00:00');
    									    
                                            if(Auth::user()->kerjasama_id == 1){
                                                $jam_strlen == 5 ? $jJad : $jJad->addSeconds(59);
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
									<td class="text-center {{ Auth::user()->jabatan->code_jabatan == 'SPV-W' ? 'hidden' : '' }}">
									    @if(Auth::user()->kerjasama_id == 11 && $arr->created_at->format('Y-m-d') > '2025-06-03')
									        @if($arr->shift?->jam_start && Carbon\Carbon::parse($arr->absensi_type_masuk)->gt(Carbon\Carbon::parse($arr->shift->jam_start)))
									            <span style="color: red">Telat {{ $diffHasil }}</span>
									        @else
									            <span>Tidak</span>
									        @endif
									    @else
    									    {!! $arr->absensi_type_masuk > $arr?->shift?->jam_start ? 
    									        '<span style="color: red">' . 'Telat  ' .  $diffHasil . '</span>' 
    									        : '<span>Tidak</span>' !!}
									    @endif
									</td>
									@php
                                        $badgeClass = 'badge text-white gap-2 overflow-hidden';
                                        if ($arr->keterangan == 'masuk' && $arr->absensi_type_pulang !== null) {
                                            if (Auth::user()->kerjasama_id == 11 && $arr->created_at->format('Y-m-d') > '2025-06-03' && Carbon\Carbon::parse($arr->absensi_type_masuk)->gt(Carbon\Carbon::parse($arr->shift->jam_start))) {
                                                $badgeClass .= ' badge-error';
                                                $label = 'Telat';
                                            } else {
                                                $badgeClass .= ' badge-success';
                                                $label = $arr->keterangan;
                                            }
                                        } elseif ($arr->keterangan == 'izin') {
                                            $badgeClass .= ' badge-warning';
                                            $label = $arr->keterangan;
                                        } else {
                                            $badgeClass .= ' badge-error';
                                            $label = 'Tidak Masuk';
                                        }
                                    @endphp
                                    
                                    <td class="flex flex-col justify-center items-center" style="width: 180px;">
                                        <div class="{{ $badgeClass }}">
                                            <p>{{ $label }}</p>
                                        </div>
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
            				    @if(auth()->user()->name == "MEI" || auth()->user()->name == "ZAKY")
            				        Poin Anda {{ $absen->whereIn('user_id', ['424', '423'])->count() }}
            				    @else
            				        {{!empty($totalPointsPerUser) ? "Point Anda Sekarang" . toRupiah(array_sum($totalPointsPerUser)) : "~ Point Belum Di Peroleh ~" }}
            				    @endif
            				</span>
        				</div>
    				@endif
			<!--MODAL-->
                <div x-data="{opModal: false}">
                    <div>
                        <button @click="opModal = true" class="btn">Lihat Persentase Kehadiran</button>
                    </div>
                    <!-- Display your modal here -->
                    <template x-if="opModal">
                        <div x-cloak x-show="opModal">
                            <div
                            	style="z-index: 9000; backdrop-filter: blur(1px);" class="fixed w-full flex justify-center items-center inset-0 bg-slate-500/10 transition-all duration-300 ease-in-out h-screen">
                                <div class="flex justify-center items-center">
                                	<div class="bg-slate-200 inset-0 w-fit p-3 mx-10 my-10 rounded-md shadow">
                                		<div class="flex justify-end mb-3">
                                			<button @click="opModal = false" class="btn btn-error scale-90 closeButton">&times;</button>
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
                    </template>
				</div>
			<!--END MODAL-->
				
				
		</div>
		<div id="pag-1" class="mt-5 mb-5 mx-10">
			{{ $absen->links() }}
		</div>
		<div class="flex justify-center sm:justify-end" style="margin-top: 5px;">
			<a href="{{ route('dashboard.index') }}" class="btn btn-error mx-2 sm:mx-10">Kembali</a>
		</div>
</x-main-div>
</x-app-layout>
