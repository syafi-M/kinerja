<x-app-layout>
    <x-main-div>
        @php
            $isEdit = (bool) $id;
            $user = $isEdit ? $cex->user : Auth::user();
            $selected = $isEdit ? (array) $cex->pekerjaan_cp_id : [];
            $sections = [
                ['id' => 'Harian', 'label' => 'Harian', 'items' => $pcp->where('type_check', 'harian'), 'tone' => 'sky'],
                ['id' => 'Mingguan', 'label' => 'Mingguan', 'items' => $pcp->where('type_check', 'mingguan'), 'tone' => 'emerald'],
                ['id' => 'Bulanan', 'label' => 'Bulanan', 'items' => $pcp->where('type_check', 'bulanan'), 'tone' => 'violet'],
                ['id' => 'Isidental', 'label' => 'Isidental', 'items' => $pcp->where('type_check', 'isidental'), 'tone' => 'amber'],
            ];
        @endphp

        <div class="mx-auto w-full max-w-5xl px-3 py-5 sm:px-5 lg:px-6">
            <div class="mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-900/5">
                <div class="border-b border-slate-200 bg-gradient-to-br from-amber-50 via-white to-sky-50 px-4 py-4 sm:px-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-400 text-slate-900 shadow-sm ring-1 ring-amber-300">
                                <i class="ri-task-line text-2xl"></i>
                            </span>
                            <div class="min-w-0">
                                <div class="mb-1 inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                                    <i class="ri-map-pin-time-line"></i>
                                    Checkpoint Planning
                                </div>
                                <h1 class="text-xl font-bold leading-tight text-slate-900 sm:text-2xl">
                                    {{ $isEdit ? 'Ubah Planning Kerja' : 'Buat Planning Kerja' }}
                                </h1>
                                <p class="mt-1 text-sm leading-5 text-slate-600">Pilih pekerjaan checkpoint yang akan dikerjakan hari ini.</p>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.index') }}"
                            class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 sm:w-auto">
                            <i class="ri-arrow-left-line"></i>
                            Kembali
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ $isEdit ? route('checkpoint-user.update', $cex->id) : route('checkpoint-user.store') }}"
                    id="form-cp" enctype="multipart/form-data" class="p-4 sm:p-6">
                    @csrf
                    @if ($isEdit)
                        @method('put')
                    @endif

                    <div class="mb-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-medium text-slate-500">Nama</p>
                            <p class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $user->nama_lengkap }}</p>
                            <input type="hidden" id="user_id" name="user_id" value="{{ $isEdit ? $cex->user_id : Auth::id() }}">
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-medium text-slate-500">Bermitra Dengan</p>
                            <p class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $user->kerjasama->client->name }}</p>
                            <input type="hidden" name="divisi_id" id="divisi_id" value="{{ $isEdit ? $cex->user->devisi_id : Auth::user()->divisi->id }}">
                        </div>
                    </div>

                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Daftar Check Point</h2>
                            <p class="text-sm text-slate-500">Klik kategori untuk membuka daftar pekerjaan.</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach ($sections as $section)
                            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                <button type="button" id="l{{ $section['id'] }}"
                                    class="flex w-full items-center justify-between gap-3 bg-slate-50 px-4 py-3 text-left transition hover:bg-slate-100">
                                    <span class="flex items-center gap-3">
                                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-{{ $section['tone'] }}-100 text-{{ $section['tone'] }}-700">
                                            <i class="ri-checkbox-multiple-line"></i>
                                        </span>
                                        <span>
                                            <span class="block text-sm font-bold text-slate-900">{{ $section['label'] }}</span>
                                            <span class="block text-xs text-slate-500">{{ $section['items']->count() }} pekerjaan tersedia</span>
                                        </span>
                                    </span>
                                    <i class="ri-arrow-down-s-line text-xl text-slate-400"></i>
                                </button>

                                <div class="hidden divide-y divide-slate-100" id="{{ strtolower(substr($section['id'], 0, 1)) }}Cont">
                                    @forelse ($section['items'] as $p)
                                        <label class="flex cursor-pointer items-start gap-3 px-4 py-2.5 transition hover:bg-slate-50">
                                            <input type="checkbox" {{ $isEdit && in_array($p->id, $selected) ? 'checked' : '' }}
                                                name="pekerjaan_id[]" value="{{ $p->id }}" class="checkbox checkbox-sm mt-0.5">
                                            <span class="text-sm font-medium text-slate-700">{{ $p->name }}</span>
                                        </label>
                                    @empty
                                        <p class="px-4 py-4 text-center text-sm text-slate-500">~ Pekerjaan tidak tersedia ~</p>
                                    @endforelse
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <x-input-error :messages="$errors->get('type_check')" class="mt-2" />

                    @if ($isEdit)
                        <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-3">
                            @if (!empty($cex->pekerjaan_cp_id))
                                @foreach ($cex->pekerjaan_cp_id as $item)
                                    @php $ce = $pcp->where('id', $item)->first(); @endphp
                                    @if (empty($ce) && $item)
                                        <label class="mb-2 flex items-center gap-3 rounded-lg bg-white px-3 py-2">
                                            <input type="checkbox" checked name="pekerjaan_id[]" value="{{ $item }}" class="checkbox checkbox-sm">
                                            <span class="text-sm font-medium text-slate-700">{{ $item }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            @endif

                            <div id="tambahan_pcp" class="space-y-2">
                                <p id="tambahan_label" class="hidden text-center text-sm font-semibold text-slate-600">~ Tambahan ~</p>
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button type="button" id="tambah_pcp_btn"
                                    class="inline-flex min-h-9 items-center gap-2 rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                                    <i class="ri-add-line"></i>
                                    Tambah CP
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="hidden">
                        <input type="text" value="" id="latitude" name="latitude" readonly>
                        <input type="text" value="" id="longitude" name="longtitude" readonly>
                        <div id="div_status"></div>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <a href="{{ route('dashboard.index') }}"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Batal
                        </a>
                        <button type="button" id="btnSubmit"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-amber-400 px-5 py-2 text-sm font-bold text-slate-900 shadow-sm transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">
                            <i class="ri-save-3-line"></i>
                            Simpan Planning
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            var latitudeInput = $('#latitude');
            var longitudeInput = $('#longitude');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    latitudeInput.val(position.coords.latitude);
                    longitudeInput.val(position.coords.longitude);
                });
            }
        </script>
        <script>
            $(document).ready(function() {
                $('.checkbox').change(function() {
                    if ($(this).is(':checked')) {
                        $('#div_status').append($('<input type="hidden" name="approve_status[]" value="proccess"/>'));
                    } else {
                        $('#div_status input[type="hidden"]').remove();
                    }
                });

                $('#btnSubmit').click(function() {
                    $(this).prop('disabled', true).text('Tunggu..');
                    $('#form-cp').submit();
                });

                $('#tambah_pcp_btn').click(function() {
                    $('#tambahan_label').show();
                    $('#tambahan_pcp').append(
                        $('<div class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto]"><input type="text" name="pekerjaan_id[]" class="input input-bordered input-sm w-full" placeholder="CP tambahan.."/> <input type="text" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" name="tanggal[]" class="input input-bordered input-sm w-full sm:w-36" readonly/></div>')
                    );
                });

                $('#lHarian').click(function() { $('#hCont').slideToggle('fast'); });
                $('#lMingguan').click(function() { $('#mCont').slideToggle('fast'); });
                $('#lBulanan').click(function() { $('#bCont').slideToggle('fast'); });
                $('#lIsidental').click(function() { $('#iCont').slideToggle('fast'); });
            });
        </script>
    </x-main-div>
</x-app-layout>
