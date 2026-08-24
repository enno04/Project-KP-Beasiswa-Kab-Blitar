<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifikasiRekomendasiRequest;
use App\Models\AuditLog;
use App\Models\Desa;
use App\Models\Pendaftaran;
use App\Models\Program;
use App\Models\Jalur;
use App\Models\RekomendasiDesa;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $kecId = $user->kecamatan_id;

        $pendaftaranQuery = Pendaftaran::whereHas('identitas', fn($q) => $q->where('kecamatan_id', $kecId))
            ->whereIn('status', ['diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd', 'proses_seleksi', 'menunggu_penetapan', 'lulus', 'tidak_lulus']);

        $stats = [
            'total' => (clone $pendaftaranQuery)->count(),
            'menunggu_verifikasi' => (clone $pendaftaranQuery)->where('status', 'menunggu_verifikasi')->count(),
            'diteruskan_ke_kecamatan' => (clone $pendaftaranQuery)->where('status', 'diteruskan_ke_kecamatan')->count(),
            'menunggu_penetapan' => (clone $pendaftaranQuery)->where('status', 'menunggu_penetapan')->count(),
            'lulus' => (clone $pendaftaranQuery)->where('status', 'lulus')->count(),
        ];

        $disetujuiKecamatan = RekomendasiDesa::whereHas('pendaftaran.identitas', fn($q) => $q->where('kecamatan_id', $kecId))
            ->where('status_kecamatan', 'disetujui')
            ->count();
            
        $persentase = $stats['total'] > 0 ? round(($disetujuiKecamatan / $stats['total']) * 100) : 0;

        $slaWarning = Pendaftaran::whereHas('identitas', fn($q) => $q->where('kecamatan_id', $kecId))
            ->where('status', 'diteruskan_ke_kecamatan')
            ->whereHas('rekomendasiDesa', function ($q) {
                $q->where('status_kecamatan', 'belum_diverifikasi')
                  ->where('created_at', '<', now()->subDays(3));
            })
            ->count();

        $aktivitasTerbaru = Pendaftaran::with(['program', 'jalur', 'identitas.desa'])
            ->whereHas('identitas', fn($q) => $q->where('kecamatan_id', $kecId))
            ->whereIn('status', ['diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd', 'proses_seleksi', 'menunggu_penetapan', 'lulus', 'tidak_lulus'])
            ->latest()->take(5)->get();

        // Monitoring: status desa se-kecamatan (Optimized N+1)
        $desaList = Desa::where('kecamatan_id', $kecId)
            ->withCount([
                'users as total_pendaftar' => function ($query) {
                    // Because we don't have direct Desa->Pendaftarans relation easily count, we will use raw or join.
                    // Wait, this might be tricky, let's keep it simple with subqueries if possible or just use the current way if it's not a bottleneck.
                    // Actually, let's write a better query to get these stats grouped by desa_id
                }
            ])
            ->get();
            
        // Optimization: Fetch all stats grouped by desa_id
        $totalPendaftarPerDesa = Pendaftaran::whereHas('identitas', fn($q) => $q->where('kecamatan_id', $kecId))
            ->join('pendaftaran_identitas', 'pendaftarans.id', '=', 'pendaftaran_identitas.pendaftaran_id')
            ->select('pendaftaran_identitas.desa_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('pendaftaran_identitas.desa_id')
            ->pluck('total', 'desa_id');

        $rekomendasiPerDesa = RekomendasiDesa::whereIn('desa_id', $desaList->pluck('id'))
            ->select('desa_id', 
                \Illuminate\Support\Facades\DB::raw('count(*) as total_kirim'),
                \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN status_kecamatan = "disetujui" THEN 1 ELSE 0 END) as total_disetujui')
            )
            ->groupBy('desa_id')
            ->get()
            ->keyBy('desa_id');

        $monitoringDesa = [];
        foreach ($desaList as $desa) {
            $totalPendaftar = $totalPendaftarPerDesa[$desa->id] ?? 0;
            $sudahKirim = $rekomendasiPerDesa[$desa->id]->total_kirim ?? 0;
            $belumKirim = $totalPendaftar - $sudahKirim;
            $disetujui = $rekomendasiPerDesa[$desa->id]->total_disetujui ?? 0;

            $monitoringDesa[] = [
                'desa' => $desa,
                'total_pendaftar' => $totalPendaftar,
                'sudah_kirim' => $sudahKirim,
                'belum_kirim' => max($belumKirim, 0),
                'disetujui' => $disetujui,
            ];
        }

        return view('kecamatan.dashboard', compact('stats', 'aktivitasTerbaru', 'monitoringDesa', 'persentase', 'slaWarning', 'disetujuiKecamatan'));
    }

    public function index(Request $request, $programSlug, $jalurSlug = null)
    {
        $user = auth()->user();
        $program = Program::with('jalurs')->where('slug', $programSlug)->firstOrFail();
        
        $jalur = null;
        if ($jalurSlug) {
            $jalur = Jalur::where('program_id', $program->id)->where('slug', $jalurSlug)->firstOrFail();
        } elseif ($program->jalurs->count() === 1) {
            $jalur = $program->jalurs->first();
        }

        $query = Pendaftaran::with(['identitas.desa', 'jalur', 'rekomendasiDesa'])
            ->whereHas('identitas', fn($q) => $q->where('kecamatan_id', $user->kecamatan_id))
            ->where('program_id', $program->id)
            ->whereIn('status', ['diteruskan_ke_kecamatan', 'ditolak_kecamatan', 'ditolak_dpmd', 'proses_seleksi', 'menunggu_penetapan', 'lulus', 'tidak_lulus']);

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

        // Filter desa
        if ($request->desa_id) {
            $query->whereHas('identitas', fn($q) => $q->where('desa_id', $request->desa_id));
        }

        $pendaftar = $query->latest()->paginate(20)->withQueryString();
        
        $desaList = \App\Models\Desa::where('kecamatan_id', $user->kecamatan_id)->orderBy('nama_desa')->get();

        return view('kecamatan.index', compact('pendaftar', 'program', 'jalur', 'desaList'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $pendaftaran = Pendaftaran::with([
            'program', 'jalur', 'identitas.desa', 'identitas.kecamatan',
            'orangtua', 'uploadDokumens.dokumen', 'penilaians.kriteria', 'rekomendasiDesa',
        ])->whereHas('identitas', fn($q) => $q->where('kecamatan_id', $user->kecamatan_id))
            ->findOrFail($id);

        return view('kecamatan.show', compact('pendaftaran'));
    }

    /**
     * Verifikasi Berita Acara & Surat Rekomendasi dari Desa.
     * Jika disetujui DAN DPMD juga sudah menyetujui, status pendaftar berubah menjadi lolos_verifikasi.
     */
    public function verifikasiRekomendasi(VerifikasiRekomendasiRequest $request, RecommendationService $recommendationService, $id)
    {
        $user = auth()->user();
        $pendaftaran = Pendaftaran::whereHas('identitas', fn($q) => $q->where('kecamatan_id', $user->kecamatan_id))
            ->findOrFail($id);

        $rekomendasi = RekomendasiDesa::where('pendaftaran_id', $pendaftaran->id)->firstOrFail();
        $rekomendasi->update([
            'status_kecamatan' => $request->keputusan,
            'catatan_kecamatan' => $request->catatan_kecamatan,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        // Cek apakah DPMD juga sudah menyetujui (mekanisme paralel)
        $fullyApproved = $recommendationService->cekPersetujuanParalel($pendaftaran);
        if ($fullyApproved) {
            $message = 'Berkas Desa diteruskan. DPMD juga sudah meneruskan. Pendaftaran masuk ke Kabupaten.';
        } else {
            $message = 'Berkas Desa berhasil diteruskan oleh Kecamatan. Menunggu persetujuan DPMD.';
        }

        AuditLog::catat(
            'Verifikasi Rekomendasi (Kecamatan)',
            "Meneruskan rekomendasi desa" . ($request->catatan_kecamatan ? " | Catatan: {$request->catatan_kecamatan}" : ''),
            Pendaftaran::class,
            $pendaftaran->id
        );

        return redirect()->back()->with('success', $message);
    }

    public function riwayat(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $query = RekomendasiDesa::with(['pendaftaran.identitas', 'desa', 'kecamatanVerifier'])
            ->whereHas('desa', fn($q) => $q->where('kecamatan_id', $user->kecamatan_id))
            ->where('status_kecamatan', '!=', 'belum_diverifikasi');

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

        // Filter status
        if ($request->status) {
            if ($request->status === 'otomatis') {
                $query->where('status_kecamatan', 'disetujui')->whereNull('verified_by');
            } else {
                $query->where('status_kecamatan', $request->status);
            }
        }

        $riwayat = $query->latest('verified_at')->paginate(20)->withQueryString();
        
        $desaList = \App\Models\Desa::where('kecamatan_id', $user->kecamatan_id)->orderBy('nama_desa')->get();

        return view('kecamatan.riwayat', compact('riwayat', 'desaList'));
    }
}
