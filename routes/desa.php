<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesaController;

Route::middleware(['auth', 'role:admin_desa'])->prefix('desa')->name('desa.')->group(function () {
    Route::get('/dashboard', [DesaController::class, 'dashboard'])->name('dashboard');
    Route::get('/program/{programSlug}/{jalurSlug?}', [DesaController::class, 'index'])->name('program.index');
    Route::get('/program/{programSlug}/{jalurSlug?}/export', [DesaController::class, 'exportData'])->name('program.export');
    Route::get('/pendaftar/{id}', [DesaController::class, 'show'])->name('show');
    Route::post('/pendaftar/{id}/rekomendasi', [DesaController::class, 'uploadRekomendasi'])->name('rekomendasi');
    
    // Penilaian & Ranking SDSS
    Route::post('/penilaian/hitung', [DesaController::class, 'hitungPenilaian'])->name('penilaian.hitung');
    Route::post('/ranking/generate', [DesaController::class, 'generateRanking'])->name('ranking.generate');
    
    // Penetapan Perwakilan SDSS
    Route::post('/pendaftar/{id}/tetapkan', [DesaController::class, 'tetapkanPerwakilan'])->name('tetapkan');
    
    // Riwayat Hasil Penetapan Desa
    Route::get('/riwayat', [DesaController::class, 'riwayat'])->name('riwayat');
});
