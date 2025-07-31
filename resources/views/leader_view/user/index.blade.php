<x-app-layout>
    <x-main-div>
        <div class="py-10 sm:mx-10">
            <p class="text-center text-lg sm:text-2xl uppercase font-bold">
                List Karyawan,<br>
                {{ auth()->id() == 175 ? 'Semua Mitra' : Auth::user()->kerjasama->client->name }}
            </p>

            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                {{-- Top Controls --}}
                <div class="flex items-center justify-center sm:justify-between gap-2 w-full mt-5">
                    <div style="width: 44.44%;">
                        @php
                            $jabatan = Auth::user()->divisi->jabatan->code_jabatan ?? Auth::user()->divisi->code_jabatan;
                        @endphp
                        <a href="{{ 
                            $jabatan === 'CO-CS' ? route('leaderView') :
                            ($jabatan === 'CO-SCR' ? route('danruView') : route('dashboard.index')) 
                        }}" class="btn btn-error">Kembali</a>
                    </div>

                    <div style="width: 55.55%;" class="mt-5">
                        <x-search />
                    </div>
                </div>

                {{-- Mitra Filter (visible only for user ID 175) --}}
                @if(auth()->id() == 175)
                    <form action="{{ route('danru_user') }}" method="GET" class="flex justify-center items-center gap-1 bg-slate-100 rounded w-full px-2 py-3 mb-5">
                        <div style="width: 66.66%;" class="w-2/3">
                            <label class="label text-xs sm:text-base">Pilih Mitra</label>
                            <select name="mitra" class="select select-bordered text-black select-sm text-xs w-full">
                                <option disabled selected>~Pilih Mitra~</option>
                                @forelse($mitra as $i)
                                    <option value="{{ $i->id }}" {{ $filterMitra == $i->id ? 'selected' : '' }}>{{ $i->client->name }}</option>
                                @empty
                                    <option disabled>~Mitra Kosong~</option>
                                @endforelse
                            </select>
                        </div>
                        <div style="width: 33.33%;" class="w-1/3 flex items-end">
                            <button class="btn btn-primary btn-sm sm:btn-md w-full">Filter</button>
                        </div>
                    </form>
                @endif

                {{-- Table --}}
                <div class="overflow-x-scroll w-full md:overflow-hidden mx-2 sm:mx-10">
                    <table id="searchTable" class="table table-xs table-zebra sm:table-md text-xs bg-slate-50 font-semibold sm:text-md">
                        <thead>
                            <tr class="text-center">
                                <th class="p-1 py-2 bg-slate-300 rounded-tl-2xl">#</th>
                                <th class="p-1 py-2 bg-slate-300">Profil</th>
                                <th class="p-1 py-2 bg-slate-300">Username</th>
                                <th class="p-1 py-2 bg-slate-300">Nama Lengkap</th>
                                <th class="p-1 py-2 bg-slate-300">Jabatan</th>
                                <th class="p-1 py-2 bg-slate-300">Email</th>
                                <th class="p-1 py-2 bg-slate-300 rounded-tr-2xl">Penempatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $n = 1; @endphp
                            @forelse ($user as $i)
                                @continue(in_array($i->nama_lengkap, ['admin', 'user', 'MITRA SAC']))
                                <tr>
                                    <td class="p-1">{{ $n++ }}</td>
                                    <td>
                                        @if ($i->image !== 'no-image.jpg' && Storage::disk('public')->exists('images/' . $i->image))
                                            <img class="lazy lazy-image" loading="lazy"
                                                 src="{{ asset('storage/images/' . $i->image) }}"
                                                 data-src="{{ asset('storage/images/' . $i->image) }}"
                                                 alt="" width="120px">
                                        @else
                                            <x-no-img />
                                        @endif
                                    </td>
                                    <td class="p-1">{{ $i->name }}</td>
                                    <td class="p-1 break-words whitespace-pre-wrap">{{ ucwords(strtolower($i->nama_lengkap)) }}</td>
                                    <td class="p-1 break-words whitespace-pre-wrap" style="width: 90px">
                                        {{ $i->divisi->jabatan->code_jabatan ?? 'Jabatan Kosong ?' }}
                                    </td>
                                    <td class="p-1 break-words whitespace-pre-line">{{ $i->email }}</td>
                                    <td class="p-1 break-words whitespace-pre-line">
                                        @php
                                            $name = $i->kerjasama->client->name ?? null;
                                            if ($name) {
                                                preg_match('/\((.*?)\)/', $name, $match);
                                                $suffix = isset($match[0]) ? ' ' . $match[0] : '';
                                                $cleanName = preg_replace('/\s*\(.*?\)\s*/', ' ', $name);
                                                $initials = collect(explode(' ', trim($cleanName)))
                                                    ->map(fn($word) => strtoupper(substr(str_replace("'", '', $word), 0, 1)))
                                                    ->implode('');
                                                echo $initials . $suffix;
                                            } else {
                                                echo 'kosong';
                                            }
                                        @endphp
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">~ Data Kosong ~</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div id="pag-1" class="mt-5 mb-5">
                    {{ $user->links() }}
                </div>
            </div>
        </div>
    </x-main-div>
</x-app-layout>
