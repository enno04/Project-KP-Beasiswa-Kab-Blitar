<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\ReferensiController;
use App\Http\Controllers\Master\KonfigurasiController;
use App\Http\Controllers\Master\KriteriaController;
use App\Http\Controllers\Master\DokumenPublikController;

Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // --- Master Data ---
    Route::prefix('master')->name('master.')->group(function () {
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');

        // OPD
        Route::get('/opd', [ReferensiController::class, 'opdIndex'])->name('opd.index');
        Route::post('/opd', [ReferensiController::class, 'opdStore'])->name('opd.store');
        Route::put('/opd/{id}', [ReferensiController::class, 'opdUpdate'])->name('opd.update');
        Route::delete('/opd/{id}', [ReferensiController::class, 'opdDestroy'])->name('opd.destroy');

        // Kecamatan
        Route::get('/kecamatan', [ReferensiController::class, 'kecamatanIndex'])->name('kecamatan.index');
        Route::post('/kecamatan', [ReferensiController::class, 'kecamatanStore'])->name('kecamatan.store');
        Route::put('/kecamatan/{id}', [ReferensiController::class, 'kecamatanUpdate'])->name('kecamatan.update');
        Route::delete('/kecamatan/{id}', [ReferensiController::class, 'kecamatanDestroy'])->name('kecamatan.destroy');

        // Desa
        Route::get('/desa', [ReferensiController::class, 'desaIndex'])->name('desa.index');
        Route::post('/desa', [ReferensiController::class, 'desaStore'])->name('desa.store');
        Route::put('/desa/{id}', [ReferensiController::class, 'desaUpdate'])->name('desa.update');
        Route::delete('/desa/{id}', [ReferensiController::class, 'desaDestroy'])->name('desa.destroy');



        // Periode
        Route::get('/periode', [KonfigurasiController::class, 'periodeIndex'])->name('periode.index');
        Route::post('/periode', [KonfigurasiController::class, 'periodeStore'])->name('periode.store');
        Route::put('/periode/{id}', [KonfigurasiController::class, 'periodeUpdate'])->name('periode.update');
        Route::delete('/periode/{id}', [KonfigurasiController::class, 'periodeDestroy'])->name('periode.destroy');

        // Program
        Route::get('/program', [KonfigurasiController::class, 'programIndex'])->name('program.index');
        Route::post('/program', [KonfigurasiController::class, 'programStore'])->name('program.store');
        Route::put('/program/{id}', [KonfigurasiController::class, 'programUpdate'])->name('program.update');
        Route::delete('/program/{id}', [KonfigurasiController::class, 'programDestroy'])->name('program.destroy');

        // Custom Fields (nested under jalur)
        Route::get('/jalur/{jalur}/custom-fields', [\App\Http\Controllers\SuperAdmin\CustomFieldController::class, 'index'])->name('custom-fields.index');
        Route::post('/jalur/{jalur}/custom-fields', [\App\Http\Controllers\SuperAdmin\CustomFieldController::class, 'store'])->name('custom-fields.store');
        Route::put('/custom-fields/{customField}', [\App\Http\Controllers\SuperAdmin\CustomFieldController::class, 'update'])->name('custom-fields.update');
        Route::delete('/custom-fields/{customField}', [\App\Http\Controllers\SuperAdmin\CustomFieldController::class, 'destroy'])->name('custom-fields.destroy');

        // Jalur (nested under program)
        Route::get('/program/{programId}/jalur', [KonfigurasiController::class, 'jalurIndex'])->name('jalur.index');
        Route::post('/program/{programId}/jalur', [KonfigurasiController::class, 'jalurStore'])->name('jalur.store');
        Route::put('/program/{programId}/jalur/{id}', [KonfigurasiController::class, 'jalurUpdate'])->name('jalur.update');
        Route::delete('/program/{programId}/jalur/{id}', [KonfigurasiController::class, 'jalurDestroy'])->name('jalur.destroy');

        // Dokumen (nested under jalur)
        Route::get('/jalur/{jalurId}/dokumen', [KonfigurasiController::class, 'dokumenIndex'])->name('dokumen.index');
        Route::post('/jalur/{jalurId}/dokumen', [KonfigurasiController::class, 'dokumenStore'])->name('dokumen.store');
        Route::put('/jalur/{jalurId}/dokumen/{id}', [KonfigurasiController::class, 'dokumenUpdate'])->name('dokumen.update');
        Route::delete('/jalur/{jalurId}/dokumen/{id}', [KonfigurasiController::class, 'dokumenDestroy'])->name('dokumen.destroy');

        // Kriteria (nested under jalur)
        Route::get('/jalur/{jalurId}/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
        Route::post('/jalur/{jalurId}/kelompok', [KriteriaController::class, 'kelompokStore'])->name('kelompok.store');
        Route::put('/jalur/{jalurId}/kelompok/{id}', [KriteriaController::class, 'kelompokUpdate'])->name('kelompok.update');
        Route::delete('/jalur/{jalurId}/kelompok/{id}', [KriteriaController::class, 'kelompokDestroy'])->name('kelompok.destroy');
        Route::post('/jalur/{jalurId}/kriteria', [KriteriaController::class, 'kriteriaStore'])->name('kriteria.store');
        Route::delete('/jalur/{jalurId}/kriteria/{id}', [KriteriaController::class, 'kriteriaDestroy'])->name('kriteria.destroy');
        Route::put('/jalur/{jalurId}/kriteria/{id}', [KriteriaController::class, 'kriteriaUpdate'])->name('kriteria.update');
        Route::post('/jalur/{jalurId}/pilihan', [KriteriaController::class, 'pilihanStore'])->name('pilihan.store');
        Route::delete('/jalur/{jalurId}/pilihan/{id}', [KriteriaController::class, 'pilihanDestroy'])->name('pilihan.destroy');
        Route::put('/jalur/{jalurId}/bobot', [KriteriaController::class, 'bobotUpdate'])->name('bobot.update');

        // Dokumen Publik
        Route::get('/dokumen-publik', [DokumenPublikController::class, 'index'])->name('dokumen-publik.index');
        Route::post('/dokumen-publik', [DokumenPublikController::class, 'store'])->name('dokumen-publik.store');
        Route::put('/dokumen-publik/{id}', [DokumenPublikController::class, 'update'])->name('dokumen-publik.update');
        Route::delete('/dokumen-publik/{id}', [DokumenPublikController::class, 'destroy'])->name('dokumen-publik.destroy');
        Route::patch('/dokumen-publik/{id}/toggle', [DokumenPublikController::class, 'toggleStatus'])->name('dokumen-publik.toggle');
    });

    // Monitoring & Histori
    Route::get('/monitoring/pendaftaran', [SuperAdminController::class, 'monitoringPendaftaran'])->name('monitoring.pendaftaran');
    Route::get('/histori', [SuperAdminController::class, 'historiPenerima'])->name('histori');
    Route::post('/histori/import/lama', [SuperAdminController::class, 'importHistori'])->name('histori.import.lama');
    Route::post('/histori/import/baru', [SuperAdminController::class, 'importHistoriBaru'])->name('histori.import.baru');

    // Audit Log
    Route::get('/audit-log', [SuperAdminController::class, 'auditLog'])->name('audit-log');

    Route::get('/pembersihan-data', [SuperAdminController::class, 'pembersihanData'])->name('pembersihan-data.index');
    Route::get('/pembersihan-data/{program_id}', [SuperAdminController::class, 'detailPembersihanData'])->name('pembersihan-data.detail');
    Route::delete('/pembersihan-data/bulk-delete', [SuperAdminController::class, 'destroyDataPendaftar'])->name('pembersihan-data.destroy');

    Route::get('/data-dummy', [SuperAdminController::class, 'dataDummy'])->name('data-dummy.index');
    Route::post('/data-dummy/generate', [SuperAdminController::class, 'generateDataDummy'])->name('data-dummy.generate');
    Route::get('/data-dummy/bypass-opd', [SuperAdminController::class, 'bypassOpdList'])->name('data-dummy.bypass-opd');
    Route::post('/data-dummy/auto-verify-opd', [SuperAdminController::class, 'autoVerifyOpd'])->name('data-dummy.auto-verify');
});
