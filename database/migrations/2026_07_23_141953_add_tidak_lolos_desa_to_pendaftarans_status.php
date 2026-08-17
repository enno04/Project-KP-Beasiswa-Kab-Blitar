<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM(
                'draft',
                'menunggu_verifikasi',
                'sedang_diverifikasi',
                'lolos_verifikasi',
                'tidak_lolos_verifikasi',
                'tidak_lolos_desa',
                'diteruskan_ke_kecamatan',
                'ditolak_kecamatan',
                'proses_seleksi',
                'menunggu_wawancara',
                'proses_wawancara',
                'menunggu_penilaian',
                'proses_penilaian',
                'menunggu_penetapan',
                'lulus',
                'tidak_lulus',
                'sk_terbit',
                'pembayaran_diproses',
                'selesai'
            ) DEFAULT 'menunggu_verifikasi'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pendaftarans MODIFY COLUMN status ENUM(
                'draft',
                'menunggu_verifikasi',
                'sedang_diverifikasi',
                'lolos_verifikasi',
                'tidak_lolos_verifikasi',
                'diteruskan_ke_kecamatan',
                'ditolak_kecamatan',
                'proses_seleksi',
                'menunggu_wawancara',
                'proses_wawancara',
                'menunggu_penilaian',
                'proses_penilaian',
                'menunggu_penetapan',
                'lulus',
                'tidak_lulus',
                'sk_terbit',
                'pembayaran_diproses',
                'selesai'
            ) DEFAULT 'menunggu_verifikasi'");
        }
    }
};
