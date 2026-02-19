<x-app-layout>
    <x-main-div>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
            <div class="card-container mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('index.rekap.data.leader') }}"
                            class="w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all">
                            <i class="ri-arrow-left-line text-xl text-white"></i>
                        </a>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Riwayat Personil Masuk</h1>
                            <p class="text-slate-200 text-sm">History Pengajuan Personil Masuk</p>
                        </div>
                    </div>
                    <a href="{{ route('person-in.index') }}"
                        class="btn-primary px-4 py-2 rounded-lg flex items-center gap-2 whitespace-nowrap">
                        <i class="ri-add-line"></i><span>Pengajuan Baru</span>
                    </a>
                </div>
            </div>

            <div class="card-white p-4 mb-6">
                <form action="{{ route('person.in.history') }}" method="GET"
                    class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-300 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Di Ajukan" {{ request('status') == 'Di Ajukan' ? 'selected' : '' }}>Di Ajukan
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                        </option>
                    </select>
                    <input type="month" name="month" value="{{ request('month') }}"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 text-sm">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama personil..."
                        class="w-full px-4 py-2 rounded-lg border border-slate-300 text-sm">
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center justify-center gap-2"><i
                                class="ri-filter-3-line"></i><span>Filter</span></button>
                        <a href="{{ route('person.in.history') }}"
                            class="flex-1 px-4 py-2 border-2 border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg font-medium flex items-center justify-center gap-2"><i
                                class="ri-refresh-line"></i><span>Reset</span></a>
                    </div>
                </form>
            </div>

            <div id="alertBox" class="mb-4"></div>

            <div class="card-white overflow-hidden">
                <div class="space-y-3 p-3 sm:hidden">
                    @forelse($personIn as $index => $person)
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $person->fullname }}</p>
                                    <p class="text-xs text-slate-500">{{ $person->jabatan->name_jabatan ?? '-' }}</p>
                                </div>
                                @if (($person->status ?? 'pending') == 'Di Ajukan')
                                    <i
                                        class="ri-checkbox-circle-fill rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700"></i>
                                @elseif(($person->status ?? 'pending') == 'rejected')
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700"><i
                                            class="ri-close-circle-fill"></i>Ditolak</span>
                                @else
                                    <i
                                        class="ri-time-fill rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700"></i>
                                @endif
                            </div>
                            <div class="mb-4 grid grid-cols-2 gap-2 text-xs text-slate-600">
                                <div>
                                    <span class="block text-slate-400">Tanggal Masuk</span>
                                    <span>{{ \Carbon\Carbon::parse($person->date_in)->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <span class="block text-slate-400">Metode Gaji</span>
                                    <span>{{ ($person->method_salary ?? '') === 'transfer' ? 'Transfer' : 'Manual / Cash' }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="viewDetail({{ $person->id }})"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-100">
                                    <i class="ri-eye-line"></i>Lihat
                                </button>
                                @if (($person->status ?? 'pending') === 'pending')
                                    <button onclick="sendPersonIn({{ $person->id }})"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-xs font-medium text-green-700 hover:bg-green-100">
                                        <i class="ri-send-plane-fill"></i>Ajukan
                                    </button>
                                    <button onclick="editData({{ $person->id }})"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-yellow-50 px-3 py-2 text-xs font-medium text-yellow-700 hover:bg-yellow-100">
                                        <i class="ri-edit-line"></i>Edit
                                    </button>
                                    <button onclick="deletePersonIn({{ $person->id }})"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-100">
                                        <i class="ri-delete-bin-line"></i>Hapus
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-10 text-center">
                            <p class="font-medium text-slate-600">Belum ada riwayat pengajuan personil masuk</p>
                            <a href="{{ route('person-in.index') }}"
                                class="btn-primary mt-3 inline-flex items-center gap-2 rounded-lg px-6 py-2"><i
                                    class="ri-add-line"></i><span>Buat Pengajuan</span></a>
                        </div>
                    @endforelse
                </div>

                <div class="hidden overflow-x-auto sm:block">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase">No</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Nama</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Jabatan
                                </th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Tanggal
                                    Masuk</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Metode
                                    Gaji</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase">Status
                                </th>
                                <th class="px-4 py-4 text-center text-xs font-semibold text-slate-600 uppercase">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($personIn as $index => $person)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-4 text-sm text-slate-700">{{ $personIn->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span
                                                    class="text-blue-600 font-semibold text-sm">{{ strtoupper(substr($person->fullname ?? 'N', 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-800 whitespace-nowrap">
                                                    {{ $person->fullname }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-700 whitespace-nowrap">
                                        {{ $person->jabatan->name_jabatan ?? '-' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-700 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($person->date_in)->format('d M Y') }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-700 whitespace-nowrap">
                                        {{ ($person->method_salary ?? '') === 'transfer' ? 'Transfer' : 'Manual / Cash' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @if (($person->status ?? 'pending') == 'Di Ajukan')
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium"><i
                                                    class="ri-checkbox-circle-fill"></i>Di Ajukan</span>
                                        @elseif(($person->status ?? 'pending') == 'rejected')
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-medium"><i
                                                    class="ri-close-circle-fill"></i>Ditolak</span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-medium"><i
                                                    class="ri-time-fill"></i>Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="viewDetail({{ $person->id }})"
                                                class="w-9 h-9 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 flex items-center justify-center"
                                                title="Lihat"><i class="ri-eye-line"></i></button>
                                            @if (($person->status ?? 'pending') === 'pending')
                                                <button onclick="editData({{ $person->id }})"
                                                    class="w-9 h-9 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-600 flex items-center justify-center"
                                                    title="Edit"><i class="ri-edit-line"></i></button>
                                                <button onclick="deletePersonIn({{ $person->id }})"
                                                    class="w-9 h-9 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center"
                                                    title="Hapus"><i class="ri-delete-bin-line"></i></button>
                                                <button onclick="sendPersonIn({{ $person->id }})"
                                                    class="w-9 h-9 rounded-lg bg-green-100 hover:bg-green-200 text-green-600 flex items-center justify-center"
                                                    title="Ajukan"><i class="ri-send-plane-fill"></i></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <p class="text-slate-600 font-medium">Belum ada riwayat pengajuan personil
                                            masuk</p>
                                        <a href="{{ route('person-in.index') }}"
                                            class="btn-primary px-6 py-2 rounded-lg inline-flex items-center gap-2 mt-3"><i
                                                class="ri-add-line"></i><span>Buat Pengajuan</span></a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($personIn->total() > 0)
                    <div class="border-t border-slate-200 px-4 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <form action="{{ route('person.in.history') }}" method="GET"
                                class="inline-flex items-center gap-2">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="month" value="{{ request('month') }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <label for="per_page" class="text-xs text-slate-500 whitespace-nowrap">Baris per
                                    halaman</label>
                                <select id="per_page" name="per_page" onchange="this.form.submit()"
                                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs">
                                    @foreach ($allowedPerPage ?? [10, 15, 25, 50] as $size)
                                        <option value="{{ $size }}"
                                            {{ (int) request('per_page', $perPage ?? 15) === $size ? 'selected' : '' }}>
                                            {{ $size }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            <div class="text-xs text-slate-500">
                                Menampilkan {{ $personIn->firstItem() }}-{{ $personIn->lastItem() }} dari
                                {{ $personIn->total() }} data
                            </div>
                        </div>
                        @if ($personIn->hasPages())
                            <div class="mt-3">
                                {{ $personIn->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="w-full flex justify-end items-center">
                <form action="{{ route('person-in-bulk.status') }}" method="post" class="mt-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="month" value="{{ request('month') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="per_page" value="{{ request('per_page', $perPage ?? 15) }}">
                    <button type="submit"
                        class="m-2 btn btn-sm rounded-sm bg-green-100 hover:bg-green-200 text-green-600 flex items-center justify-center transition-all"><i
                            class="ri-send-plane-fill"></i>Ajukan Semua</button>
                </form>
            </div>
        </div>
    </x-main-div>

    <input type="checkbox" id="modal-edit" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box max-w-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-slate-800">Edit Personil Masuk</h3>
                <label for="modal-edit" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"><i
                        class="ri-close-line text-xl"></i></label>
            </div>
            <form id="personInForm" x-data="personInFormData()">
                <input type="hidden" id="person_in_id" />
                <div class="mb-4"><label for="fullname"
                        class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap <span
                            class="text-red-500">*</span></label><input type="text" name="fullname"
                        id="fullname" class="w-full px-4 py-2 rounded-lg border border-slate-300" required></div>
                <div class="mb-4"><label for="jabatan_id"
                        class="block text-sm font-semibold text-slate-700 mb-2">Jabatan <span
                            class="text-red-500">*</span></label><select name="jabatan_id" id="jabatan_id"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300" required>
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach ($jabatans as $jabatan)
                            <option value="{{ $jabatan->id }}">{{ $jabatan->name_jabatan }}</option>
                        @endforeach
                    </select></div>
                <div class="mb-4"><label for="date_in"
                        class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Masuk <span
                            class="text-red-500">*</span></label><input type="date" name="date_in" id="date_in"
                        class="w-full px-4 py-2 rounded-lg border border-slate-300" required></div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-3">Metode Gaji <span
                            class="text-red-500">*</span></label>
                    <div class="space-y-3">
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer"
                            :class="selectedMethod === 'transfer' ? 'border-blue-500 bg-blue-50' : ''"><input
                                type="radio" name="method_salary" value="transfer" x-model="selectedMethod"
                                class="mt-1 w-4 h-4" required>
                            <div class="flex-1">
                                <p class="font-medium text-slate-800 text-sm">Transfer</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer"
                            :class="selectedMethod === 'cash' ? 'border-blue-500 bg-blue-50' : ''"><input
                                type="radio" name="method_salary" value="cash" x-model="selectedMethod"
                                class="mt-1 w-4 h-4" required>
                            <div class="flex-1">
                                <p class="font-medium text-slate-800 text-sm">Manual / Cash</p>
                            </div>
                        </label>
                        <div x-show="selectedMethod === 'transfer'" x-transition><input type="text"
                                name="method_salary_manual" id="method_salary_manual" x-model="manualMethod"
                                placeholder="Masukkan nomor rekening..."
                                class="w-full px-4 py-2 rounded-lg border border-slate-300"
                                :required="selectedMethod === 'transfer'"></div>
                    </div>
                </div>
                <div id="formErrors" class="mb-4"></div>
            </form>
            <div class="modal-action"><label for="modal-edit" class="btn btn-ghost">Batal</label><button
                    id="btnSave" class="btn btn-primary gap-2"><i class="ri-save-line"></i>Update Data</button>
            </div>
        </div>
    </div>

    <div x-data="{ open: false, detail: {} }" x-show="open" @detail-modal.window="open = true; detail = $event.detail" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" style="display:none;">
        <div class="fixed inset-0 bg-black/50" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6" @click.away="open = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Detail Pengajuan Personil Masuk</h3><button
                        @click="open = false"
                        class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center"><i
                            class="ri-close-line text-xl text-slate-600"></i></button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pb-4 border-b border-slate-200">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Nama Pegawai</p>
                            <p class="font-semibold text-slate-800" x-text="detail.fullname || '-' "></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Jabatan</p>
                            <p class="font-semibold text-slate-800" x-text="detail.jabatan?.name_jabatan || '-' "></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Tanggal Masuk</p>
                            <p class="font-semibold text-slate-800"
                                x-text="detail.date_in ? new Date(detail.date_in).toLocaleDateString('id-ID') : '-' ">
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Metode Gaji</p>
                            <p class="font-semibold text-slate-800"
                                x-text="detail.method_salary === 'transfer' ? 'Transfer' : 'Manual / Cash'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Nomor Rekening</p>
                            <p class="font-semibold text-slate-800" x-text="detail.method_salary_manual || '-' "></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Status</p><span x-text="detail.status ?? 'pending'"
                                :class="{
                                    'bg-green-100 text-green-700': detail.status ==
                                        'Di Ajukan',
                                    'bg-yellow-100 text-yellow-700': !detail.status || detail.status ==
                                        'pending',
                                    'bg-red-100 text-red-700': detail.status == 'rejected'
                                }"
                                class="inline-block px-3 py-1 rounded-full text-xs font-medium"></span>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Tanggal Pengisian</p>
                            <p class="text-slate-800"
                                x-text="detail.created_at?.replace('T', ' ').split('.')[0] || '-'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function personInFormData() {
            return {
                selectedMethod: '',
                manualMethod: '',
                init() {
                    this.$watch('selectedMethod', value => {
                        if (value !== 'transfer') {
                            this.manualMethod = '';
                            $('#method_salary_manual').val('');
                        }
                    });
                }
            }
        }

        function showAlert(type, message) {
            const isError = type === 'error';
            const wrapperClass = isError ?
                'border-rose-200 bg-rose-50 text-rose-700' :
                'border-emerald-200 bg-emerald-50 text-emerald-700';
            const icon = isError ? 'error-warning' : 'checkbox-circle';
            $('#alertBox').html(
                `<div class="rounded-lg border px-4 py-3 shadow-sm ${wrapperClass}"><div class="flex items-center gap-2"><i class="ri-${icon}-line"></i><span>${message}</span></div></div>`
            );
            setTimeout(() => $('#alertBox').html(''), 4000);
        }

        function resetForm() {
            $('#personInForm')[0].reset();
            $('#person_in_id').val('');
            $('#formErrors').html('');
            const formElement = document.getElementById('personInForm');
            if (formElement && formElement._x_dataStack) {
                const alpineData = formElement._x_dataStack[0];
                if (alpineData) {
                    alpineData.selectedMethod = '';
                    alpineData.manualMethod = '';
                }
            }
        }

        async function viewDetail(id) {
            const url = "{{ route('person-in-id', ':id') }}".replace(':id', id);
            try {
                const res = await fetch(url);
                const response = await res.json();
                window.dispatchEvent(new CustomEvent('detail-modal', {
                    detail: response.data
                }));
            } catch (err) {
                console.error(err);
            }
        }

        function deletePersonIn(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/person-in/${id}`;
            form.innerHTML =
                `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
            document.body.appendChild(form);
            form.submit();
        }

        function sendPersonIn(id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/person-in-change-status/${id}`;
            form.innerHTML =
                `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PATCH">`;
            document.body.appendChild(form);
            form.submit();
        }

        function editData(id) {
            $.get("{{ route('person-in.show', ':id') }}".replace(':id', id))
                .done(response => {
                    const data = response.data;
                    resetForm();
                    $('#person_in_id').val(data.id);
                    $('#fullname').val(data.fullname || '');
                    $('#jabatan_id').val(data.jabatan_id || '');
                    $('#date_in').val(data.date_in || '');
                    $('#method_salary_manual').val(data.method_salary_manual || '');
                    $(`input[name="method_salary"][value="${data.method_salary}"]`).prop('checked', true);
                    setTimeout(() => {
                        const formElement = document.getElementById('personInForm');
                        if (formElement && formElement._x_dataStack) {
                            const alpineData = formElement._x_dataStack[0];
                            if (alpineData) {
                                alpineData.selectedMethod = data.method_salary || '';
                                alpineData.manualMethod = data.method_salary_manual || '';
                            }
                        }
                    }, 100);
                    $('#modal-edit').prop('checked', true);
                })
                .fail(() => showAlert('error', 'Gagal mengambil data'));
        }

        function saveData(e) {
            e.preventDefault();
            $('#formErrors').html('');
            const id = $('#person_in_id').val();
            const payload = {
                fullname: $('#fullname').val(),
                jabatan_id: $('#jabatan_id').val(),
                date_in: $('#date_in').val(),
                method_salary: $('input[name="method_salary"]:checked').val(),
                method_salary_manual: $('#method_salary_manual').val()
            };
            $.ajax({
                    url: "{{ route('person-in.update', ':id') }}".replace(':id', id),
                    method: 'PUT',
                    data: payload
                })
                .done(() => {
                    $('#modal-edit').prop('checked', false);
                    showAlert('success', 'Data berhasil diperbarui');
                    setTimeout(() => window.location.reload(), 400);
                })
                .fail(xhr => {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        $('#formErrors').html(
                            `<div class="alert alert-error shadow-sm rounded-lg"><i class="ri-error-warning-line"></i><ul class="list-disc pl-5 ml-6">${errors.map(err => `<li>${err}</li>`).join('')}</ul></div>`
                        );
                    } else showAlert('error', 'Gagal menyimpan data');
                });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#btnSave').on('click', saveData);
        });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f1f5f9;
        }

        .card-container {
            background: #64748b;
            border-radius: 16px;
            padding: 24px;
        }

        .card-white {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        }

        .btn-primary {
            background: #fbbf24;
            color: #1f2937;
            font-weight: 600;
            transition: all .2s ease;
        }

        .btn-primary:hover {
            background: #f59e0b;
            transform: translateY(-1px);
        }

        [x-cloak] {
            display: none !important;
        }

        @media (max-width: 640px) {
            .card-container {
                padding: 16px;
            }
        }
    </style>
</x-app-layout>
