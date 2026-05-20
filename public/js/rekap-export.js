class RekapExporter {
    constructor(kerjasamaId, month = null) {
        this.kerjasamaId = kerjasamaId;
        this.month = month || new Date().toISOString().slice(0, 7);
        this.apiUrl = `/api/v1/all-rekap-export/${kerjasamaId}?month=${this.month}`;
    }

    async fetchAllData() {
        const response = await fetch(this.apiUrl);
        if (!response.ok) throw new Error("Failed to fetch data");
        return await response.json();
    }

    async exportToExcel() {
        const result = await this.fetchAllData();
        if (!result.success) throw new Error(result.message);
        const data = result.data;
        const wb = XLSX.utils.book_new();

        XLSX.utils.book_append_sheet(
            wb,
            XLSX.utils.json_to_sheet(
                this.formatOvertimes(data.overtimes, true),
            ),
            "Rekap Lembur",
        );
        XLSX.utils.book_append_sheet(
            wb,
            XLSX.utils.json_to_sheet(
                this.formatPersonOuts(data.person_outs, true),
            ),
            "Rekap Personil Keluar",
        );
        XLSX.utils.book_append_sheet(
            wb,
            XLSX.utils.json_to_sheet(
                this.formatPersonIns(data.person_ins, true),
            ),
            "Rekap Personil Masuk",
        );
        XLSX.utils.book_append_sheet(
            wb,
            XLSX.utils.json_to_sheet(this.formatCuttings(data.cuttings, true)),
            "Rekap Cutting",
        );
        XLSX.utils.book_append_sheet(
            wb,
            XLSX.utils.json_to_sheet(
                this.formatFinishedTrainings(data.finished_trainings, true),
            ),
            "Rekap Lepas Training",
        );

        XLSX.writeFile(
            wb,
            `Data_Rekap_${data.client.name}_${new Date().toISOString().split("T")[0]}.xlsx`,
        );
    }

    async exportToPDF() {
        const result = await this.fetchAllData();
        if (!result.success) throw new Error(result.message);
        const data = result.data;
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l", "mm", "a4");

        let y = 14;
        doc.setFontSize(14);
        doc.text(`Data Rekap - ${data.client.name}`, 14, y);
        y += 6;
        doc.setFontSize(10);
        doc.text(`Periode: ${data.period}`, 14, y);
        y += 6;

        y = this.addSectionTable(
            doc,
            y,
            "Rekap Lembur",
            this.getOvertimeHeaders(),
            this.toBody(this.formatOvertimes(data.overtimes, true)),
        );
        y = this.addSectionTable(
            doc,
            y,
            "Rekap Personil Keluar",
            this.getPersonOutHeaders(),
            this.toBody(this.formatPersonOuts(data.person_outs, true)),
        );
        y = this.addSectionTable(
            doc,
            y,
            "Rekap Personil Masuk",
            this.getPersonInHeaders(),
            this.toBody(this.formatPersonIns(data.person_ins, true)),
        );
        y = this.addSectionTable(
            doc,
            y,
            "Rekap Cutting",
            this.getCuttingHeaders(),
            this.toBody(this.formatCuttings(data.cuttings, true)),
        );
        y = this.addSectionTable(
            doc,
            y,
            "Rekap Lepas Training",
            this.getFinishedTrainingHeaders(),
            this.toBody(
                this.formatFinishedTrainings(data.finished_trainings, true),
            ),
        );

        doc.save(
            `Data_Rekap_${data.client.name}_${new Date().toISOString().split("T")[0]}.pdf`,
        );
    }

    addSectionTable(doc, y, title, headers, body) {
        if (y > 170) {
            doc.addPage();
            y = 14;
        }
        doc.setFontSize(11);
        doc.setFont(undefined, "bold");
        doc.text(title, 14, y);
        y += 3;

        doc.autoTable({
            head: [headers],
            body,
            startY: y,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [37, 99, 235] },
            theme: "grid",
        });
        return doc.lastAutoTable.finalY + 6;
    }

    toBody(rows) {
        return rows.map((r) => Object.values(r));
    }

    // ===== formatters sinkron logic lama =====
    formatOvertimes(d, showEmpty = false) {
        const grouped = {};
        d.forEach((item) => {
            const key = item.user?.id || "unknown";
            if (!grouped[key])
                grouped[key] = {
                    nama: item.user?.nama_lengkap || "-",
                    mitra: item.user?.kerjasama?.client?.name || "-",
                    posisi: item.user?.jabatan?.name_jabatan || "-",
                    tanggal: item.date_overtime,
                    hari: 0,
                    jam: [],
                    lainnya: [],
                };
            const t = (item.type_overtime || "").toLowerCase();
            if (t === "shift") grouped[key].hari += item.count || 1;
            else if (t === "jam" && item.type_overtime_manual)
                grouped[key].jam.push(item.type_overtime_manual);
            else if (t === "lainnya" && item.type_overtime_manual)
                grouped[key].lainnya.push(item.type_overtime_manual);
        });
        const rows = Object.values(grouped).map((e, i) => ({
            no: i + 1,
            nama_karyawan: e.nama,
            mitra_kerja: e.mitra,
            posisi: e.posisi,
            tanggal: e.tanggal,
            hari: e.hari > 0 ? `${e.hari} hari` : "-",
            jam: e.jam.length ? e.jam.join(", ") : "-",
            lainnya: e.lainnya.length ? e.lainnya.join(", ") : "-",
        }));
        if (!rows.length && showEmpty)
            return [
                {
                    no: "-",
                    nama_karyawan: "Tidak ada data",
                    mitra_kerja: "-",
                    posisi: "-",
                    tanggal: "-",
                    hari: "-",
                    jam: "-",
                    lainnya: "-",
                },
            ];
        return rows;
    }

    formatPersonOuts(d, showEmpty = false) {
        const rows = d.map((item, i) => ({
            no: i + 1,
            nama_karyawan: item.user?.nama_lengkap || "-",
            mitra_kerja: item.user?.kerjasama?.client?.name || "-",
            posisi: item.user?.jabatan?.name_jabatan || "-",
            jumlah_mk: item.total_mk || "0",
        }));
        if (!rows.length && showEmpty)
            return [
                {
                    no: "-",
                    nama_karyawan: "Tidak ada data",
                    mitra_kerja: "-",
                    posisi: "-",
                    jumlah_mk: "-",
                },
            ];
        return rows;
    }

    formatPersonIns(d, showEmpty = false) {
        const rows = d.map((r, i) => ({
            no: i + 1,
            nama: r.fullname || "-",
            jabatan: r.jabatan?.name_jabatan || "-",
            tanggal_masuk: this.fmt(r.date_in),
            metode_gaji: r.method_salary || "-",
            no_rek: r.method_salary_manual || "-",
        }));
        if (!rows.length && showEmpty)
            return [
                {
                    no: "-",
                    nama: "Tidak ada data",
                    jabatan: "-",
                    tanggal_masuk: "-",
                    metode_gaji: "-",
                    no_rek: "-",
                },
            ];
        return rows;
    }

    formatCuttings(d, showEmpty = false) {
        const rows = d.map((r, i) => ({
            no: i + 1,
            nama: r.user?.nama_lengkap || "-",
            tanggal_cutting: this.fmt(r.date_cut),
            jenis_cutting: r.type_cut || "-",
            nominal: r.manual_type_cut || "-",
            keterangan: r.desc || "-",
        }));
        if (!rows.length && showEmpty)
            return [
                {
                    no: "-",
                    nama: "Tidak ada data",
                    tanggal_cutting: "-",
                    jenis_cutting: "-",
                    nominal: "-",
                    keterangan: "-",
                },
            ];
        return rows;
    }

    formatFinishedTrainings(d, showEmpty = false) {
        const rows = d.map((r, i) => ({
            no: i + 1,
            nama: r.user?.nama_lengkap || "-",
            tanggal_masuk: this.fmt(r.date_in),
            tanggal_selesai: this.fmt(r.date_finish_train),
            keterangan: r.desc || "-",
        }));
        if (!rows.length && showEmpty)
            return [
                {
                    no: "-",
                    nama: "Tidak ada data",
                    tanggal_masuk: "-",
                    tanggal_selesai: "-",
                    keterangan: "-",
                },
            ];
        return rows;
    }

    getOvertimeHeaders() {
        return [
            "No",
            "Nama Karyawan",
            "Mitra Kerja",
            "Posisi",
            "Tanggal",
            "Hari",
            "Jam",
            "Lainnya",
        ];
    }
    getPersonOutHeaders() {
        return ["No", "Nama Karyawan", "Mitra Kerja", "Posisi", "Jumlah MK"];
    }
    getPersonInHeaders() {
        return [
            "No",
            "Nama",
            "Jabatan",
            "Tanggal Masuk",
            "Metode Gaji",
            "No Rek",
        ];
    }
    getCuttingHeaders() {
        return [
            "No",
            "Nama",
            "Tanggal Cutting",
            "Jenis Cutting",
            "Nominal",
            "Keterangan",
        ];
    }
    getFinishedTrainingHeaders() {
        return ["No", "Nama", "Tanggal Masuk", "Tanggal Selesai", "Keterangan"];
    }

    fmt(v) {
        if (!v) return "-";
        const d = new Date(v);
        return isNaN(d) ? "-" : d.toLocaleDateString("id-ID");
    }
}
