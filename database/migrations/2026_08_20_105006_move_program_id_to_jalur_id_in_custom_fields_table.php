<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus data lama karena struktur berubah drastis
        Schema::disableForeignKeyConstraints();
        DB::table('custom_fields')->truncate();
        Schema::enableForeignKeyConstraints();

        Schema::table('custom_fields', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropColumn('program_id');
            $table->foreignId('jalur_id')->after('id')->constrained('jalurs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('custom_fields')->truncate();
        Schema::enableForeignKeyConstraints();
        
        Schema::table('custom_fields', function (Blueprint $table) {
            $table->dropForeign(['jalur_id']);
            $table->dropColumn('jalur_id');
            $table->foreignId('program_id')->after('id')->constrained('programs')->cascadeOnDelete();
        });
    }
};
