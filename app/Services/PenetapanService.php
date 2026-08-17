<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Pendaftaran;
use App\Models\PenetapanModel;
use App\Models\PenerimaBeasiswa;
use Illuminate\Support\Facades\DB;

class PenetapanService
{
    /**
     * Tetapkan penerima beasiswa (batch).
     * Sesuai 03_BUSINESS_RULE.md §18 — penetapan bersifat final.
     */
    public function tetapkan(array $pendaftaranIds, string $keputusan, ?string $catatan = null): array
    {
        if (!in_array($keputusan, ['lulus', 'tidak_lulus'])) {
            return ['status' => false, 'message' => 'Keputusan tidak valid.'];
        }

        DB::beginTransaction();
        try {
            $berhasil = 0;
            foreach ($pendaftaranIds as $id) {
                $pendaftaran = Pendaftaran::find($id);
                if (!$pendaftaran || !in_array($pendaftaran->status, ['menunggu_penetapan', 'lolos_verifikasi'])) continue;

                // Buat record penetapan
                PenetapanModel::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'user_id' => auth()->id(),
                    'keputusan' => $keputusan,
                    'catatan' => $catatan,
                    'tanggal_penetapan' => now(),
                ]);

                // Update status pendaftaran
                $pendaftaran->status = $keputusan;
                $pendaftaran->save();

                // Jika lulus, masukkan ke tabel penerima
                if ($keputusan === 'lulus') {
                    PenerimaBeasiswa::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'periode_id' => $pendaftaran->periode_id,
                        'program_id' => $pendaftaran->program_id,
                        'jalur_id' => $pendaftaran->jalur_id,
                        'total_nilai' => $pendaftaran->total_nilai,
                        'ranking' => $pendaftaran->ranking,
                    ]);
                }

                AuditLog::catat(
                    'Penetapan Penerima',
                    "Keputusan: {$keputusan}" . ($catatan ? " | {$catatan}" : ''),
                    Pendaftaran::class,
                    $pendaftaran->id
                );

                $berhasil++;
            }

            DB::commit();
            return ['status' => true, 'message' => "Berhasil menetapkan {$berhasil} pendaftar."];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal menetapkan: ' . $e->getMessage()];
        }
    }
}
