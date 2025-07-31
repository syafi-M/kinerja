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

		th {
			background-color: rgb(19, 110, 170);
			color: white;
		}

		tr:nth-child(even) {
			background-color: #e2e8f0;
		}
        
        .mtli{
            padding-left: 2px;
            padding-right:2px;
            font-size: 12px;
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
		<div>
    		<div class="title">
    			<img class="hero" src="{{ $base64 }}" width="60px">
    			<span class="sub-title" style="vertical-align: 20px; font-weight: bolder; font-size: 25px; ">Rekab Absensi PT. Surya
    				Amanah Cendekia</span>
    		</div>
    		
    		<div style="text-align: center; margin: 16px auto 12px auto; font-size: 14px; ">
                <span style="display: inline-block; font-weight: bold; ">
                    {{ $kerjasama ? $kerjasama->client->name : 'Semua Klien' }}
                </span>
                <br>
                <span style="display: inline-block;">{{ $starte->isoFormat('D MMMM Y') }} / {{ $ende->isoFormat('D MMMM Y') }}</span>
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
						<th colspan="{{ $kantor ? 7 : 6 }}">Total</th>
					</tr>

					<tr>
						@foreach ($calendarHeaders as $header)
                            <th style="{{ ($header['isWeekend'] || $header['isHoliday']) ? 'background-color: #ef4444;' : '' }} font-size: 12px; padding: 0 2px 0 2px;">
                                {{ $header['day'] }}
                            </th>
                        @endforeach
						<th class="mtli">HE</th>
						<th class="mtli">M</th>
						<th class="mtli">I</th>
						<th class="mtli">T</th>
						<th class="mtli">L</th>
						<th>%</th>
						<th style="{{ $kantor ? '' : 'display: none;' }}">Point</th>
					</tr>
				</thead>
				<tbody>
                    @php
                        $rowCount = 0;
                        $noGap = $kantor ? 13 : 26;
                        $noGap2 = $kantor ? 20 : 38;
                    @endphp
                    @foreach ($processedUsers as $userRow)
                        <!--@if ($rowCount < $noGap)-->
                        <!--    @php $rowCount++; @endphp-->
                        <!--@elseif ($rowCount == $noGap || ($rowCount - $noGap) % $noGap2 == 0)-->
                        <!--    </tbody></table></div>-->
                        <!--    <div class="table-wrapper">-->
                        <!--        <table class="border" id="myTable">-->
                        <!--            <tbody>-->
                        <!--@endif-->
                        <tr>
                            <td rowspan="{{ $kantor ? 1 : 2 }}" style="text-align: center; font-size: 14px; padding: 2px; width: 14px;">{{ $loop->iteration }}</td>
                            <td rowspan="{{ $kantor ? 1 : 2 }}" class="nama-lengkap" style="text-align: start; padding-left: 5px; font-size: 12px; width: 100px;">{{ ucwords(strtolower($userRow['user']->nama_lengkap)) }}</td>
                            <td rowspan="{{ $kantor ? 1 : 2 }}" class="nama-lengkap" style="text-align: center; padding: 0 5px; font-size: 12px; width: 40px;">{{ $userRow['user']->divisi->jabatan->code_jabatan ?? '-' }}</td>
                            @foreach ($userRow['rows'] as $day)
                                @php
                                    $bg = match ($day['symbol']) {
                                        'M' => 'background-color: #90EE90;',   // LightGreen
                                        'TP' => 'background-color: #dc2626;',   // Dark Red
                                        'T' => 'background-color: #dc2626;',   // Dark Red
                                        'I' => 'background-color: #f59e0b;',   // DeepSkyBlue
                                        'N' => 'background-color: #3b82f6;',   // LimeGreen
                                        'NT' => 'background-color: #3b82f6;',  // Tomato (for telat terus)
                                        'MS' => 'background-color: #FFD700;',  // Gold (for Menukar Shift)
                                        'ST' => 'background-color: #FFA07A;',  // LightSalmon (for Shift Tukar)
                                        default => '',
                                    };
                                @endphp
                                <td style="text-align: center; font-size: 12px; width: 18px; {{ $bg }}">{{ $day['symbol'] }}</td>
                            @endforeach
                            <td rowspan="{{ $kantor ? 1 : 2 }}" style="background-color: #7dd3fc; text-align: center; font-size: 12px; width: 18px;">{{ $userRow['totalHariKerja'] }}</td>
                            <td rowspan="{{ $kantor ? 1 : 2 }}" style="background-color: #7dd3fc; text-align: center; font-size: 12px; width: 18px;">{{ $userRow['m'] + $userRow['terus'] }}</td>
                            <td rowspan="{{ $kantor ? 1 : 2 }}" style="background-color: #7dd3fc; text-align: center; font-size: 12px; width: 18px;">{{ $userRow['z'] }}</td>
                            <td rowspan="{{ $kantor ? 1 : 2 }}" style="background-color: #7dd3fc; text-align: center; font-size: 12px; width: 18px;">{{ $userRow['t'] }}</td>
                            <td rowspan="{{ $kantor ? 1 : 2 }}" style="background-color: #7dd3fc; text-align: center; font-size: 12px; width: 18px;">{{ $libur }}</td>
                            <td rowspan="{{ $kantor ? 1 : 2 }}" style="text-align: center; font-size: 14px; width: 24px; {{ $userRow['percentage'] < 80 ? 'background-color: #f97316;' : '' }}">{{ $userRow['percentage'] }}%</td>
                            @if ($kantor)
                                <td rowspan="1" style="font-size: 12px;">{{ toRupiah($userRow['totalPoints']) }}</td>
                            @endif
                        </tr>
                        @if (!$kantor)
                            <tr>
                                @foreach ($userRow['rows'] as $day)
                                    @php
                                        $bg = match ($day['alterSymbol']) {
                                            'N' => 'background-color: #60a5fa;',   // LimeGreen
                                            'NT' => 'background-color: #3b82f6;',  // Tomato (for telat terus)
                                            'MS' => 'background-color: #FFD700;',  // Gold (for Menukar Shift)
                                            'ST' => 'background-color: #FFA07A;',  // LightSalmon (for Shift Tukar)
                                            default => '',
                                        };
                                    @endphp
                                    <td style="text-align: center; font-size: 12px; {{ $bg }}">{{ $day['alterSymbol'] }}</td>
                                @endforeach
                            </tr>
                        @endif
                        
                        @php
                            $rowCount++;
                    
                            $shouldSplit = $rowCount === $noGap || ($rowCount > $noGap && ($rowCount - $noGap) % $noGap2 === 0);
                    
                            if ($shouldSplit && !$loop->last) {
                                echo '</tbody></table></div>';
                                echo '<div class="table-wrapper"><table class="border" id="myTable"><tbody>';
                            }
                        @endphp
                    @endforeach
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
        					@foreach ($processedUsers as $item)
    	                        @if($item['user'])    
    	                        @php
    	                            $total = $item['user']->target_tunjangan * ($item['percentage'] / 100);
    	                        @endphp
            				    <tr style="padding: 2px 0 2px 0;">
            				        <td style="text-align: center; font-size: 14px;">{{ $loop->iteration }}.</td>
            				        <td style="padding-left: 8px; text-align: start; font-size: 14px;">{{ ucwords(strtolower($item['user']->nama_lengkap)) }}</td>
            				        <td style="padding-left: 8px; text-align: start; font-size: 14px;">{{ toRupiah($item['user']->target_tunjangan == 0 ? 0 : $item['user']->target_tunjangan) }}</td>
            				        <td style="padding-left: 8px; text-align: start; font-size: 14px;">{{ $item['percentage'] != 0 ? $item['percentage'] : 0 }}%</td>
            				        @if($item['percentage'] != 0)
                                        <td style="padding-left: 8px; text-align: start; font-size: 14px;">{{ toRupiah($total) }} </td>
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

		<table style="border: none; border-collapse: collapse;">
            <tbody>
                <tr style="background: none; border: none;"><td style="border: none;"><strong>HE</strong></td><td style="border: none;">: Hari Efektif</td></tr>
                <tr style="background: none; border: none;"><td style="border: none;"><strong>M</strong></td><td style="border: none;">: Hadir</td></tr>
                <tr style="background: none; border: none;"><td style="border: none;"><strong>TP</strong></td><td style="border: none;">: Tidak Pulang</td></tr>
                <tr style="background: none; border: none;"><td style="border: none;"><strong>I</strong></td><td style="border: none;">: Izin</td></tr>
                <tr style="background: none; border: none;"><td style="border: none;"><strong>T</strong></td><td style="border: none;">: Telat</td></tr>
                <tr style="background: none; border: none;"><td style="border: none;"><strong>L</strong></td><td style="border: none;">: Libur</td></tr>
        
                @if(!$kantor)
                    <tr style="background: none; border: none;"><td style="border: none;"><strong>MS</strong></td><td style="border: none;">: Menggantikan Shift</td></tr>
                    <tr style="background: none; border: none;"><td style="border: none;"><strong>ST</strong></td><td style="border: none;">: Shift Tergantikan</td></tr>
                    <tr style="background: none; border: none;"><td style="border: none;"><strong>N</strong></td><td style="border: none;">: Meneruskan Shift</td></tr>
                    <tr style="background: none; border: none;"><td style="border: none;"><strong>NT</strong></td><td style="border: none;">: Meneruskan Shift tapi telat</td></tr>
                @endif
        
                <tr style="background: none; border: none;"><td style="border: none;"><strong>-</strong></td><td style="border: none;">: Kosong</td></tr>
                <tr style="background: none; border: none;"><td style="border: none;"><strong>//</strong></td><td style="border: none;">: Libur/merah</td></tr>
            </tbody>
        </table>

		<div style="right: 25px; bottom: 250px; position:absolute;">
			<span>Ponorogo, {{ Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</span>
			<!--<span style="right: 0; top: 100px; left: 60px; position:absolute;">TTD</span>-->
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
