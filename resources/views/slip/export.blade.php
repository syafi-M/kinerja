<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Export Slip Gaji</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <style>
        body {
			font-family: Arial, Helvetica, sans-serif;
		}
    </style>
  </head>
  <body>
    <div
      class="rounded-md relative"
      style="user-select: none; position: relative;"
    >
      <img
        src="{{ asset('logo/desain_slip.png') }}"
        class="rounded-md"
        style="width: 100%; height: 100%; border-radius: 8px;"
      />

      <div style="z-index: 10; position: absolute; inset: 0;" class="absolute inset-0 container-data">
        @php
            $totalPenghasilan = $slip?->gaji_pokok + $slip?->gaji_lembur + $slip?->tj_jabatan + $slip?->tj_kehadiran + $slip?->tj_kinerja + $slip?->tj_lain;
            $totalPotongan = $slip?->bpjs + $slip?->pinjaman + $slip?->absen + $slip?->lain_lain;
            $totalBersih = 0;
            if($totalPotongan > 0){
                $totalBersih = $totalPenghasilan - $totalPotongan;
            }else{
                $totalBersih = $totalPenghasilan + $totalPotongan;
            }
        @endphp
        <!--bulan-->
        <p id="bulan" class="absolute font-semibold" style="left: 51.1%; top: 30.33%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ Carbon\Carbon::createFromFormat('Y-m', $slip->bulan_tahun)->isoFormat('MMMM Y') }}
        </p>
        <p id="dibuat" class="absolute font-semibold" style="right: 5.4%; top: 25.4%; position: absolute; font-weight: 400; font-size: 49px;">
          Dibuat Pada : {{ Carbon\Carbon::createFromFormat('Y-m-d', $slip->created_at->format('Y-m-d'))->isoFormat('D MMMM Y') }}
        </p>
        <!--data diri-->
        <p id="nama" class="absolute font-semibold" style="left: 20%; top: 32.7%; position: absolute; font-weight: 400; font-size: 49px; text-transform: capitalize;">
          {{ ucwords(strtolower(Auth::user()->nama_lengkap)) }}
        </p>
        <p id="jabatan" class="absolute font-semibold" style="left: 20%; top: 35.2%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ Auth::user()->divisi->jabatan->name_jabatan }}
        </p>
        <p id="mitra" class="absolute font-semibold" style="left: 20%; top: 37.7%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ Auth::user()->kerjasama->client->name }}
        </p>
        <p id="status" class="absolute font-semibold" style="left: 20%; top: 40.2%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ $slip->status ? 'Kontrak' : 'Training' }}
        </p>
        <!--penghasilan-->
        <p id="gaji_pokok" class="absolute font-semibold" style="left: 25%; top: 49.9%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->gaji_pokok) }}
        </p>
        <p id="gaji_lembur" class="absolute font-semibold" style="left: 25%; top: 52.5%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->gaji_lembur) }}
        </p>
        <p id="tj_jabatan" class="absolute font-semibold" style="left: 25%; top: 55.1%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->tj_jabatan) }}
        </p>
        <p id="tj_kehadiran" class="absolute font-semibold" style="left: 25%; top: 57.7%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->tj_kehadiran) }}
        </p>
        <p id="tj_kinerja" class="absolute font-semibold" style="left: 25%; top: 60.3%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->tj_kinerja) }}
        </p>
        <p id="tj_lain" class="absolute font-semibold" style="left: 25%; top: 62.9%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->tj_lain) }}
        </p>
        <p
          id="total_penghasilan"
          class="absolute"
          style="left: 25%; top: 67.9%; position: absolute; font-weight: bold; font-size: 49px;"
        >
          {{ toRupiah($totalPenghasilan) }}
        </p>
        <!--potongan-->
        <p id="bpjs" class="absolute font-semibold" style="left: 77%; top: 49.9%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->bpjs) }}
        </p>
        <p id="pinjaman" class="absolute font-semibold" style="left: 77%; top: 52.5%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->pinjaman) }}
        </p>
        <p id="absen" class="absolute font-semibold" style="left: 77%; top: 55.1%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->absen) }}
        </p>
        <p id="lain_lain" class="absolute font-semibold" style="left: 77%; top: 57.7%; position: absolute; font-weight: 400; font-size: 49px;">
          {{ toRupiah($slip?->lain_lain) }}
        </p>
        <p
          id="total_potongan"
          class="absolute"
          style="left: 77%; top: 67.9%; position: absolute; font-weight: bold; font-size: 49px;"
        >
          {{ toRupiah($totalPotongan) }}
        </p>
        <!--total bersih-->
        <p
          id="total_bersih"
          class="absolute"
          style="left: 39%; top: 72.1%; position: absolute; font-weight: bold; font-size: 49px;"
        >
          {{ toRupiah($totalBersih) }}
        </p>
      </div>
    </div>
  </body>
</html>
