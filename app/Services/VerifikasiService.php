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
            if ($pendaftaran && $pendaftaran->status !== 'tidak_lolos_verifikasi') {
                $pendaftaran->status = 'tidak_lolos_verifikasi';
                $pendaftaran->save();
                
                AuditLog::catat(
                    'Pendaftaran Gugur',
                    "Pendaftar dinyatakan Tidak Lolos Verifikasi OPD karena dokumen {$upload->dokumen->nama} dinyatakan Tidak Valid oleh OPD.",
                    Pendaftaran::class,
                    $pendaftaran->id
                );

                // Jika pendaftar ini sudah pernah dinilai & diranking oleh desa (SDSS),
                // maka kita harus me-reset rankingnya dan me-re-generate ranking untuk desa tersebut.
                $pendaftaran->loadMissing(['program', 'identitas']);
                if ($pendaftaran->total_nilai > 0 && $pendaftaran->program && $pendaftaran->program->isSdss()) {
                    $pendaftaran->ranking = null;
                    $pendaftaran->save();
                    
                    app(\App\Services\RankingService::class)->generateRankingPerDesa(
                        $pendaftaran->jalur_id,
                        $pendaftaran->periode_id,
                        $pendaftaran->identitas?->desa_id
                    );
                }
            }
            return ['status' => true, 'message' => 'Verifikasi disimpan. Pendaftar dinyatakan Tidak Lolos Verifikasi karena dokumen tidak valid.'];
        } elseif ($hasil === 'valid') {
            $pendaftaran = $upload->pendaftaran;
            if ($pendaftaran && $pendaftaran->status === 'tidak_lolos_verifikasi') {
                // Cek apakah masih ada dokumen lain yang tidak_valid
                $hasTidakValid = UploadDokumen::where('pendaftaran_id', $pendaftaran->id)
                    ->where('status', 'tidak_valid')
                    ->exists();
                
                if (!$hasTidakValid) {
                    $pendaftaran->status = 'sedang_diverifikasi';
                    $pendaftaran->save();
                }
            }
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

            // Jika sebelumnya pendaftar ini digugurkan lalu dikembalikan ke lolos, 
            // dan dia sudah punya nilai, kita harus re-generate ranking desa tersebut.
            $pendaftaran->loadMissing(['program', 'identitas']);
            if ($pendaftaran->total_nilai > 0 && $pendaftaran->program && $pendaftaran->program->isSdss()) {
                app(\App\Services\RankingService::class)->generateRankingPerDesa(
                    $pendaftaran->jalur_id,
                    $pendaftaran->periode_id,
                    $pendaftaran->identitas?->desa_id
                );
            }
        }

        return true;
    }

    /**
     * Ambil dokumen yang menjadi kewenangan OPD tertentu.
     */
    public function getDokumenByOpd(int $opdId, ?string $statusFilter = null, ?int $programId = null, ?int $kecamatanId = null, ?int $desaId = null, ?string $search = null)
    {
        $query = UploadDokumen::with(['dokumen', 'pendaftaran.identitas'])
            ->whereHas('dokumen', fn($q) => $q->where('opd_id', $opdId));

        if ($statusFilter) {
            $query->where('status', $statusFilter);
            
            // Jika memfilter 'belum_diverifikasi' (Antrean Aktif), pastikan status pendaftarannya juga masih aktif untuk verifikasi OPD
            if ($statusFilter === 'belum_diverifikasi') {
                $query->whereHas('pendaftaran', fn($q) => $q->opdActive());
            }
        }

        if ($programId) {
            $query->whereHas('pendaftaran', fn($q) => $q->where('program_id', $programId));
        }

        if ($kecamatanId) {
            $query->whereHas('pendaftaran.identitas', fn($q) => $q->where('kecamatan_id', $kecamatanId));
        }

        if ($desaId) {
            $query->whereHas('pendaftaran.identitas', fn($q) => $q->where('desa_id', $desaId));
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('pendaftaran', function($q2) use ($search) {
                    $q2->where('nomor_pendaftaran', 'like', "%{$search}%")
                       ->orWhereHas('identitas', function($q3) use ($search) {
                           $q3->where('nama_lengkap', 'like', "%{$search}%")
                              ->orWhere('nik', 'like', "%{$search}%");
                       });
                })
                ->orWhereHas('dokumen', function($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%");
                });
            });
        }

        return $query->latest()->paginate(20);
    }
}
