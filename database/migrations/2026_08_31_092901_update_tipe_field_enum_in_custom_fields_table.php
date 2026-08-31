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
        Schema::table('custom_fields', function (Blueprint $table) {
            \DB::statement("ALTER TABLE custom_fields MODIFY COLUMN tipe_field ENUM('text', 'textarea', 'number', 'rupiah', 'date', 'select') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            \DB::statement("ALTER TABLE custom_fields MODIFY COLUMN tipe_field ENUM('text', 'textarea', 'number', 'date', 'select') NOT NULL");
        });
    }
};
