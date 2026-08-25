<?php

use Illuminate\Support\Facades\Route;

// Fallback untuk pengetikan URL relatif /mengabdi dari halaman manapun
Route::get('{any}/mengabdi', function () {
    return redirect('/mengabdi');
})->where('any', '.*');

// Redirect dashboard based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    $dashboardRoute = match($user->getRoleKode()) {
        'super_admin' => 'super-admin.dashboard',
        'admin_kabupaten' => 'kabupaten.dashboard',
        'admin_opd' => 'opd.dashboard',
        'admin_kecamatan' => 'kecamatan.dashboard',
        'admin_desa' => 'desa.dashboard',
        'admin_dpmd' => 'dpmd.dashboard',
        default => 'home',
    };
    return redirect()->route($dashboardRoute);
})->middleware('auth')->name('dashboard');

// Load separate route files
require __DIR__.'/public.php';
require __DIR__.'/auth.php';
require __DIR__.'/super-admin.php';
require __DIR__.'/admin-kabupaten.php';
require __DIR__.'/opd.php';
require __DIR__.'/kecamatan.php';
require __DIR__.'/desa.php';
require __DIR__.'/dpmd.php';
