<?php
use Illuminate\Support\Facades\DB;
DB::statement("ALTER TABLE verifikasi_dokumens MODIFY COLUMN hasil ENUM('valid', 'tidak_valid', 'tidak_wajib', 'gugur_desa') NOT NULL");
DB::statement("ALTER TABLE upload_dokumens MODIFY COLUMN status ENUM('belum_diverifikasi', 'valid', 'tidak_valid', 'tidak_wajib', 'gugur_desa') DEFAULT 'belum_diverifikasi'");
echo "Success\n";
