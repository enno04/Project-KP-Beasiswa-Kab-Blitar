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
            $table->dropColumn('alamat');
            $table->string('asal_perguruan_tinggi')->after('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_identitas', function (Blueprint $table) {
            $table->text('alamat')->nullable();
            $table->dropColumn('asal_perguruan_tinggi');
        });
    }
};
