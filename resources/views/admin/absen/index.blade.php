<x-app-layout>
    <x-main-div>
        <div class="py-10">
            <p class="text-center text-2xl font-bold py-5 uppercase">Data Absensi</p>
            <div class="flex justify-between items-center mx-10">
                <div class="flex justify-between items-center w-full">
                    <div>
                        <form id="filterForm" action="{{ route('admin.absen') }}" method="GET" class="p-1 flex">
                            <span class="flex gap-2">
                                <select name="filterKerjasama" id="filterKerjasama" style="width: 16rem;"
                                    class="select  select-bordered text-md active:border-none border-none">
                                    <option selected disabled>~ Nama Klien ~</option>
                                    @foreach ($absenSi as $i)
                                        <option value="{{ $i->id }}" {{ $filter == $i->id ? 'selected' : '' }}>
                                            {{ $i?->client?->name }}</option>
                                    @endforeach
                                </select>
                            </span>
                            <span class="flex mx-2 gap-2">
                                <select name="filterDevisi" id="filterDevisi"
                                    class="select select-bordered  text-md active:border-none border-none">
                                    <option selected disabled>~ Devisi ~</option>
                                    @foreach ($divisi as $i)
                                        <option value="{{ $i->id }}"
                                            {{ $filterDivisi == $i->id ? 'selected' : '' }}>{{ $i?->name }}</option>
                                    @endforeach
                                </select>
                            </span>
                            <div>
                                <button type="submit"
                                    class="bg-blue-500 px-5 py-2 rounded-md hover:bg-blue-600 transition-colors ease-in .2s font-bold ml-3">Filter</button>
                                <a href="{{ route('admin.index') }}" class="btn btn-error">Kembali</a>
                            </div>
                        </form>
                    </div>
                    <div class="flex justify-end items-center mr-10 mt-5">
                        <x-search />
                    </div>
                    <div class="hidden">
                        <form method="GET" action="{{ route('admin.export') }}">
                            <div class="flex items-center">
                                <!--LIBUR-->
                                <div>
                                    <input type="text" name="libur" class="input input-bordered"
                                        placeholder="Hari libur untuk semua.." />
                                </div>
                                <div class="flex justify-end mx-10 mb-2 ">
                                    <button type="submit"
                                        class="bg-yellow-400 px-4 py-2 shadow rounded-md flex flex-col items-center">
                                        <i class="ri-file-download-line text-2xl"></i>
                                        <span class="text-sm">All</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Export to Pdf / Edit to Excel --}}
            <form method="GET">
                <div class="flex items-center justify-center mx-10 w-screen">
                    <div class="flex items-center gap-2">
                        <div class="mr-5">
                            <select name="kerjasama_id" id="selectInput" style="width: 10rem;"
                                class="text-sm py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
                                <option disabled selected>Pilih Mitra</option>
                                @forelse ($absenSi as $i)
                                    <option value="{{ $i->id }}" class="break-words whitespace-pre-wrap">
                                        {{ $i?->client?->name }}</option>
                                @empty
                                    <option>~Kosong~</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="flex mr-2">
                            <div class="mr-2">
                                <input type="date" name="str1" id="str1" placeholder="Tanggal Mulai"
                                    class="text-sm block px-3 py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
                            </div>
                            <div class="ml-2">
                                <input type="date" name="end1" id="end1"
                                    class="text-sm block px-3 py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="mr-2">
                                <select name="divisi_id"
                                    class="text-sm block px-10 py-2 rounded-lg bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none">
                                    <option disabled selected>Pilih Divisi</option>
                                    @forelse($divisi as $i)
                                        <option value="{{ $i->id }}">{{ $i->name }}</option>
                                    @empty
                                        <option>~Kosong~</option>
                                    @endforelse
                                </select>
                            </div>
                            <!--LIBUR-->
                            <div>
                                <input type="text" name="libur" style="height: 2.5rem;"
                                    class="input input-bordered w-fit text-xs" placeholder="Masukkan hari libur.." />
                            </div>
                            {{-- + Jadwal --}}
                            <div class="flex justify-center items-center px-2 py-1 bg-slate-100 rounded mx-2">
                                <input type="checkbox" name="jadwal" id="jadwal" value="1" class="checkbox ">
                                <label for="jadwal" class="label label-text font-semibold text-xs text-slate-500">+
                                    Jadwal</label>
                            </div>
                            <div class="flex mx-10 mb-2 gap-x-3 p-5">
                                {{-- To PDF --}}
                                <button type="submit" formaction="{{ route('admin.exportV2') }}"
                                    class="bg-yellow-400/80 text-yellow-900 btn border-0 px-3 py-2 overflow-hidden shadow rounded-md text-2xl hover:bg-yellow-500 hover:scale-105 hover:text-yellow-800">
                                    <i class="ri-file-pdf-2-fill"></i>
                                </button>

                                {{-- To Excel Edit Page --}}
                                <button type="submit" formaction="{{ route('attendanceReport') }}"
                                    class="bg-emerald-400/80 text-emerald-900 btn border-0 px-3 py-2 shadow rounded-md text-2xl hover:bg-emerald-500 hover:scale-105 hover:text-emerald-800">
                                    <i class="ri-file-excel-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
            </form>
        </div>

        {{-- Delete Image Attendance --}}
        <div class="bg-slate-50 rounded-md w-fit p-2 mx-10 font-semibold">
            <p>*Hapus Foto Absen</p>
            <form action="{{ route('absen.hapusFotoAbsen') }}" method="post">
                @csrf
                <div class="flex gap-2 items-end">
                    <span>
                        <label class="label">Mulai: </label>
                        <input type="date" min="{{ $min }}" max="{{ $max }}" name="mulai"
                            class="input input-bordered input-sm" />
                    </span>
                    <span>
                        <label class="label">Selesai: </label>
                        <input type="date" min="{{ $min }}" max="{{ $max }}" name="selesai"
                            class="input input-bordered input-sm" />
                    </span>
                    <span>
                        <button type="submit" class="btn btn-sm btn-warning">Hapus</button>
                    </span>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto mx-10 my-10">
            <table class="table table-zebra w-full bg-slate-50" id="searchTable">
                <thead>
                    <tr>
                        <th class="bg-slate-300 rounded-tl-2xl">#</th>
                        <th class="bg-slate-300 " style="padding: 0 24px;">Photo</th>
                        <th class="bg-slate-300 ">Nama User</th>
                        <th class="bg-slate-300" style="padding: 0 34px;">Tanggal</th>
                        <th class="bg-slate-300 text-center" style="padding: 0 58px">Shift</th>
                        <th class="bg-slate-300 ">Client</th>
                        <th class="bg-slate-300 ">Ibadah</th>
                        <th class="bg-slate-300 ">Jam Masuk</th>
                        <th class="bg-slate-300 ">Jam Pulang</th>
                        <th class="bg-slate-300 ">Lokasi Presensi</th>
                        <th class="bg-slate-300 ">Keterangan</th>
                        <th class="bg-slate-300 hidden">Point</th>
                        <th class="bg-slate-300 rounded-tr-2xl">Tipe Absen</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($absen as $arr)
                        <tr>
                            <td>{{ $no++ }}.</td>
                            <td>
                                @if ($arr->image == 'no-image.jpg')
                                    <img class="lazy lazy-image" loading="lazy"
                                        srcset="{{ URL::asset('/logo/no-image.jpg') }}"
                                        data-src="{{ asset('storage/images/' . $arr->image) }}"
                                        alt="data-absensi-image" width="120px" />
                                @else
                                    <img class="lazy lazy-image" loading="lazy"
                                        src="{{ asset('storage/images/' . $arr->image) }}"
                                        data-src="{{ asset('storage/images/' . $arr->image) }}"
                                        alt="data-absensi-image" width="120px" />
                                @endif
                            </td>
                            <td class="break-words whitespace-pre-line">
                                {{ $arr->user ? ucwords(strtolower($arr->user->nama_lengkap)) : 'user_id' . ' : ' . $arr->user_id . 'AKU KOSONG' }}
                            </td>
                            <td>{{ $arr->tanggal_absen }}</td>
                            @if ($arr->shift != null)
                                <td id="mitra" class="text-center">{{ $arr->shift->shift_name }} <br /> <span
                                        style="font-size: 10pt;">{{ $arr->shift->jam_start }} -
                                        {{ $arr->shift->jam_end }}</span></td>
                            @else
                                <td class="break-words whitespace-pre-wrap text-red-500 font-semibold">Shift Kosong
                                </td>
                            @endif
                            <td class="break-words whitespace-pre-line">
                                {{ $arr?->kerjasama?->client?->panggilan ? $arr?->kerjasama?->client?->panggilan : $arr?->kerjasama?->client?->name }}
                            </td>
                            <td class="break-words whitespace-pre-line">
                                @if ($arr->subuh != 0 || $arr->dzuhur != 0 || $arr->asar != 0 || $arr->magrib != 0 || $arr->isya != 0)
                                    <span>{{ $arr->subuh ? 'subuh,' : '' }} {{ $arr->dzuhur ? 'dzuhur,' : '' }}
                                        {{ $arr->asar ? 'asar,' : '' }} {{ $arr->magrib ? 'magrib,' : '' }}
                                        {{ $arr->isya ? 'isya' : '' }}</span>
                                @endif
                            </td>
                            <td>{{ $arr->absensi_type_masuk }}</td>
                            {{-- Handle Absensi Type Pulang --}}
                            <td>
                                @if ($arr->absensi_type_pulang == null)
                                    <span class="text-red-500 underline font-bold">Belum Absen Pulang</span>
                                @else
                                    {{ $arr->absensi_type_pulang }}
                                @endif
                            </td>
                            @php
                                $msLat = strlen($arr->msk_lat);
                                $msLong = strlen($arr->msk_long);

                                $plgLat = strlen($arr->plg_lat);
                                $plgLong = strlen($arr->plg_long);
                            @endphp


                            <td class="flex justify-center items-center">
                                <a class="btn btn-sm btn-primary flex justify-center items-center text-center"
                                    href="{{ route('admin-lihatMap', $arr->id) }}"><i class="ri-map-pin-2-line"></i>
                                    Lihat</a>
                            </td>
                            {{-- End Handle Absensi Type Pulang --}}

                            {{-- Handle Keterangan --}}
                            <td>
                                @php
                                    $jamAbs = $arr->absensi_type_masuk ?? '00:00:00';
                                    $jamStr = $arr->shift?->jam_start ?? '00:00:00';

                                    // Determine time format
                                    $formatAbs = strlen($jamAbs) === 5 ? 'H:i' : 'H:i:s';
                                    $formatStr = strlen($jamStr) === 5 ? 'H:i' : 'H:i:s';

                                    // Parse times using Carbon
                                    $jAbs = Carbon\Carbon::createFromFormat($formatAbs, $jamAbs);
                                    $jJad = Carbon\Carbon::createFromFormat($formatStr, $jamStr);

                                    // Calculate the difference
                                    $jDiff = $jAbs->diff($jJad);

                                    // Build readable difference
                                    $diffHasil = '';
                                    if ($jDiff->h > 0) {
                                        $diffHasil .= $jDiff->format('%h Jam ');
                                    }
                                    if ($jDiff->i > 0) {
                                        $diffHasil .= $jDiff->format('%i Menit ');
                                    }
                                    if ($jDiff->s > 0) {
                                        $diffHasil .= $jDiff->format('%s Detik');
                                    }

                                    $diffHasil = trim($diffHasil);
                                @endphp
                                @if ($arr->keterangan == 'masuk')
                                    <div class="badge badge-success gap-2 overflow-hidden">
                                        {{ $arr->keterangan }}
                                    </div>
                                @elseif ($arr->keterangan == 'izin')
                                    <div class="badge badge-warning gap-2 overflow-hidden">
                                        {{ $arr->keterangan }}
                                    </div>
                                @else
                                    <div class="badge badge-error rounded-md gap-2 overflow-hidden text-center text-xs"
                                        style="height: 50pt; width: 60pt;">
                                        Terlambat <br /> {{ $diffHasil }}
                                    </div>
                                @endif
                            </td>
                            {{-- EndHandle Keterangan --}}

                            <td class="hidden">
                                @if ($arr->keterangan != 'telat' && $arr->keterangan != 'izin')
                                    <form action="{{ route('claim.point', $arr->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @forelse ($point as $item)
                                            @if ($arr?->kerjasama?->client_id == $item?->client_id && $arr?->point_id != null)
                                                {{ $arr->point->sac_point }}
                                            @endif
                                            @if ($arr->point_id == null)
                                                <input type="text" name="point_id" id="point_id"
                                                    value="{{ $item->id }}" class="hidden">
                                                <button class="px-2 py-1 w-16 rounded bg-amber-400" type="submit">+
                                                    Point</button>
                                                @break
                                            @endif

                                        @empty
                                            ~
                                        @endforelse

                                    </form>
                                @else
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($arr->tipe_id)
                                    @if ($arr->tipe_id == 1 && !$arr->terus)
                                        <span
                                            style="background-color: #02c9cc; color: black; padding: .45rem; border-radius: 5px; font-weight: bold;">
                                            {{ $arr->tipeAbsensi->name }}</span>
                                    @elseif($arr->tipe_id == 1 && $arr->terus)
                                        <span
                                            style="background-color: #02c9cc; color: black; padding: .45rem; border-radius: 5px; font-weight: bold;">
                                            Nerus</span>
                                    @else
                                        <span
                                            style="background-color: #cc0213; color: white; padding: .45rem; border-radius: 5px; font-weight: bold;">
                                            {{ $arr->tipeAbsensi->name }}</span>
                                    @endif
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">~ Kosong ~</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 mx-10 ">
            {{ $absen->links() }}
        </div>
        </div>
    </x-main-div>
    <script>
        $(document).ready(function() {
            // Saat halaman dimuat, ambil semua elemen dengan class "lazy-image"
            var lazyImages = $('.lazy-image');

            // Fungsi untuk memuat gambar ketika mendekati jendela pandangan pengguna
            function lazyLoad() {
                lazyImages.each(function() {
                    var image = $(this);
                    if (image.is(':visible') && !image.attr('src')) {
                        image.attr('src', image.attr('data-src'));
                    }
                });
            }

            // Panggil fungsi lazyLoad saat halaman dimuat dan saat pengguna menggulir
            lazyLoad();
            $(window).on('scroll', lazyLoad);
        });
    </script>
</x-app-layout>
