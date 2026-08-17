<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\Master\ReferensiController;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/informasi', [PublicController::class, 'informasi'])->name('informasi');
Route::get('/kriteria-persyaratan', [PublicController::class, 'kriteriaPersyaratan'])->name('kriteria.persyaratan');
Route::get('/seleksi-penetapan', [PublicController::class, 'seleksiPenetapan'])->name('seleksi.penetapan');
Route::get('/cek-status', [PublicController::class, 'cekStatus'])->name('cek.status');
Route::post('/cek-status', [PublicController::class, 'cekStatusProses'])->name('cek.status.proses');
Route::get('/dokumen-publik/{id}/download', [PublicController::class, 'downloadDokumenPublik'])->name('dokumen.publik.download');

Route::prefix('pendaftaran')->name('pendaftaran.')->group(function () {
    Route::get('/', [PendaftaranController::class, 'index'])->name('index');
    Route::post('/', [PendaftaranController::class, 'store'])->name('store');
    Route::get('/bukti/{id}', [PendaftaranController::class, 'buktiPendaftaran'])->name('bukti');
    Route::get('/{programSlug}/{jalurSlug}', [PendaftaranController::class, 'create'])->name('create');
});

// API publik
Route::get('/api/desa/{kecamatanId}', [PendaftaranController::class, 'getDesaByKecamatan'])->name('api.desa');
Route::get('/api/desa-by-kecamatan/{kecamatanId}', [ReferensiController::class, 'getDesaByKecamatan'])->name('api.desa.by.kecamatan');
Route::post('/api/cek-nik', [PendaftaranController::class, 'cekNik'])->name('api.cek_nik');
