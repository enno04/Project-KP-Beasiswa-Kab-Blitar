<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RekomendasiDesa;
use App\Models\AuditLog;

class AutoVerifyRekomendasi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beasiswa:auto-verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menyetujui rekomendasi yang melebihi batas waktu 3 hari di tingkat Kecamatan dan DPMD.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Menjalankan Auto-Verifikasi Rekomendasi (SLA 3 Hari)...");

        // Batas waktu: 3 hari yang lalu
        $batasWaktu = now()->subDays(3);

        // Cari RekomendasiDesa yang masih "belum_diverifikasi" oleh Kecamatan ATAU DPMD
        // dan tanggal rekomendasinya sudah melewati batas waktu 3 hari.
        $rekomendasiList = RekomendasiDesa::where(function ($query) {
                $query->where('status_kecamatan', 'belum_diverifikasi')
                      ->orWhere('status_dpmd', 'belum_diverifikasi');
            })
            ->where('tanggal_rekomendasi', '<=', $batasWaktu)
            ->with('pendaftaran')
            ->get();

        if ($rekomendasiList->isEmpty()) {
            $this->info("Tidak ada data yang perlu diauto-verifikasi saat ini.");
            return;
        }

        $count = 0;

        foreach ($rekomendasiList as $rek) {
            $diubah = false;
            $dataLama = $rek->only(['status_kecamatan', 'status_dpmd', 'catatan_kecamatan', 'catatan_dpmd']);

            if ($rek->status_kecamatan === 'belum_diverifikasi') {
                $rek->status_kecamatan = 'disetujui';
                $rek->catatan_kecamatan = 'Disetujui otomatis oleh sistem karena melewati batas waktu (SLA 3 Hari).';
                $rek->verified_at = now();
                $diubah = true;
            }

            if ($rek->status_dpmd === 'belum_diverifikasi') {
                $rek->status_dpmd = 'disetujui';
                $rek->catatan_dpmd = 'Disetujui otomatis oleh sistem karena melewati batas waktu (SLA 3 Hari).';
                $rek->dpmd_verified_at = now();
                $diubah = true;
            }

            if ($diubah) {
                $rek->save();
                
                // Catat audit log sistem
                AuditLog::catat(
                    'Auto-Verifikasi Sistem',
                    "Rekomendasi diverifikasi otomatis karena melewati batas SLA 3 hari.",
                    RekomendasiDesa::class,
                    $rek->id,
                    $dataLama,
                    $rek->only(['status_kecamatan', 'status_dpmd', 'catatan_kecamatan', 'catatan_dpmd'])
                );

                // Cek apakah dengan persetujuan ini, rekomendasi menjadi full approved
                if ($rek->isFullyApproved() && $rek->pendaftaran && $rek->pendaftaran->status === 'diteruskan_ke_kecamatan') {
                    // Gunakan service yang sama dengan controller agar statusnya konsisten
                    app(\App\Services\RecommendationService::class)->cekPersetujuanParalel($rek->pendaftaran);
                    
                    \App\Models\AuditLog::catat(
                        'Ubah Status Pendaftaran (Sistem)',
                        'Status pendaftaran diubah otomatis ke tahapan selanjutnya karena rekomendasi disetujui (SLA 3 Hari).',
                        \App\Models\Pendaftaran::class,
                        $rek->pendaftaran->id
                    );
                }

                $count++;
            }
        }

        $this->info("Berhasil memproses $count data rekomendasi.");
    }
}
