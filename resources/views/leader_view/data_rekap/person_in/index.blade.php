<x-app-layout>
    <x-main-div>
        <div class="w-full max-w-3xl px-3 py-4 mx-auto sm:px-5 lg:px-6">
            <div class="p-4 mb-4 bg-white border rounded-lg shadow-sm border-white/60 ring-1 ring-slate-900/5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center min-w-0 gap-3">
                        <a href="{{ route('index.rekap.data.leader') }}"
                            class="inline-flex items-center justify-center w-10 h-10 ml-1 transition rounded-lg shrink-0 sm:ml-0 bg-slate-100 text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
                            aria-label="Kembali ke rekapitulasi">
                            <i class="text-xl ri-arrow-left-line"></i>
                        </a>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500">Data Rekap</p>
                            <h1 class="text-xl font-bold leading-tight truncate text-slate-900 sm:text-2xl">
                                Pengajuan Personil Masuk
                            </h1>
                            <p class="mt-1 text-sm leading-5 text-slate-500">
                                Tambahkan personil masuk dan lengkapi detail awalnya.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('person.in.history') }}"
                        class="items-center hidden gap-2 px-3 text-sm font-semibold transition bg-white border rounded-lg min-h-10 shrink-0 border-slate-200 text-slate-700 hover:bg-slate-50 sm:inline-flex">
                        <i class="ri-history-line"></i>
                        Riwayat
                    </a>
                </div>
            </div>

            <div id="alertBox" class="mb-4"></div>

            <form id="personInForm" x-data="personInForm()" enctype="multipart/form-data"
                data-store-url="{{ route('person-in.store') }}"
                data-user-search-url="{{ route('person-in.users.search') }}" class="space-y-4">
                <input type="hidden" id="person_in_id" />
                <input type="hidden" name="has_account" :value="hasAccount">
                <input type="hidden" name="fullname" :value="getResolvedFullname()">

                <section class="p-4 bg-white border rounded-lg shadow-sm border-slate-200 sm:p-5">
                    <div class="mb-3">
                        <h2 class="text-sm font-semibold text-slate-800">Status Akun</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Pilih apakah personil sudah memiliki akun di sistem.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                        <label
                            class="flex items-start gap-3 p-3 transition border rounded-lg cursor-pointer min-h-14 border-slate-200 hover:bg-slate-50"
                            :class="hasAccount === 'yes' ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-200' : ''">
                            <input type="radio" name="has_account_option" value="yes" x-model="hasAccount"
                                @change="setAccountMode('yes')" class="w-4 h-4 mt-1 text-sky-600 focus:ring-sky-500">
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-slate-800">Sudah memiliki akun</span>
                                <span class="mt-0.5 block text-xs leading-4 text-slate-500">Cari dari data user yang tersedia.</span>
                            </span>
                        </label>
                        <label
                            class="flex items-start gap-3 p-3 transition border rounded-lg cursor-pointer min-h-14 border-slate-200 hover:bg-slate-50"
                            :class="hasAccount === 'no' ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-200' : ''">
                            <input type="radio" name="has_account_option" value="no" x-model="hasAccount"
                                @change="setAccountMode('no')" class="w-4 h-4 mt-1 text-sky-600 focus:ring-sky-500">
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-slate-800">Belum memiliki akun</span>
                                <span class="mt-0.5 block text-xs leading-4 text-slate-500">Isi nama lengkap secara manual.</span>
                            </span>
                        </label>
                    </div>

                    <div class="mt-4" x-show="hasAccount === 'yes'" x-collapse>
                        <label for="user_search" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Cari User <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2" @click.outside="openSearch = false">
                            <div class="relative">
                                <input type="text" id="user_search" x-model="userQuery" @focus="openSearch = true"
                                    placeholder="Ketik minimal 2 huruf nama user..."
                                    class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 pr-10 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                    :class="showFullnameError ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : ''">
                                <span class="absolute pointer-events-none right-3 top-3 text-slate-400">
                                    <i class="ri-search-line"></i>
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Pilih user dari hasil pencarian yang muncul.</p>

                            <div x-show="selectedUserName" class="px-3 py-2 border rounded-lg border-sky-100 bg-sky-50">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="min-w-0 text-sm truncate text-slate-700">Terpilih:
                                        <span class="font-semibold" x-text="selectedUserName"></span>
                                    </span>
                                    <button type="button"
                                        class="inline-flex items-center px-2 text-xs font-semibold rounded-lg min-h-8 text-sky-700 hover:bg-sky-100"
                                        @click="clearSelectedUser()">
                                        Ganti
                                    </button>
                                </div>
                            </div>

                            <div x-show="shouldShowSearchPanel()" x-transition
                                class="overflow-y-auto bg-white border rounded-lg shadow-sm max-h-64 border-slate-200">
                                <template x-if="isSearching">
                                    <div class="px-4 py-3 text-sm text-slate-500">Mencari user...</div>
                                </template>
                                <template x-if="!isSearching && searchError">
                                    <div class="px-4 py-3 text-sm text-red-600" x-text="searchError"></div>
                                </template>
                                <template x-if="!isSearching && !searchError && userQuery.trim().length >= 2 && userResults.length === 0">
                                    <div class="px-4 py-3 text-sm text-slate-500">User tidak ditemukan.</div>
                                </template>
                                <template x-for="user in userResults" :key="user.id">
                                    <button type="button"
                                        class="flex items-center justify-between w-full px-4 py-3 text-sm text-left min-h-11 hover:bg-slate-50"
                                        @click="selectUser(user)">
                                        <span x-text="user.nama_lengkap"></span>
                                        <i class="ri-check-line text-sky-600" x-show="selectedUserName === user.nama_lengkap"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4" x-show="hasAccount === 'no'" x-collapse>
                        <label for="fullname_manual" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="fullname_manual" placeholder="Masukkan nama lengkap"
                            class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                            :class="showFullnameError ? 'border-red-400 focus:border-red-500 focus:ring-red-100' : ''"
                            x-model="manualName">
                    </div>
                </section>

                <section class="p-4 bg-white border rounded-lg shadow-sm border-slate-200 sm:p-5">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="jabatan_id" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Jabatan <span class="text-red-500">*</span>
                            </label>
                            <select name="jabatan_id" id="jabatan_id"
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                required>
                                <option value="">Pilih jabatan</option>
                                @foreach ($jabatans as $jabatan)
                                    <option value="{{ $jabatan->id }}">{{ $jabatan->name_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date_in" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Tanggal Masuk <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date_in" id="date_in"
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                required>
                        </div>
                    </div>
                </section>

                <section class="p-4 bg-white border rounded-lg shadow-sm border-slate-200 sm:p-5">
                    <div class="mb-3">
                        <h2 class="text-sm font-semibold text-slate-800">
                            Metode Gaji <span class="text-red-500">*</span>
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500">Pilih metode pembayaran yang disepakati.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-2.5">
                        <label
                            class="flex items-start gap-3 p-3 transition border rounded-lg cursor-pointer min-h-14 border-slate-200 hover:bg-slate-50"
                            :class="selectedMethod === 'transfer' ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-200' : ''">
                            <input type="radio" name="method_salary" value="transfer" x-model="selectedMethod"
                                class="w-4 h-4 mt-1 text-sky-600 focus:ring-sky-500" required>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-slate-800">Transfer</span>
                                <span class="mt-0.5 block text-xs leading-4 text-slate-500">Pembayaran melalui transfer bank.</span>
                            </span>
                        </label>

                        <div x-show="selectedMethod === 'transfer'" x-collapse class="pl-0 sm:pl-7">
                            <label for="method_salary_manual" class="mb-1.5 block text-xs font-medium text-slate-600">
                                Nomor rekening
                            </label>
                            <input type="text" name="method_salary_manual" id="method_salary_manual"
                                x-model="manualMethod" placeholder="Masukkan nomor rekening"
                                class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                :required="selectedMethod === 'transfer'" :disabled="selectedMethod !== 'transfer'">
                        </div>

                        <label
                            class="flex items-start gap-3 p-3 transition border rounded-lg cursor-pointer min-h-14 border-slate-200 hover:bg-slate-50"
                            :class="selectedMethod === 'cash' ? 'border-sky-500 bg-sky-50 ring-1 ring-sky-200' : ''">
                            <input type="radio" name="method_salary" value="cash" x-model="selectedMethod"
                                class="w-4 h-4 mt-1 text-sky-600 focus:ring-sky-500" required>
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-slate-800">Manual / Cash</span>
                                <span class="mt-0.5 block text-xs leading-4 text-slate-500">Pembayaran tunai/manual tanpa rekening.</span>
                            </span>
                        </label>
                    </div>
                </section>

                <div id="formErrors"></div>

                <div
                    class="sticky sm:bottom-16 z-20 mx-1 border-t rounded-md border-slate-200 bg-white/95 p-3 shadow-[0_-8px_18px_rgba(15,23,42,0.08)] backdrop-blur sm:static sm:mx-0 sm:rounded-lg sm:border sm:shadow-sm">
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <button type="submit" id="btnSave"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-amber-400 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">
                            <i class="ri-save-line"></i>
                            <span id="btnSaveText">Simpan Data</span>
                        </button>
                        <button type="button" id="btnReset"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                            <i class="ri-refresh-line"></i>
                            Reset Form
                        </button>
                    </div>
                </div>
            </form>

            <div class="p-4 mt-4 border rounded-lg border-sky-100 bg-sky-50">
                <div class="flex items-start gap-3">
                    <i class="ri-information-line mt-0.5 text-xl text-sky-600"></i>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Informasi Penting</p>
                        <ul class="mt-2 space-y-1 text-xs leading-5 list-disc list-inside text-slate-600">
                            <li>Pastikan data personil dan jabatan sudah benar.</li>
                            <li>Pilih metode gaji sesuai kesepakatan.</li>
                            <li>Data dapat diubah kembali setelah disimpan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </x-main-div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('personInForm', () => ({
                hasAccount: 'yes',
                selectedUserId: null,
                selectedUserName: '',
                manualName: '',
                userQuery: '',
                userResults: [],
                openSearch: false,
                isSearching: false,
                searchError: '',
                searchTimeout: null,
                selectedMethod: '',
                manualMethod: '',
                showFullnameError: false,

                getResolvedFullname() {
                    return this.hasAccount === 'yes' ? (this.selectedUserName || '').trim() : (this.manualName || '').trim();
                },

                setAccountMode(mode) {
                    this.hasAccount = mode;
                    this.showFullnameError = false;
                    this.selectedUserId = null;
                    this.selectedUserName = '';
                    this.manualName = '';
                    this.userQuery = '';
                    this.userResults = [];
                    this.searchError = '';
                    this.openSearch = false;
                },

                selectUser(user) {
                    this.selectedUserId = user.id;
                    this.selectedUserName = user.nama_lengkap;
                    this.userQuery = user.nama_lengkap;
                    this.openSearch = false;
                    this.userResults = [];
                    this.showFullnameError = false;
                },

                clearSelectedUser() {
                    this.selectedUserId = null;
                    this.selectedUserName = '';
                    this.userQuery = '';
                    this.userResults = [];
                    this.searchError = '';
                    this.openSearch = true;
                },

                shouldShowSearchPanel() {
                    return this.openSearch && this.userQuery.trim().length >= 2;
                },

                searchUsers() {
                    const keyword = this.userQuery.trim();

                    if (!this.selectedUserId || keyword !== this.selectedUserName) {
                        this.selectedUserId = null;
                        this.selectedUserName = '';
                    }
                    this.searchError = '';

                    if (keyword.length < 2) {
                        this.userResults = [];
                        return;
                    }

                    this.isSearching = true;

                    fetch(`${this.$el.dataset.userSearchUrl}?q=${encodeURIComponent(keyword)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(async response => {
                            if (!response.ok) throw new Error('Gagal mencari user');
                            return response.json();
                        })
                        .then(result => {
                            this.userResults = result.data || [];
                        })
                        .catch(() => {
                            this.userResults = [];
                            this.searchError = 'Terjadi kesalahan saat mencari user.';
                        })
                        .finally(() => {
                            this.isSearching = false;
                            this.openSearch = true;
                        });
                },

                init() {
                    this.$watch('selectedMethod', value => {
                        if (value !== 'transfer') this.manualMethod = '';
                    });

                    this.$watch('userQuery', () => {
                        if (this.hasAccount !== 'yes') return;
                        if (this.selectedUserId && this.userQuery.trim() === this.selectedUserName) return;

                        if (this.searchTimeout) clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => this.searchUsers(), 300);
                    });
                },

                resetFormState() {
                    this.setAccountMode('yes');
                    this.selectedMethod = '';
                    this.manualMethod = '';
                    this.showFullnameError = false;
                }
            }));
        });

        $(document).ready(function() {
            const $form = $('#personInForm');
            const storeUrl = $form.data('storeUrl');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function showAlert(type, message) {
                const isError = type === 'error';
                const wrapperClass = isError ?
                    'border-rose-200 bg-rose-50 text-rose-700' :
                    'border-emerald-200 bg-emerald-50 text-emerald-700';
                const icon = isError ? 'error-warning' : 'checkbox-circle';

                $('#alertBox').html(`
                    <div class="rounded-lg border px-4 py-3 shadow-sm ${wrapperClass}">
                        <div class="flex items-center gap-2">
                            <i class="ri-${icon}-line"></i>
                            <span>${message}</span>
                        </div>
                    </div>
                `);

                setTimeout(() => $('#alertBox').html(''), 4000);
                $('html, body').animate({ scrollTop: 0 }, 300);
            }

            function showFormErrors(errors) {
                const errorList = errors.map(err => `<li>${err}</li>`).join('');
                $('#formErrors').html(`
                    <div class="px-4 py-3 text-sm border rounded-lg shadow-sm border-rose-200 bg-rose-50 text-rose-700">
                        <div class="flex items-start gap-2">
                            <i class="ri-error-warning-line mt-0.5"></i>
                            <ul class="pl-5 list-disc">${errorList}</ul>
                        </div>
                    </div>
                `);
            }

            function resetForm() {
                $form[0].reset();
                $('#person_in_id').val('');
                $('#formErrors').html('');
                $('#btnSaveText').text('Simpan Data');

                const alpineData = Alpine.$data($form[0]);
                if (alpineData) alpineData.resetFormState();
            }

            function saveData(e) {
                e.preventDefault();
                $('#formErrors').html('');

                const alpineData = Alpine.$data($form[0]);
                const fullname = alpineData ? alpineData.getResolvedFullname() : '';

                if (!fullname) {
                    if (alpineData) alpineData.showFullnameError = true;
                    showFormErrors(['Nama lengkap wajib diisi.']);
                    return;
                }

                if (alpineData) alpineData.showFullnameError = false;

                const payload = {
                    has_account: alpineData ? alpineData.hasAccount : 'yes',
                    fullname: fullname,
                    jabatan_id: $('#jabatan_id').val(),
                    date_in: $('#date_in').val(),
                    method_salary: $('input[name="method_salary"]:checked').val(),
                    method_salary_manual: alpineData ? alpineData.manualMethod : ''
                };

                $.ajax({
                        url: storeUrl,
                        method: 'POST',
                        data: payload
                    })
                    .done(() => {
                        resetForm();
                        showAlert('success', 'Data berhasil ditambahkan');
                    })
                    .fail(xhr => {
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            showFormErrors(Object.values(xhr.responseJSON.errors).flat());
                        } else {
                            showAlert('error', 'Gagal menyimpan data');
                        }
                    });
            }

            $form.on('submit', saveData);

            $('#btnReset').on('click', function() {
                resetForm();
                showAlert('success', 'Form berhasil direset');
            });
        });
    </script>
</x-app-layout>
