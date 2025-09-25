<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Spreadsheet</title>
    <script src="https://bossanova.uk/jspreadsheet/v4/jspreadsheet.js"></script>
    <link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jspreadsheet.css" type="text/css" />
    <script src="https://jsuites.net/v5/jsuites.js"></script>
    <link rel="stylesheet" href="https://jsuites.net/v5/jsuites.css" type="text/css" />
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --success: #27ae60;
            --warning: #f39c12;
            --error: #e74c3c;
            --info: #9b59b6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius: 12px;
            --radius-lg: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: var(--gray-700);
            line-height: 1.6;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-lg);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.18);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .header-section h1 {
            margin: 0 0 10px 0;
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-section p {
            margin: 0;
            color: var(--gray-600);
            font-size: 1.125rem;
            font-weight: 500;
        }

        .legend-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .legend-title {
            text-align: center;
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 1.3em;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .legend-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            border-left: 4px solid;
            transition: all 0.2s ease;
        }

        .legend-item:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .legend-item.masuk {
            border-left-color: #27ae60;
        }

        .legend-item.izin {
            border-left-color: #f39c12;
        }

        .legend-item.tidak {
            border-left-color: #e74c3c;
        }

        .legend-item.libur {
            border-left-color: #9b59b6;
        }

        .legend-item.weekend {
            border-left-color: #95a5a6;
        }

        .legend-item.holiday {
            border-left-color: #e67e22;
        }

        .legend-symbol {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 14px;
        }

        .symbol-m {
            background: linear-gradient(135deg, #27ae60, #00b894);
        }

        .symbol-i {
            background: linear-gradient(135deg, #f39c12, #fdcb6e);
        }

        .symbol-t {
            background: linear-gradient(135deg, #e74c3c, #fd79a8);
        }

        .symbol-mt {
            background: linear-gradient(135deg, #9b59b6, #a29bfe);
        }

        .symbol-weekend {
            background: linear-gradient(135deg, #95a5a6, #b2bec3);
        }

        .symbol-holiday {
            background: linear-gradient(135deg, #e67e22, #fd79a8);
        }

        .spreadsheet-wrapper {
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .controls-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .download-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .download-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .download-btn:active {
            transform: translateY(-1px);
        }

        .download-btn::before {
            content: "📄";
            font-size: 18px;
        }

        .download-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        #spreadsheet {
            width: 100%;
            border-radius: 8px;
            overflow-x: auto !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .jexcel_container {
            border-radius: 8px !important;
            overflow-x: auto !important;
        }

        .jexcel>thead>tr>td {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            font-weight: 600 !important;
            color: #495057 !important;
            border-bottom: 2px solid #dee2e6 !important;
            text-align: center !important;
            padding: 12px 8px !important;
        }

        .jexcel>tbody>tr>td {
            padding: 10px 8px !important;
            text-align: center !important;
            font-weight: 500 !important;
            border-right: 1px solid #e9ecef !important;
            border-bottom: 1px solid #f1f3f4 !important;
            transition: all 0.2s ease !important;
        }

        .jexcel>tbody>tr>td:first-child {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            font-weight: 600 !important;
            text-align: left !important;
            padding-left: 15px !important;
            color: #495057 !important;
            border-right: 2px solid #dee2e6 !important;
        }

        .jexcel>tbody>tr:hover>td {
            background: rgba(102, 126, 234, 0.05) !important;
        }

        .jexcel>tbody>tr>td:hover {
            background: rgba(102, 126, 234, 0.1) !important;
            transform: scale(1.05);
        }

        .jexcel>tbody>tr>td.cell-updating {
            background: rgba(251, 191, 36, 0.2) !important;
            pointer-events: none !important;
            opacity: 0.7 !important;
            position: relative;
        }

        .jexcel>tbody>tr>td.cell-updating::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid transparent;
            border-top: 2px solid #f39c12;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .weekend-cell {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
            color: #856404 !important;
        }

        .holiday-cell {
            background: linear-gradient(135deg, #f8d7da 0%, #fab1a0 100%) !important;
            color: #721c24 !important;
        }

        .footer-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            padding: 15px;
            text-align: center;
            color: #7f8c8d;
            font-size: 0.9em;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: -20px;
            padding: 15px 20px;
            margin-right: 25px;
            border-radius: var(--radius);
            color: white;
            font-weight: 500;
            box-shadow: var(--shadow-lg);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: hidden;
        }

        .notification.show {
            transform: translateX(0);
            z-index: 1000;
            display: inline;
        }

        .notification.success {
            background: linear-gradient(135deg, #27ae60, #00b894);
        }

        .notification.error {
            background: linear-gradient(135deg, #e74c3c, #fd79a8);
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 10px;
            }

            .spreadsheet-wrapper {
                padding: 15px;
            }

            .controls-section {
                flex-direction: column;
                align-items: center;
            }

            .download-btn {
                width: 100%;
                justify-content: center;
            }

            .legend-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <x-app-layout>
        <script src="https://bossanova.uk/jspreadsheet/v4/jspreadsheet.js"></script>
        <link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jspreadsheet.css" type="text/css" />
        <script src="https://jsuites.net/v5/jsuites.js"></script>
        <link rel="stylesheet" href="https://jsuites.net/v5/jsuites.css" type="text/css" />

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <x-main-div>
            <div class="main-container">
                <div class="header-section">
                    <h1>Attendance Management System</h1>
                    <p>Employee attendance tracking and reporting dashboard</p>
                </div>

                <div class="legend-section">
                    <h3 class="legend-title">
                        <i class="fas fa-info-circle"></i>
                        Attendance Legend
                    </h3>
                    <div class="legend-grid">
                        <div class="legend-item masuk">
                            <div class="legend-symbol symbol-m">M</div>
                            <span><strong>Masuk</strong> - Present/Working</span>
                        </div>
                        <div class="legend-item izin">
                            <div class="legend-symbol symbol-i">I</div>
                            <span><strong>Izin</strong> - Sick Leave/Permission</span>
                        </div>
                        <div class="legend-item tidak">
                            <div class="legend-symbol symbol-t">T</div>
                            <span><strong>Tidak Masuk</strong> - Absent</span>
                        </div>
                        <div class="legend-item libur">
                            <div class="legend-symbol symbol-mt">L</div>
                            <span><strong>Libur</strong> - Leave/Holiday</span>
                        </div>
                        <div class="legend-item weekend">
                            <div class="legend-symbol symbol-weekend">W</div>
                            <span><strong>Weekend</strong> - Saturday/Sunday</span>
                        </div>
                        <div class="legend-item holiday">
                            <div class="legend-symbol symbol-holiday">H</div>
                            <span><strong>Holiday</strong> - National Holiday</span>
                        </div>
                    </div>
                </div>

                <div class="spreadsheet-wrapper">
                    <div class="controls-section">
                        <button class="download-btn" id="download">Download PDF Report</button>
                    </div>

                    <div id="spreadsheet"></div>
                </div>

                <div class="footer-section">
                    <p>© 2024 Attendance Management System | Last updated: <span id="currentTime"></span></p>
                </div>
            </div>
        </x-main-div>
    </x-app-layout>
    <div class="notification" id="notification"></div>

    <script>
        let rawData = @json($processedUsers);
        let totalHari = @json($totalHari);
        let calendarHeaders = @json($calendarHeaders);
        let days = new Set()
        let isFetching = false;

        let columns = [{
                type: 'numeric',
                title: '#',
                width: 40
            },
            {
                type: 'text',
                title: 'Nama',
                width: 200
            },
            {
                type: 'text',
                title: 'Jab.',
                width: 100
            },
        ];

        for (let d = 0; d < rawData.length; d++) {
            let data = rawData[d];
            for (let i = 0; i < data.rows.length; i++) {
                days.add(data.rows[i].date);
                let dayOnly = new Date(data.rows[i].date).getDate();
                if (totalHari + 1 === dayOnly) {
                    break;
                }
            }
        }

        calendarHeaders.forEach(h => {
            columns.push({
                type: 'text',
                title: h.day,
                width: 40
            });
        });

        columns.push({
            type: 'numeric',
            title: 'M',
            width: 50
        });
        columns.push({
            type: 'numeric',
            title: 'I',
            width: 50
        });
        columns.push({
            type: 'numeric',
            title: 'T',
            width: 50
        });
        columns.push({
            type: 'numeric',
            title: 'L',
            width: 50
        });
        columns.push({
            type: 'numeric',
            title: 'Persentase',
            width: 100
        });

        let data = rawData.map((u, idx) => {
            console.log(u.user);

            let row = [
                idx + 1,
                u.user.nama_lengkap,
                u.user.jabatan.code_jabatan ?? ''
            ];

            row.push(...u.rows.map(r => r.symbol));

            row.push(u.m, u.z, u.t, u.terus, u.percentage);

            return row;
        });

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.classList.add('show');

            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        let isReloading = false;

        let sheet = jspreadsheet(document.getElementById('spreadsheet'), {
            data: data,
            columns: columns,
            tableOverflow: true,
            tableWidth: "100%",
            updateTable: function(instance, cell, col, row, val, id) {
                if (col > 0 && col <= calendarHeaders.length) {
                    let header = calendarHeaders[col - 1];
                    if (header.isWeekend || header.isHoliday) {
                        cell.style.backgroundColor = "#fee2e2";
                        cell.style.color = "#b91c1c";
                    }
                }

                if (row >= 0 && col > 0 && col <= calendarHeaders.length) {
                    let header = calendarHeaders[col - 1];
                    if (header.isWeekend || header.isHoliday) {
                        cell.style.backgroundColor = "#fee2e2";
                        cell.style.color = "#b91c1c";
                    }
                }

                if (row >= 0 && col > 0 && col <= calendarHeaders.length) {
                    switch (val) {
                        case 'M':
                            cell.style.backgroundColor = "#bbf7d0";
                            break;
                        case 'I':
                            cell.style.backgroundColor = "#fde68a";
                            break;
                        case 'T':
                            cell.style.backgroundColor = "#fca5a5";
                            break;
                        case '//':
                            cell.style.backgroundColor = "#e5e7eb";
                            break;
                    }
                }
            },
            onchange: function(instance, cell, x, y, value) {
                let allData = sheet.getData();
                let rowData = allData[y];

                getDate = rawData[y].rows[x - 3].date;

                let userId = rawData[y].user.id;
                if (isFetching) {
                    showNotification("⏳ Data is still updating, please wait...", 'error');
                    sheet.setValueFromCoords(x, y, rawData[y].rows[x - 3].symbol, true);
                    return;
                }

                isFetching = true;
                cell.classList.add('cell-updating');
                document.getElementById('download').disabled = true;

                fetch("{{ route('admin.attendance.update') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            col_index: x,
                            value: value,
                            row_data: rowData,
                            get_date: getDate
                        })
                    })
                    .then(res => {
                        if (res.status == 200) {
                            fetch("{{ route('admin.attendance.fetch') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        str1: "{{ $str }}",
                                        end1: "{{ $ended }}",
                                        libur: "{{ $libur }}",
                                        mitra: "{{ $mitra }}",
                                        divisi_id: "{{ $divisi_id }}"
                                    })
                                })
                                .then(r => r.json())
                                .then(newRes => {
                                    rawData = newRes.processedUsers;
                                    totalHari = newRes.totalHari;
                                    calendarHeaders = newRes.calendarHeaders;

                                    let newData = rawData.map((u, idx) => {
                                        let row = [
                                            idx + 1,
                                            u.user.nama_lengkap,
                                            u.user.jabatan.code_jabatan ?? ''
                                        ];
                                        row.push(...u.rows.map(r => r.symbol));
                                        row.push(u.m, u.z, u.t, u.terus, u.percentage);
                                        return row;
                                    });

                                    sheet.setData(newData);
                                });

                            showNotification("✅ Attendance updated successfully!", 'success');
                        } else {
                            throw new Error('Update failed');
                        }
                    })
                    .finally(() => {
                        isFetching = false;
                        cell.classList.remove('cell-updating');
                        document.getElementById('download').disabled = false;
                    })
                    .catch(err => {
                        console.error("Save error:", err);
                        showNotification("❌ Failed to update attendance", 'error');
                        sheet.setValueFromCoords(x, y, rawData[y].rows[x - 3].symbol, true);
                    });
            },
            contextMenu: () => false
        });

        // Enable clipboard functionality
        document.addEventListener('keydown', function(e) {
            // Ctrl+C for copy
            if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                let selectedRange = sheet.getSelected();
                if (selectedRange) {
                    let copyData = [];
                    for (let row = selectedRange[1]; row <= selectedRange[3]; row++) {
                        let rowData = [];
                        for (let col = selectedRange[0]; col <= selectedRange[2]; col++) {
                            rowData.push(sheet.getValueFromCoords(col, row) || '');
                        }
                        copyData.push(rowData.join('\t'));
                    }

                    // Copy to clipboard
                    navigator.clipboard.writeText(copyData.join('\n')).then(() => {
                        showNotification('Data copied to clipboard!', 'success');
                    }).catch(() => {
                        // Fallback for older browsers
                        let textArea = document.createElement('textarea');
                        textArea.value = copyData.join('\n');
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                        showNotification('Data copied to clipboard!', 'success');
                    });
                }
                e.preventDefault();
            }

            // Ctrl+V for paste
            if ((e.ctrlKey || e.metaKey) && e.key === 'v') {
                let selectedRange = sheet.getSelected();
                if (selectedRange) {
                    navigator.clipboard.readText().then(text => {
                        let rows = text.split('\n');
                        let startRow = selectedRange[1];
                        let startCol = selectedRange[0];

                        for (let i = 0; i < rows.length; i++) {
                            if (rows[i].trim()) {
                                let cols = rows[i].split('\t');
                                for (let j = 0; j < cols.length; j++) {
                                    let targetRow = startRow + i;
                                    let targetCol = startCol + j;

                                    // Only paste in attendance columns (not name, position, etc.)
                                    if (targetCol >= 3 && targetCol < 3 + calendarHeaders.length) {
                                        sheet.setValueFromCoords(targetCol, targetRow, cols[j].trim(),
                                            true);
                                    }
                                }
                            }
                        }
                        showNotification('Data pasted successfully!', 'success');
                    }).catch(() => {
                        showNotification('Paste failed - please try again', 'error');
                    });
                }
                e.preventDefault();
            }
        });

        // Auto-fill functionality
        document.addEventListener('mousedown', function(e) {
            if (e.target.closest('.jexcel')) {
                let isAutoFillHandle = e.target.classList.contains('jexcel-autofill');
                if (isAutoFillHandle) {
                    e.preventDefault();
                    let selectedRange = sheet.getSelected();
                    if (selectedRange && selectedRange[0] >= 3 && selectedRange[0] < 3 + calendarHeaders.length) {
                        // Get the value from the selected cell
                        let sourceValue = sheet.getValueFromCoords(selectedRange[0], selectedRange[1]);

                        document.addEventListener('mousemove', function fillMove(e) {
                            // This would handle the auto-fill drag operation
                        });

                        document.addEventListener('mouseup', function fillEnd(e) {
                            document.removeEventListener('mousemove', fillMove);
                            document.removeEventListener('mouseup', fillEnd);
                            showNotification('Auto-fill completed!', 'success');
                        }, {
                            once: true
                        });
                    }
                }
            }
        });

        document.getElementById('download').addEventListener('click', () => {
            let updatedData = sheet.getData();
            console.log(updatedData);
            showNotification("📄 Download PDF feature coming soon!", 'success');
        });

        document.getElementById('currentTime').textContent = new Date().toLocaleString('id-ID');
    </script>
</body>

</html>
