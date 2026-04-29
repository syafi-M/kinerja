# Kinerja - SAC Changelog

Dokumen ini berisi riwayat perubahan aplikasi Kinerja - SAC dengan format versioning berbasis **Semantic Versioning (SemVer)**.

## Versioning Policy

- `MAJOR` (`X.0.0`): perubahan besar / breaking changes.
- `MINOR` (`0.X.0`): fitur baru tanpa breaking changes.
- `PATCH` (`0.0.X`): perbaikan bug, optimasi kecil, dan polish UI.

Format rilis:
- `## [vX.Y.Z] - YYYY-MM-DD`
- Gunakan kategori: `Added`, `Changed`, `Fixed`, `Removed`.

## [v1.13.4] - 2026-04-29

### Added
- Menambahkan toggle tema Mitra dengan mode `System`, `Light`, dan `Dark` yang tersimpan di `localStorage`.
- Menambahkan transisi pergantian tema khusus layout Mitra dengan overlay animasi yang lebih halus.
- Menambahkan styling scrollbar baru pada area Mitra untuk tabel, sidebar, dan halaman utama.
- Menambahkan helper responsive image pada layout Mitra agar thumbnail hanya dimuat untuk viewport yang aktif.

### Changed
- Merapikan dan menyeragamkan UI/UX seluruh modul `mitra_view` agar lebih profesional, clean, dan konsisten pada light/dark mode.
- Meningkatkan responsivitas halaman Mitra, termasuk card mode mobile untuk modul data utama dan polish sidebar, pagination, serta spacing layar kecil.
- Memoles interaksi picker tema di desktop menjadi popover klik dengan tampilan yang lebih refined.
- Mengoptimalkan halaman laporan, kehadiran, karyawan, dan lembur dengan lazy loading thumbnail, decoding async, serta pengurangan beban render untuk daftar panjang.
- Menyederhanakan efek visual pada layout Mitra agar biaya render lebih ringan tanpa mengorbankan kualitas tampilan.

### Fixed
- Memperbaiki animasi overlay pergantian tema yang sempat tidak muncul atau terasa bertabrakan dengan perubahan warna elemen halaman.
- Memperbaiki komponen Blade yang salah referensi pada halaman `brief/create`.
- Memperbaiki pengalaman mobile pada beberapa halaman Mitra yang sebelumnya masih terasa padat atau kurang stabil.

### Removed
- Menghapus pemanggilan `jqueryNew.min.js` yang redundan pada layout Mitra.

## [v1.13.3] - 2026-04-23

### Added
- Menambahkan peta mini pada modal absen pulang untuk menampilkan lokasi absen masuk, lokasi pulang saat ini, radius lokasi kerja, dan garis jarak antar titik.
- Menambahkan informasi jarak dari lokasi absen masuk ke posisi pulang saat ini pada modal absen pulang.

### Changed
- Mengoptimalkan tracking GPS pulang agar koordinat yang dikirim selalu memakai posisi valid terbaru.
- Membatasi data lokasi radius pulang berdasarkan client absensi terkait agar pengecekan radius lebih tepat.
- Membuat tampilan modal absen pulang lebih compact dan informatif.
- Memuat Leaflet hanya saat user berada pada kondisi yang membutuhkan tracking pulang.

### Fixed
- Memperbaiki input `lat_user` dan `long_user` yang dapat terkirim kosong saat absen pulang.
- Menambahkan validasi backend untuk koordinat pulang dan memastikan `plg_lat` serta `plg_long` tersimpan pada jalur update pulang.

## [v1.13.2] - 2026-04-22

### Added
- Menambahkan route untuk history overtime application dan person-out.

### Changed
- Memperbarui UI/UX pada halaman person in dan person out untuk konsistensi dan responsivitas.
- Memperbarui hash pada skills-lock.json.

## [v1.13.1] - 2026-04-14

### Fixed
- Memperbaiki perhitungan persentase absensi.
- Menyesuaikan link laporan pada beberapa view agar mengarah ke halaman yang tepat.

## [v1.13.0] - 2026-03-26

### Changed
- Merapikan relasi model untuk konsistensi akses data.
- Menyesuaikan query controller agar lebih seragam dengan relasi model terbaru.

## [v1.12.1] - 2026-03-12

### Fixed
- Menyesuaikan kalkulasi persentase absensi dengan mengeluarkan data keterlambatan dari perhitungan tertentu.

## [v1.12.0] - 2026-03-09

