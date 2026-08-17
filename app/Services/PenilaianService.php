<?php

namespace App\Services;

use App\Models\JawabanKriteria;
use App\Models\KelompokKriteria;
use App\Models\Kriteria;
use App\Models\Pendaftaran;
use App\Models\Penilaian;
use Illuminate\Support\Facades\DB;

class PenilaianService
{
    /**
     * Hitung penilaian menggunakan Model Penilaian Berbobot.
     *
     * Rumus per kriteria:
     *   Nilai Variabel = (Skor Yang Diperoleh / Skor Maksimal Variabel) × Bobot Variabel
     *
     * Rumus total:
     *   Nilai Total = Σ (Nilai Variabel)
     *
     * Contoh:
     *   Desil: skor 80, max 100, bobot 15% → (80/100) × 15 = 12.00
     *   Pekerjaan: skor 30, max 50, bobot 5% → (30/50) × 5 = 3.00
     *   Total = 12.00 + 3.00 + ... (seluruh kriteria)
     *
     * Skala akhir: 0–100
     */
    public function hitungNilai(Pendaftaran $pendaftaran): array
    {
        $jalur = $pendaftaran->jalur;
        $kelompoks = $jalur->kelompokKriterias()->with(['kriterias'])->get();

        if ($kelompoks->isEmpty()) {
            return ['status' => false, 'message' => 'Belum ada kelompok kriteria untuk jalur ini.'];
        }

        // Validasi total bobot = 100%
        $totalBobot = 0;
        foreach ($kelompoks as $kelompok) {
            $bobotKelompok = \App\Models\BobotPenilaian::where('jalur_id', $jalur->id)
                ->where('kelompok_kriteria_id', $kelompok->id)
                ->value('bobot_persen');

            foreach ($kelompok->kriterias as $kriteria) {
                if ((float) $kriteria->bobot > 0) {
                    $totalBobot += (float) $kriteria->bobot;
                } else if ($bobotKelompok && $kelompok->kriterias->count() > 0) {
                    $totalBobot += ((float) $bobotKelompok / $kelompok->kriterias->count());
                }
            }
        }

        if (abs($totalBobot - 100) > 0.01) {
            return [
                'status' => false,
                'message' => "Total bobot kriteria saat ini {$totalBobot}%. Harus tepat 100% sebelum penilaian dapat dilakukan.",
            ];
        }

        DB::beginTransaction();
        try {
            $totalNilai = 0;

            foreach ($kelompoks as $kelompok) {
                $bobotKelompok = \App\Models\BobotPenilaian::where('jalur_id', $jalur->id)
                    ->where('kelompok_kriteria_id', $kelompok->id)
                    ->value('bobot_persen');

                foreach ($kelompok->kriterias as $kriteria) {
                    $jawaban = JawabanKriteria::where('pendaftaran_id', $pendaftaran->id)
                        ->where('kriteria_id', $kriteria->id)
                        ->first();

                    if (!$jawaban) continue;

                    // Ambil skor mentah dari jawaban
                    $skorDiperoleh = $jawaban->getSkor();

                    // Skor Maksimal dari konfigurasi kriteria
                    $skorMaksimal = (float) $kriteria->nilai_max;
                    $bobotKriteria = (float) $kriteria->bobot;
                    if ($bobotKriteria <= 0 && $bobotKelompok && $kelompok->kriterias->count() > 0) {
                        $bobotKriteria = (float) $bobotKelompok / $kelompok->kriterias->count();
                    }

                    // Rumus: (Skor Diperoleh / Skor Maksimal) × Bobot
                    $nilaiTerbobot = 0;
                    if ($skorMaksimal > 0) {
                        $nilaiTerbobot = ($skorDiperoleh / $skorMaksimal) * $bobotKriteria;
                    }

                    // Simpan penilaian per kriteria
                    Penilaian::updateOrCreate(
                        ['pendaftaran_id' => $pendaftaran->id, 'kriteria_id' => $kriteria->id],
                        [
                            'pilihan_kriteria_id' => $jawaban->pilihan_kriteria_id,
                            'skor' => round($skorDiperoleh, 2),
                            'nilai_terbobot' => round($nilaiTerbobot, 4),
                        ]
                    );

                    $totalNilai += $nilaiTerbobot;
                }
            }

            // Simpan total nilai ke pendaftaran (skala 0-100)
            $pendaftaran->setAttribute('total_nilai', round($totalNilai, 4));
            $pendaftaran->save();

            DB::commit();
            return [
                'status' => true,
                'message' => 'Penilaian berhasil dihitung.',
                'total_nilai' => round($totalNilai, 4),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Gagal menghitung penilaian: ' . $e->getMessage()];
        }
    }

    /**
     * Hitung penilaian untuk semua pendaftar yang lolos verifikasi pada jalur + periode tertentu.
     */
    public function hitungSemuaPendaftar(int $jalurId, int $periodeId, ?int $desaId = null): array
    {
        $query = Pendaftaran::where('jalur_id', $jalurId)
            ->where('periode_id', $periodeId)
            ->where('status', 'lolos_verifikasi');

        if ($desaId) {
            $query->whereHas('identitas', fn($q) => $q->where('desa_id', $desaId));
        }

        $pendaftarans = $query->get();

        if ($pendaftarans->isEmpty()) {
            return ['status' => false, 'message' => 'Tidak ada pendaftar yang siap dinilai.'];
        }

        $berhasil = 0;
        $gagal = [];
        foreach ($pendaftarans as $pendaftaran) {
            $result = $this->hitungNilai($pendaftaran);
            if ($result['status']) {
                $pendaftaran->update(['status' => 'proses_penilaian']);
                $berhasil++;
            } else {
                $gagal[] = $result['message'];
            }
        }

        if ($berhasil === 0 && !empty($gagal)) {
            return ['status' => false, 'message' => $gagal[0]];
        }

        return ['status' => true, 'message' => "Berhasil menilai {$berhasil} dari {$pendaftarans->count()} pendaftar."];
    }
}
