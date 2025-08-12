<x-app-layout>
    <x-main-div>
        <div class="py-10">
            <div>
                <p class="text-center text-lg sm:text-2xl font-bold uppercase">
                    {{ ($type == 'rencana' ? 'Rencana Kerja' : 'Pekerjaan') . ' ' . $user->nama_lengkap }}
                </p>
            </div>

            {{-- Filter Buttons --}}
            <div class="flex justify-center mt-5 rounded-md">
                <div class="flex flex-col gap-2 bg-slate-200 p-4 drop-shadow-md rounded-md w-fit">
                    <p class="text-center font-semibold text-sm">~&gt;Filter&lt;~</p>
                    <div class="flex gap-2 justify-center sm:justify-start overflow-hidden">
                        @foreach (['rencana' => 'Rencana', 'dikerjakan' => 'Dikerjakan'] as $filterType => $label)
                            <form action="" method="get" class="btn btn-info btn-sm overflow-hidden">
                                <input type="hidden" name="type" value="{{ $filterType }}">
                                <button type="submit" class="overflow-hidden">{{ $label }}</button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Edit Rencana Button --}}
            <div class="mx-2 sm:mx-10 my-2">
                <a href="{{ $type == 'rencana' && $cex2 ? route('checkpoint-user.edit', $cex2->id) : '#' }}"
                    class="{{ $type == 'rencana' && $cex2 ? '' : 'pointer-events-none opacity-50' }}">
                    <button type="button" class="btn btn-warning btn-sm"
                        {{ $type == 'rencana' && $cex2 ? '' : 'disabled' }}>
                        + Ubah Rencana Kerja
                    </button>
                </a>
            </div>

            {{-- Data Table --}}
            <div class="flex flex-col items-center mx-2 my-2 sm:justify-center justify-start">
                <div class="overflow-x-auto w-full md:overflow-hidden mx-2 sm:mx-0 sm:w-full rounded">
                    <table
                        class="table w-full table-xs bg-slate-50 table-zebra sm:table-md text-sm sm:text-md md:scale-90">
                        <thead class="text-center">
                            <tr>
                                <th class="bg-slate-300 rounded-tl-2xl">#</th>
                                @if ($type !== 'rencana')
                                    <th class="bg-slate-300">Gambar Bukti</th>
                                @endif
                                <th class="bg-slate-300">Nama CP</th>
                                @if ($type !== 'rencana')
                                    <th class="bg-slate-300">Deskripsi</th>
                                @endif
                                <th class="bg-slate-300">Tanggal</th>
                                <th class="bg-slate-300">Check Point</th>
                                @if ($type !== 'rencana')
                                    <th class="bg-slate-300">Status</th>
                                @endif
                                <th class="bg-slate-300 rounded-tr-2xl">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cex2 ?? [] as $c2)
                                @foreach ($c2->pekerjaan_cp_id as $index => $cpId)
                                    <tr>
                                        <td>{{ $loop->parent->iteration . '.' . ($index + 1) }}</td>

                                        @if ($type !== 'rencana')
                                            <td>
                                                @php
                                                    $img = $c2->img[$index] ?? null;
                                                @endphp
                                                @if (empty($cpId) || $cpId == 'no-image.jpg' || !$img)
                                                    <x-no-img class="scale-50" />
                                                @else
                                                    <img src="{{ asset('storage/images/' . $img) }}" width="70"
                                                        class="rounded" alt="Gambar Bukti">
                                                @endif
                                            </td>
                                        @endif

                                        <td class="capitalize text-start min-w-[100px]">
                                            @php
                                                $pc = $pcp->firstWhere('id', $cpId);
                                            @endphp
                                            {{ $pc ? '~ ' . $pc->name : '~ ' . $cpId }}
                                        </td>

                                        @if ($type !== 'rencana')
                                            <td class="capitalize text-start min-w-[200px]">
                                                ~ {{ $c2->deskripsi[$index] ?? '' }}
                                            </td>
                                        @endif

                                        <td>~ {{ $c2->tanggal[$index] ?? 'Kosong' }}</td>

                                        <td>{{ $pc ? '~ ' . $pc->type_check : '' }}</td>

                                        @if ($type !== 'rencana')
                                            <td>
                                                @php
                                                    $status = $c2->approve_status[$index] ?? null;
                                                    $note = $c2->note[$index] ?? null;
                                                @endphp
                                                @if ($status)
                                                    <div class="flex flex-col items-center">
                                                        <span
                                                            class="badge px-2 text-xs text-white
                                                            {{ $status === 'accept' ? 'bg-emerald-700' : ($status === 'proccess' ? 'bg-amber-500' : 'bg-red-500') }}">
                                                            {{ ucfirst($status) }}
                                                        </span>
                                                        @if ($note)
                                                            <p class="text-xs italic">Note: {{ $note }}</p>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        @endif

                                        <td>
                                            @if ($type == 'dikerjakan')
                                                <button data-id="{{ $cpId }}" data-index="{{ $index }}"
                                                    class="btn btn-info btn-sm btn-nilai">
                                                    Nilai
                                                </button>
                                            @else
                                                <button data-id="{{ $c2->id }}" data-index="{{ $index }}"
                                                    class="btn btn-error btn-sm btn-hapus">
                                                    Hapus
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="{{ $type === 'rencana' ? 6 : 8 }}" class="text-center">~ Kosong ~</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Modal containers --}}
                <div id="div_form_nilai"
                    class="hidden fixed inset-0 z-50 flex justify-center items-center bg-slate-900/50 p-10 drop-shadow-md overflow-hidden">
                </div>
                <div id="div_form_delete"
                    class="hidden fixed inset-0 z-50 flex justify-center items-center bg-slate-900/50 p-10 drop-shadow-md overflow-hidden">
                </div>

                {{-- Back Button --}}
                <div class="flex justify-end gap-2 mx-5 sm:mx-10 my-2">
                    @php
                        $backRoute = Auth::user()->role_id == 2 ? route('admin.cp.index') : route('direksi.cp.index');
                    @endphp
                    <a href="{{ $backRoute }}" class="btn btn-error">Kembali</a>
                </div>

                {{-- Modal Show --}}
                <div id="modalShow"
                    class="modalShow hidden fixed inset-0 z-[9000] flex justify-center items-center bg-slate-500/10 backdrop-blur-sm p-5">
                    <div class="bg-slate-50 rounded-md shadow p-6 w-full max-w-md relative">
                        <button id="closeButton" class="btn btn-error absolute top-2 right-2 scale-90">&times;</button>
                        <div class="flex flex-col justify-center items-center gap-4">
                            <span id="status" class="text-lg p-2 rounded-lg text-white font-semibold"></span>
                            <img id="modalImg" loading="lazy" alt="" width="120" class="rounded" />
                            <p id="modalTitle" class="font-semibold whitespace-pre-wrap break-words py-5 text-center">
                            </p>
                            <div class="flex flex-col w-full">
                                <label for="notes">Note</label>
                                <textarea id="notes" name="note" class="textarea textarea-bordered" placeholder="notes..."></textarea>
                            </div>
                            <button id="confirmButton" class="btn btn-warning rounded-btn px-10">Confirm</button>
                        </div>
                    </div>
                </div>

                {{-- Pagination --}}
                <div>
                    {{ $cek->links() }}
                </div>
            </div>
        </div>

        {{-- Scripts --}}
        <script>
            $(function() {
                // Open nilai or hapus modals
                $('.btn-nilai, .btn-hapus').click(function() {
                    const isDelete = $(this).hasClass('btn-hapus');
                    const id = $(this).data('id');
                    const index = $(this).data('index');

                    if (isDelete) {
                        const route = "{{ url('direksi/deleteRencana') }}/" + id;
                        const html = `
                            <div class="bg-slate-200 rounded-md p-4 mx-10 flex flex-col">
                                <div class="flex justify-end">
                                    <button id="btnClose" class="btn btn-error">&times;</button>
                                </div>
                                <form action="${route}" method="POST" class="flex flex-col items-center gap-4">
                                    @csrf
                                    <p class="text-center text-sm">Yakin ingin menghapus data ini?</p>
                                    <input type="hidden" name="arrKe" value="${index}">
                                    <button type="submit" class="btn btn-info btn-sm">Submit</button>
                                </form>
                            </div>`;
                        $('#div_form_delete').html(html).removeClass('hidden').addClass('flex');
                    } else {
                        @if ($cex2)
                            const dataCex = @json($cex2);
                            const dataPcp = @json($pcp);
                            const filteredPc = dataPcp.find(pc => pc.id == id);
                            const route = "{{ route('direksi.uploadNilai', ' + id+') }}";
                            const imgSrc = dataCex.img && dataCex.img[index] ?
                                `{{ asset('storage/images') }}/${dataCex.img[index]}` : '';
                            const deskripsi = dataCex.deskripsi && dataCex.deskripsi[index] ? dataCex.deskripsi[
                                index] : '';
                            const html = `
                            <div class="bg-slate-200 rounded-md p-5 flex flex-col min-w-full sm:min-w-fit sm:max-w-xs mx-auto">
                                <div class="flex justify-end">
                                    <button id="btnClose" class="btn btn-error">&times;</button>
                                </div>
                                <form action="${route}" method="POST" class="flex flex-col items-center gap-4">
                                    @csrf
                                    <p class="font-semibold text-center">~${filteredPc ? filteredPc.name : ''}~</p>
                                    <div class="flex justify-center">
                                        ${imgSrc ? `<img src="${imgSrc}" class="rounded" alt="Gambar Bukti" width="70">` : ''}
                                    </div>
                                    <p class="text-xs sm:text-sm line-clamp-2 text-start my-2">${deskripsi}</p>
                                    <fieldset class="w-full">
                                        <legend class="font-semibold mb-2 text-center">Status:</legend>
                                        <label class="inline-flex items-center gap-2">
                                            <input type="radio" name="approve_status[]" value="accept" checked class="radio radio-sm radio-success">
                                            Disetujui
                                        </label>
                                        <label class="inline-flex items-center gap-2 ml-4">
                                            <input type="radio" name="approve_status[]" value="denied" class="radio radio-sm radio-error">
                                            Ditolak
                                        </label>
                                    </fieldset>
                                    <input type="text" name="note[]" placeholder="note.." class="input input-bordered w-full input-sm text-sm">
                                    <input type="hidden" name="arrKe" value="${index}">
                                    <button type="submit" class="btn btn-info btn-sm w-full">Submit</button>
                                </form>
                            </div>`;
                            $('#div_form_nilai').html(html).removeClass('hidden').addClass('flex');
                        @endif
                    }
                    $('body').addClass('overflow-hidden');
                });

                // Close modals
                $(document).on('click', '#btnClose', function() {
                    $('#div_form_nilai, #div_form_delete').addClass('hidden').removeClass('flex').empty();
                    $('body').removeClass('overflow-hidden');
                });

                // Modal show close button
                $('#closeButton').click(function() {
                    $('#modalShow').toggleClass('hidden');
                });

                // Confirm button in modalShow
                $('#confirmButton').click(function() {
                    const route = $(this).data('route');
                    const status = $(this).data('status');
                    const id = $(this).data('id');
                    const note = $('#notes').val();

                    $.ajax({
                        url: route,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'PATCH',
                            approve_status: status,
                            note: note,
                        },
                        success: () => location.reload(),
                        error: (xhr, status, error) => console.error(error)
                    });
                });
            });
        </script>
    </x-main-div>
</x-app-layout>