### Added
- Menambahkan layout Mitra.
- Menambahkan view Mitra untuk dashboard, absensi, manajemen karyawan, izin, lembur, dan laporan.
- Menambahkan template laporan Mitra.
- Menambahkan halaman rekap data untuk Mitra.
- Menambahkan pencarian dan pengambilan data dinamis pada halaman rekap.

### Changed
- Membuat halaman laporan memilih view secara dinamis berdasarkan role user.
- Menyesuaikan sidebar admin.

## [v1.11.2] - 2026-02-24

### Changed
- Merapikan handling tanggal pada method `show`.
- Menyesuaikan range tanggal untuk perhitungan lembur.

## [v1.11.1] - 2026-02-23

### Fixed
- Memperbaiki halaman rekap data yang sempat tidak tampil.
- Memperbaiki beberapa bagian pada halaman daftar user admin.

## [v1.11.0] - 2026-02-19

### Added
- Menambahkan layout admin baru.
- Menambahkan fitur data rekap.
- Menambahkan modul `personIn`, `personOut`, `performanceCutt`, dan `lepasTraining`.
- Menambahkan menu baru pada admin.
- Menambahkan menu baru pada SPV dan Marketing Management.
- Mengimplementasikan view rekap pada layout admin terbaru.

## [v1.10.1] - 2026-02-06

### Added
- Menambahkan tabel notifikasi.

### Changed
- Menyesuaikan perbandingan nilai pada beberapa logic.

### Fixed
- Memperbaiki bug saat memilih minimum requirement hours.

## [v1.10.0] - 2026-02-05

### Added
- Menambahkan section lembur pada Leader.
- Menambahkan section personil keluar pada Leader.
- Menambahkan halaman `personOut` pada SPV.
- Menambahkan export Excel dan PDF untuk SPV dan MRT.
- Menambahkan section rekap data.
- Menambahkan middleware untuk pengecekan jabatan user yang sedang login.
- Menambahkan group routing berdasarkan jabatan.

### Changed
- Mengubah format export PDF dan Excel pada modul lembur.
- Mengubah konfigurasi shift menjadi satu hari.
- Mengubah penggunaan `hari` menjadi `shift`.

### Fixed
- Menyesuaikan logic perform per section.

## [v1.9.3] - 2026-01-30

### Fixed
- Menghapus logic tampilan client dari informasi user pada halaman index.

## [v1.9.2] - 2026-01-29

### Changed
- Menyesuaikan logic user tidak aktif.
- Menyesuaikan konstanta dashboard.
- Mengoptimalkan pengambilan session pada method index.
- Menyesuaikan limit data terbaru yang ditampilkan.

### Fixed
- Memperbaiki pengambilan data user.
- Memperbaiki logic tampilan client pada halaman index.

## [v1.9.1] - 2026-01-21

### Added
- Menambahkan field `is_overnight` pada shift.
- Menambahkan dukungan shift overnight pada model, view create/edit shift, dan logic absensi.

### Changed
- Menyesuaikan tampilan status absensi.
- Menyesuaikan logic batas akhir shift untuk supervisor.
- Memperbarui konstanta `MINUTES_AFTER_SHIFT_END`.
- Mengubah pesan pulang agar tidak lagi menampilkan indikasi lembur.

### Fixed
- Memperbaiki status tombol checkout.
- Menambahkan fungsi tutup modal.
- Menghapus event listener `beforeunload` yang tidak dipakai pada konfirmasi absensi.

## [v1.9.0] - 2026-01-14

### Added
- Menambahkan seed counter username.
- Menambahkan penghapusan client dengan transaction handling.
- Menambahkan penguatan relasi model terkait client.

### Changed
- Menyederhanakan logic pembuatan username.
- Merapikan route dan view Kerjasama.
- Merapikan beberapa view dan controller untuk konsistensi UI dan fungsi.
- Mengoptimalkan handling geolocation dan perhitungan jarak.
- Mengurutkan lokasi terdekat pada pemilihan lokasi.

### Fixed
- Memperbaiki filter role pada query absensi.
- Memperbaiki konfigurasi Redis cache.
- Memperbaiki mismatch collation pada query username.
- Menyesuaikan threshold jarak pada filtering lokasi.
- Mengubah judul riwayat absensi agar memakai nama perusahaan statis.

## [v1.8.0] - 2025-12-26

