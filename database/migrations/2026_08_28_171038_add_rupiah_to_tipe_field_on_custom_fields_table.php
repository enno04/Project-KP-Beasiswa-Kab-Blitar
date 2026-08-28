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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE custom_fields MODIFY COLUMN tipe_field ENUM('text', 'textarea', 'number', 'date', 'select', 'rupiah') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE custom_fields MODIFY COLUMN tipe_field ENUM('text', 'textarea', 'number', 'date', 'select') NOT NULL");
    }
};
