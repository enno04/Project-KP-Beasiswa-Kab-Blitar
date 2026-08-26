<?php

use Illuminate\Support\Facades\Route;

// Secret route untuk memperbaiki masalah symlink di production server
Route::get('/fix-storage-link', function () {
    try {
        $targetFolder = storage_path('app/public');
        $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
        
        // Hapus link lama jika ada (mencegah bentrok)
        if (file_exists($linkFolder) || is_link($linkFolder)) {
            unlink($linkFolder);
        }
        
        // Buat link baru
        symlink($targetFolder, $linkFolder);
        
        return '✅ Berhasil! Symlink berhasil dibuat ulang di direktori ' . $_SERVER['DOCUMENT_ROOT'] . '. Silakan kembali ke dashboard OPD dan refresh halamannya.';
    } catch (\Exception $e) {
        return '❌ Gagal membuat symlink: ' . $e->getMessage() . '. Mintalah bantuan IT untuk mengecek permission server.';
    }
});

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
