<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CheckPointController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JadwalUserController;
use App\Http\Controllers\KerjasamaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LEADER_Controller\MainController as LeaderController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PerlengkapanController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReportBrefController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SVP_Controller\MainController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PekerjaanCpController;
use App\Http\Controllers\SubareaController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\FinalisasiController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ListPekerjaanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});


Route::view('/map', 'absensi.maps');
Route::get('/get-uptime', [AdminController::class, 'exportCheck']);
Route::get('/send', [DashboardController::class, 'sendTestEmail']);
// Route::resource('/coba-coba', NewsController::class);
// Route::resource('brief', ReportBrefController::class);
Route::post('/proses-export', [AdminController::class, 'prosesExport'])->name('export_checklist');
Route::get('tes-news/', [NewsController::class, 'NewsBefore']);
Route::get('tes-news-dw/{id}', [NewsController::class, 'NewsDownload'])->name('newsDownload');

// Only AUTH
Route::middleware(['auth', 'apdt'])->group(function () {
    Route::view('/scan', 'admin.qrcode.scan');
    Route::get('/laporan/scan/{ruanganId}/{kerjasamaId}', [LaporanController::class, 'getcode'])->name('lapcode');
    Route::put('/data/{id}/updatePulang', [AbsensiController::class, 'updatePulang'])->name('data.update');
    Route::put('/data/{id}/updateSiang', [AbsensiController::class, 'updateSiang'])->name('data.update.siang');
    Route::post('/data/{id}/updateAbsenPulang', [AbsensiController::class, 'updateAbsenPulang'])->name('data-telat.update');
    Route::resource('/dashboard', DashboardController::class);
    Route::resource('/absensi', AbsensiController::class);
    Route::get('/historyAbsensi', [AbsensiController::class, 'historyAbsensi']);
    Route::resource('/profile', ProfileController::class);
    Route::resource('/lembur', LemburController::class)->only('index', 'store', 'update');
    Route::get('/lembur-history', [LemburController::class, 'lemburIndexUser'])->name('lemburIndexUser');
    Route::get('/rate/{id}', [RatingController::class, 'myRate'])->name('ratingSaya');
    Route::resource('/izin', IzinController::class);
    Route::resource('/laporan', LaporanController::class)->only('index', 'create', 'store');
    Route::get('/mypoint/{id}', [PointController::class, 'myPoint'])->name('mypoint');

    Route::resource('checkpoint-user', CheckPointController::class);
    Route::get('editBukti-checkpoint-user', [CheckPointController::class, 'editBukti'])->name('editBukti-checkpoint-user');
    Route::post('uploadBukti-checkpoint-user', [CheckPointController::class, 'uploadBukti'])->name('uploadBukti-checkpoint-user');
    
    Route::get('/riwayat-kerja/{id}', [RatingController::class, 'rateKerja'])->name('rate.kerja');
    Route::get('/getJadwal/{id}', [JadwalUserController::class, 'getJadwal'])->name('get-jadwal');
    
    Route::get('/absensi-private', [AbsensiController::class, 'indexPrivate']);
    Route::post('/absensi-private-store', [AbsensiController::class, 'storePrivate']);
    
    Route::put('/subuh/{id}', [AbsensiController::class, 'updateSubuh'])->name('updateSubuh');
    Route::put('/dhuhur/{id}', [AbsensiController::class, 'updateDzuhur'])->name('updateDzuhur');
    Route::put('/asar/{id}', [AbsensiController::class, 'updateAsar'])->name('updateAsar');
    Route::put('/magrib/{id}', [AbsensiController::class, 'updateMagrib'])->name('updateMagrib');
    Route::put('/isya/{id}', [AbsensiController::class, 'updateIsya'])->name('updateIsya');
    
    Route::get('/get-shifts/{cli}/{jab}', [AbsensiController::class, 'getShift'])->name('olehShift');
    
});

