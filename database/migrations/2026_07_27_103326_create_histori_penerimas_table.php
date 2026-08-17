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
        Schema::create('histori_penerimas', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->string('nama_lengkap');
            $table->string('asal_perguruan_tinggi')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('jenis_beasiswa')->nullable(); // e.g. BBP, Berdaya Berjaya
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histori_penerimas');
    }
};
