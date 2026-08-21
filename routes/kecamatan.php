<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KecamatanController;

Route::middleware(['auth', 'role:admin_kecamatan'])->prefix('kecamatan')->name('kecamatan.')->group(function () {
    Route::get('/dashboard', [KecamatanController::class, 'dashboard'])->name('dashboard');
    Route::get('/riwayat', [KecamatanController::class, 'riwayat'])->name('riwayat');
    Route::get('/program/{programSlug}/{jalurSlug?}', [KecamatanController::class, 'index'])->name('program.index');
    Route::get('/pendaftar/{id}', [KecamatanController::class, 'show'])->name('show');
    Route::post('/pendaftar/{id}/verifikasi', [KecamatanController::class, 'verifikasiRekomendasi'])->name('verifikasi.rekomendasi');
});
