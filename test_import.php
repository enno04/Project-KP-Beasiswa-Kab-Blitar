<?php

require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Imports\RiwayatPenetapanImport;

$import = new RiwayatPenetapanImport();
$data = collect([
    collect(['Nomor Pendaftaran', 'Nama Lengkap', 'NIK', 'Asal Desa', 'Asal Kecamatan', 'Total Nilai', 'Waktu Penetapan SK', 'Asal Perguruan Tinggi', 'Program Beasiswa', 'Jalur Beasiswa']),
    collect(['REG-2026-001', 'Budi Santoso', '1234567890123456', 'Desa Sukamaju', 'Kecamatan Blitar', '3.85', '21/08/2026', 'Universitas Brawijaya', 'SDSS', 'Reguler'])
]);

DB::table('histori_penerimas')->where('nik', '1234567890123456')->delete();
$import->collection($data);

// Gunakan reflection untuk membaca protected colMap jika tidak bisa, ah mending echo DB


$result = DB::table('histori_penerimas')->orderBy('id', 'desc')->first();
echo json_encode($result, JSON_PRETTY_PRINT);
