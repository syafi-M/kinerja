<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<style>
		*,
		body {
			font-family: Arial, Helvetica, sans-serif;
		}

		table {
			border-collapse: collapse;
			width: 100%;
		}

		table,
		th,
		td {
			border: 1px solid black;
		}

		/*td {*/
		/*	text-align: center;*/
		/*}*/

		th {
			background-color: rgb(19, 110, 170);
			color: white;
		}

		tr:nth-child(even) {
			background-color: #e2e8f0;
		}
		
		/*tr:nth-child(odd) .nama-lengkap {*/
  /*          background-color: #fcd34d;*/
  /*      }*/
    
  /*      tr:nth-child(even) .nama-lengkap {*/
  /*          background-color: #fbbf24;*/
  /*      }*/
        
        .mtli{
            padding-left: 2px;
            padding-right:2px;
        }

		.page-break {
			page-break-before: always;
		}
		.table-wrapper {
            page-break-inside: avoid;
        }
	</style>
</head>

<body>
	<main>
		@php
			$starte = \Carbon\Carbon::createFromFormat('Y-m-d', $str1);
			$ende = \Carbon\Carbon::createFromFormat('Y-m-d', $end1);
			
			$kantor = false;
			if (!isset($liburCount)) {
                $liburCount = 0; // Initialize the liburCount variable only if it's not set
            }
            $hae = 0;
            
		@endphp
		@for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
            @php
                $isHoliday = in_array($date->format('Y-m-j'), $dailyData);
                // Check if the current day is a weekend and increment the count
                if ($date->isWeekend() || $isHoliday) {
                    $liburCount++;
                }
                
                if (!$date->isWeekend() && !$isHoliday) {
                    $hae++;
                }
            @endphp
        @endfor
		<div>
    		<div class="title">
    			<img class="hero" src="{{ $base64 }}" width="60px">
    			<span class="sub-title" style="vertical-align: 20px; font-weight: bolder; font-size: 25px; ">Rekab Absensi PT. Surya
    				Amanah Cendekia</span>
    		</div>
    		
    		<div style="text-align: center; margin: 16px auto 12px auto; font-size: 14px; ">
                @foreach ($mit as $mitName)
                        @if ($mitName->id == $mitra)
                            @if($mitName->id == 1)
                                @php
                                    $kantor = true;
                                @endphp
                            @endif
                            <span style="display: inline-block; font-weight: bold; ">
                                {{ $mitName->client->name }}
                            </span>
                        @endif
                @endforeach
                <br>
                <span style="display: inline-block;">{{ $starte->isoFormat('D-MMMM-Y') }} / {{ $ende->isoFormat('D-MMMM-Y') }}</span>
            </div>


		</div>
		<div class="table-wrapper">
			<table class="border" id="myTable">
				<thead>
					<tr>
						<th rowspan="2" style="font-size: 14px; text-align: center;">#</th>
						<th rowspan="2">Nama</th>
						<th rowspan="2">Jab.</th>
						<th colspan="{{ $totalHari + 1 }}" style="font-size: 14px;">Rekab Bulanan</th>
						<th colspan="{{ $kantor ? 7 : 8 }}">Total</th>
					</tr>

					<tr>
						@for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
    						@php
                                $isWeekend = $date->isWeekend();
                                $isHoliday = in_array($date->format('Y-m-j'), $dailyData);
                            @endphp
    						<th style="{{$isWeekend || $isHoliday ? "background-color: #ef4444" : ""}}; font-size: 14px; padding: 0 2px 0 2px;">{{ $date->format('d') }}</th>
						@endfor
						<th class="mtli">HE</th>
						<th class="mtli">M</th>
						<th class="mtli">I</th>
						<th class="mtli">T</th>
						<th class="mtli">L</th>
						@if(!$kantor)
						    <th class="mtli">MS</th>
						    <th class="mtli">ST</th>
						@endif
						<th>%</th>
						<th style="{{ $kantor ? '' : 'display: none;' }}">Point</th>
					</tr>
				</thead>
				<tbody>
					@php
						$sortedData = collect($expPDF)->sortBy('user.nama_lengkap');
						$previousUser = null;
						$n = 1;
						$rowCount = 0;
						if($kantor){
						    $noGap = 13;
						    $noGap2 = 20;
						}else{
						    $noGap = 26;
						    $noGap2 = 28;
						}
					@endphp
					
					@forelse ($sortedData as $data)
						@if ($previousUser != $data->nama_lengkap)
						@if ($rowCount < $noGap)
    						@php
                                $rowCount++;
                            @endphp
                        @elseif ($rowCount == $noGap)
                            </tbody>
                            </table>
                            </div>
                            <div class="table-wrapper">
                            <table class="border" id="myTable">
                            <tbody>
                        @elseif (($rowCount - $noGap) % $noGap2 == 0)
                            <!-- Close the table and div to start a new page -->
                            </tbody>
                            </table>
                            </div>
                            <!-- Open a new table wrapper for subsequent pages -->
                            <div class="table-wrapper">
                            <table class="border" id="myTable">
                            <tbody>
                        @endif
						    @if($data->nama_lengkap != 'admin' && $data->nama_lengkap != 'user' && $data->nama_lengkap != 'SUBHAN SANTOSA')
							<tr>
								<!--Valid name cuy-->
								@php
									$previousUser = $data->nama_lengkap;
									$userAbsensi = collect($expPDF)->where('user', $data->user);
								@endphp
								<td style="text-align: center; font-size: 14px;" rowspan="{{ $kantor ? '1' : '2' }}">{{ $n++ }}</td>
								<td class="nama-lengkap" rowspan="{{ $kantor ? '1' : '2' }}" style="text-align: start; padding-left: 5px; font-size: 12px;">{{ $data->nama_lengkap }}</td>
								<td class="nama-lengkap" rowspan="{{ $kantor ? '1' : '2' }}" style="text-align: center; padding-left: 5px; padding-right: 5px; font-size: 12px;">{{ $data->divisi->jabatan->code_jabatan }}</td>
								@for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
    									@php
    									    $isHoliday = in_array($date->format('Y-m-j'), $dailyData);
    										$absensi = $data->absensi->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
                                            $nerus = $data->absensi->where('terus', '!=', null)->first(function ($item) use ($date){
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
    										$ngizin = $izin->where('user_id', $data->id)->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
                                            
                                            $isDateInRange = $date->gte($starte) && $date->lte($ende);
                                            
    										$keterangan = $absensi ? $absensi->keterangan : '-';
    										$keterus = $nerus ? $nerus->terus : '';
    										$jadiIzin = $ngizin ? $ngizin->approve_status : '';
    									@endphp
    									@if ($isDateInRange)
                                            @if ($keterangan == 'masuk' && $keterus == null)
                                                <td style="background-color: rgb(112, 226, 112); text-align: center; font-size: 14px;">M</td>
                                            @elseif($keterus == 1)
                                                <td style="background-color: rgb(112, 226, 112); text-align: center; font-size: 14px;">M|K</td>
                                            @elseif($jadiIzin == 'accept')
                                                <td style="background-color: rgb(250, 114, 65); text-align: center; font-size: 14px;">I</td>
                                            @elseif($keterangan == 'telat')
                                                <td style="background-color:rgb(202, 5, 5); text-align: center; font-size: 14px;">T</td>
                                            @else
                                                @if($date->isWeekend() || $isHoliday)
                                                    <td style="text-align: center; font-size: 14px;">//</td>
                                                @else
                                                    <td style="text-align: center; font-size: 14px;">-</td>
                                                @endif
                                            @endif
                                        @endif
    									
								@endfor
								@php
									$startDate = $user->min('created_at')->startOfMonth();
									$endDate = $user->max('created_at')->endOfMonth();
									$hari = $totalHari ;
									$period = Carbon\CarbonPeriod::create($startDate, $endDate);
									$numberOfDays = $period->count();
									
									$uid = $data->id;
									
									
									$m = $data->absensi->where('keterangan', 'masuk')->count();
									$z = $hitungIzin->where('approve_status', 'accept')->where('user_id', $uid)->count();
									$t = $data->absensi->where('keterangan', 'telat')->count();
									$k = $data->absensi->where('terus', '!=', null)->count();
									
									$ms = $data->absensi->where('tukar', '!=', null)->count();
									$st = $data->absensi->where('tukar_id', $uid)->count();
									
									if($kantor){
									    $lib = $liburCount + $libur;
									}else{
									    $lib = $libur;
									}
									
									$total = $m + $z + $t;
									$hlibur = ($hae - $libur) - $lib;
									
									if ($total != 0) {
                                        if ($hlibur >= 0) {
                                            $totalPercentage = round($m / ($hari - $lib) * 100);
                                            if($kantor){
                                            
                                                $tesPer = round(($m + $t) / $hae * 100); 
                                                

                                            }else{
                                                $tesPer = round(($m + $t + $ms + $k) / ($hari - $lib + 1) * 100); 
                                            }
                                            $total = $tesPer >= 100 ? 100 : $tesPer;
                                        } else {
                                            $total = "libur";
                                        }
                                    } else {
                                        $total = 0;
                                    }
                                    
                                    if($data->kerjasama_id == 1){
                                    
									    $totP = $data->absensi->where('point_id', '!=', null);
                                        $dataToSendOutsideLoop[] = [
                                            'nama' => $data->nama_lengkap,
                                            'tesPersentage' => $tesPer,
                                            'total' => $total,
                                            'target' => $data->target_tunjangan,
                                            'kerjasama' => $data->where('kerjasama_id', 1),
                                        ];
                                    }
                                    
                                    
								@endphp
							
								
								<td id="hae" rowspan="{{ $kantor ? '1' : '2' }}" style="background-color: #7dd3fc; text-align: center; width: 20px; font-size: 14px;">{{ $kantor ? $hae - $libur : $hari - $libur + 1 }}</td>
								<td id="masuk" rowspan="{{ $kantor ? '1' : '2' }}" style="background-color: #7dd3fc; text-align: center; width: 20px; font-size: 14px;">{{ $kantor ? $m : $m + $k }}</td>
								<td id="izin" rowspan="{{ $kantor ? '1' : '2' }}" style="background-color: #7dd3fc; text-align: center; width: 20px; font-size: 14px;">{{ $z }}</td>
								<td id="telat" rowspan="{{ $kantor ? '1' : '2' }}" style="background-color: #7dd3fc; text-align: center; width: 20px; font-size: 14px;">{{ $t }}</td>
								<td id="libur" rowspan="{{ $kantor ? '1' : '2' }}" style="background-color: #7dd3fc; text-align: center; width: 20px; font-size: 14px;">{{ $lib }}</td>
								@if(!$kantor)
								    <td id="ms" rowspan="{{ $kantor ? '1' : '2' }}" style="background-color: #7dd3fc; text-align: center; width: 20px; font-size: 14px;">{{ $ms }}</td>
								    <td id="st" rowspan="{{ $kantor ? '1' : '2' }}" style="background-color: #7dd3fc; text-align: center; width: 20px; font-size: 14px;">{{ $st }}</td>
								@endif
								@if ($total >= 80)
									<td id="persen" rowspan="{{ $kantor ? '1' : '2' }}" style="text-align: center;">{{ $total }}%</td>
								@elseif($total == "libur" || $total == 0)
									<td id="persen" rowspan="{{ $kantor ? '1' : '2' }}" style="background-color: #f97316; text-align: center;">0%</td>
								@elseif($total <= 80)
									<td id="persen" rowspan="{{ $kantor ? '1' : '2' }}" style="background-color: #f97316; text-align: center; font-size: 14px;">{{ $total }}%</td>
								@else
								    <td>Kosong</td>
								@endif
								    @if($data->kerjasama_id == 1)
    								    <td style="font-size: 12px;" rowspan="{{ $kantor ? '1' : '2' }}">
    								        @php
                                                $totalPoints = 0;
                                            @endphp
    								        @foreach($totP as $tesz)
        								        @php
                                                    $totalPoints += intval($tesz->point->sac_point);
                                                @endphp
    								        @endforeach
    								        {{toRupiah($totalPoints)  }}
    								    </td>
								    @endif
							</tr>
							@if(!$kantor)
							<tr>
							    @for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
    									@php
    									    $isHoliday = in_array($date->format('Y-m-j'), $dailyData);
    										$absensi = $data->absensi->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
    										$ngizin = $izin->where('user_id', $data->id)->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
                                            
                                            $isDateInRange = $date->gte($starte) && $date->lte($ende);
                                            
    										$keterangan = $absensi ? $absensi->keterangan : '-';
    										$jadiIzin = $ngizin ? $ngizin->approve_status : '';
    									@endphp
    									@if ($isDateInRange)
                                            @if ($absensi?->tukar)
                                                <td style="background-color: rgb(112, 226, 112); text-align: center; font-size: 14px;">MS</td>
                                            @elseif($absensi?->tukar_id == $data->id)
                                                <td style="background-color: rgb(250, 114, 65); text-align: center; font-size: 14px;">ST</td>
                                            @else
                                                @if($date->isWeekend() || $isHoliday)
                                                    <td style="text-align: center; font-size: 14px;">//</td>
                                                @else
                                                    <td style="text-align: center; font-size: 14px;">-</td>
                                                @endif
                                            @endif
                                        @endif
    									
								@endfor
							</tr>
							@endif
							@endif
						@php
                            // Your PHP code here
                            $rowCount++;
                        @endphp
						@endif
					@empty
						<td colspan="31" class="text-center" style="text-align: center;">Kosong</td>
					@endforelse
				</tbody>
			</table>
		</div>
		
	
		    
		    
		    @if($kantor)
    		        <section class="page-break">
            		<div class="title">
            			<img class="hero" src="{{ $base64 }}" width="60px">
            			<span class="sub-title" style="vertical-align: 20px; font-weight: bolder; font-size: 25px;">PT. Surya
            				Amanah Cendekia</span>
            			<span></span>
            		</div>
            		<div class="table-wrapper">
            			<table class="border" id="myTable">
            				<thead style="">
            					<tr>
            						<th>No.</th>
            						<th>Nama lengkap</th>
            						<th>Tunjangan</th>
            						<th>Persen</th>
            						<th>Hasil</th>
            					</tr>
            				</thead>
            				<tbody >
            				@php
        				        $no = 1;
        				    @endphp
        					@foreach ($dataToSendOutsideLoop as $item)
    	                        @if($item['kerjasama'])    
            	    			
            				    <tr style="padding: 2px 0 2px 0;">
            				        <td style="text-align: center; font-size: 14px;">{{ $no++ }}.</td>
            				        <td style="padding-left: 8px; text-align: start; font-size: 14px;">{{ $item['nama'] }}</td>
            				        <td style="padding-left: 8px; text-align: start; font-size: 14px;">{{ toRupiah($item['target'] == 0 ? 0 : $item['target']) }}</td>
            				        <td style="padding-left: 8px; text-align: start; font-size: 14px;">{{ $item['total'] != 0 ? $item['tesPersentage'] : 0 }}%</td>
            				        @if($item['total'] != 0)
                                        <td style="padding-left: 8px; text-align: start; font-size: 14px;">{{ toRupiah($item['target'] * ( $item['tesPersentage'] / 100 )) }} </td>
                                    @else 
                                        <td style="padding-left: 8px; text-align: start; font-size: 14px;">Rp.0 </td>
                                    @endif
            				    </tr>
            				    
            				   @endif
                            @endforeach
            				</tbody>
            			</table>
            		</div>
            	</section>
		    @endif
		

		<h2 style="padding-top: 10px" class="page-break">Keterangan</h2>

		<ul>
			<li>
			    <span>HE </span>
			    <span>: Hari Efektif</span>
			</li>
			<li>M : Hadir</li>
			<li>I : Izin</li>
			<li>T : Telat</li>
			<li>L : Libur</li>
			@if(!$kantor)
			<li>MS : Menggantikan Shift</li>
			<li>ST : Shift Tergantikan</li>
			<li>K : Meneruskan Shift</li>
			@endif
			<li>- : Kosong</li>
		</ul>

		<div style="right: 25px; position:absolute;">
			<span>Ponorogo, {{ Carbon\Carbon::now()->format('d-m-Y') }}</span>
			<span style="right: 0; top: 100px; left: 60px; position:absolute;">TTD</span>
		</div>
		<span style="right: 0; bottom: 150px; position:absolute;">PT. Surya Amanah Cendekia</span>
	</main>
	
	

	@if ($jdwl)
	
	{{-- section jadwal user --}}
	<section class="page-break">
		<div class="title">
			<img class="hero" src="{{ $base64 }}" width="60px">
			<span class="sub-title" style="vertical-align: 20px; font-weight: bolder; font-size: 25px;">Jadwal PT. Surya
				Amanah Cendekia</span>
			<span></span>
		</div>
		<div>
			<table>
				<thead>
					<tr>
						<th rowspan="2">No.</th>
						<th rowspan="2">Nama lengkap</th>
						<th colspan="{{ $totalHari + 1 }}">Tanggal</th>
					</tr>
					@php
						$starte = \Carbon\Carbon::createFromFormat('Y-m-d', $str1);
						$ende = \Carbon\Carbon::createFromFormat('Y-m-d', $end1);
					@endphp
					<tr>
						@for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
							<th class="p-2 bg-stone-300 border-r-slate-400 border-r-[1.1px]">{{ $date->format('d') }}</th>
						@endfor
					</tr>
				</thead>
				<tbody>
					@php
						$sortedData = collect($expPDF)->sortBy('user.nama_lengkap');
						$previousUser = null;
						$n = 1;
					@endphp
					@forelse ($sortedData as $data)
						@if ($previousUser != $data->nama_lengkap)
							<tr>
								<!--Valid name cuy-->
								@php
									$previousUser = $data->nama_lengkap;
									$userAbsensi = collect($expPDF)->where('user', $data->user);
								@endphp
								<td>{{ $n++ }}</td>
								<td>{{ $data->nama_lengkap }}</td>
								@for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
									@php
										$jadwal = $data->jadwalUser->firstWhere('tanggal', $date->format('Y-m-d'));
										$hasil = $jadwal ? $jadwal->status : 'off';
									@endphp
									@if ($hasil == 'M')
										<td>{{ $hasil }}</td>
									@else
										<td style="background-color: yellow;  font-size: 15px;">{{ $hasil }}</td>
									@endif
								@endfor
							</tr>
						@endif
					@empty

					@endforelse
				</tbody>
			</table>
		</div>
	</section>
	@endif
</body>

</html>