// Untuk Direksi
Route::middleware('direksi')->group(function () {
    Route::resource('/direksi-rating', RatingController::class);
    Route::get('/direksi-laporan', [LeaderController::class, 'indexLaporan'])->name('direksi_laporan');
    Route::get('/direksi-lembur', [MainController::class, 'indexLembur'])->name('direksi_lembur');
    Route::get('/direksi-absensi-izin', [IzinController::class, 'indexLead'])->name('direksi_izin');
    Route::get('/direksi-absensi', [LeaderController::class, 'indexAbsen'])->name('direksi_absensi');
    Route::get('/direksi-jadwal', [JadwalUserController::class, 'index'])->name('direksi_jadwal');
    Route::get('/direksi-user', [LeaderController::class, 'indexUser'])->name('direksi_user');
    Route::get('/direksi-checkpoint', [AdminController::class, 'checkPoint'])->name('direksi.cp.index');
    Route::get('/direksi-lihat-check/{id}', [AdminController::class, 'lihatCheck'])->name('direksi.cp.show');
    Route::put('/direksi-nilai-cp/{id}', [CheckPointController::class, 'uploadNilai'])->name('direksi.uploadNilai');
    Route::delete('/direksi-delete-rk/{id}', [CheckPointController::class, 'deleteRencana'])->name('direksi.deleteRencana');
    // Route::patch('/direksi-approve-cp/{id}', [AdminController::class, 'approveCheck'])->name('direksi.approveCP');
    // Route::patch('/direksi-denied-cp/{id}', [AdminController::class, 'deniedCheck'])->name('direksi.deniedCP');
    Route::get('/direksi-check-koordinat/{id}', [CheckPointController::class, "show"])->name('direksi-lihatMap');
});

// Untuk Mitra
Route::middleware('mitra')->group(function () {
    Route::resource('/mitra-rating', RatingController::class);
    Route::get('/mitra-laporan', [LeaderController::class, 'indexLaporan'])->name('mitra_laporan');
    Route::get('/mitra-laporan/{id}', [LeaderController::class, 'showLaporan'])->name('mitra_laporan.show');
    Route::get('/mitra-lembur', [MainController::class, 'indexLembur'])->name('mitra_lembur');
    Route::get('/mitra-absensi-izin', [IzinController::class, 'indexLead'])->name('mitra_izin');
    Route::get('/mitra-absensi', [LeaderController::class, 'indexAbsen'])->name('mitra_absensi');
    Route::get('/mitra-check-koordinat/{id}', [AbsensiController::class, "showLocation"])->name('mitra-lihatMap');
    Route::get('/mitra-jadwal', [JadwalUserController::class, 'index'])->name('mitra_jadwal');
    Route::get('/mitra-user', [LeaderController::class, 'indexUser'])->name('mitra_user');
});

// untuk SPV
Route::middleware(['auth', 'spv', 'apdt'])->group(function () {
    Route::get('/SPV/spv-absensi', [MainController::class, 'indexAbsen'])->name('spv_absensi');
    Route::get('/SPV/spv-laporan', [MainController::class, 'indexLaporan'])->name('spv_laporan');
    Route::get('/SPV/spv-lembur', [MainController::class, 'indexLembur'])->name('spv_lembur');
    Route::get('/SPV/spv-user', [MainController::class, 'indexUser'])->name('spv_user');
});

