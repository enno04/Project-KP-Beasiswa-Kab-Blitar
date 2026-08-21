<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KabupatenController;

Route::middleware(['auth', 'role:admin_kabupaten'])->prefix('kabupaten')->name('kabupaten.')->group(function () {
    Route::get('/dashboard', [KabupatenController::class, 'dashboard'])->name('dashboard');
    Route::get('/rekap-data', [KabupatenController::class, 'rekapData'])->name('rekap-data');
    Route::get('/riwayat-penetapan', [KabupatenController::class, 'riwayatPenetapan'])->name('riwayat-penetapan');
    Route::get('/export-riwayat', [KabupatenController::class, 'exportRiwayat'])->name('export-riwayat');

    // Pendaftar per program (generik)
    Route::get('/program/{programSlug}/{jalurSlug?}', [KabupatenController::class, 'index'])->name('program.index');
    Route::get('/pendaftar/{id}', [KabupatenController::class, 'show'])->name('show');

    // Penilaian & Ranking
    Route::post('/penilaian/hitung', [KabupatenController::class, 'hitungPenilaian'])->name('penilaian.hitung');
    Route::post('/ranking/generate', [KabupatenController::class, 'generateRanking'])->name('ranking.generate');
    Route::post('/penilaian-ranking', [KabupatenController::class, 'hitungDanRanking'])->name('penilaian.ranking');
    Route::post('/wawancara/{id}', [KabupatenController::class, 'simpanWawancara'])->name('wawancara.simpan');
    Route::get('/hasil/ranking', [KabupatenController::class, 'hasilRanking'])->name('hasil.ranking');
    Route::get('/hasil/penetapan', [KabupatenController::class, 'penetapan'])->name('hasil.penetapan');
    Route::post('/hasil/penetapan', [KabupatenController::class, 'penetapanStore'])->name('hasil.penetapan.store');
    Route::post('/penetapan/{id}', [KabupatenController::class, 'tetapkanSatu'])->name('penetapan.satu');
});
