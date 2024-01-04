<x-app-layout>
    <style>
        @media screen and (max-width: 576px) {
            th:nth-child(3),
            th:nth-child(n+6),
            td:nth-child(3),
            td:nth-child(n+6) {
                display: none;
            }
            th:nth-child(5) {
                border-top-right-radius: 1rem; /* Adjust this value as needed */
            }
        }
    </style>
    <x-main-div>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold uppercase">Check Point {{ $user->nama_lengkap }}</p>
            </div>
            
            <div class="flex justify-end gap-2  mx-5 sm:mx-10 my-2">
                @if(Auth::user()->role_id == 2)
                    <a href="{{ route('admin.cp.index') }}" class="btn btn-error">Kembali</a>
                @else
                    <a href="{{ route('direksi.cp.index') }}" class="btn btn-error">Kembali</a>
                @endif
            </div>
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full">
                    <table class="table w-full table-xs bg-slate-50 table-zebra sm:table-md text-sm sm:text-md  md:scale-90">
                        <thead class="text-center">
                            <tr>
								<th class="bg-slate-300 rounded-tl-2xl">#</th>
								<th class="bg-slate-300">Gambar Bukti</th>
								<th class="bg-slate-300">Nama CP</th>
								<th class="bg-slate-300">Deskripsi</th>
								<th class="bg-slate-300">Harian</th>
								<th class="bg-slate-300">Mingguan</th>
								<th class="bg-slate-300">Bulanan</th>
								<th class="bg-slate-300">Check Point</th>
								<th class="bg-slate-300" id="displayTH">Informasi</th>
								<th class="bg-slate-300">Status</th>
								<th class="bg-slate-300 rounded-tr-2xl">Action</th>
							</tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @forelse ($cek as $c)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    @if ($c->img == 'no-image.jpg')
                                        <td >
                                            <x-no-img />
                                        </td>
                                    @elseif(Storage::disk('public')->exists('images/' . $c->img))
                                        <td ><img class="lazy lazy-image" loading="lazy" src="" data-src="{{ asset('storage/images/' . $c->img) }}" alt="" srcset="{{ asset('storage/images/' . $c->img) }}" width="120px"></td>
                                    @else
                                        <td >
                                            <x-no-img />
                                        </td>
                                    @endif
                                    <td>{{ $c->pekerjaancp ? $c->pekerjaancp->name : $c->deskripsi }}</td>
                                    <td>{{ $c->deskripsi }}, {{ $c->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $c->type_check }}</td>
                                    <td class="overflow-hidden">
                                        <a href="{{ Auth::user()->role_id == 2 ? route('admin-lihatMap', $c->id) : route('direksi-lihatMap', $c->id) }}"
                                            class="btn btn-sm btn-info text-xs overflow-hidden">
                                            <span id="displayText">Lokasi</span> <!-- Empty span to hold the text -->
                                        </a>
                                    </td>

                                    <td >
                                        @if($c->approve_status == "proccess")
                                            <span class="badge bg-amber-500 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status }}</span> 
                                        @elseif($c->approve_status == "accept")
                                            <span class="badge bg-emerald-700 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status }}</span> 
                                        @else
                                            <span class="badge bg-red-500 px-2 text-xs text-white overflow-hidden">{{ $c->approve_status }}</span> 
                                        @endif
                                    </td>
                                    <td >
                                        @if ($c->approve_status == 'proccess')
                                            <div class="flex justify-center gap-1 items-center text-center">
                                                <div>
                                                    <button class="btn btn-success btn-xs rounded-btn flex items-center" onclick="approveRequest('{{ route('direksi.approveCP', $c->id) }}', 'accept')">
                                                        <i class="ri-check-double-line"></i>
                                                        <p>accept</p>
                                                    </button>
                                                </div>
                                                <div>
                                                    <button class="btn btn-error btn-xs rounded-btn flex items-center" onclick="approveRequest('{{ route('direksi.approveCP', $c->id) }}', 'denied')">
                                                        <i class="ri-close-line"></i>
                                                        <p>denied</p>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                        {{--<div class="flex gap-2 hidden">
                                            <form action="{{ route('admin.deletedIzin', $i->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="overflow-hidden ">
                                                    <button  class="text-red-400 hover:text-red-500 text-xl transition-all ease-linear .2s"><i class="ri-delete-bin-5-line"></i></button>
                                                </div>
                                            </form>
                                        </div> --}}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">~ Kosong ~</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div>
                    {{ $cek->links()}}
                </div>
            </div>
        </div>
        <script>
            function approveRequest(route, status) {
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        '_method': 'PATCH',
                        'approve_status': status
                    },
                    success: function (response) {
                        // Handle success, e.g., update UI
                        console.log(response);
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        // Handle error, e.g., show error message
                        console.error(error);
                    }
                });
            }
        </script>
        <script>
            function updateTextBasedOnScreenSize() {
                var screenWidth = window.innerWidth;
        
                var displayText = document.getElementById('displayText');
                var displayTH = document.getElementById('displayTH');
        
                if (screenWidth >= 576) {
                    displayText.textContent = 'Info Lokasi'; // Set text for larger screens
                    displayTH.textContent = 'Koordinat'; // Set text for larger screens
                } else {
                    displayText.textContent = 'Info Selengkapnya'; // Set text for smaller screens
                    displayTH.textContent = 'Informasi';
                }
            }
        
            // Call the function initially and add an event listener for window resize
            updateTextBasedOnScreenSize();
            window.addEventListener('resize', updateTextBasedOnScreenSize);
        </script>
    </x-main-div>
</x-app-layout>