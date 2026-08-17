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
            $table->dropForeign(['perguruan_tinggi_id']);
            $table->dropColumn('perguruan_tinggi_id');
        });

        Schema::dropIfExists('perguruan_tinggi');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('perguruan_tinggi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pt');
            $table->enum('jenis_pt', ['negeri', 'swasta', 'kedinasan'])->default('negeri');
            $table->timestamps();
        });

        Schema::table('pendaftaran_identitas', function (Blueprint $table) {
            $table->foreignId('perguruan_tinggi_id')->nullable()->after('email')->constrained('perguruan_tinggi')->nullOnDelete();
        });
    }
};
