<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * V2 Master Referensi Tables.
     * Sesuai 08_DATABASE.md §3 & 07_MASTER_DATA.md §3
     *
     * Tables: roles, kecamatan, desa, opd, perguruan_tinggi
     */
    public function up(): void
    {
        // 1. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nama'); // Super Admin, Admin Kabupaten, dll
            $table->string('kode')->unique(); // super_admin, admin_kabupaten, dll
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Kecamatan
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('kode_kecamatan')->unique();
            $table->string('nama_kecamatan');
            $table->timestamps();
            $table->softDeletes();

            $table->index('nama_kecamatan');
        });

        // 3. Desa / Kelurahan
        Schema::create('desa', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->string('kode_desa')->unique();
            $table->string('nama_desa');
            $table->timestamps();
            $table->softDeletes();

            $table->index('nama_desa');
        });

        // 4. OPD (Organisasi Perangkat Daerah)
        Schema::create('opd', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nama_opd');
            $table->string('singkatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Perguruan Tinggi
        Schema::create('perguruan_tinggi', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nama');
            $table->enum('jenis', ['negeri', 'swasta'])->default('negeri');
            $table->string('akreditasi')->nullable(); // A, B, C, Unggul, Baik Sekali, dll
            $table->enum('lokasi', ['dalam_daerah', 'luar_daerah'])->default('luar_daerah');
            $table->timestamps();
            $table->softDeletes();

            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perguruan_tinggi');
        Schema::dropIfExists('opd');
        Schema::dropIfExists('desa');
        Schema::dropIfExists('kecamatan');
        Schema::dropIfExists('roles');
    }
};