### Added
- Menambahkan template email notifikasi OTP baru.
- Menambahkan peningkatan layout untuk email OTP.
- Mengubah retrieval laporan agar memakai model `UploadImage`.

### Changed
- Meningkatkan deteksi kualitas gambar kamera.
- Menyesuaikan handling environment pada kamera.
- Memperbarui fungsi kamera.
- Meningkatkan layout detail izin dan laporan.

### Fixed
- Memperbaiki referensi role user pada logic pengambilan shift.

## [v1.7.0] - 2025-11-27

### Changed
- Meningkatkan handling upload image.
- Meningkatkan deteksi kualitas snapshot.
- Meningkatkan view user dan absensi dengan filter serta layout yang lebih baik.

## [v1.6.0] - 2025-10-16

### Added
- Menambahkan export PDF pada report.
- Menambahkan analytic.
- Menambahkan field `hari` pada manajemen shift.
- Menambahkan password untuk export PDF.

### Changed
- Memperbaiki struktur dan formatting beberapa view.
- Merapikan struktur kode untuk maintainability.
- Memperbarui workflow `main.yml`.
- Meningkatkan UI/UX dashboard admin.
- Memperbarui footer component.
- Menyesuaikan opacity menu admin.
- Menyesuaikan logic export ketika data bukan `kerjasama == 1`.
- Memindahkan teks informasi shift.

### Fixed
- Memperbaiki filter shift.
- Menghapus pembuatan `userAbsen` yang tidak dipakai pada `addKaryawanStore`.
- Mencegah password ikut berubah ketika field password tidak diisi.
- Menyesuaikan logic export user dan export name card pada `AdminController`.

## [v1.5.0] - 2025-09-30

### Added
- Menambahkan field alamat pada form tambah karyawan.
- Menambahkan field alamat pada `UserRequest`, `Employe`, dan `User`.
- Menambahkan kontrak baru dan halaman pengecekan kontrak untuk direksi.
- Menambahkan logic edit Excel pada halaman export.
- Menambahkan export PDF pada report.

### Changed
- Meningkatkan struktur form create dan upload gambar.
- Meningkatkan layout form export.
- Merapikan tombol duplicate pada halaman QR code.
- Menyesuaikan tampilan list pekerjaan ketika field `ruangan` kosong.
- Menyesuaikan label dan layout halaman client, divisi, dan shift.
- Melakukan refactor handling absensi dan komponen UI.

### Fixed
- Memperbaiki retrieval `jabatan_id` pada `addKaryawanStore`.
- Memperbaiki `kerjasama_id` pada kontrak baru untuk `userAbsen`.
- Memperbaiki halaman Excel.

## [v1.4.0] - 2025-08-27

### Added
- Menambahkan request komplain pada slip gaji.
- Menambahkan dukungan upload bukti dengan format gambar yang lebih luas.
- Menambahkan alert kompatibilitas browser pada halaman login.
- Menambahkan toggle visibilitas password pada login.

### Changed
- Mengoptimalkan dashboard direksi.
- Mengoptimalkan CP dan tampilan nilai CP.
- Memperbarui index slip user.
- Menyesuaikan kondisi submit form absensi berdasarkan role user.
- Meningkatkan error handling dan feedback UI pada form absensi.
- Meningkatkan layout login dan guest layout dengan styling serta animasi.
- Menyesuaikan placeholder input login.

### Fixed
- Memperbaiki error absen pulang.
- Memperbaiki beberapa error pada history.
- Memperbaiki index laporan.
- Memperbaiki dashboard leader.
- Menghapus `dd()` pada halaman user.
- Memperbaiki name case dan foto absensi.
- Memperbaiki absensi SPV utama.
- Memperbaiki input file pada edit bukti.
- Memperbaiki logic absensi untuk tambahan pengecekan role user.
- Memperbaiki penempatan logo pada guest layout.

## [v1.3.0] - 2025-08-08

### Added
- Mengimpor project awal dari cPanel.
- Menambahkan workflow GitHub Actions `main.yml`.

### Fixed
- Melakukan beberapa perbaikan awal pada absensi, history, CP, dan dashboard.

## [v1.2.0] - 2024-06-11

### Added
- Menambahkan sistem CP baru.
- Menambahkan update QR Code.
- Menambahkan Rencana Kerja V2.

### Changed
- Memperbarui tampilan direksi.
- Menambahkan beberapa backup dan build update dari periode 2024.

## [v1.0.0] - 2023-12-29

### Added
- Commit awal aplikasi Kinerja - SAC.