// leader
Route::middleware(['auth', 'leader', 'apdt'])->group(function () {
    Route::resource('/LEADER/leader-rating', RatingController::class);
    Route::get('/LEADER/leader-absensi', [LeaderController::class, 'indexAbsen'])->name('lead_absensi');
    Route::get('/LEADER/leader-laporan', [LeaderController::class, 'indexLaporan'])->name('lead_laporan');
    Route::get('/LEADER/leader-lembur', [LeaderController::class, 'indexLembur'])->name('lead_lembur');
    Route::get('/LEADER/leader-user', [LeaderController::class, 'indexUser'])->name('lead_user');

    Route::resource('/LEADER/leader-jadwal', JadwalUserController::class);
    Route::post('/LEADER/jadwal-store', [JadwalUserController::class, 'storeJadwal'])->name("storeJadwalLeader");
    Route::get('/LEADER/leader-jadwal-new', [JadwalUserController::class, 'processDate'])->name('store.processDate');
    Route::get('/LEADER/leader-jadwal-export', [JadwalUserController::class, 'exportJadwal'])->name('lead_jadwal_export');

    Route::get('/LEADER/leader-absensi-izin', [IzinController::class, 'indexLead'])->name('lead_izin');
    Route::patch('/LEADER/leader-absensi-izin/accept/{id}', [IzinController::class, 'updateSuccess'])->name('lead_acc');
    Route::patch('/LEADER/leader-absensi/denied/{id}', [IzinController::class, 'updateDenied'])->name('lead_denied');
    Route::view('leaderView','leader_view/leaderView')->name('leaderView');
    
    Route::resource('/leader-checklist', ChecklistController::class);
    Route::post('/leader-checklist-ajx', [ChecklistController::class, 'signatureChecklistAJX'])->name('leader-checklist.ajx');
    Route::resource('/absensi-karyawan-co-cs', AbsensiController::class);
   
});
// danru
Route::middleware(['auth', 'danru', 'apdt'])->group(function () {
    Route::resource('/DANRU/danru-rating', RatingController::class);
    Route::get('/DANRU/danru-absensi', [LeaderController::class, 'indexAbsen'])->name('danru_absensi');
    Route::get('/DANRU/danru-laporan', [LeaderController::class, 'indexLaporan'])->name('danru_laporan');
    Route::get('/DANRU/danru-lembur', [LeaderController::class, 'indexLembur'])->name('danru_lembur');
    Route::get('/DANRU/danru-user', [LeaderController::class, 'indexUser'])->name('danru_user');

    Route::resource('/DANRU/danru-jadwal', JadwalUserController::class);
    Route::post('/DANRU/danru-store', [JadwalUserController::class, 'storeJadwal'])->name("storeJadwaldanru");
    Route::get('/DANRU/danru-jadwal-new', [JadwalUserController::class, 'processDate'])->name('danruStore.processDate');
    Route::get('/DANRU/danru-jadwal-export', [JadwalUserController::class, 'exportJadwal'])->name('danru_jadwal_export');

    Route::get('/DANRU/danru-absensi-izin', [IzinController::class, 'indexLead'])->name('danru_izin');
    Route::patch('/DANRU/danru-absensi-izin/accept/{id}', [IzinController::class, 'updateSuccess'])->name('danru_acc');
    Route::patch('/DANRU/danru-absensi/denied/{id}', [IzinController::class, 'updateDenied'])->name('danru_denied');
    Route::view('danruView','leader_view/leaderView')->name('danruView');
    Route::resource('/absensi-karyawan-co-scr', AbsensiController::class);
   
});


