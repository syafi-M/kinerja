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
                        <h1 class="text-2xl font-bold text-white sm:text-3xl">Pengajuan Cutting</h1>
                        <p class="text-sm text-slate-200">Formulir Pengajuan Cutting</p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('cutting.history') }}"
                        class="btn btn-sm border-none bg-white/20 text-white hover:bg-white/30">
                        <i class="ri-history-line"></i>
                        Lihat Riwayat
                    </a>
                </div>
            </div>

            <div id="alertBox" class="mb-4"></div>

            <div class="card-white p-6 sm:p-8">
                <form id="cuttingForm" x-data="cuttingForm()" enctype="multipart/form-data"
                    data-store-url="{{ route('cutting.store') }}"
                    data-user-search-url="{{ route('cutting.users.search') }}">
                    <input type="hidden" id="cutting_id" />

                    <div class="mb-6">
                        <label class="label">
                            <span class="label-text font-semibold">
                                Fullname <span class="text-error">*</span>
                            </span>
                        </label>

                        <div class="space-y-2" @click.outside="openSearch = false">
                            <div class="relative">
                                <input type="text" x-model="userQuery" @focus="openSearch = true"
                                    placeholder="Ketik minimal 2 huruf nama user..."
                                    class="input input-bordered w-full pr-10"
                                    :class="showUserError ? 'input-error' : ''">
                                <span class="pointer-events-none absolute right-3 top-3 text-slate-400">
                                    <i class="ri-search-line"></i>
                                </span>
                            </div>

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

                    <div class="mb-6">
                        <label for="date_cutting" class="mb-2 block text-sm font-semibold text-slate-700">
                            Tanggal Cutting <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_cutting" id="date_cutting"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            required>
                    </div>

                    <div class="mb-6">
                        <label for="type_cutting" class="mb-2 block text-sm font-semibold text-slate-700">
                            Type Cutting <span class="text-red-500">*</span>
                        </label>
                        <select name="type_cutting" id="type_cutting"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            x-model="selectedTypeCutting" required>
                            <option value="">-- Pilih Type Cutting --</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Kinerja">Kinerja</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-6" x-show="selectedTypeCutting === 'Lainnya'" x-transition>
                        <label for="type_cutting_manual" class="mb-2 block text-sm font-semibold text-slate-700">
                            Type Cutting Manual <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="type_cutting_manual" id="type_cutting_manual" x-model="manualType"
                            placeholder="Masukkan type cutting manual"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            :required="selectedTypeCutting === 'Lainnya'">
                    </div>

                    <div class="mb-6">
                        <label for="desc" class="mb-2 block text-sm font-semibold text-slate-700">
                            Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="desc" id="desc" rows="4" placeholder="Masukkan keterangan cutting"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            required></textarea>
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
        </div>
    </x-main-div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cuttingForm', () => ({
                selectedUserId: null,
                selectedUserName: '',
                userQuery: '',
                userResults: [],
                openSearch: false,
                isSearching: false,
                searchError: '',
                searchTimeout: null,

                selectedTypeCutting: '',
                manualType: '',
                showUserError: false,

                selectUser(user) {
                    this.selectedUserId = user.id;
                    this.selectedUserName = user.nama_lengkap;
                    this.userQuery = user.nama_lengkap;
                    this.openSearch = false;
                    this.userResults = [];
                    this.showUserError = false;
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
                    this.$watch('selectedTypeCutting', value => {
                        if (value !== 'Lainnya') {
                            this.manualType = '';
                        }
                    });

                    this.$watch('userQuery', () => {
                        if (this.selectedUserId && this.userQuery.trim() === this.selectedUserName) return;

                        if (this.searchTimeout) clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => this.searchUsers(), 300);
                    });
                },

                resetFormState() {
                    this.selectedUserId = null;
                    this.selectedUserName = '';
                    this.userQuery = '';
                    this.userResults = [];
                    this.openSearch = false;
                    this.isSearching = false;
                    this.searchError = '';
                    this.selectedTypeCutting = '';
                    this.manualType = '';
                    this.showUserError = false;
                }
            }));
        });

        $(document).ready(function() {
            const $form = $('#cuttingForm');
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
                $('#cutting_id').val('');
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

                if (!alpineData?.selectedUserId) {
                    if (alpineData) alpineData.showUserError = true;
                    showFormErrors(['Fullname wajib dipilih dari user yang tersedia.']);
                    return;
                }

                if (alpineData) alpineData.showUserError = false;

                const payload = {
                    user_id: alpineData.selectedUserId,
                    date_cutting: $('#date_cutting').val(),
                    type_cutting: $('#type_cutting').val(),
                    type_cutting_manual: alpineData?.manualType || '',
                    desc: $('#desc').val()
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
