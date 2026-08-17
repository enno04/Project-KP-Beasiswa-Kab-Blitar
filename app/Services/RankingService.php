<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Pendaftaran;
use App\Models\PenerimaBeasiswa;
use Illuminate\Support\Facades\DB;

class RankingService
{
    /**
     * Generate ranking untuk jalur + periode tertentu.
     * Tie-breaker: pendaftaran paling awal (created_at ASC).
     * Sesuai 09_PENILAIAN.md §8
     */
    public function generateRanking(int $jalurId, int $periodeId): array
    {
        $pendaftarans = Pendaftaran::where('jalur_id', $jalurId)
            ->where('periode_id', $periodeId)
            ->whereIn('status', ['proses_penilaian', 'menunggu_penetapan'])
            ->whereNotNull('total_nilai')
            ->orderBy('total_nilai', 'desc')
            ->orderBy('created_at', 'asc') // Tie-breaker
            ->get();

        if ($pendaftarans->isEmpty()) {
            return ['status' => false, 'message' => 'Tidak ada pendaftar yang siap diranking.'];
        }

        DB::beginTransaction();
        try {
            $rank = 1;
            foreach ($pendaftarans as $p) {
                $p->ranking = $rank;
                $p->status = 'menunggu_penetapan';
                $p->save();

                AuditLog::catat(
                    'Generate Ranking',
                    "Ranking #{$rank} | Nilai: {$p->total_nilai}",
                    Pendaftaran::class,
                    $p->id
                );

                $rank++;
            }

            DB::commit();
            return ['status' => true, 'message' => "Berhasil menyusun peringkat untuk {$pendaftarans->count()} pendaftar."];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal menyusun ranking: ' . $e->getMessage()];
        }
    }

    /**
     * Generate ranking per desa (khusus SDSS).
     * Sesuai 02_WORKFLOW.md §7A — setiap desa mengirim 1 kandidat terbaik.
     */
    public function generateRankingPerDesa(int $jalurId, int $periodeId, ?int $desaId = null): array
    {
        $query = Pendaftaran::with('identitas')
            ->where('jalur_id', $jalurId)
            ->where('periode_id', $periodeId)
            ->whereIn('status', ['lolos_verifikasi', 'proses_penilaian', 'diteruskan_ke_kecamatan', 'menunggu_penetapan'])
            ->whereNotNull('total_nilai');

        if ($desaId) {
            $query->whereHas('identitas', fn($q) => $q->where('desa_id', $desaId));
        }

        $pendaftarans = $query->orderBy('total_nilai', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($pendaftarans->isEmpty()) {
            return ['status' => false, 'message' => 'Tidak ada pendaftar SDSS yang siap diranking.'];
        }

        // Group by desa_id
        $grouped = $pendaftarans->groupBy(fn($p) => $p->identitas?->desa_id);

        DB::beginTransaction();
        try {
            foreach ($grouped as $desaId => $desaPendaftarans) {
                $rank = 1;
                foreach ($desaPendaftarans as $p) {
                    /** @var Pendaftaran $p */
                    $p->ranking = $rank;
                    if ($p->status === 'proses_penilaian') {
                        $p->status = 'lolos_verifikasi';
                    }
                    $p->save();
                    $rank++;
                }
            }

            DB::commit();
            return ['status' => true, 'message' => "Berhasil ranking per desa untuk {$pendaftarans->count()} pendaftar."];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal ranking per desa: ' . $e->getMessage()];
        }
    }
}
