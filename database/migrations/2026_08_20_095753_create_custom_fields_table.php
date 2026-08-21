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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('nama_field');
            $table->enum('tipe_field', ['text', 'textarea', 'number', 'date', 'select']);
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->enum('penempatan', ['identitas_diri', 'orang_tua', 'akademik', 'tambahan']);
            $table->integer('urutan')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
