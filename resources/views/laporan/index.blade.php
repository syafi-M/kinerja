<x-app-layout>
    <x-main-div>
        <div>
            <p class="text-center text-lg sm:text-2xl font-bold py-10 uppercase">Data Laporan</p>
        </div>
        <span class="flex justify-between items-center w-full">
            @if (Auth::user()->role_id == 2)
                <div class="ml-5" style="width: 75%">
                    <form action="{{ route('export.laporans') }}" method="get" class="w-full mb-5">
                        <div class="flex items-center justify-start gap-2 w-full">
                            <div class="flex items-center gap-2 " style="width: 90%">
                                <div style="width: 50%;" class="flex flex-col gap-2">
                                    <label for="client_id" class="label">Pilih Mitra & Ruangan (opsional)</label>
                                    <span class="flex flex-col gap-2" style="width: 100%;">
                                        <select name="client_id" id="client_id" style=""
                                            class="select select-sm text-xs select-bordered">
                                            <option selected disabled>~Pilih Mitra~</option>
                                            @forelse ($mitra as $i)
                                                <option value="{{ $i->id }}">{{ $i->client->name }}</option>
                                            @empty
                                                <option>~Kosong~</option>
                                            @endforelse
                                        </select>
                                        <select name="ruangan_id" style=""
                                            class="select select-sm text-xs select-bordered">
                                            <option selected disabled>~Pilih Ruangan (opsional)~</option>
                                            @forelse($ruangan as $ru)
                                                <option value="{{ $ru->id }}">{{ $ru->nama_ruangan }}</option>
                                            @empty
                                                <option>~Kosong~</option>
                                            @endforelse
                                        </select>
                                    </span>
                                </div>
                                <span style="width: 30%" class="flex flex-col gap-2">
                                    <label for="str1" class="label">Tanggal</label>
                                    <div class="flex flex-col gap-2" style="">
                                        <input type="date" name="str1" id="str1" placeholder="Tanggal Mulai"
                                            class="text-md input input-sm input-bordered">
                                        <input type="date" name="end1" id="end1"
                                            class="text-md input input-sm input-bordered">
                                    </div>
                                </span>
                                <div style="width: 20%;" class="">
                                    <label for="nilai" class="label">Nilai</label>
                                    <div class="form-control bg-slate-50 rounded-lg">
                                        <label class="label cursor-pointer"
                                            style="padding-top: 2px; padding-bottom: 2px;">
                                            <span class="label-text">Baik</span>
                                            <input type="checkbox" name="nilai[]" value="baik" class="checkbox" />
                                        </label>
                                        <label class="label cursor-pointer"
                                            style="padding-top: 2px; padding-bottom: 2px;">
                                            <span class="label-text">Cukup</span>
                                            <input type="checkbox" name="nilai[]" value="cukup" class="checkbox" />
                                        </label>
                                        <label class="label cursor-pointer"
                                            style="padding-top: 2px; padding-bottom: 2px;">
                                            <span class="label-text">Kurang</span>
                                            <input type="checkbox" name="nilai[]" value="kurang" class="checkbox" />
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center" style="width: 10%">
                                <input type="hidden" name="action" value="download" />
                                <button type="submit" class="btn btn-warning text-sm sm:btn-sm btn-xs">Print
                                    PDF</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <div class="flex justify-end items-center">
                <x-search />
            </div>
        </span>

        @if (Auth::user()->role_id == 2)
            <div class="bg-slate-50 rounded-md w-fit p-2 mx-10 mb-5 font-semibold">
                <p>*Hapus Foto Laporan</p>
                <form action="{{ route('laporan.hapusFotoLaporan') }}" method="post">
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
        @endif

        <div class="overflow-x-auto mx-5">
            <table class="table table-zebra table-xs sm:table-md bg-slate-50 w-full" id="searchTable">
                <thead>
                    <tr>
                        <th class="bg-slate-300 rounded-tl-2xl">#</th>
                        <th class="bg-slate-300 text-center">Foto Progres</th>

                        @if (Auth::user()->role_id == 2 ||
                                Auth::user()->divisi->jabatan->code_jabatan == 'LEADER' ||
                                Auth::user()->divisi->jabatan->code_jabatan == 'MITRA')
                            <th class="bg-slate-300 ">Nama</th>
                        @endif

                        <th class="bg-slate-300 ">Mitra</th>
                        <th class="bg-slate-300 ">Ruangan</th>
                        <th class="bg-slate-300 ">Pekerjaan</th>
                        <th class="bg-slate-300 ">Nilai</th>

                        @if (Auth::user()->role_id == 2)
                            <th class="bg-slate-300 ">Keterangan</th>
                            <th class="bg-slate-300 ">Tanggal</th>
                            <th class="bg-slate-300 rounded-tr-2xl">Action</th>
                        @else
                            <th class="bg-slate-300">Keterangan</th>
                            <th class="bg-slate-300 rounded-tr-2xl">Tanggal</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($laporan as $i)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td class="scale-75 flex flex-col gap-2" style="overflow: hidden;">
                                <div class="flex justify-center items-center gap-2">
                                    @if ($i->image1 && $i->image1 != 'no-image.jpg' && file_exists(public_path('storage/images/' . $i->image1)))
                                        <img src="{{ asset('storage/images/' . $i->image1) }}" alt=""
                                            srcset="{{ asset('storage/images/' . $i->image1) }}" width="100px"
                                            class="lazy lazy-image" loading="lazy"
                                            style="min-width: 100px; max-width: 100px; height: 100%; object-fit: contain;">
                                    @else
                                        <x-no-img />
                                    @endif
                                    @if ($i->image2 && $i->image2 != 'no-image.jpg' && file_exists(public_path('storage/images/' . $i->image2)))
                                        <img src="{{ asset('storage/images/' . $i->image2) }}" alt=""
                                            srcset="{{ asset('storage/images/' . $i->image2) }}" width="100px"
                                            class="lazy lazy-image" loading="lazy"
                                            style="min-width: 100px; max-width: 100px; height: 100%; object-fit: contain;">
                                    @else
                                        <x-no-img />
                                    @endif
                                </div>
                                <div class="flex justify-center items-center gap-2">
                                    @if ($i->image3 && $i->image3 != 'no-image.jpg' && file_exists(public_path('storage/images/' . $i->image3)))
                                        <img src="{{ asset('storage/images/' . $i->image3) }}" alt=""
                                            srcset="{{ asset('storage/images/' . $i->image3) }}" width="100px"
                                            class="lazy lazy-image" loading="lazy"
                                            style="min-width: 100px; max-width: 100px; height: 100%; object-fit: contain;">
                                    @else
                                        <x-no-img />
                                    @endif
                                    @if ($i->image4 && $i->image4 != 'no-image.jpg' && file_exists(public_path('storage/images/' . $i->image4)))
                                        <img src="{{ asset('storage/images/' . $i->image4) }}" alt=""
                                            srcset="{{ asset('storage/images/' . $i->image4) }}" width="100px"
                                            class="lazy lazy-image" loading="lazy"
                                            style="min-width: 100px; max-width: 100px; height: 100%; object-fit: contain;">
                                    @else
                                        <x-no-img />
                                    @endif
                                    @if ($i->image5 && $i->image5 != 'no-image.jpg' && file_exists(public_path('storage/images/' . $i->image5)))
                                        <img src="{{ asset('storage/images/' . $i->image5) }}" alt=""
                                            srcset="{{ asset('storage/images/' . $i->image5) }}" width="100px"
                                            class="lazy lazy-image" loading="lazy"
                                            style="min-width: 100px; max-width: 100px; height: 100%; object-fit: contain;">
                                    @else
                                        <x-no-img />
                                    @endif
                                </div>
                            </td>

                            @if (Auth::user()->role_id == 2 ||
                                    Auth::user()->divisi->jabatan->code_jabatan == 'LEADER' ||
                                    Auth::user()->divisi->jabatan->code_jabatan == 'MITRA')
                                <td>{{ $i->user?->nama_lengkap }}</td>
                            @endif

                            <td>{{ $i?->client?->name }}</td>
                            <td>{{ $i->ruangan?->nama_ruangan }}</td>
                            <td>
                                @php
                                    // Decode the JSON string to get an array
                                    $pekerjaanArray = json_decode($i->pekerjaan);
                                @endphp
                                @if (!empty($pekerjaanArray))
                                    @foreach ($pekerjaanArray as $value)
                                        {{ $value }}
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if ($i->nilai)
                                    @if ($i->nilai == 'baik')
                                        <span class="badge badge-success overflow-hidden">Baik</span>
                                    @elseif($i->nilai == 'cukup')
                                        <span class="badge badge-info overflow-hidden">Cukup</span>
                                    @else
                                        <span class="badge badge-error overflow-hidden">Kurang</span>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $i->keterangan }}</td>
                            <td style="width: 125px;">{{ $i->created_at->format('Y-m-d') }}</td>

                            @if (Auth::user()->role_id == 2)
                                <td>
                                    <form action="{{ url('laporans/' . $i->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-btn-submit></x-btn-submit>
                                    </form>
                                </td>
                            @else
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Laporan Saat Ini Kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5 mx-10">
            {{ $laporan->links() }}
        </div>
        <div>
            <div class="flex justify-center sm:justify-end my-3 gap-2 mr-0 sm:mr-9">
                <a href="{{ url('/scan') }}"
                    class="btn btn-warning hover:bg-yellow-600 border-none transition-all ease-in-out .2s">+
                    Laporan</a>
                <a href="{{ route('dashboard.index') }}"
                    class="btn btn-error border-none hover:bg-red-500 transition-all ease-in-out .2s">Kembali</a>
            </div>
        </div>
    </x-main-div>
</x-app-layout>
