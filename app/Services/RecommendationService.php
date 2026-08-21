<?php

namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\RekomendasiDesa;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Exception;

class RecommendationService
{
    public function uploadRekomendasi(Pendaftaran $pendaftaran, int $desaId, int $userId, array $files, ?string $catatan): void
    {
        if (!$pendaftaran->ranking) {
            throw new Exception('Pendaftar belum diranking atau belum ditetapkan oleh Desa.');
        }

        DB::beginTransaction();
        try {
            $rekPath = $files['surat_rekomendasi']
                ->storeAs('rekomendasi/' . $pendaftaran->id, 'rekomendasi_desa_' . time() . '.pdf', 'public');
            
            $baPath = $files['berita_acara']
                ->storeAs('rekomendasi/' . $pendaftaran->id, 'berita_acara_' . time() . '.pdf', 'public');

            RekomendasiDesa::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                [
                    'desa_id' => $desaId,
                    'user_id' => $userId,
                    'surat_rekomendasi_path' => $rekPath,
                    'berita_acara_path' => $baPath,
                    'catatan' => $catatan,
                    'tanggal_rekomendasi' => now(),
                    'status_kecamatan' => 'belum_diverifikasi',
                    'status_dpmd' => 'belum_diverifikasi',
                ]
            );

            // Update status pendaftaran menjadi diteruskan_ke_kecamatan
            // (pendaftaran juga muncul di dashboard DPMD secara paralel)
            $pendaftaran->update(['status' => 'diteruskan_ke_kecamatan']);

            AuditLog::catat('Upload Rekomendasi & Berita Acara', 'Surat rekomendasi dan berita acara musyawarah diunggah oleh Desa. Diteruskan ke Kecamatan & DPMD secara paralel.', Pendaftaran::class, $pendaftaran->id);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function tetapkanPerwakilan(Pendaftaran $pendaftaran, int $desaId): void
    {
        if (!$pendaftaran->ranking || $pendaftaran->total_nilai <= 0) {
            throw new Exception('Pendaftar belum diranking atau belum memiliki nilai.');
        }

        DB::beginTransaction();
        try {
            // Cari semua pendaftar lain di desa ini untuk jalur & periode yang sama
            $pendaftarLain = Pendaftaran::whereHas('identitas', fn($q) => $q->where('desa_id', $desaId))
                ->where('jalur_id', $pendaftaran->jalur_id)
                ->where('periode_id', $pendaftaran->periode_id)
                ->where('id', '!=', $pendaftaran->id)
                ->get();

            foreach ($pendaftarLain as $pLain) {
                // Gugurkan pendaftar ini di level desa
                $pLain->update(['status' => 'tidak_lolos_desa']);

                // Cari semua dokumen yang masih nyangkut (belum diverifikasi OPD)
                $dokumenBelumDiperiksa = \App\Models\UploadDokumen::where('pendaftaran_id', $pLain->id)
                    ->where('status', 'belum_diverifikasi')
                    ->get();

                foreach ($dokumenBelumDiperiksa as $dok) {
                    $dok->update(['status' => 'gugur_desa']);
                    
                    // Catat ke riwayat verifikasi agar muncul di OPD
                    \App\Models\VerifikasiDokumen::create([
                        'upload_dokumen_id' => $dok->id,
                        'user_id' => auth()->id() ?? 1,
                        'hasil' => 'gugur_desa',
                        'catatan' => 'Otomatis digugurkan: Desa telah menetapkan kandidat lain (Kuota Desa terpenuhi).',
                        'tanggal_verifikasi' => now(),
                    ]);
                }
            }

            // Tandai pendaftar ini sudah ditetapkan dengan membuat record RekomendasiDesa kosong
            \App\Models\RekomendasiDesa::firstOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                [
                    'desa_id' => $desaId,
                    'user_id' => auth()->id() ?? 1,
                    'status_kecamatan' => 'belum_diverifikasi',
                    'status_dpmd' => 'belum_diverifikasi'
                ]
            );

            AuditLog::catat('Penetapan Desa', "Pendaftar ID {$pendaftaran->id} ditetapkan sebagai perwakilan desa. Pendaftar lain digugurkan.", Pendaftaran::class, $pendaftaran->id);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cek apakah Kecamatan DAN DPMD sudah menyetujui rekomendasi.
     * Jika ya, update status pendaftaran ke menunggu_penetapan.
     * Dipanggil oleh KecamatanController dan DpmdController setelah masing-masing menyetujui.
     */
    public function cekPersetujuanParalel(Pendaftaran $pendaftaran): bool
    {
        $rekomendasi = RekomendasiDesa::where('pendaftaran_id', $pendaftaran->id)->first();
        if (!$rekomendasi) {
            return false;
        }

        if ($rekomendasi->isFullyApproved()) {
            // Kedua pihak sudah menyetujui, majukan ke Kabupaten
            $newStatus = 'lolos_verifikasi';
            if ($pendaftaran->program && $pendaftaran->program->isSdss() && $pendaftaran->total_nilai > 0 && $pendaftaran->ranking !== null) {
                $newStatus = 'menunggu_penetapan';
            }
            $pendaftaran->update(['status' => $newStatus]);

            AuditLog::catat(
                'Persetujuan Paralel Lengkap',
                'Kecamatan dan DPMD telah menyetujui rekomendasi. Pendaftaran diteruskan ke Kabupaten.',
                Pendaftaran::class,
                $pendaftaran->id
            );

            return true;
        }

        return false;
    }
}
