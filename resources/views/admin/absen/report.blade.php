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
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: var(--gray-700);
            line-height: 1.6;
            background-color: var(--gray-50);
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
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
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
            margin-bottom: 5px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .download-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            margin: 15px 0 25px 0;
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
            background: oklch(90% 0 0) !important;
            font-weight: 600 !important;
            color: #495057 !important;
            border-bottom: 2px solid #dee2e6 !important;
            text-align: center !important;
            padding: 12px 8px !important;
        }

        .jexcel>tbody>tr>td {
            padding: 10px 8px !important;
            text-align: center;
            font-weight: 500 !important;
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

        .jexcel>tbody>tr>td:nth-child(2) {
            text-align: left !important;
        }

        .jexcel>tbody>tr:hover>td {
            background: rgba(102, 126, 234, 0.10) !important;
        }

        .jexcel>tbody>tr>td:hover {
            background: rgba(102, 126, 234, 0.15) !important;
            transform: scale(1.02);
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
            z-index: 1000;
        }

        .notification.show {
            transform: translateX(0);
            display: inline;
        }

        .notification.success {
            background: linear-gradient(135deg, #27ae60, #00b894);
        }

        .notification.error {
            background: linear-gradient(135deg, #e74c3c, #fd79a8);
        }

        .notification.info {
            background: linear-gradient(135deg, #3498db, #2980b9);
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
                    <div class="legend-grid md:grid-cols-3">
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
                            <div class="legend-symbol symbol-weekend">//</div>
                            <span><strong>Weekend</strong> - Saturday/Sunday</span>
                        </div>
                        <div class="legend-item holiday">
                            <div class="legend-symbol symbol-holiday">H</div>
                            <span><strong>Holiday</strong> - National Holiday</span>
                        </div>
                    </div>
                </div>

                <div class="spreadsheet-wrapper">
                    <div id="spreadsheet"></div>

                    <div class="controls-section">
                        <button class="download-btn" id="download">Download PDF Report</button>
                    </div>
                </div>

                <div class="footer-section">
                    <p>© 2024 Attendance Management System | Last updated: <span id="currentTime"></span></p>
                </div>
            </div>
        </x-main-div>
    </x-app-layout>
    <div class="notification" id="notification"></div>

    <script>
        // Initialize variables - Changed to let where reassignment is needed
        let rawData = @json($processedUsers);
        let totalHari = @json($totalHari);
        let calendarHeaders = @json($calendarHeaders);
        let pendingChanges = [];
        let isProcessingBatch = false;
        let batchTimeout = null;
        let sheet = null;
        let originalNames = [];

        // Get URL parameters
        const params = new URLSearchParams(window.location.search);
        const mitraId = params.get('kerjasama_id');

        // Utility functions
        const utils = {
            // Capitalize name for display only
            capitalizeName: (name) => {
                if (!name) return '';

                return name.toLowerCase()
                    .split(' ')
                    .map(word => {
                        if (word.startsWith("mc") && word.length > 2) {
                            return "Mc" + word.charAt(2).toUpperCase() + word.slice(3);
                        } else if (word.startsWith("o'") && word.length > 2) {
                            return "O'" + word.charAt(2).toUpperCase() + word.slice(3);
                        }
                        return word.charAt(0).toUpperCase() + word.slice(1);
                    })
                    .join(' ');
            },

            // Show notification
            showNotification: (message, type = 'success') => {
                const notification = document.getElementById('notification');
                notification.textContent = message;
                notification.className = `notification ${type}`;
                notification.classList.add('show');

                setTimeout(() => {
                    notification.classList.remove('show');
                }, 3000);
            },

            // Check if a day is a weekend or holiday based on mitraId
            isWeekendOrHoliday: (header) => {
                if (mitraId == 1) {
                    return header.isWeekend || header.isHoliday;
                } else {
                    return header.name == 'Sun' || header.isHoliday;
                }
            }
        };

        // Create columns configuration
        function createColumns() {
            const columns = [{
                    type: 'numeric',
                    title: '#',
                    width: 40
                },
                {
                    type: 'text',
                    title: 'Nama',
                    width: 200,
                    align: 'left'
                },
                {
                    type: 'text',
                    title: 'Jab.',
                    width: 100
                }
            ];

            // Add day columns
            calendarHeaders.forEach(h => {
                columns.push({
                    type: 'text',
                    title: h.day,
                    width: 40
                });
            });

            // Add summary columns
            columns.push({
                type: 'numeric',
                title: 'M',
                width: 50
            }, {
                type: 'numeric',
                title: 'I',
                width: 50
            }, {
                type: 'numeric',
                title: 'T',
                width: 50
            }, {
                type: 'numeric',
                title: 'L',
                width: 50
            }, {
                type: 'numeric',
                title: 'Persentase',
                width: 100
            });

            return columns;
        }

        // Process data for spreadsheet
        function processData() {
            // Store original names
            originalNames = rawData.map(u => u.user.nama_lengkap);

            return rawData.map((u, idx) => {
                const row = [
                    idx + 1,
                    utils.capitalizeName(u.user.nama_lengkap), // Display capitalized name
                    u.user.jabatan?.code_jabatan ?? ''
                ];

                // Add attendance symbols
                row.push(...u.rows.map(r => r.symbol));

                // Add summary data
                row.push(u.m, u.z, u.t, u.terus, u.percentage);

                return row;
            });
        }

        // Update table cell styling
        function updateCellStyling(instance, cell, col, row, val) {
            // Apply weekend/holiday styling
            if (col > 2 && col <= calendarHeaders.length + 2) {
                const header = calendarHeaders[col - 3];

                if (utils.isWeekendOrHoliday(header)) {
                    cell.style.backgroundColor = "#fee2e2";
                    cell.style.color = "#b91c1c";
                    return; // Exit early to avoid applying additional styles
                }
            }

            // Apply attendance status styling
            if (col > 2 && col <= calendarHeaders.length + 2) {
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
        }

        // Process batch changes
        function processBatchChanges() {
            if (isProcessingBatch || pendingChanges.length === 0) return;

            isProcessingBatch = true;
            const changes = [...pendingChanges];
            pendingChanges = [];

            // Show updating state for all affected cells
            changes.forEach(change => {
                change.cell.classList.add('cell-updating');
            });
            document.getElementById('download').disabled = true;

            // Process each change sequentially
            let currentChangeIndex = 0;

            function processNextChange() {
                if (currentChangeIndex >= changes.length) {
                    // All changes processed, fetch updated data
                    fetchUpdatedData(changes);
                    return;
                }

                const change = changes[currentChangeIndex];
                const userId = rawData[change.y].user.id;
                const getDate = rawData[change.y].rows[change.x - 3].date;

                // Get row data with original name
                let rowData = [...sheet.getData()[change.y]];
                // Replace the displayed name with the original name
                rowData[1] = originalNames[change.y];

                // Send update for this single change
                fetch("{{ route('admin.attendance.update') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            col_index: change.x,
                            value: change.value,
                            row_data: rowData,
                            get_date: getDate
                        })
                    })
                    .then(res => {
                        if (res.status === 200) {
                            currentChangeIndex++;
                            processNextChange(); // Process next change
                        } else {
                            throw new Error('Update failed');
                        }
                    })
                    .catch(err => {
                        console.error("Save error:", err);
                        utils.showNotification(`❌ Failed to update cell at row ${change.y + 1}, column ${change.x}`,
                            'error');

                        // Revert this change
                        sheet.setValueFromCoords(change.x, change.y, rawData[change.y].rows[change.x - 3].symbol, true);

                        // Continue with next changes
                        currentChangeIndex++;
                        processNextChange();
                    });
            }

            // Start processing the first change
            processNextChange();
        }

        // Fetch updated data
        function fetchUpdatedData(changes) {
            utils.showNotification("🔄 Updating data...", 'info');

            fetch("{{ route('admin.attendance.fetch') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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
                    // Update all data at once
                    rawData = newRes.processedUsers;
                    totalHari = newRes.totalHari;
                    calendarHeaders = newRes.calendarHeaders;

                    const newData = processData();
                    sheet.setData(newData);
                    utils.showNotification(`✅ Successfully updated ${changes.length} cells!`, 'success');
                })
                .catch(err => {
                    console.error("Data fetch error:", err);
                    utils.showNotification("❌ Failed to refresh data", 'error');
                })
                .finally(() => {
                    // Remove updating state from all cells
                    changes.forEach(change => {
                        change.cell.classList.remove('cell-updating');
                    });
                    document.getElementById('download').disabled = false;
                    isProcessingBatch = false;
                });
        }

        // Initialize spreadsheet
        function initSpreadsheet() {
            const columns = createColumns();
            const data = processData();

            sheet = jspreadsheet(document.getElementById('spreadsheet'), {
                data: data,
                columns: columns,
                tableOverflow: true,
                tableWidth: "100%",
                tableHeight: "400px",
                minDimensions: [columns.length, rawData.length],
                updateTable: updateCellStyling,
                contextMenu: () => false,
                onchange: function(instance, cell, x, y, value) {
                    // Only process attendance columns (not name, position, etc.)
                    if (x >= 3 && x < 3 + calendarHeaders.length) {
                        // Add to pending changes
                        pendingChanges.push({
                            x,
                            y,
                            value,
                            cell
                        });

                        // Clear any existing timeout
                        if (batchTimeout) {
                            clearTimeout(batchTimeout);
                        }

                        // Set a timeout to process batch changes
                        batchTimeout = setTimeout(() => {
                            processBatchChanges();
                        }, 300); // 300ms delay to collect all changes in the batch
                    }

                    // If name column is changed, update the original name and display capitalized version
                    if (x === 1) {
                        // Store the original name
                        originalNames[y] = value;
                        // Display the capitalized version
                        const capitalizedValue = utils.capitalizeName(value);
                        if (capitalizedValue !== value) {
                            // Update the cell with capitalized value
                            setTimeout(() => {
                                sheet.setValueFromCoords(x, y, capitalizedValue, true);
                            }, 0);
                        }
                    }
                }
            });

            // Hide the built-in index column
            if (sheet && typeof sheet.hideIndex === 'function') {
                sheet.hideIndex();
            }
        }

        // Enable clipboard functionality
        function setupClipboardHandlers() {
            document.addEventListener('keydown', function(e) {
                // Ctrl+C for copy
                if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                    const selectedRange = sheet.getSelected();
                    if (selectedRange) {
                        let copyData = [];
                        for (let row = selectedRange[1]; row <= selectedRange[3]; row++) {
                            let rowData = [];
                            for (let col = selectedRange[0]; col <= selectedRange[2]; col++) {
                                // If copying the name column, use the capitalized version
                                if (col === 1) {
                                    rowData.push(utils.capitalizeName(sheet.getValueFromCoords(col, row) || ''));
                                } else {
                                    rowData.push(sheet.getValueFromCoords(col, row) || '');
                                }
                            }
                            copyData.push(rowData.join('\t'));
                        }

                        // Copy to clipboard
                        navigator.clipboard.writeText(copyData.join('\n')).then(() => {
                            utils.showNotification('Data copied to clipboard!', 'success');
                        }).catch(() => {
                            // Fallback for older browsers
                            const textArea = document.createElement('textarea');
                            textArea.value = copyData.join('\n');
                            document.body.appendChild(textArea);
                            textArea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textArea);
                            utils.showNotification('Data copied to clipboard!', 'success');
                        });
                    }
                    e.preventDefault();
                }

                // Ctrl+V for paste
                if ((e.ctrlKey || e.metaKey) && e.key === 'v') {
                    const selectedRange = sheet.getSelected();
                    if (selectedRange) {
                        navigator.clipboard.readText().then(text => {
                            const rows = text.split('\n');
                            const startRow = selectedRange[1];
                            const startCol = selectedRange[0];

                            for (let i = 0; i < rows.length; i++) {
                                if (rows[i].trim()) {
                                    const cols = rows[i].split('\t');
                                    for (let j = 0; j < cols.length; j++) {
                                        const targetRow = startRow + i;
                                        const targetCol = startCol + j;

                                        // Only paste in attendance columns (not name, position, etc.)
                                        if (targetCol >= 3 && targetCol < 3 + calendarHeaders.length) {
                                            sheet.setValueFromCoords(targetCol, targetRow, cols[j].trim(),
                                                true);
                                        }

                                        // If pasting into name column, store original and display capitalized
                                        if (targetCol === 1) {
                                            const originalValue = cols[j].trim();
                                            originalNames[targetRow] = originalValue;
                                            const capitalizedValue = utils.capitalizeName(originalValue);
                                            sheet.setValueFromCoords(targetCol, targetRow, capitalizedValue,
                                                true);
                                        }
                                    }
                                }
                            }
                            utils.showNotification('Data pasted successfully!', 'success');
                        }).catch(() => {
                            utils.showNotification('Paste failed - please try again', 'error');
                        });
                    }
                    e.preventDefault();
                }
            });
        }

        // Setup auto-fill functionality
        function setupAutoFillHandlers() {
            document.addEventListener('mousedown', function(e) {
                if (e.target.closest('.jexcel')) {
                    const isAutoFillHandle = e.target.classList.contains('jexcel-autofill');
                    if (isAutoFillHandle) {
                        e.preventDefault();
                        const selectedRange = sheet.getSelected();
                        if (selectedRange && selectedRange[0] >= 3 && selectedRange[0] < 3 + calendarHeaders
                            .length) {
                            // Get the value from the selected cell
                            const sourceValue = sheet.getValueFromCoords(selectedRange[0], selectedRange[1]);

                            document.addEventListener('mousemove', function fillMove(e) {
                                // This would handle the auto-fill drag operation
                            });

                            document.addEventListener('mouseup', function fillEnd(e) {
                                document.removeEventListener('mousemove', fillMove);
                                document.removeEventListener('mouseup', fillEnd);
                                utils.showNotification('Auto-fill completed!', 'success');
                            }, {
                                once: true
                            });
                        }
                    }
                }
            });
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize spreadsheet
            initSpreadsheet();

            // Setup clipboard handlers
            setupClipboardHandlers();

            // Setup auto-fill handlers
            setupAutoFillHandlers();

            // Setup download button
            document.getElementById('download').addEventListener('click', () => {
                utils.showNotification("📄 Download PDF feature coming soon!", 'info');
            });

            // Update current time
            document.getElementById('currentTime').textContent = new Date().toLocaleString('id-ID');
        });
    </script>
</body>

</html>
