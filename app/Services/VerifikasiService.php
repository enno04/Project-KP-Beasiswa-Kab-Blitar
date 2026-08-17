<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Dokumen;
use App\Models\UploadDokumen;
use App\Models\VerifikasiDokumen;
use App\Models\Pendaftaran;

class VerifikasiService
{
    /**
     * Verifikasi satu dokumen oleh OPD.
     * Sesuai 02_WORKFLOW.md §8 — verifikasi paralel per OPD.
     */
    public function verifikasiDokumen(int $uploadDokumenId, string $hasil, ?string $catatan = null): array
    {
        $upload = UploadDokumen::with('dokumen')->find($uploadDokumenId);
        if (!$upload) {
            return ['status' => false, 'message' => 'Dokumen tidak ditemukan.'];
        }

        // Simpan record verifikasi
        VerifikasiDokumen::create([
            'upload_dokumen_id' => $upload->id,
            'user_id' => auth()->id(),
            'hasil' => $hasil,
            'catatan' => $catatan,
            'tanggal_verifikasi' => now(),
        ]);

        // Update status dokumen
        $upload->status = $hasil;
        $upload->save();

        AuditLog::catat(
            'Verifikasi Dokumen',
            "Dokumen: {$upload->dokumen->nama} | Hasil: {$hasil}" . ($catatan ? " | {$catatan}" : ''),
            UploadDokumen::class,
            $upload->id
        );

        // Jika dokumen dinyatakan tidak valid, pendaftar otomatis gugur
        if ($hasil === 'tidak_valid') {
            $pendaftaran = $upload->pendaftaran;
            if ($pendaftaran && $pendaftaran->status !== 'tidak_lulus') {
                $pendaftaran->status = 'tidak_lulus';
                $pendaftaran->save();
                
                AuditLog::catat(
                    'Pendaftaran Gugur',
                    "Pendaftar dinyatakan Tidak Lulus karena dokumen {$upload->dokumen->nama} dinyatakan Tidak Valid oleh OPD.",
                    Pendaftaran::class,
                    $pendaftaran->id
                );
            }
            return ['status' => true, 'message' => 'Verifikasi disimpan. Pendaftar dinyatakan Tidak Lulus karena dokumen tidak valid.'];
        }

        return ['status' => true, 'message' => 'Verifikasi dokumen berhasil.'];
    }

    /**
     * Cek apakah semua dokumen wajib sudah diverifikasi valid.
     * Jika ya, update status pendaftaran ke 'lolos_verifikasi'.
     */
    public function cekStatusVerifikasi(Pendaftaran $pendaftaran): bool
    {
        $jalur = $pendaftaran->jalur;
        $dokumenWajib = Dokumen::where('jalur_id', $jalur->id)->where('wajib', true)->pluck('id');

        foreach ($dokumenWajib as $dokId) {
            $upload = UploadDokumen::where('pendaftaran_id', $pendaftaran->id)
                ->where('dokumen_id', $dokId)
                ->first();

            if (!$upload || $upload->status !== 'valid') {
                return false;
            }
        }

        // Semua dokumen wajib valid → update status
        if (in_array($pendaftaran->status, ['menunggu_verifikasi', 'sedang_diverifikasi'])) {
            $pendaftaran->status = 'lolos_verifikasi';
            $pendaftaran->save();

            AuditLog::catat(
                'Lolos Verifikasi',
                'Semua dokumen wajib telah diverifikasi valid.',
                Pendaftaran::class,
                $pendaftaran->id
            );
        }

        return true;
    }

    /**
     * Ambil dokumen yang menjadi kewenangan OPD tertentu.
     */
    public function getDokumenByOpd(int $opdId, ?string $statusFilter = null)
    {
        $query = UploadDokumen::with(['dokumen', 'pendaftaran.identitas'])
            ->whereHas('dokumen', fn($q) => $q->where('opd_id', $opdId));

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        return $query->latest()->paginate(20);
    }
}
