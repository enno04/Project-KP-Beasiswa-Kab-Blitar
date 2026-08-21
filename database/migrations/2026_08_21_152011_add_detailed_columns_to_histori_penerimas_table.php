<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('histori_penerimas', function (Blueprint $table) {
            $table->string('nomor_pendaftaran')->nullable()->after('id');
            $table->string('jalur_beasiswa')->nullable()->after('jenis_beasiswa');
            $table->decimal('ipk_nilai', 5, 2)->nullable()->after('jalur_beasiswa');
            $table->string('asal_sekolah')->nullable()->after('asal_perguruan_tinggi');
            $table->string('kecamatan')->nullable()->after('asal_sekolah');
            $table->string('desa')->nullable()->after('kecamatan');
            $table->string('waktu_penetapan')->nullable()->after('desa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histori_penerimas', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_pendaftaran',
                'jalur_beasiswa',
                'ipk_nilai',
                'asal_sekolah',
                'kecamatan',
                'desa',
                'waktu_penetapan'
            ]);
        });
    }
};
