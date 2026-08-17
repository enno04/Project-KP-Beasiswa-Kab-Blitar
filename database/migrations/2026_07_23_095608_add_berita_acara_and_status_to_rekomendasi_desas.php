<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekomendasi_desas', function (Blueprint $table) {
            $table->string('berita_acara_path')->nullable()->after('surat_rekomendasi_path')
                  ->comment('Path file PDF Berita Acara Musyawarah Desa');
            $table->string('status_kecamatan')->default('belum_diverifikasi')->after('tanggal_rekomendasi')
                  ->comment('Status verifikasi oleh Kecamatan: belum_diverifikasi, disetujui, ditolak');
            $table->text('catatan_kecamatan')->nullable()->after('status_kecamatan');
            $table->unsignedBigInteger('verified_by')->nullable()->after('catatan_kecamatan')
                  ->comment('User ID Kecamatan yang memverifikasi');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('rekomendasi_desas', function (Blueprint $table) {
            $table->dropColumn(['berita_acara_path', 'status_kecamatan', 'catatan_kecamatan', 'verified_by', 'verified_at']);
        });
    }
};
