<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * V2 Master Konfigurasi Tables.
     * Sesuai 08_DATABASE.md §4 & 07_MASTER_DATA.md §4
     *
     * Hierarchy: Periode → Program → Jalur → (Tahapan, Persyaratan, Dokumen,
     *            KelompokKriteria → Kriteria → Pilihan, Bobot)
     */
    public function up(): void
    {
        // 1. Periode
        Schema::create('periode', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->year('tahun')->unique();
            $table->string('nama'); // "Periode 2026"
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('tahun');
        });

        // 2. Program Beasiswa
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->string('nama'); // "Satu Desa Satu Sarjana (SDSS)", "Berdaya Berjaya", "Bantuan Biaya Pendidikan (BBP)"
            $table->string('kode'); // "sdss", "berdaya_berjaya", "bbp"
            $table->string('slug'); // URL-friendly
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_buka')->nullable();
            $table->date('tanggal_tutup')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['periode_id', 'kode']);
            $table->index('aktif');
        });

        // 3. Jalur Beasiswa
        Schema::create('jalurs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('nama'); // "Reguler", "Prestasi", "Kurang Mampu"
            $table->string('kode'); // "reguler", "prestasi", "kurang_mampu"
            $table->string('slug');
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['program_id', 'kode']);
        });

        // 4. Tahapan Seleksi
        Schema::create('tahapans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('jalur_id')->constrained('jalurs')->cascadeOnDelete();
            $table->string('nama'); // "Verifikasi OPD", "Penilaian Otomatis", "Penetapan Desa", dll
            $table->string('kode'); // "verifikasi_opd", "penilaian", "penetapan_desa", dll
            $table->integer('urutan');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['jalur_id', 'urutan']);
        });

        // 5. Persyaratan
        Schema::create('persyaratans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('jalur_id')->constrained('jalurs')->cascadeOnDelete();
            $table->text('deskripsi'); // "Warga Kabupaten Blitar", "Belum menikah", dll
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Dokumen
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('jalur_id')->constrained('jalurs')->cascadeOnDelete();
            $table->string('nama'); // "KTP", "Kartu Keluarga", dll
            $table->text('deskripsi')->nullable();
            $table->boolean('wajib')->default(true);
            $table->foreignId('opd_id')->nullable()->constrained('opd')->nullOnDelete(); // OPD verifikator
            $table->string('format_file')->default('pdf,jpg,jpeg,png');
            $table->integer('max_size_kb')->default(2048);
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['jalur_id', 'urutan']);
        });

        // 7. Kelompok Kriteria
        Schema::create('kelompok_kriterias', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('jalur_id')->constrained('jalurs')->cascadeOnDelete();
            $table->string('nama'); // "Status Ekonomi", "Akademik"
            $table->string('kode'); // "ekonomi", "akademik"
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['jalur_id', 'urutan']);
        });

        // 8. Kriteria
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('kelompok_kriteria_id')->constrained('kelompok_kriterias')->cascadeOnDelete();
            $table->string('nama'); // "DTSEN", "Pekerjaan Orang Tua", "IPK", dll
            $table->string('kode');
            $table->enum('tipe_input', ['pilihan', 'angka'])->default('pilihan');
            $table->decimal('nilai_min', 8, 2)->nullable(); // Untuk tipe "angka"
            $table->decimal('nilai_max', 8, 2)->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kelompok_kriteria_id', 'urutan']);
        });

        // 9. Pilihan Kriteria
        Schema::create('pilihan_kriterias', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('kriteria_id')->constrained('kriterias')->cascadeOnDelete();
            $table->string('label'); // "Desil 1", "Desil 2", dll
            $table->decimal('skor', 8, 2)->default(0); // Skor untuk pilihan ini
            $table->integer('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kriteria_id', 'urutan']);
        });

        // 10. Bobot Penilaian
        Schema::create('bobot_penilaians', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('jalur_id')->constrained('jalurs')->cascadeOnDelete();
            $table->foreignId('kelompok_kriteria_id')->constrained('kelompok_kriterias')->cascadeOnDelete();
            $table->decimal('bobot_persen', 5, 2); // 70.00 = 70%
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['jalur_id', 'kelompok_kriteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bobot_penilaians');
        Schema::dropIfExists('pilihan_kriterias');
        Schema::dropIfExists('kriterias');
        Schema::dropIfExists('kelompok_kriterias');
        Schema::dropIfExists('dokumens');
        Schema::dropIfExists('persyaratans');
        Schema::dropIfExists('tahapans');
        Schema::dropIfExists('jalurs');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('periode');
    }
};
