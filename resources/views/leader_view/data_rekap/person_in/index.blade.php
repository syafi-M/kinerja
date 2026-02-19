<x-app-layout>
    <x-main-div>
        <div class="mx-auto max-w-4xl p-4 sm:p-6 lg:p-8">
            <div class="card-container mb-6">
                <div class="mb-2 flex items-center gap-3">
                    <a href="{{ route('index.rekap.data.leader') }}"
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10 transition-all hover:bg-white/20">
                        <i class="ri-arrow-left-line text-xl text-white"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-white sm:text-3xl">Pengajuan Personil Masuk</h1>
                        <p class="text-sm text-slate-200">Formulir Pengajuan Personil Masuk</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('person.in.history') }}"
                        class="btn btn-sm border-none bg-white/20 text-white hover:bg-white/30">
                        <i class="ri-history-line"></i>
                        Lihat Riwayat
                    </a>
                </div>
            </div>

            <div id="alertBox" class="mb-4"></div>

            <div class="card-white p-6 sm:p-8">
                <form id="personInForm" x-data="personInForm()" enctype="multipart/form-data"
                    data-store-url="{{ route('person-in.store') }}"
                    data-user-search-url="{{ route('person-in.users.search') }}">
                    <input type="hidden" id="person_in_id" />
                    <input type="hidden" name="has_account" :value="hasAccount">
                    <input type="hidden" name="fullname" :value="getResolvedFullname()">

                    <div class="mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">Status Akun</span>
                        </label>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-6">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="has_account_option" value="yes" class="radio radio-primary"
                                    x-model="hasAccount" @change="setAccountMode('yes')">
                                <span>Sudah Memiliki Akun</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="has_account_option" value="no" class="radio radio-primary"
                                    x-model="hasAccount" @change="setAccountMode('no')">
                                <span>Belum Memiliki Akun</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6" x-show="hasAccount === 'yes'" x-transition>
                        <label class="label">
                            <span class="label-text font-semibold">
                                Cari User <span class="text-error">*</span>
                            </span>
                        </label>

                        <div class="space-y-2" @click.outside="openSearch = false">
                            <div class="relative">
                                <input type="text" x-model="userQuery" @focus="openSearch = true"
                                    placeholder="Ketik minimal 2 huruf nama user..."
                                    class="input input-bordered w-full pr-10"
                                    :class="showFullnameError ? 'input-error' : ''">
                                <span class="pointer-events-none absolute right-3 top-3 text-slate-400">
                                    <i class="ri-search-line"></i>
                                </span>
                            </div>

                            <p class="text-xs text-slate-500">Masukkan kata kunci untuk menampilkan daftar user.</p>

                            <div x-show="selectedUserName" class="rounded-lg border border-info/30 bg-info/10 px-3 py-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm text-slate-700">Terpilih: <span class="font-semibold"
                                            x-text="selectedUserName"></span></span>
                                    <button type="button" class="btn btn-ghost btn-xs" @click="clearSelectedUser()">
                                        Ganti
                                    </button>
                                </div>
                            </div>

                            <div x-show="shouldShowSearchPanel()" x-transition
                                class="max-h-60 overflow-y-auto rounded-lg border border-slate-200 bg-white shadow-sm">
                                <template x-if="isSearching">
                                    <div class="px-4 py-3 text-sm text-slate-500">Mencari user...</div>
                                </template>
                                <template x-if="!isSearching && searchError">
                                    <div class="px-4 py-3 text-sm text-error" x-text="searchError"></div>
                                </template>
                                <template x-if="!isSearching && !searchError && userQuery.trim().length >= 2 && userResults.length === 0">
                                    <div class="px-4 py-3 text-sm text-slate-500">User tidak ditemukan.</div>
                                </template>
                                <template x-for="user in userResults" :key="user.id">
                                    <button type="button"
                                        class="flex w-full items-center justify-between px-4 py-3 text-left text-sm hover:bg-slate-50"
                                        @click="selectUser(user)">
                                        <span x-text="user.nama_lengkap"></span>
                                        <i class="ri-check-line text-info"
                                            x-show="selectedUserName === user.nama_lengkap"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6" x-show="hasAccount === 'no'" x-transition>
                        <label for="fullname_manual" class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="fullname_manual" placeholder="Masukkan nama lengkap"
                            class="input input-bordered w-full" :class="showFullnameError ? 'input-error' : ''"
                            x-model="manualName">
                    </div>

                    <div class="mb-6">
                        <label for="jabatan_id" class="mb-2 block text-sm font-semibold text-slate-700">
                            Jabatan <span class="text-red-500">*</span>
                        </label>
                        <select name="jabatan_id" id="jabatan_id"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}">{{ $jabatan->name_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="date_in" class="mb-2 block text-sm font-semibold text-slate-700">
                            Tanggal Masuk <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_in" id="date_in"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            required>
                    </div>

                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-semibold text-slate-700">
                            Metode Gaji <span class="text-red-500">*</span>
                        </label>

                        <div class="space-y-3">
                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-4 transition-all hover:bg-slate-50"
                                :class="selectedMethod === 'transfer' ? 'border-blue-500 bg-blue-50' : ''">
                                <input type="radio" name="method_salary" value="transfer" x-model="selectedMethod"
                                    class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500" required>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800">Transfer</p>
                                    <p class="mt-1 text-xs text-slate-500">Pembayaran melalui transfer bank</p>
                                </div>
                            </label>

                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-4 transition-all hover:bg-slate-50"
                                :class="selectedMethod === 'cash' ? 'border-blue-500 bg-blue-50' : ''">
                                <input type="radio" name="method_salary" value="cash" x-model="selectedMethod"
                                    class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500" required>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800">Manual / Cash</p>
                                    <p class="mt-1 text-xs text-slate-500">Pembayaran tunai/manual tanpa transfer
                                        rekening</p>
                                </div>
                            </label>

                            <div x-show="selectedMethod === 'transfer'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2" class="mt-2">
                                <input type="text" name="method_salary_manual" id="method_salary_manual"
                                    x-model="manualMethod" placeholder="Masukkan nomor rekening..."
                                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                    :required="selectedMethod === 'transfer'">
                                <p class="mt-1 text-xs text-slate-500">Wajib diisi jika metode gaji Transfer.</p>
                            </div>
                        </div>
                    </div>

                    <div id="formErrors" class="mb-6"></div>

                    <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row">
                        <button type="submit" id="btnSave"
                            class="btn-primary flex flex-1 items-center justify-center gap-2 rounded-lg px-6 py-3 font-semibold">
                            <i class="ri-save-line"></i>
                            <span id="btnSaveText">Simpan Data</span>
                        </button>
                        <button type="button" id="btnReset"
                            class="flex flex-1 items-center justify-center gap-2 rounded-lg border-2 border-slate-300 px-6 py-3 font-semibold text-slate-700 transition-all hover:bg-slate-50">
                            <i class="ri-refresh-line"></i>
                            <span>Reset Form</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-white mt-4 border-l-4 border-blue-500 p-4">
                <div class="flex items-start gap-3">
                    <i class="ri-information-line mt-0.5 text-xl text-blue-500"></i>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Informasi Penting</p>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-xs text-slate-600">
                            <li>Pastikan semua data yang diisi sudah benar</li>
                            <li>Pilih metode gaji sesuai dengan kesepakatan</li>
                            <li>Data dapat diubah kembali setelah disimpan</li>
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
                    return this.hasAccount === 'yes' ? (this.selectedUserName || '').trim() : (this.manualName || '')
                        .trim();
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
                            if (!response.ok) {
                                throw new Error('Gagal mencari user');
                            }
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
                        if (value !== 'transfer') {
                            this.manualMethod = '';
                        }
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
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            }

            function showFormErrors(errors) {
                const errorList = errors.map(err => `<li>${err}</li>`).join('');
                $('#formErrors').html(`
                    <div class="alert alert-error rounded-lg shadow-sm">
                        <i class="ri-error-warning-line"></i>
                        <ul class="ml-6 list-disc pl-5">${errorList}</ul>
                    </div>
                `);
            }

            function resetForm() {
                $form[0].reset();
                $('#person_in_id').val('');
                $('#formErrors').html('');
                $('#btnSaveText').text('Simpan Data');

                const alpineData = Alpine.$data($form[0]);
                if (alpineData) {
                    alpineData.resetFormState();
                }
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #fbbf24;
            color: #1f2937;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: #f59e0b;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(251, 191, 36, 0.3);
        }

        input[type="radio"]:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(0.5);
        }

        input[type="date"]:hover::-webkit-calendar-picker-indicator {
            filter: invert(0.3);
        }

        select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            appearance: none;
        }

        @media (max-width: 640px) {
            .card-container {
                padding: 16px;
            }
        }
    </style>
</x-app-layout>
