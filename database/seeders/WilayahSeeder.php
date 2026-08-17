<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/data/v_district.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan di {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            $this->command->error("Gagal membuka file CSV.");
            return;
        }

        // Skip Header: "id_kecamatan","nama_kecamatan","id_desa","nama_desa"
        fgetcsv($handle, 1000, ',');

        $kecamatanCache = [];

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) < 4) {
                continue;
            }

            $kodeKecamatan = trim($row[0]);
            $namaKecamatan = trim($row[1]);
            $kodeDesa = trim($row[2]);
            $namaDesa = trim($row[3]);

            if (empty($kodeKecamatan) || empty($kodeDesa)) {
                continue;
            }

            // Create or get Kecamatan
            if (!isset($kecamatanCache[$kodeKecamatan])) {
                $kecamatan = Kecamatan::firstOrCreate(
                    ['kode_kecamatan' => $kodeKecamatan],
                    [
                        'uuid' => (string) Str::uuid(),
                        'nama_kecamatan' => $namaKecamatan,
                    ]
                );
                $kecamatanCache[$kodeKecamatan] = $kecamatan->id;
            }

            $kecamatanId = $kecamatanCache[$kodeKecamatan];

            // Create or update Desa
            Desa::firstOrCreate(
                ['kode_desa' => $kodeDesa],
                [
                    'uuid' => (string) Str::uuid(),
                    'kecamatan_id' => $kecamatanId,
                    'nama_desa' => $namaDesa,
                ]
            );
        }

        fclose($handle);

        $this->command->info("Seeder Wilayah berhasil diimpor dari v_district.csv!");
    }
}
