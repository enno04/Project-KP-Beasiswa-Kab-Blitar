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
        Schema::table('pendaftaran_identitas', function (Blueprint $table) {
            $table->text('alamat_ktp')->after('alamat')->nullable();
        });

        Schema::table('pendaftaran_orangtuas', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'pekerjaan_ayah', 'penghasilan_ayah',
                'pekerjaan_ibu', 'penghasilan_ibu',
                'pekerjaan_wali', 'penghasilan_wali',
                'jumlah_tanggungan'
            ]);

            // Add Ayah
            $table->string('nik_ayah', 16)->after('nama_ayah')->nullable();
            $table->text('alamat_ayah')->after('nik_ayah')->nullable();
            $table->string('tempat_lahir_ayah')->after('alamat_ayah')->nullable();
            $table->date('tanggal_lahir_ayah')->after('tempat_lahir_ayah')->nullable();
            $table->string('no_hp_ayah', 20)->after('tanggal_lahir_ayah')->nullable();

            // Add Ibu
            $table->string('nik_ibu', 16)->after('nama_ibu')->nullable();
            $table->text('alamat_ibu')->after('nik_ibu')->nullable();
            $table->string('tempat_lahir_ibu')->after('alamat_ibu')->nullable();
            $table->date('tanggal_lahir_ibu')->after('tempat_lahir_ibu')->nullable();
            $table->string('no_hp_ibu', 20)->after('tanggal_lahir_ibu')->nullable();

            // Add Wali
            $table->string('nik_wali', 16)->after('nama_wali')->nullable();
            $table->text('alamat_wali')->after('nik_wali')->nullable();
            $table->string('tempat_lahir_wali')->after('alamat_wali')->nullable();
            $table->date('tanggal_lahir_wali')->after('tempat_lahir_wali')->nullable();
            $table->string('no_hp_wali', 20)->after('tanggal_lahir_wali')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_orangtuas', function (Blueprint $table) {
            $table->dropColumn([
                'nik_ayah', 'alamat_ayah', 'tempat_lahir_ayah', 'tanggal_lahir_ayah', 'no_hp_ayah',
                'nik_ibu', 'alamat_ibu', 'tempat_lahir_ibu', 'tanggal_lahir_ibu', 'no_hp_ibu',
                'nik_wali', 'alamat_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali', 'no_hp_wali'
            ]);

            $table->string('pekerjaan_ayah')->nullable();
            $table->decimal('penghasilan_ayah', 15, 2)->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->decimal('penghasilan_ibu', 15, 2)->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->decimal('penghasilan_wali', 15, 2)->nullable();
            $table->integer('jumlah_tanggungan')->nullable();
        });

        Schema::table('pendaftaran_identitas', function (Blueprint $table) {
            $table->dropColumn('alamat_ktp');
        });
    }
};