// ADIMIN
Route::middleware(['auth', 'admin', 'apdt'])->group(function () {
    Route::resource('/admin/qrcode', QrCodeController::class);
    Route::POST('/admin/qrcode/export', [QrCodeController::class, 'exportPDF'])->name('qrcode.export');    
    
    Route::get('/admin/data-absen', [AdminController::class, 'absen'])->name('admin.absen');
    Route::get('/admin/export', [AdminController::class, 'export'])->name('admin.export');
    Route::get('/admin/exportV2', [AdminController::class, 'exportWith'])->name('admin.exportV2');
    Route::get('/admin/export-izin', [AdminController::class, 'exp'])->name('admin.export-izin');
    Route::resource('/admin', AdminController::class);
    Route::resource('/client/data-client', ClientController::class);
    Route::resource('/users', UserController::class);
    Route::resource('/kerjasamas', KerjasamaController::class);
    Route::resource('/devisi', DivisiController::class);
    Route::resource('/perlengkapan', PerlengkapanController::class);
    
    Route::get('/divisi/{divisiId}/add-equipment', [DivisiController::class, 'editEquipment'])->name('editRquipment');
    Route::post('/divisi/{divisiId}/add-equipment', [DivisiController::class,'addEquipment'])->name('addEquipment');
    
    
    Route::get('/area/{areaId}/add-subarea', [SubareaController::class, 'editSubarea'])->name('edit.subarea');
    Route::post('/area/{areaId}/add-subarea', [SubareaController::class,'addSub'])->name('add.subarea');
    
    Route::resource('/data-lembur', LemburController::class);
    Route::get('/data-lembur-saat-ini', [LemburController::class, 'lemburIndexAdmin'])->name('lemburList');
    Route::resource('/shift', ShiftController::class);
    Route::resource('/jabatan', JabatanController::class);
    Route::delete('/laporans/{id}', [LaporanController::class, 'destroy']);
    Route::get('/export/laporans', [LaporanController::class, 'exportWith'])->name('export.laporans');
    Route::resource('/ruangan', RuanganController::class);
    Route::resource('/point', PointController::class);
    Route::patch('/claim-point/{id}', [AbsensiController::class, 'claimPoint'])->name('claim.point');
    Route::resource('holiday', HolidayController::class);
    Route::resource('/lokasi', LokasiController::class);
    Route::resource('/area', AreaController::class);
    
    Route::get('/admin-check-koordinat/{id}', [CheckPointController::class, "show"])->name('admin-lihatMap');

    Route::get('/admin-checkpoint', [AdminController::class, 'checkPoint'])->name('admin.cp.index');
    Route::patch('/admin-check-approve/{id}', [AdminController::class, 'approveCheck'])->name('admin.cp.approve');
    Route::patch('/admin-check-denied/{id}', [AdminController::class, 'deniedCheck'])->name('admin.cp.denied');
    Route::get('/admin-lihat-check/{id}', [AdminController::class, 'lihatCheck'])->name('admin.cp.show');
    Route::delete('/admin-checkpoint-delete/{id}', [AdminController::class, 'destroyCheck'])->name('admin.cp.delete');

    Route::resource('admin-jadwal', JadwalUserController::class);
    Route::post('storeJadwalAdmin', [JadwalUserController::class, 'storeJadwal'])->name('storeJadwalAdmin');
    Route::post('jadwal-import', [JadwalUserController::class, 'import'])->name('import-jadwal');
    Route::get('admin-jadwal-new', [JadwalUserController::class, 'processDate'])->name('store.processDate.admin');
    Route::get('admin-jadwal-export', [JadwalUserController::class, 'exportJadwal'])->name('jadwal_export.admin');
    Route::get('admin-cp-export', [CheckPointController::class, 'exportWith'])->name('cp_export.admin');
    
    Route::post('admin-ruangan-import', [RuanganController::class, 'import'])->name('ruangan.import');
    
    Route::get('/data-izin', [IzinController::class, 'indexAdmin'])->name('data-izin.admin');
    Route::patch('/absensi-izin/admin-accept/{id}', [IzinController::class, 'updateSuccess'])->name('admin_acc');
    Route::patch('/absensi-izin/admin-denied/{id}', [IzinController::class, 'updateDenied'])->name('admin_denied');
    Route::delete('/absensi-izin/{id}/deleted', [IzinController::class, 'deleteAdmin'])->name('admin.deletedIzin');
    
    Route::post('/pekerjaanCp-import', [PekerjaanCpController::class, 'import'])->name('import-pekerjaan');
    Route::resource('/pekerjaanCp', PekerjaanCpController::class);
    Route::resource('/admin-rating', RatingController::class);
    Route::resource('/news', NewsController::class);
    Route::resource('/subarea', SubareaController::class);
    Route::resource('/admin-checklist', ChecklistController::class);
    Route::post('/admin-checklist-ajx', [ChecklistController::class, 'signatureChecklistAJX'])->name('admin-checklist.ajx');
    Route::get('/admin-finalisasi/{start_date}/{end_date}', [FinalisasiController::class, 'exportPDF'])->name('finalisasi.export');
    
    Route::resource('/listPekerjaan', ListPekerjaanController::class);
    Route::post('/listPekerjaan-import', [ListPekerjaanController::class, 'importExcel'])->name('listPekerjaan-excell');
});


require __DIR__.'/auth.php';
