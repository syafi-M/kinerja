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
            background-color: #cbd5e1;
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
                        <th colspan="3">Foto Progress Pekerjaan</th>
                        <th rowspan="2">Ruangan</th>
                        <th rowspan="2">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Before</th>
                        <th>Progress</th>
                        <th>After</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($expPDF as $arr)
                    
                        @if($arr->user->kerjasama_id == $mitra)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td style="text-align: center;">{{ $arr->user->nama_lengkap }}</td>
                                <td class="image-cell">
                                    <img src="{{ asset('storage/images/' . $arr->image1) }}" alt="Before Image" width="120px">
                                </td>
                                @if ($arr->image2)
                                    <td class="image-cell">
                                        <img src="{{ asset('storage/images/' . $arr->image2) }}" alt="Progress Image" width="120px">
                                    </td>
                                @else
                                    <td class="image-cell">
                                        <x-no-img />
                                    </td>
                                @endif
                                <td class="image-cell">
                                    <img src="{{ asset('storage/images/' . $arr->image3) }}" alt="After Image" width="120px">
                                </td>
                                <td style="text-align: center;">{{ $arr->ruangan->nama_ruangan }}</td>
                                <td style="padding-left: 5px; border-right: 1px solid black;">{{ $arr->keterangan }}</td>
                            </tr>
                        @elseif($arr->user->kerjasama_id != $mitra)
                        <tr>
                            <td colspan="8" style="text-align: center">KOSONG</td>
                        </tr>
                        @break
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center">KOSONG</td>
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
