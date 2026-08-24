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
                'diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd', 'proses_seleksi',
                'menunggu_penetapan', 'lulus', 'tidak_lulus',
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
            'lulus' => (clone $pendaftaranQuery)->where('status', 'lulus')->count(),
        ];

        $persentase = $stats['total'] > 0 ? round(($stats['disetujui_dpmd'] / $stats['total']) * 100) : 0;

        $slaWarning = Pendaftaran::whereHas('program', fn($q) => $q->where('kode', 'sdss'))
            ->where('status', 'diteruskan_ke_kecamatan')
            ->whereHas('rekomendasiDesa', function ($q) {
                $q->where('status_dpmd', 'belum_diverifikasi')
                  ->where('created_at', '<', now()->subDays(3));
            })
            ->count();

        $aktivitasTerbaru = Pendaftaran::with(['program', 'jalur', 'identitas.desa', 'identitas.kecamatan', 'rekomendasiDesa'])
            ->whereHas('program', fn($q) => $q->where('kode', 'sdss'))
            ->whereIn('status', [
                'diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd', 'proses_seleksi',
                'menunggu_penetapan', 'lulus', 'tidak_lulus',
            ])
            ->latest()->take(5)->get();

        // Monitoring: status per kecamatan
        $monitoringKecamatan = DB::table('pendaftarans')
            ->join('pendaftaran_identitas', 'pendaftarans.id', '=', 'pendaftaran_identitas.pendaftaran_id')
            ->join('kecamatan', 'pendaftaran_identitas.kecamatan_id', '=', 'kecamatan.id')
            ->join('programs', 'pendaftarans.program_id', '=', 'programs.id')
            ->where('programs.kode', 'sdss')
            ->whereIn('pendaftarans.status', [
                'diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd', 'proses_seleksi',
                'menunggu_penetapan', 'lulus', 'tidak_lulus',
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

        return view('dpmd.dashboard', compact('stats', 'aktivitasTerbaru', 'monitoringKecamatan', 'persentase', 'slaWarning'));
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
                'diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'proses_seleksi',
                'menunggu_penetapan', 'lulus', 'tidak_lulus',
            ]);

        if ($jalur) $query->where('jalur_id', $jalur->id);
        if ($request->tahun) $query->where('tahun', $request->tahun);
        if ($request->status) $query->where('status', $request->status);

        // Filter search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_pendaftaran', 'like', '%' . $request->search . '%')
                  ->orWhereHas('identitas', function($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter wilayah
        if ($request->kecamatan_id) {
            $query->whereHas('identitas', fn($q) => $q->where('kecamatan_id', $request->kecamatan_id));
        }
        if ($request->desa_id) {
            $query->whereHas('identitas', fn($q) => $q->where('desa_id', $request->desa_id));
        }

        // Filter khusus DPMD: hanya yang belum diverifikasi DPMD
        if ($request->filter_dpmd === 'belum') {
            $query->whereHas('rekomendasiDesa', fn($q) => $q->where('status_dpmd', 'belum_diverifikasi'));
        } elseif ($request->filter_dpmd === 'disetujui') {
            $query->whereHas('rekomendasiDesa', fn($q) => $q->where('status_dpmd', 'disetujui'));
        }

        $pendaftar = $query->latest()->paginate(20)->withQueryString();
        
        $kecamatanList = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        $desaList = \App\Models\Desa::orderBy('nama_desa')->get();

        return view('dpmd.index', compact('pendaftar', 'program', 'jalur', 'kecamatanList', 'desaList'));
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

        // Cek apakah Kecamatan juga sudah menyetujui (mekanisme paralel)
        $fullyApproved = $recommendationService->cekPersetujuanParalel($pendaftaran);
        if ($fullyApproved) {
            $message = 'Berkas Desa diteruskan. Kecamatan juga sudah meneruskan. Pendaftaran masuk ke Kabupaten.';
        } else {
            $message = 'Berkas Desa berhasil diteruskan oleh DPMD. Menunggu proses Kecamatan.';
        }

        AuditLog::catat(
            'Verifikasi Rekomendasi (DPMD)',
            "Meneruskan rekomendasi desa" . ($request->catatan_dpmd ? " | Catatan: {$request->catatan_dpmd}" : ''),
            Pendaftaran::class,
            $pendaftaran->id
        );

        return redirect()->back()->with('success', $message);
    }

    public function riwayat(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\RekomendasiDesa::with(['pendaftaran.identitas', 'desa.kecamatan', 'dpmdVerifier', 'kecamatanVerifier'])
            ->where('status_dpmd', '!=', 'belum_diverifikasi');

        // Filter search
        if ($request->search) {
            $query->whereHas('pendaftaran', function($q) use ($request) {
                $q->where('nomor_pendaftaran', 'like', '%' . $request->search . '%')
                  ->orWhereHas('identitas', function($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter desa
        if ($request->desa_id) {
            $query->where('desa_id', $request->desa_id);
        }

        // Filter kecamatan
        if ($request->kecamatan_id) {
            $query->whereHas('desa', fn($q) => $q->where('kecamatan_id', $request->kecamatan_id));
        }

        // Filter status
        if ($request->status) {
            if ($request->status === 'otomatis') {
                $query->where('status_dpmd', 'disetujui')->whereNull('dpmd_verified_by');
            } else {
                $query->where('status_dpmd', $request->status);
            }
        }

        $riwayat = $query->latest('dpmd_verified_at')->paginate(20)->withQueryString();
        
        $kecamatanList = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        // Since we need Desa list, maybe load them based on selected kecamatan or just all? 
        // For DPMD, since there are many villages, maybe just kecamatan filter is enough, but user asked for "filter desa dan lainnya".
        // Let's pass all desa if performance is okay, there are only ~248 villages in blitar.
        $desaList = \App\Models\Desa::orderBy('nama_desa')->get();

        return view('dpmd.riwayat', compact('riwayat', 'kecamatanList', 'desaList'));
    }
}
