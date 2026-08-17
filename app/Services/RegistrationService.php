<?php

namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\PendaftaranIdentitas;
use App\Models\PendaftaranOrangtua;
use App\Models\JawabanKriteria;
use App\Models\UploadDokumen;
use App\Models\Program;
use App\Models\Jalur;
use App\Models\Periode;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Exception;

class RegistrationService
{
    /**
     * Validasi aturan penguncian NIK pendaftaran beasiswa.
     */
    public function validateNikAvailability(string $nik, Program $program, Periode $periode): void
    {
        // 1. Cek Histori Penerima Legacy (Data Tahun-tahun Sebelumnya)
        $isLegacyPenerima = \App\Models\HistoriPenerima::where('nik', $nik)->exists();
        if ($isLegacyPenerima) {
            throw new Exception("NIK {$nik} telah terdaftar sebagai penerima beasiswa pada histori periode sebelumnya. NIK Anda terkunci dan tidak dapat mendaftar kembali.");
        }

        // 2. Cek Penerima Beasiswa Sistem Berjalan (Sudah Pernah Lulus/Ditetapkan)
        $isPenerimaBerjalan = Pendaftaran::whereHas('identitas', fn($q) => $q->where('nik', $nik))
            ->whereIn('status', ['lulus', 'sk_terbit'])
            ->exists();
        if ($isPenerimaBerjalan || \App\Models\PenerimaBeasiswa::pernahMenerima($nik)) {
            throw new Exception("NIK {$nik} telah ditetapkan sebagai penerima beasiswa. NIK Anda terkunci dan tidak dapat mendaftar kembali.");
        }

        // 3. Cek Pendaftaran di Program yang Sama pada Periode Aktif
        $sudahDaftarProgramSama = Pendaftaran::where('periode_id', $periode->id)
            ->where('program_id', $program->id)
            ->whereHas('identitas', fn($q) => $q->where('nik', $nik))
            ->exists();
        if ($sudahDaftarProgramSama) {
            throw new Exception("NIK {$nik} sudah mendaftar pada program beasiswa ini di periode aktif saat ini.");
        }

        // 4. Cek Pendaftaran di Program Berbeda pada Periode Aktif yang Masih Berjalan (Belum Ditolak/Gugur)
        $pendaftaranAktifLain = Pendaftaran::where('periode_id', $periode->id)
            ->where('program_id', '!=', $program->id)
            ->whereHas('identitas', fn($q) => $q->where('nik', $nik))
            ->whereNotIn('status', ['tidak_lolos_verifikasi', 'tidak_lolos_desa', 'ditolak_kecamatan', 'gugur_wawancara', 'tidak_lulus'])
            ->exists();
        if ($pendaftaranAktifLain) {
            throw new Exception("NIK {$nik} masih memiliki pendaftaran aktif yang sedang diproses pada program beasiswa lain di periode ini.");
        }
    }

    public function register(array $data, array $files, Program $program, Jalur $jalur, Periode $periode): Pendaftaran
    {
        // Validasi penguncian NIK terlebih dahulu
        $this->validateNikAvailability($data['nik'], $program, $periode);

        DB::beginTransaction();
        try {
            $nomorPendaftaran = Pendaftaran::generateNomorPendaftaran($program->kode, $jalur->kode, $periode->tahun);

            // 1. Pendaftaran utama
            $pendaftaran = Pendaftaran::create([
                'nomor_pendaftaran' => $nomorPendaftaran,
                'periode_id' => $periode->id,
                'program_id' => $program->id,
                'jalur_id' => $jalur->id,
                'tahun' => $periode->tahun,
                'status' => 'menunggu_verifikasi',
            ]);

            // 2. Identitas
            PendaftaranIdentitas::create([
                'pendaftaran_id' => $pendaftaran->id,
                'nik' => $data['nik'],
                'nama_lengkap' => $data['nama_lengkap'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'alamat_ktp' => $data['alamat_ktp'],
                'google_maps_url' => $data['google_maps_url'],
                'desa_id' => $data['desa_id'],
                'kecamatan_id' => $data['kecamatan_id'],
                'no_hp' => $data['no_hp'],
                'email' => $data['email'] ?? null,
                'asal_perguruan_tinggi' => $data['asal_perguruan_tinggi'],
                'program_studi' => $data['program_studi'],
                'semester' => $data['semester'],
            ]);

            // 3. Orangtua
            PendaftaranOrangtua::create([
                'pendaftaran_id' => $pendaftaran->id,
                'nama_ayah' => $data['nama_ayah'],
                'nik_ayah' => $data['nik_ayah'],
                'alamat_ayah' => $data['alamat_ayah'],
                'tempat_lahir_ayah' => $data['tempat_lahir_ayah'],
                'tanggal_lahir_ayah' => $data['tanggal_lahir_ayah'],
                'no_hp_ayah' => $data['no_hp_ayah'],

                'nama_ibu' => $data['nama_ibu'],
                'nik_ibu' => $data['nik_ibu'],
                'alamat_ibu' => $data['alamat_ibu'],
                'tempat_lahir_ibu' => $data['tempat_lahir_ibu'],
                'tanggal_lahir_ibu' => $data['tanggal_lahir_ibu'],
                'no_hp_ibu' => $data['no_hp_ibu'],

                'nama_wali' => $data['nama_wali'] ?? null,
                'nik_wali' => $data['nik_wali'] ?? null,
                'alamat_wali' => $data['alamat_wali'] ?? null,
                'tempat_lahir_wali' => $data['tempat_lahir_wali'] ?? null,
                'tanggal_lahir_wali' => $data['tanggal_lahir_wali'] ?? null,
                'no_hp_wali' => $data['no_hp_wali'] ?? null,
            ]);

            // 4. Jawaban Kriteria
            $kriterias = $jalur->getAllKriterias();
            foreach ($kriterias as $k) {
                $jawaban = [
                    'pendaftaran_id' => $pendaftaran->id,
                    'kriteria_id' => $k->id,
                ];
                if ($k->isPilihan()) {
                    $jawaban['pilihan_kriteria_id'] = $data["kriteria_{$k->id}"] ?? null;
                } else {
                    $jawaban['nilai_input'] = $data["kriteria_{$k->id}"] ?? null;
                }
                JawabanKriteria::create($jawaban);
            }

            // 5. Upload Dokumen
            $dokumens = $jalur->dokumens;
            foreach ($dokumens as $dok) {
                $fileKey = "dokumen_{$dok->id}";
                if (isset($files[$fileKey])) {
                    $file = $files[$fileKey];
                    $fileName = $nomorPendaftaran . '_' . $dok->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('dokumen/' . $pendaftaran->id, $fileName, 'public');

                    UploadDokumen::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'dokumen_id' => $dok->id,
                        'nama_file' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'status' => 'belum_diverifikasi',
                    ]);
                }
            }

            AuditLog::catat('Pendaftaran Baru', "No: {$nomorPendaftaran} | {$data['nama_lengkap']}", Pendaftaran::class, $pendaftaran->id);
            DB::commit();

            return $pendaftaran;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
