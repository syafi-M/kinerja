<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<head>
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
	</style>
</head>

<body>
	<main>
		@php
			$starte = \Carbon\Carbon::createFromFormat('Y-m-d', $str1);
			$ende = \Carbon\Carbon::createFromFormat('Y-m-d', $end1);
		@endphp
		<div>
    		<div class="title">
    			<img class="hero" src="{{ $base64 }}" width="60px">
    			<span class="sub-title" style="vertical-align: 20px; font-weight: bolder; font-size: 25px; ">Rekab Absensi PT. Surya
    				Amanah Cendekia</span>
    		</div>
    		<div style="text-align: center; margin: 16px auto 12px auto; font-size: 14px; ">
                @foreach ($mit as $mitName)
                        @if ($mitName->id == $mitra)
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
			<table class="border">
				<thead>
					<tr>
						<th rowspan="2">No.</th>
						<th rowspan="2">Nama</th>
						<th rowspan="2">Jabatan</th>
						<th colspan="{{ $totalHari + 1 }}">Rekab Bulanan</th>
						<th colspan="5">Total</th>
					</tr>

					<tr>
						@for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
							<th>{{ $date->format('d') }}</th>
						@endfor
						<th class="mtli">M</th>
						<th class="mtli">I</th>
						<th class="mtli">T</th>
						<th class="mtli">L</th>
						<th>Persentase</th>
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
						    @if($data->nama_lengkap != 'admin' && $data->nama_lengkap != 'user' && $data->nama_lengkap != 'SUBHAN SANTOSA')
							<tr>
								<!--Valid name cuy-->
								@php
									$previousUser = $data->nama_lengkap;
									$userAbsensi = collect($expPDF)->where('user', $data->user);
								@endphp
								<td style="text-align: center;">{{ $n++ }}</td>
								<td class="nama-lengkap" style="text-align: start; padding-left: 5px;">{{ $data->nama_lengkap }}</td>
								<td class="nama-lengkap" style="text-align: center; padding-left: 5px;">{{ $data->divisi->jabatan->code_jabatan }}</td>
								@for ($date = $starte->copy(); $date->lte($ende); $date->addDay())
    									@php
    										$absensi = $data->absensi->firstWhere('tanggal_absen', $date->format('Y-m-d'));
    										$ngizin = $izin->where('user_id', $data->id)->first(function ($item) use ($date) {
                                                return $item->created_at->format('Y-m-d') === $date->format('Y-m-d');
                                            });
    										$keterangan = $absensi ? $absensi->keterangan : '-';
    										$jadiIzin = $ngizin ? $ngizin->approve_status : '';
    									@endphp
    									@if ($keterangan == 'masuk')
    										<td style="background-color: rgb(112, 226, 112); text-align: center;">M</td>
    									@elseif($jadiIzin == 'accept')
    										<td style="background-color: rgb(250, 114, 65); text-align: center;">I</td>
    									@elseif($keterangan == 'telat')
    										<td style="background-color:rgb(202, 5, 5); text-align: center;">T</td>
    									@else
    										<td style="text-align: center;">-</td>
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
									
									$total = $m + $z;
									$hlibur = $hari - $libur;
									
									if ($total != 0) {
                                        if ($hlibur >= 0) {
                                            $totalPercentage = round($m / ($hari - $libur) * 100);
                                            $total = ($totalPercentage >= 100) ? 100 : $totalPercentage;
                                        } else {
                                            $total = "libur";
                                        }
                                    } else {
                                        $total = 0;
                                    }
									
								@endphp
								<td id="masuk" style="background-color: #7dd3fc; text-align: center;">{{ $m }}</td>
								<td id="izin" style="background-color: #7dd3fc; text-align: center;">{{ $z }}</td>
								<td id="telat" style="background-color: #7dd3fc; text-align: center;">{{ $t }}</td>
								<td id="libur" style="background-color: #7dd3fc; text-align: center;">{{ $libur }}</td>
								@if ($total >= 80)
									<td id="persen" style="text-align: center;">{{ $total }}%</td>
								@elseif($total == "libur")
									<td id="persen" style="background-color: #f97316; text-align: center;">0%</td>
								@elseif($total <= 80)
									<td id="persen" style="background-color: #f97316; text-align: center;">{{ $total }}%</td>
								@endif
							</tr>
							@endif
						@endif
					@empty
						<td colspan="31" class="text-center" style="text-align: center;">Kosong</td>
					@endforelse
				</tbody>
			</table>
		</div>

		<h2 style="padding-top: 10px">Keterangan</h2>

		<ul>
			<li><span class="box-color true"></span>M : Hadir</li>
			<li><span class="box-color false"></span> I : Izin</li>
			<li><span class="box-color false"></span> T : Telat</li>
			<li><span class="box-color false"></span> L : Libur</li>
			<li><span class="box-color false"></span> - : Kosong</li>
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
