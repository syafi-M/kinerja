
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
			font-size: 12px;
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
            $rowCounter = 0;
		@endphp
		<div>
    		<div class="title">
    			<img class="hero" src="{{ $base64 }}" width="60px">
    			<span class="sub-title" style="vertical-align: 20px; font-weight: bolder; font-size: 25px; ">Rekab Absen Sholat PT. Surya
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
						<th rowspan="2">Ibadah</th>
						<th colspan="{{ $totalHari + 1 }}" style="font-size: 14px;">Rekab Bulanan</th>
					</tr>
					<tr>
						@for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
    						@php
                                $isWeekend = $date->isWeekend();
                                $isHoliday = in_array($date->format('Y-m-j'), $dailyData);
                            @endphp
    						<th style="{{$isWeekend || $isHoliday ? "background-color: #ef4444" : ""}}; font-size: 14px; padding: 0 2px 0 2px;">{{ $date->format('d') }}</th>
						@endfor
					</tr>
				</thead>
				<tbody style="text-align: center;">
					@php
						$sortedData = collect($expPDF)->sortBy('user.nama_lengkap');
						$previousUser = null;
					@endphp
					@forelse ($sortedData as $index => $data)
						@if ($previousUser != $data->nama_lengkap)
						    @php 
								$previousUser = $data->nama_lengkap; 
								$rowCounter++;
							@endphp
							@if ($rowCounter == 7 || ($rowCounter > 7 && ($rowCounter - 6) % 7 == 1))
								<tr class="page-break"></tr>
							@endif
							<tr>
							    <td rowspan="5" style="width: 20px; border-top: 2px solid black; border-bottom: 2px solid black;">{{ $index + 1 }}</td>
							    <td rowspan="5" style="border-top: 2px solid black; border-bottom: 2px solid black;">{{ $data->nama_lengkap }}</td>
							    <td rowspan="5" style="border-top: 2px solid black; border-bottom: 2px solid black;">{{ $data?->jabatan->code_jabatan }}</td>
							    <td style="font-size: 10px; border-top: 2px solid black;">S</td>
							    @for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
							        @php
							            $absensi = $data->absensi->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
							        @endphp
							        
							        <td style="{{ $absensi?->subuh == 1 ? 'background-color: rgb(112, 226, 112);' : '' }} border-top: 2px solid black;"></td>
							    @endfor
							</tr>
							<tr>
							    <td style="font-size: 10px;">D</td>
							    @for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
							        @php
							            $absensi = $data->absensi->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
							        @endphp
							        
							        <td style="{{ $absensi?->dzuhur == 1 ? 'background-color: rgb(112, 226, 112);' : '' }}"></td>
							    @endfor
							</tr>
							<tr>
							    <td style="font-size: 10px;">A</td>
							    @for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
							        @php
							            $absensi = $data->absensi->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
							        @endphp
							        
							        <td style="{{ $absensi?->asar == 1 ? 'background-color: rgb(112, 226, 112);' : '' }}"></td>
							    @endfor
							</tr>
							<tr>
							    <td style="font-size: 10px;">M</td>
							    @for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
							        @php
							            $absensi = $data->absensi->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
							        @endphp
							        
							        <td style="{{ $absensi?->maghrib == 1 ? 'background-color: rgb(112, 226, 112);' : '' }}"></td>
							    @endfor
							</tr>
							<tr>
							    <td style="font-size: 10px; border-bottom: 2px solid black;">I</td>
							    @for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
							        @php
							            $absensi = $data->absensi->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
							        @endphp
							        
							        <td style="{{ $absensi?->isya == 1 ? 'background-color: rgb(112, 226, 112);' : '' }} border-bottom: 2px solid black;"></td>
							    @endfor
							</tr>
						@endif
					@empty
					    <tr>
    						<td colspan="{{ $totalHari + 5 }}" class="text-center" style="text-align: center;">Kosong</td>
					    </tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<h2 style="padding-top: 10px" class="page-break">Keterangan</h2>
		<ul>
		    <li>Jab. : Jabatan</li>
		    <li>S    : Subuh</li>
		    <li>D    : Dzuhur</li>
		    <li>A    : Asar</li>
		    <li>M    : Magrib</li>
		    <li>I    : Isya</li>
		    <li>
              <div style="width: 10px; height: 10px; background-color: rgb(112, 226, 112); border: 1px solid black; display: inline-block; vertical-align: middle;"></div>
              <span style="display: inline-block; vertical-align: middle;">: Sudah Absen</span>
            </li>
		    <li>
              <div style="width: 10px; height: 10px; border: 1px solid black; display: inline-block; vertical-align: middle;"></div>
              <span style="display: inline-block; vertical-align: middle;">: Belum Absen</span>
            </li>
		</ul>

		<div style="right: 25px; position:absolute;">
			<span>Ponorogo, {{ Carbon\Carbon::now()->format('d-m-Y') }}</span>
			<span style="right: 0; top: 100px; left: 60px; position:absolute;">TTD</span>
		</div>
		<span style="right: 0; bottom: 150px; position:absolute;">PT. Surya Amanah Cendekia</span>
	</main>
</body>

</html>