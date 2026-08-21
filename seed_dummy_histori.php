<?php

require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\HistoriPenerima;

$faker = Faker::create('id_ID');

$data = [];
$programs = ['Satu Desa Satu Sarjana (SDSS)', 'Mahasiswa Berprestasi (MBP)', 'Kurang Mampu'];
$jalurs = ['Reguler', 'Prestasi Akademik', 'Prestasi Non Akademik'];

for ($i = 1; $i <= 65; $i++) {
    $data[] = [
        'tahun' => '2025',
        'nik' => $faker->nik,
        'nama_lengkap' => '[DUMMY] ' . $faker->name,
        'asal_perguruan_tinggi' => 'Universitas ' . $faker->city,
        'jenis_beasiswa' => $faker->randomElement($programs),
        'nomor_pendaftaran' => 'REG-2025-' . str_pad($i, 3, '0', STR_PAD_LEFT),
        'jalur_beasiswa' => $faker->randomElement($jalurs),
        'ipk_nilai' => $faker->randomFloat(2, 3.2, 4),
        'asal_sekolah' => 'SMAN ' . rand(1, 5) . ' Blitar',
        'kecamatan' => 'Kecamatan Blitar',
        'desa' => 'Desa ' . $faker->citySuffix,
        'waktu_penetapan' => '2025-08-20 10:00:00',
        'sumber_data' => 'sistem',
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

HistoriPenerima::insert($data);
echo "Successfully inserted " . count($data) . " dummy records.\n";
