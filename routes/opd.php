<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OpdController;

Route::middleware(['auth', 'role:admin_opd'])->prefix('opd')->name('opd.')->group(function () {
    Route::get('/dashboard', [OpdController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifikasi', [OpdController::class, 'verifikasiIndex'])->name('verifikasi.index');
    Route::get('/verifikasi/{id}', [OpdController::class, 'verifikasiShow'])->name('verifikasi.show');
    Route::post('/verifikasi/{id}', [OpdController::class, 'verifikasiStore'])->name('verifikasi.store');
    Route::get('/riwayat', [OpdController::class, 'riwayat'])->name('riwayat');
});
