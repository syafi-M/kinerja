<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid black;
        }
        table, th, td {
            border: 1px solid black;
        }
        td {
            text-align: start;
        }
        th {
            background-color: rgb(19, 110, 170);
            color: white;
            border: 1px solid black;
        }
        tr:nth-child(even) {
            /*background-color: #cbd5e1;*/
        }
        .page-break {
            page-break-before: always;
        }
        .title {
            text-align: center;
            margin-top: 20px;
            font-size: 25px;
        }
        .hero {
            width: 60px;
            vertical-align: middle;
        }
        .sub-title {
            font-weight: bolder;
        }
        .date-range {
            text-align: center;
            font-size: 14px;
            margin: 16px auto 12px auto;
        }
        .client-name {
            font-weight: bold;
        }
        .table-wrapper {
            margin: 0 auto;
        }
        .border {
            border: 1px solid black;
        }
        .image-cell {
            width: 120px;
            text-align: center;
        }
    </style>
</head>
<body>
    <main>
        @php
            $starte = \Carbon\Carbon::createFromFormat('Y-m-d', $str1);
            $ende = \Carbon\Carbon::createFromFormat('Y-m-d', $end1);
        @endphp
        <!--<div class="title">-->
        <!--    <img class="hero" src="{{ $base64 }}" alt="Company Logo">-->
        <!--    <span class="sub-title">Laporan PT. Surya Amanah Cendekia</span>-->
        <!--</div>-->
        <div class="date-range">
            <span class="client-name">FOTO KEGIATAN KEBERSIHAN CLEANING SERVICE</span><br>
            <span class="client-name">PT SURYA AMANAH CENDIKIA</span><br>
            <span class="client-name" style="text-transform: uppercase;">AREA {{$kerjasama->client->name }}</span>
            <br>
            @if($starte->isoFormat('MMMM-Y') == $ende->isoFormat('MMMM-Y'))
                <span style="text-transform: uppercase;" class="client-name">PERIODE {{ $starte->isoFormat('MMMM Y') }}</span>
            @else
                <span style="text-transform: uppercase;" class="client-name">PERIODE {{ $starte->isoFormat('MMMM') }} - {{ $ende->isoFormat('MMMM Y') }}</span>
            @endif
        </div>
        <div class="table-wrapper">
            <table class="border">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">Nama</th>
                        <th colspan="5">Foto Progress Pekerjaan</th>
                        <th rowspan="2">Ruangan</th>
                        <th rowspan="2">Pekerjaan</th>
                        <th rowspan="2">Nilai</th>
                        <th rowspan="2">Tanggal</th>
                    </tr>
                    <tr>
                        <th colspan="5">Progres Pengerjaan</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($expPDF as $arr)
                    
                            <tr>
                                <td style=" font-size: 12px; text-align: center;">{{ $no++ }}.</td>
                                <td style="text-align: center; font-size: 12px;">{{ $arr->user->nama_lengkap }}</td>
                                    <td style="text-align: center;">
                                        @if($arr->image1)
                                            <img src="{{ asset('storage/images/' . $arr->image1) }}" style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto;" alt="Before Image" width="80px">
                                        @else
                                            <div style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto; width: 80px;"></div>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if ($arr->image2)
                                            <img src="{{ asset('storage/images/' . $arr->image2) }}" style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto;" alt="Progress Image" width="80px">
                                        @else
                                            <div style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto; width: 80px;"></div>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($arr->image3)
                                            <img src="{{ asset('storage/images/' . $arr->image3) }}" style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto;" alt="After Image" width="80px">
                                        @else
                                            <div style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto; width: 80px;"></div>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($arr->image4)
                                            <img src="{{ asset('storage/images/' . $arr->image4) }}" style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto;" alt="4 Image" width="80px">
                                        @else
                                            <div style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto; width: 80px;"></div>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if($arr->image5)
                                            <img src="{{ asset('storage/images/' . $arr->image5) }}" style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto;" alt="5 Image" width="80px">
                                        @else
                                            <div style="display: block; padding-left: 4px; padding-right: 4px; margin: 0 auto; width: 80px;"></div>
                                        @endif
                                    </td>
                                <td style="text-align: center;font-size: 12px;">{{ $arr->ruangan?->nama_ruangan }}</td>
                                <td style="font-size: 10px; text-align: center;">
                                    @php
                                        $array = json_decode($arr->pekerjaan);
                                        $formattedString = $array != null ? implode(', ', $array) : "";
                                    @endphp
                                    {{$formattedString}}
                                </td>
                                <td style="font-size: 12px; text-align: center; width: 20px;">{{ $arr->nilai }}</td>
                                <td style="border-right: 1px solid black; font-size: 12px; width: 90px; text-align: center;">{{ $arr->created_at->format('Y-m-d') }}</td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center">Data Belum Ada</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
    <script>
        window.print();
    </script>
</body>
</html>
