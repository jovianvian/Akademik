<?php

use App\Http\Controllers\AdminAkademikController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:8,1')->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth', 'session.login'])->name('logout');

Route::middleware(['auth', 'session.login'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/profile', 'profile.index', ['title' => 'Profil Saya'])->name('profile.index');
    Route::get('/krs', [KrsController::class, 'index'])->middleware(['role:mahasiswa,super_admin', 'ability:krs.view'])->name('krs.index');
    Route::post('/krs', [KrsController::class, 'store'])->middleware(['role:mahasiswa,super_admin', 'ability:krs.manage'])->name('krs.store');
    Route::post('/krs/generate-auto', [KrsController::class, 'generateAuto'])->middleware(['role:mahasiswa,super_admin', 'ability:krs.manage'])->name('krs.generate-auto');
    Route::post('/krs/finalize', [KrsController::class, 'finalize'])->middleware(['role:mahasiswa,super_admin', 'ability:krs.manage'])->name('krs.finalize');
    Route::get('/khs', [MahasiswaController::class, 'khs'])->middleware(['role:mahasiswa,super_admin', 'ability:nilai.view'])->name('khs.index');
    Route::get('/khs/export', [MahasiswaController::class, 'exportKhsCsv'])->middleware(['role:mahasiswa,super_admin', 'ability:nilai.view'])->name('khs.export');
    Route::get('/khs/export-pdf', [MahasiswaController::class, 'exportKhsPdf'])->middleware(['role:mahasiswa,super_admin', 'ability:nilai.view'])->name('khs.export-pdf');
    Route::get('/transkrip', [MahasiswaController::class, 'transkrip'])->middleware(['role:mahasiswa,super_admin', 'ability:nilai.view'])->name('transkrip.index');
    Route::get('/nilai/{krsDetailId}', [MahasiswaController::class, 'detailNilai'])->middleware(['role:mahasiswa,super_admin', 'ability:nilai.view'])->name('nilai.detail');
    Route::get('/ukt', [MahasiswaController::class, 'ukt'])->middleware(['role:mahasiswa,super_admin', 'ability:ukt.view'])->name('ukt.index');
    Route::get('/jadwal-mahasiswa', [MahasiswaController::class, 'jadwal'])->middleware(['role:mahasiswa,super_admin', 'ability:krs.view'])->name('mahasiswa.jadwal.index');
    Route::get('/profil-mahasiswa', [MahasiswaController::class, 'profil'])->middleware(['role:mahasiswa,super_admin', 'ability:dashboard.view'])->name('mahasiswa.profil.index');
    Route::get('/evaluasi-dosen', [MahasiswaController::class, 'evaluasi'])->middleware(['role:mahasiswa,super_admin', 'ability:krs.view'])->name('mahasiswa.evaluasi.index');
    Route::post('/evaluasi-dosen', [MahasiswaController::class, 'storeEvaluasi'])->middleware(['role:mahasiswa,super_admin', 'ability:krs.manage'])->name('mahasiswa.evaluasi.store');

    Route::prefix('master')->name('master.')->middleware('role:admin_akademik,super_admin')->group(function () {
        Route::get('/fakultas', [MasterDataController::class, 'fakultas'])->middleware('ability:master.view')->name('fakultas.index');
        Route::post('/fakultas', [MasterDataController::class, 'storeFakultas'])->middleware('ability:master.manage')->name('fakultas.store');
        Route::patch('/fakultas/{id}', [MasterDataController::class, 'updateFakultas'])->middleware('ability:master.manage')->name('fakultas.update');
        Route::delete('/fakultas/{id}', [MasterDataController::class, 'destroyFakultas'])->middleware('ability:master.manage')->name('fakultas.destroy');

        Route::get('/prodi', [MasterDataController::class, 'prodi'])->middleware('ability:master.view')->name('prodi.index');
        Route::post('/prodi', [MasterDataController::class, 'storeProdi'])->middleware('ability:master.manage')->name('prodi.store');
        Route::patch('/prodi/{id}', [MasterDataController::class, 'updateProdi'])->middleware('ability:master.manage')->name('prodi.update');
        Route::delete('/prodi/{id}', [MasterDataController::class, 'destroyProdi'])->middleware('ability:master.manage')->name('prodi.destroy');

        Route::get('/mata-kuliah', [MasterDataController::class, 'mataKuliah'])->middleware('ability:master.view')->name('mata-kuliah.index');
        Route::post('/mata-kuliah', [MasterDataController::class, 'storeMataKuliah'])->middleware('ability:master.manage')->name('mata-kuliah.store');
        Route::patch('/mata-kuliah/{id}', [MasterDataController::class, 'updateMataKuliah'])->middleware('ability:master.manage')->name('mata-kuliah.update');
        Route::delete('/mata-kuliah/{id}', [MasterDataController::class, 'destroyMataKuliah'])->middleware('ability:master.manage')->name('mata-kuliah.destroy');

        Route::get('/jadwal', [MasterDataController::class, 'jadwal'])->middleware('ability:master.view')->name('jadwal.index');
        Route::post('/jadwal', [MasterDataController::class, 'storeJadwal'])->middleware('ability:master.manage')->name('jadwal.store');
        Route::patch('/jadwal/{id}', [MasterDataController::class, 'updateJadwal'])->middleware('ability:master.manage')->name('jadwal.update');
        Route::delete('/jadwal/{id}', [MasterDataController::class, 'destroyJadwal'])->middleware('ability:master.manage')->name('jadwal.destroy');

        Route::get('/jabatan-dosen', [MasterDataController::class, 'jabatanDosen'])->middleware('ability:master.view')->name('jabatan-dosen.index');
        Route::post('/jabatan-dosen', [MasterDataController::class, 'storeJabatanDosen'])->middleware('ability:master.manage')->name('jabatan-dosen.store');
        Route::patch('/jabatan-dosen/{id}', [MasterDataController::class, 'updateJabatanDosen'])->middleware('ability:master.manage')->name('jabatan-dosen.update');
        Route::delete('/jabatan-dosen/{id}', [MasterDataController::class, 'destroyJabatanDosen'])->middleware('ability:master.manage')->name('jabatan-dosen.destroy');
    });

    Route::prefix('keuangan')->name('keuangan.')->middleware('role:admin_keuangan,super_admin')->group(function () {
        Route::get('/tagihan', [KeuanganController::class, 'tagihan'])->middleware('ability:keuangan.view')->name('tagihan.index');
        Route::post('/tagihan', [KeuanganController::class, 'storeTagihan'])->middleware('ability:keuangan.manage')->name('tagihan.store');
        Route::patch('/tagihan/{id}/status', [KeuanganController::class, 'updateStatusTagihan'])->middleware('ability:keuangan.manage')->name('tagihan.status');

        Route::get('/pembayaran', [KeuanganController::class, 'pembayaran'])->middleware('ability:keuangan.view')->name('pembayaran.index');
        Route::post('/pembayaran', [KeuanganController::class, 'storePembayaran'])->middleware('ability:keuangan.manage')->name('pembayaran.store');
        Route::get('/monitoring-pembayaran', [KeuanganController::class, 'monitoringPembayaran'])->middleware('ability:keuangan.view')->name('monitoring-pembayaran.index');
        Route::get('/pembayaran/export', [KeuanganController::class, 'exportPembayaranCsv'])->middleware('ability:keuangan.view')->name('pembayaran.export');
        Route::get('/pembayaran/export-pdf', [KeuanganController::class, 'exportPembayaranPdf'])->middleware('ability:keuangan.view')->name('pembayaran.export-pdf');
    });

    Route::prefix('dosen')->name('dosen.')->middleware('role:dosen,super_admin')->group(function () {
        Route::get('/jadwal', [DosenController::class, 'jadwal'])->middleware('ability:jadwal.view')->name('jadwal.index');
        Route::get('/nilai', [DosenController::class, 'nilai'])->middleware('ability:nilai.manage')->name('nilai.index');
        Route::post('/nilai', [DosenController::class, 'storeNilai'])->middleware('ability:nilai.manage')->name('nilai.store');
        Route::get('/nilai/export', [DosenController::class, 'exportNilaiCsv'])->middleware('ability:nilai.manage')->name('nilai.export');
        Route::get('/monitoring-mahasiswa', [DosenController::class, 'monitoringMahasiswa'])->middleware('ability:mahasiswa.monitor')->name('monitoring-mahasiswa.index');
        Route::get('/evaluasi-saya', [DosenController::class, 'evaluasiSaya'])->middleware('ability:mahasiswa.monitor')->name('evaluasi-saya.index');
    });

    Route::prefix('akademik')->name('akademik.')->middleware('role:admin_akademik,super_admin')->group(function () {
        Route::get('/generate-khs', [AdminAkademikController::class, 'generateKhs'])->middleware('ability:khs.generate')->name('generate-khs.index');
        Route::post('/generate-khs/{krsId}/finalize', [AdminAkademikController::class, 'finalizeKhs'])->middleware('ability:khs.generate')->name('generate-khs.finalize');
        Route::patch('/generate-khs/{krsId}/unlock-nilai', [AdminAkademikController::class, 'unlockNilai'])->middleware('ability:khs.generate')->name('generate-khs.unlock-nilai');
        Route::get('/periode-krs', [AdminAkademikController::class, 'periodeKrs'])->middleware('ability:master.view')->name('periode-krs.index');
        Route::patch('/periode-krs/{id}', [AdminAkademikController::class, 'updatePeriodeKrs'])->middleware('ability:master.manage')->name('periode-krs.update');
        Route::get('/mahasiswa-status', [AdminAkademikController::class, 'mahasiswaStatus'])->middleware('ability:master.view')->name('mahasiswa-status.index');
        Route::patch('/mahasiswa-status/{id}', [AdminAkademikController::class, 'updateMahasiswaStatus'])->middleware('ability:master.manage')->name('mahasiswa-status.update');
        Route::post('/mahasiswa-status/sync-ukt', [AdminAkademikController::class, 'syncStatusByUkt'])->middleware('ability:master.manage')->name('mahasiswa-status.sync-ukt');
        Route::get('/monitoring-krs', [AdminAkademikController::class, 'monitoringKrs'])->middleware('ability:master.view')->name('monitoring-krs.index');
        Route::get('/nilai-mahasiswa', [AdminAkademikController::class, 'nilaiMahasiswa'])->middleware('ability:master.view')->name('nilai-mahasiswa.index');
        Route::get('/evaluasi-dosen', [AdminAkademikController::class, 'evaluasiDosen'])->middleware('ability:master.view')->name('evaluasi-dosen.index');
    });

    Route::prefix('super-admin')->name('super-admin.')->middleware('role:super_admin')->group(function () {
        Route::get('/users', [SuperAdminController::class, 'users'])->middleware('ability:users.manage')->name('users.index');
        Route::patch('/users/{id}', [SuperAdminController::class, 'updateUser'])->middleware('ability:users.manage')->name('users.update');
        Route::get('/role-permissions', [SuperAdminController::class, 'rolePermissions'])->middleware('ability:roles.manage')->name('role-permissions.index');
        Route::patch('/role-permissions/{roleId}', [SuperAdminController::class, 'updateRolePermissions'])->middleware('ability:roles.manage')->name('role-permissions.update');
        Route::get('/system-settings', [SuperAdminController::class, 'systemSettings'])->middleware('ability:roles.manage')->name('system-settings.index');
        Route::patch('/system-settings', [SuperAdminController::class, 'updateSystemSettings'])->middleware('ability:roles.manage')->name('system-settings.update');
        Route::get('/master-recovery', [SuperAdminController::class, 'masterRecovery'])->middleware('ability:roles.manage')->name('master-recovery.index');
        Route::post('/master-recovery/restore', [SuperAdminController::class, 'restoreMasterData'])->middleware('ability:roles.manage')->name('master-recovery.restore');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->middleware('ability:audit.view')->name('audit-logs.index');
        Route::get('/audit-logs/export', [AuditLogController::class, 'exportCsv'])->middleware('ability:audit.view')->name('audit-logs.export');
        Route::get('/audit-logs/export-pdf', [AuditLogController::class, 'exportPdf'])->middleware('ability:audit.view')->name('audit-logs.export-pdf');
    });
});
