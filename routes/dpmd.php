<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DpmdController;

Route::middleware(['auth', 'role:admin_dpmd'])->prefix('dpmd')->name('dpmd.')->group(function () {
    Route::get('/dashboard', [DpmdController::class, 'dashboard'])->name('dashboard');
    Route::get('/riwayat', [DpmdController::class, 'riwayat'])->name('riwayat');
    Route::get('/program/{programSlug}/{jalurSlug?}', [DpmdController::class, 'index'])->name('program.index');
    Route::get('/pendaftar/{id}', [DpmdController::class, 'show'])->name('show');
    Route::post('/pendaftar/{id}/verifikasi', [DpmdController::class, 'verifikasiRekomendasi'])->name('verifikasi.rekomendasi');
});
