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
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            text-align: center;
        }

        .header-section h1 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 2.2em;
            font-weight: 600;
        }

        .header-section p {
            margin: 0;
            color: #7f8c8d;
            font-size: 1.1em;
        }

        .spreadsheet-wrapper {
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
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

        #spreadsheet {
            width: 100%;
            border-radius: 8px;
            overflow-x: auto !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Custom spreadsheet styling */
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

        /* Weekend and holiday cell styling */
        .weekend-cell {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
            color: #856404 !important;
        }

        .holiday-cell {
            background: linear-gradient(135deg, #f8d7da 0%, #fab1a0 100%) !important;
            color: #721c24 !important;
        }

        .legend-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .legend-title {
            text-align: center;
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 1.3em;
            font-weight: 600;
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

        .footer-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            color: #7f8c8d;
            font-size: 0.9em;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
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
                    <h1>📊 Attendance Management System</h1>
                    <p>Employee attendance tracking and reporting dashboard</p>
                </div>

                <div class="legend-section">
                    <h3 class="legend-title">Attendance Legend</h3>
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

    <script>
        // ORIGINAL LOGIC - NO CHANGES TO FUNCTIONALITY
        // Data dari Laravel
        let rawData = @json($processedUsers);
        let totalHari = @json($totalHari);
        let calendarHeaders = @json($calendarHeaders);

        // Header kolom dinamis
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
                let dayOnly = new Date(data.rows[i].date).getDate();
                if (totalHari + 1 === dayOnly) {
                    break; // ✅ works here
                }
            }
        }

        calendarHeaders.forEach(h => {
            columns.push({
                type: 'text',
                title: h.day, // judul kolom = nomor hari
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

            // tambahkan simbol per tanggal
            row.push(...u.rows.map(r => r.symbol));

            // rekap
            row.push(u.m, u.z, u.t, u.terus, u.percentage);

            return row;
        });

        // Init spreadsheet
        let sheet = jspreadsheet(document.getElementById('spreadsheet'), {
            data: data,
            columns: columns,
            tableOverflow: true,
            tableWidth: "100%",
            updateTable: function(instance, cell, col, row, val, id) {
                // cek apakah kolom ini termasuk tanggal
                if (col > 0 && col <= calendarHeaders.length) {
                    let header = calendarHeaders[col - 1];
                    if (header.isWeekend || header.isHoliday) {
                        cell.style.backgroundColor = "#fee2e2"; // merah muda
                        cell.style.color = "#b91c1c"; // teks merah tua
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
                            cell.style.backgroundColor = "#bbf7d0"; // green-200
                            break;
                        case 'I':
                            cell.style.backgroundColor = "#fde68a"; // yellow-200
                            break;
                        case 'T':
                            cell.style.backgroundColor = "#fca5a5"; // red-300
                            break;
                        case '//':
                            cell.style.backgroundColor = "#e5e7eb"; // gray-200
                            break;
                    }
                }
            },
            onchange: function(instance, cell, x, y, value) {
                // Ambil semua data
                let allData = sheet.getData();

                // Ambil row tertentu
                let rowData = allData[y];

                let userId = rawData[y].user.id;

                // Simpan ke server
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
                            row_data: rowData
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        // Optional: Show success feedback
                        if (res.success) {
                            cell.style.border = "2px solid #27ae60";
                            setTimeout(() => {
                                cell.style.border = "";
                            }, 1000);
                        }
                    })
                    .catch(err => {
                        console.error("Save error:", err);
                        // Optional: Show error feedback
                        cell.style.border = "2px solid #e74c3c";
                        setTimeout(() => {
                            cell.style.border = "";
                        }, 2000);
                    });
            },
            contextMenu: () => false

        });

        // Tombol download PDF (nanti bisa POST ke Laravel)
        document.getElementById('download').addEventListener('click', () => {
            let updatedData = sheet.getData();
            console.log(updatedData); // cek dulu datanya
            alert("Download PDF nanti tinggal kirim ke route Laravel");
        });

        // Update current time
        document.getElementById('currentTime').textContent = new Date().toLocaleString('id-ID');
    </script>
</body>

</html>
