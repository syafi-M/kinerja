<x-app-layout>
    <x-main-div>
        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

            <!-- Header Section with Back Button -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <a href="{{ route('manajemen_rekap') }}"
                                class="p-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Data Lembur</h1>
                                <p class="text-gray-600 text-sm mt-1">{{ $client->name ?? 'Nama Mitra' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button onclick="exportToExcel()"
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span>Excel</span>
                        </button>
                        <button onclick="exportToPDF()"
                            class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span>PDF</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-indigo-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide font-medium">Total Lembur</p>
                            <p class="text-gray-900 text-2xl font-bold" id="totalOvertimes">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-emerald-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide font-medium">Disetujui</p>
                            <p class="text-gray-900 text-2xl font-bold" id="approvedCount">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-violet-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide font-medium">Total Karyawan</p>
                            <p class="text-gray-900 text-2xl font-bold" id="totalEmployees">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-amber-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide font-medium">Periode</p>
                            <p class="text-gray-900 text-lg font-bold" id="periodDate">{{ now()->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm mb-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Karyawan</label>
                        <input type="text" id="searchEmployee" placeholder="Nama karyawan..."
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Tanggal</label>
                        <input type="date" id="filterDate"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="filterStatus"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="resetFilters()"
                            class="w-full sm:w-auto px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="bg-white border border-gray-200 rounded-lg p-8 text-center shadow-sm">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Memuat data...</p>
            </div>

            <!-- Table Section -->
            <div id="tableContainer"
                class="hidden bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="overtimeTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Karyawan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipe Lembur</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tableBody">
                            <!-- Data will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState"
                class="hidden bg-white border border-gray-200 rounded-lg p-8 sm:p-12 text-center shadow-sm">
                <div class="max-w-sm mx-auto">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-800 text-base sm:text-lg font-semibold mb-1" id="emptyStateTitle">Tidak ada
                        data lembur</p>
                    <p class="text-gray-500 text-sm" id="emptyStateDesc">Belum ada data lembur untuk periode ini</p>
                </div>
            </div>

        </div>
    </x-main-div>

    <!-- Include SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- Include jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    <script>
        let overtimeData = [];
        let filteredData = [];

        // Fetch overtime data with better error handling
        async function fetchOvertimeData() {
            try {
                console.log('Fetching overtime data...'); // Debug log
                const kerjasamaId = window.location.pathname.split('/').pop();
                console.log(kerjasamaId);

                const response = await fetch(`/api/v1/overtimes-api/${kerjasamaId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });

                console.log('Response status:', response.status); // Debug log

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('API Response:', result); // Debug log

                // Hide loading state
                document.getElementById('loadingState').classList.add('hidden');

                if (result.success && result.data) {
                    overtimeData = result.data;
                    filteredData = overtimeData;

                    if (overtimeData.length > 0) {
                        renderTable();
                        updateStats();
                        document.getElementById('tableContainer').classList.remove('hidden');
                    } else {
                        showEmptyState('Tidak ada data lembur', 'Belum ada data lembur untuk periode ini');
                    }
                } else {
                    showEmptyState('Gagal memuat data', result.message || 'Terjadi kesalahan saat mengambil data');
                }
            } catch (error) {
                console.error('Fetch Error:', error); // Debug log
                document.getElementById('loadingState').classList.add('hidden');
                showEmptyState('Terjadi Kesalahan', error.message || 'Tidak dapat terhubung ke server');
            }
        }

        // Show empty state with custom message
        function showEmptyState(title, description) {
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('emptyStateTitle').textContent = title;
            document.getElementById('emptyStateDesc').textContent = description;
        }

        // Get overtime type display
        function getOvertimeTypeDisplay(item) {
            const typeOvertime = (item.type_overtime || '').toLowerCase();

            if (typeOvertime === 'lainnya') {
                return item.type_overtime_manual || 'Lainnya';
            }

            return item.type_overtime || '-';
        }

        // Get status badge HTML
        function getStatusBadge(status) {
            const statusLower = (status || '').toLowerCase();
            const badges = {
                'pending': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
                'approved': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>',
                'rejected': '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>'
            };
            return badges[statusLower] ||
                `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">${status || '-'}</span>`;
        }

        // Render table
        // Render table
        function renderTable() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            if (filteredData.length === 0) {
                showEmptyState('Tidak ada hasil', 'Tidak ada data yang sesuai dengan filter');
                document.getElementById('tableContainer').classList.add('hidden');
                return;
            }

            filteredData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';

                const overtimeType = getOvertimeTypeDisplay(item);
                const statusBadge = getStatusBadge(item.status);

                row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${index + 1}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.user?.nama_lengkap || '-'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${formatDate(item.date_overtime)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span class="px-2 py-1 text-xs font-medium rounded bg-indigo-100 text-indigo-800">${overtimeType}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">${statusBadge}</td>
            <td class="px-6 py-4 text-sm text-gray-600">
                <span class="font-semibold text-indigo-600">${item.type_overtime_manual || '-'}</span>
                ${item.count > 1 ? `<span class="ml-2 text-xs text-gray-500">(${item.count} data)</span>` : ''}
            </td>
        `;

                tbody.appendChild(row);
            });

            document.getElementById('emptyState').classList.add('hidden');
        }

        // Update fungsi parseOvertimeManual untuk export
        function parseOvertimeManual(text) {
            // Data sudah diproses dari API, langsung return
            return text || '-';
        }

        // Update statistics
        function updateStats() {
            const totalOvertimes = filteredData.length;
            const approvedCount = filteredData.filter(item =>
                (item.status || '').toLowerCase() === 'approved'
            ).length;
            const uniqueEmployees = new Set(filteredData.map(item => item.user?.id).filter(Boolean)).size;

            document.getElementById('totalOvertimes').textContent = totalOvertimes;
            document.getElementById('approvedCount').textContent = approvedCount;
            document.getElementById('totalEmployees').textContent = uniqueEmployees;
        }

        // Format date
        function formatDate(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                };
                return date.toLocaleDateString('id-ID', options);
            } catch (e) {
                return dateString;
            }
        }

        // Search filter
        document.getElementById('searchEmployee').addEventListener('input', function(e) {
            applyFilters();
        });

        // Date filter
        document.getElementById('filterDate').addEventListener('change', function(e) {
            applyFilters();
        });

        // Status filter
        document.getElementById('filterStatus').addEventListener('change', function(e) {
            applyFilters();
        });

        // Apply filters
        function applyFilters() {
            const searchTerm = document.getElementById('searchEmployee').value.toLowerCase();
            const filterDate = document.getElementById('filterDate').value;
            const filterStatus = document.getElementById('filterStatus').value.toLowerCase();

            filteredData = overtimeData.filter(item => {
                const matchName = !searchTerm || (item.user?.name || '').toLowerCase().includes(searchTerm);
                const matchDate = !filterDate || item.date_overtime === filterDate;
                const matchStatus = !filterStatus || (item.status || '').toLowerCase() === filterStatus;

                return matchName && matchDate && matchStatus;
            });

            renderTable();
            updateStats();

            if (filteredData.length === 0) {
                document.getElementById('tableContainer').classList.add('hidden');
                showEmptyState('Tidak ada hasil pencarian', 'Coba ubah filter atau kata kunci pencarian');
            } else {
                document.getElementById('tableContainer').classList.remove('hidden');
                document.getElementById('emptyState').classList.add('hidden');
            }
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('searchEmployee').value = '';
            document.getElementById('filterDate').value = '';
            document.getElementById('filterStatus').value = '';
            filteredData = overtimeData;
            renderTable();
            updateStats();

            if (filteredData.length > 0) {
                document.getElementById('tableContainer').classList.remove('hidden');
                document.getElementById('emptyState').classList.add('hidden');
            }
        }

        // Export to Excel
        // Export to Excel
        function exportToExcel() {
            if (filteredData.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }

            const data = filteredData.map((item, index) => ({
                'No': index + 1,
                'Nama Karyawan': item.user?.nama_lengkap || '-',
                'Mitra Kerja': item.user?.kerjasama?.client?.name || '-',
                'Posisi': item.user?.jabatan?.name_jabatan || '-',
                'Jumlah Lembur': item.type_overtime_manual || '-',
                'Jumlah Data': item.count || 1
            }));

            const ws = XLSX.utils.json_to_sheet(data);

            ws['!cols'] = [{
                    wch: 5
                },
                {
                    wch: 25
                },
                {
                    wch: 25
                },
                {
                    wch: 20
                },
                {
                    wch: 20
                },
                {
                    wch: 12
                }
            ];

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Data Lembur');

            const fileName = `Data_Lembur_{{ $client->name ?? 'Mitra' }}_${new Date().toISOString().split('T')[0]}.xlsx`;
            XLSX.writeFile(wb, fileName);
        }

        // Export to PDF
        // Export to PDF
        function exportToPDF() {
            if (filteredData.length === 0) {
                alert('Tidak ada data untuk diekspor');
                return;
            }

            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape');

            doc.setFontSize(16);
            doc.setFont(undefined, 'bold');
            doc.text('Data Lembur - {{ $client->name ?? 'Mitra' }}', 14, 15);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text(`Periode: ${document.getElementById('periodDate').textContent}`, 14, 22);
            doc.text(
                `Total Karyawan: ${filteredData.length} | Total Disetujui: ${document.getElementById('approvedCount').textContent}`,
                14, 28);

            const tableData = filteredData.map((item, index) => [
                index + 1,
                item.user?.nama_lengkap || '-',
                item.user?.kerjasama?.client?.name || '-',
                item.user?.jabatan?.name_jabatan || '-',
                item.type_overtime_manual || '-'
            ]);

            doc.autoTable({
                startY: 35,
                head: [
                    ['No', 'Nama Karyawan', 'Mitra Kerja', 'Posisi', 'Jumlah Lembur']
                ],
                body: tableData,
                theme: 'grid',
                styles: {
                    fontSize: 9,
                    cellPadding: 3
                },
                headStyles: {
                    fillColor: [79, 70, 229],
                    fontStyle: 'bold'
                },
                columnStyles: {
                    0: {
                        cellWidth: 10
                    },
                    1: {
                        cellWidth: 60
                    },
                    2: {
                        cellWidth: 50
                    },
                    3: {
                        cellWidth: 50
                    },
                    4: {
                        cellWidth: 40
                    }
                },
                alternateRowStyles: {
                    fillColor: [245, 247, 250]
                }
            });

            const fileName = `Data_Lembur_{{ $client->name ?? 'Mitra' }}_${new Date().toISOString().split('T')[0]}.pdf`;
            doc.save(fileName);
        }

        function parseOvertimeManual(text) {
            if (!text || typeof text !== 'string') {
                return
            }

            // Ambil semua angka, buang selain digit
            const clean = text.replace(/[^0-9]/g, '');

            if (clean === '') {
                return
            }

            const value = parseInt(clean, 10);

            // 10–500 dianggap JAM
            if (value >= 10 && value <= 500) {
                return value + value
            }

            // >= 1000 dianggap IDR
            if (value >= 1000) {
                return value
            }

            // selain itu diabaikan
            return
        }


        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, fetching data...'); // Debug log
            fetchOvertimeData();
        });
    </script>
</x-app-layout>
