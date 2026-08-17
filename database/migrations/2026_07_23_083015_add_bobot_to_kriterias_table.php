<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom bobot (%) per kriteria untuk Model Penilaian Berbobot.
     * Contoh: Desil = 15, Pekerjaan = 5, Penghasilan = 10, dst.
     * Total bobot seluruh kriteria dalam satu jalur harus = 100.
     */
    public function up(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->decimal('bobot', 5, 2)->default(0)->after('nilai_max')
                  ->comment('Bobot persen kriteria (misal 15 = 15%). Total per jalur harus 100%.');
        });
    }

    public function down(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->dropColumn('bobot');
        });
    }
};
