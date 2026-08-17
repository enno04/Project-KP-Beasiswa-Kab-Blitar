<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifikasiDpmdRequest;
use App\Models\AuditLog;
use App\Models\Desa;
use App\Models\Pendaftaran;
use App\Models\Program;
use App\Models\Jalur;
use App\Models\RekomendasiDesa;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DpmdController extends Controller
{
    /**
     * Dashboard DPMD — melihat semua pendaftar SDSS yang sudah di-forward oleh Desa.
     */
    public function dashboard()
    {
        // DPMD melihat semua pendaftar SDSS se-Kabupaten yang sudah diteruskan ke kecamatan/DPMD
        $pendaftaranQuery = Pendaftaran::whereHas('program', fn($q) => $q->where('kode', 'sdss'))
            ->whereIn('status', [
                'diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd',
                'lolos_verifikasi', 'menunggu_penetapan', 'lulus', 'tidak_lulus',
            ]);

        $stats = [
            'total' => (clone $pendaftaranQuery)->count(),
            'menunggu_verifikasi_dpmd' => Pendaftaran::whereHas('program', fn($q) => $q->where('kode', 'sdss'))
                ->where('status', 'diteruskan_ke_kecamatan')
                ->whereHas('rekomendasiDesa', fn($q) => $q->where('status_dpmd', 'belum_diverifikasi'))
                ->count(),
            'disetujui_dpmd' => Pendaftaran::whereHas('program', fn($q) => $q->where('kode', 'sdss'))
                ->whereHas('rekomendasiDesa', fn($q) => $q->where('status_dpmd', 'disetujui'))
                ->count(),
            'ditolak_dpmd' => Pendaftaran::whereHas('program', fn($q) => $q->where('kode', 'sdss'))
                ->where('status', 'ditolak_dpmd')
                ->count(),
            'lulus' => (clone $pendaftaranQuery)->where('status', 'lulus')->count(),
        ];

        $aktivitasTerbaru = Pendaftaran::with(['program', 'jalur', 'identitas.desa', 'identitas.kecamatan', 'rekomendasiDesa'])
            ->whereHas('program', fn($q) => $q->where('kode', 'sdss'))
            ->whereIn('status', [
                'diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd',
                'lolos_verifikasi', 'menunggu_penetapan', 'lulus', 'tidak_lulus',
            ])
            ->latest()->take(10)->get();

        // Monitoring: status per kecamatan
        $monitoringKecamatan = DB::table('pendaftarans')
            ->join('pendaftaran_identitas', 'pendaftarans.id', '=', 'pendaftaran_identitas.pendaftaran_id')
            ->join('kecamatan', 'pendaftaran_identitas.kecamatan_id', '=', 'kecamatan.id')
            ->join('programs', 'pendaftarans.program_id', '=', 'programs.id')
            ->where('programs.kode', 'sdss')
            ->whereIn('pendaftarans.status', [
                'diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd',
                'lolos_verifikasi', 'menunggu_penetapan', 'lulus', 'tidak_lulus',
            ])
            ->select(
                'kecamatan.nama_kecamatan',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN pendaftarans.status = 'diteruskan_ke_kecamatan' THEN 1 ELSE 0 END) as menunggu"),
                DB::raw("SUM(CASE WHEN pendaftarans.status IN ('lolos_verifikasi','menunggu_penetapan','lulus') THEN 1 ELSE 0 END) as disetujui")
            )
            ->groupBy('kecamatan.nama_kecamatan')
            ->orderBy('kecamatan.nama_kecamatan')
            ->get();

        return view('dpmd.dashboard', compact('stats', 'aktivitasTerbaru', 'monitoringKecamatan'));
    }

    /**
     * Daftar pendaftar SDSS yang sudah diteruskan dari Desa.
     */
    public function index(Request $request, $programSlug, $jalurSlug = null)
    {
        $program = Program::with('jalurs')->where('slug', $programSlug)->firstOrFail();
        
        $jalur = null;
        if ($jalurSlug) {
            $jalur = Jalur::where('program_id', $program->id)->where('slug', $jalurSlug)->firstOrFail();
        } elseif ($program->jalurs->count() === 1) {
            $jalur = $program->jalurs->first();
        }

        $query = Pendaftaran::with(['identitas.desa', 'identitas.kecamatan', 'jalur', 'rekomendasiDesa'])
            ->where('program_id', $program->id)
            ->whereIn('status', [
                'diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd',
                'lolos_verifikasi', 'menunggu_penetapan', 'lulus', 'tidak_lulus',
            ]);

        if ($jalur) $query->where('jalur_id', $jalur->id);
        if ($request->tahun) $query->where('tahun', $request->tahun);
        if ($request->status) $query->where('status', $request->status);

        // Filter khusus DPMD: hanya yang belum diverifikasi DPMD
        if ($request->filter_dpmd === 'belum') {
            $query->whereHas('rekomendasiDesa', fn($q) => $q->where('status_dpmd', 'belum_diverifikasi'));
        } elseif ($request->filter_dpmd === 'disetujui') {
            $query->whereHas('rekomendasiDesa', fn($q) => $q->where('status_dpmd', 'disetujui'));
        } elseif ($request->filter_dpmd === 'ditolak') {
            $query->whereHas('rekomendasiDesa', fn($q) => $q->where('status_dpmd', 'ditolak'));
        }

        $pendaftar = $query->latest()->paginate(20)->withQueryString();
        return view('dpmd.index', compact('pendaftar', 'program', 'jalur'));
    }

    /**
     * Detail pendaftar SDSS.
     */
    public function show($id)
    {
        $pendaftaran = Pendaftaran::with([
            'program', 'jalur', 'identitas.desa', 'identitas.kecamatan',
            'orangtua', 'uploadDokumens.dokumen', 'penilaians.kriteria',
            'rekomendasiDesa.kecamatanVerifier', 'rekomendasiDesa.dpmdVerifier',
        ])->findOrFail($id);

        return view('dpmd.show', compact('pendaftaran'));
    }

    /**
     * Verifikasi Rekomendasi Desa oleh DPMD.
     * Jika disetujui DAN Kecamatan juga sudah menyetujui, pendaftaran maju ke Kabupaten.
     */
    public function verifikasiRekomendasi(VerifikasiDpmdRequest $request, RecommendationService $recommendationService, $id)
    {
        $pendaftaran = Pendaftaran::with('program')->findOrFail($id);

        // Validasi: hanya SDSS
        if (!$pendaftaran->program || !$pendaftaran->program->isSdss()) {
            return redirect()->back()->with('error', 'Verifikasi DPMD hanya berlaku untuk program SDSS.');
        }

        $rekomendasi = RekomendasiDesa::where('pendaftaran_id', $pendaftaran->id)->firstOrFail();
        $rekomendasi->update([
            'status_dpmd' => $request->keputusan,
            'catatan_dpmd' => $request->catatan_dpmd,
            'dpmd_verified_by' => auth()->id(),
            'dpmd_verified_at' => now(),
        ]);

        if ($request->keputusan === 'disetujui') {
            // Cek apakah Kecamatan juga sudah menyetujui (mekanisme paralel)
            $fullyApproved = $recommendationService->cekPersetujuanParalel($pendaftaran);
            if ($fullyApproved) {
                $message = 'Rekomendasi disetujui oleh DPMD. Kecamatan juga sudah menyetujui. Pendaftaran diteruskan ke Kabupaten.';
            } else {
                $message = 'Rekomendasi disetujui oleh DPMD. Menunggu persetujuan Kecamatan.';
            }
        } else {
            $pendaftaran->update(['status' => 'ditolak_dpmd']);
            $message = 'Rekomendasi ditolak oleh DPMD.';
        }

        AuditLog::catat(
            'Verifikasi DPMD',
            "Keputusan: {$request->keputusan}" . ($request->catatan_dpmd ? " | Catatan: {$request->catatan_dpmd}" : ''),
            Pendaftaran::class,
            $pendaftaran->id
        );

        return redirect()->back()->with('success', $message);
    }
}
