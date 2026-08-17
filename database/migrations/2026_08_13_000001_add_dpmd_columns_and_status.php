<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah kolom DPMD di tabel rekomendasi_desas
     * dan status enum baru di tabel pendaftarans
     * untuk mendukung workflow paralel Kecamatan + DPMD.
     */
    public function up(): void
    {
        // 1. Tambah kolom DPMD di rekomendasi_desas
        Schema::table('rekomendasi_desas', function (Blueprint $table) {
            $table->string('status_dpmd')->default('belum_diverifikasi')->after('verified_at')
                  ->comment('Status verifikasi oleh DPMD: belum_diverifikasi, disetujui, ditolak');
            $table->text('catatan_dpmd')->nullable()->after('status_dpmd');
            $table->unsignedBigInteger('dpmd_verified_by')->nullable()->after('catatan_dpmd')
                  ->comment('User ID Admin DPMD yang memverifikasi');
            $table->timestamp('dpmd_verified_at')->nullable()->after('dpmd_verified_by');
        });

        // 2. Tambah status 'ditolak_dpmd' ke enum pendaftarans
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
                'ditolak_dpmd',
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
                'selesai',
                'gugur_wawancara'
            ) DEFAULT 'menunggu_verifikasi'");
        }
    }

    public function down(): void
    {
        Schema::table('rekomendasi_desas', function (Blueprint $table) {
            $table->dropColumn(['status_dpmd', 'catatan_dpmd', 'dpmd_verified_by', 'dpmd_verified_at']);
        });

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
                'selesai',
                'gugur_wawancara'
            ) DEFAULT 'menunggu_verifikasi'");
        }
    }
};
