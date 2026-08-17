<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * V2 Transaksi Tables.
     * Sesuai 08_DATABASE.md §5 & 02_WORKFLOW.md
     *
     * Tables: pendaftarans, pendaftaran_identitas, pendaftaran_orangtuas,
     *         jawaban_kriterias, upload_dokumens, verifikasi_dokumens,
     *         penilaians, wawancaras, rekomendasi_desas, penetapans, pembayarans
     */
    public function up(): void
    {
        // 1. Pendaftaran (Tabel Utama)
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nomor_pendaftaran')->unique();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('jalur_id')->constrained('jalurs')->cascadeOnDelete();
            $table->year('tahun');

            // Hasil Penilaian
            $table->decimal('total_nilai', 10, 4)->nullable();
            $table->integer('ranking')->nullable();

            // Status (sesuai 02_WORKFLOW.md §13)
            $table->enum('status', [
                'menunggu_verifikasi',
                'sedang_diverifikasi',
                'lolos_verifikasi',
                'tidak_lolos_verifikasi',
                'proses_seleksi',
                'menunggu_wawancara',
                'proses_wawancara',
                'menunggu_penilaian',
                'proses_penilaian',
                'menunggu_penetapan',
                'lulus',
                'tidak_lulus',
                'sk_terbit',
                'pembayaran_diproses',
                'selesai',
            ])->default('menunggu_verifikasi');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Index (sesuai 08_DATABASE.md §15)
            $table->index('periode_id');
            $table->index('program_id');
            $table->index('jalur_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('tahun');
        });

        // 2. Pendaftaran Identitas (Step 1)
        Schema::create('pendaftaran_identitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->string('nik', 16);
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->foreignId('desa_id')->constrained('desa')->cascadeOnDelete();
            $table->foreignId('kecamatan_id')->constrained('kecamatan')->cascadeOnDelete();
            $table->string('no_hp', 20);
            $table->string('email')->nullable();
            $table->foreignId('perguruan_tinggi_id')->nullable()->constrained('perguruan_tinggi')->nullOnDelete();
            $table->string('program_studi')->nullable();
            $table->integer('semester')->nullable();
            $table->timestamps();

            $table->index('nik');
            $table->index('desa_id');
            $table->index('kecamatan_id');
        });

        // 3. Pendaftaran Orang Tua (Step 2)
        Schema::create('pendaftaran_orangtuas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->string('nama_ayah');
            $table->string('pekerjaan_ayah')->nullable();
            $table->decimal('penghasilan_ayah', 15, 2)->nullable();
            $table->string('nama_ibu');
            $table->string('pekerjaan_ibu')->nullable();
            $table->decimal('penghasilan_ibu', 15, 2)->nullable();
            $table->integer('jumlah_tanggungan')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->decimal('penghasilan_wali', 15, 2)->nullable();
            $table->timestamps();
        });

        // 4. Jawaban Kriteria (Step 3 & 4 — Dinamis)
        Schema::create('jawaban_kriterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriterias')->cascadeOnDelete();
            $table->foreignId('pilihan_kriteria_id')->nullable()->constrained('pilihan_kriterias')->nullOnDelete();
            $table->decimal('nilai_input', 8, 2)->nullable(); // Untuk tipe "angka"
            $table->timestamps();

            $table->unique(['pendaftaran_id', 'kriteria_id']);
        });

        // 5. Upload Dokumen (Step 5)
        Schema::create('upload_dokumens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->foreignId('dokumen_id')->constrained('dokumens')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('file_path');
            $table->integer('versi')->default(1);
            $table->enum('status', [
                'belum_diverifikasi',
                'valid',
                'tidak_valid',
                'tidak_wajib',
            ])->default('belum_diverifikasi');
            $table->timestamps();

            $table->index(['pendaftaran_id', 'dokumen_id']);
        });

        // 6. Verifikasi Dokumen
        Schema::create('verifikasi_dokumens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('upload_dokumen_id')->constrained('upload_dokumens')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('hasil', ['valid', 'tidak_valid', 'tidak_wajib']);
            $table->text('catatan')->nullable();
            $table->datetime('tanggal_verifikasi');
            $table->timestamps();
        });

        // 7. Penilaian (Otomatis oleh Sistem)
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriterias')->cascadeOnDelete();
            $table->foreignId('pilihan_kriteria_id')->nullable()->constrained('pilihan_kriterias')->nullOnDelete();
            $table->decimal('skor', 8, 2)->default(0); // Skor mentah dari pilihan
            $table->decimal('nilai_terbobot', 10, 4)->default(0); // skor × bobot kelompok
            $table->timestamps();

            $table->unique(['pendaftaran_id', 'kriteria_id']);
        });

        // 8. Wawancara (Khusus Program yang memiliki tahapan wawancara)
        Schema::create('wawancaras', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Pewawancara
            $table->decimal('nilai', 8, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->datetime('tanggal_wawancara')->nullable();
            $table->timestamps();
        });

        // 9. Rekomendasi Desa (Khusus SDSS)
        Schema::create('rekomendasi_desas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->foreignId('desa_id')->constrained('desa')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Admin Desa
            $table->string('surat_rekomendasi_path')->nullable();
            $table->text('catatan')->nullable();
            $table->datetime('tanggal_rekomendasi')->nullable();
            $table->timestamps();
        });

        // 10. Penetapan
        Schema::create('penetapans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Yang menetapkan
            $table->enum('keputusan', ['lulus', 'tidak_lulus']);
            $table->text('catatan')->nullable();
            $table->datetime('tanggal_penetapan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penetapans');
        Schema::dropIfExists('rekomendasi_desas');
        Schema::dropIfExists('wawancaras');
        Schema::dropIfExists('penilaians');
        Schema::dropIfExists('verifikasi_dokumens');
        Schema::dropIfExists('upload_dokumens');
        Schema::dropIfExists('jawaban_kriterias');
        Schema::dropIfExists('pendaftaran_orangtuas');
        Schema::dropIfExists('pendaftaran_identitas');
        Schema::dropIfExists('pendaftarans');
    }
};
