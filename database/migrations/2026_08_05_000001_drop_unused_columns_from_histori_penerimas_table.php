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
            $columnsToDrop = [];
            if (Schema::hasColumn('histori_penerimas', 'jenis_kelamin')) {
                $columnsToDrop[] = 'jenis_kelamin';
            }
            if (Schema::hasColumn('histori_penerimas', 'program_studi')) {
                $columnsToDrop[] = 'program_studi';
            }
            if (Schema::hasColumn('histori_penerimas', 'keterangan')) {
                $columnsToDrop[] = 'keterangan';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histori_penerimas', function (Blueprint $table) {
            $table->string('jenis_kelamin')->nullable()->after('nama_lengkap');
            $table->string('program_studi')->nullable()->after('asal_perguruan_tinggi');
            $table->text('keterangan')->nullable()->after('jenis_beasiswa');
        });
    }
};
