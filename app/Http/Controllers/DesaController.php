<?php

namespace App\Http\Controllers;

use App\Http\Requests\HitungPenilaianRequest;
use App\Http\Requests\UploadRekomendasiRequest;
use App\Models\Jalur;
use App\Models\Pendaftaran;
use App\Models\Program;
use App\Models\RekomendasiDesa;
use App\Services\PenilaianService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class DesaController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $desaId = $user->desa_id;

        $pendaftaranQuery = Pendaftaran::whereHas('program', fn($q) => $q->where('kode', 'sdss'))
            ->whereHas('identitas', fn($q) => $q->where('desa_id', $desaId));

        $stats = [
            'total' => (clone $pendaftaranQuery)->count(),
            'menunggu_verifikasi' => (clone $pendaftaranQuery)->where('status', 'menunggu_verifikasi')->count(),
            'lolos_verifikasi' => (clone $pendaftaranQuery)->where('status', 'lolos_verifikasi')->count(),
            'sudah_dinilai' => (clone $pendaftaranQuery)->whereNotNull('total_nilai')->count(),
            'lulus' => (clone $pendaftaranQuery)->where('status', 'lulus')->count(),
        ];

        $aktivitasTerbaru = Pendaftaran::with(['program', 'jalur'])
            ->whereHas('program', fn($q) => $q->where('kode', 'sdss'))
            ->whereHas('identitas', fn($q) => $q->where('desa_id', $desaId))
            ->latest()->take(10)->get();

        // Cek status rekomendasi desa
        $rekomendasiDesa = RekomendasiDesa::where('desa_id', $desaId)->latest()->first();

        return view('desa.dashboard', compact('stats', 'aktivitasTerbaru', 'rekomendasiDesa'));
    }

    /**
     * Daftar pendaftar per program — GENERIK.
     */
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

        $query = Pendaftaran::with(['identitas', 'jalur', 'rekomendasiDesa'])
            ->whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))
            ->where('program_id', $program->id);

        if ($jalur) $query->where('jalur_id', $jalur->id);
        $tahun = $request->tahun ?? date('Y');
        if ($request->tahun) $query->where('tahun', $request->tahun);
        if ($request->status) $query->where('status', $request->status);

        $periodeAktif = \App\Models\Periode::where('tahun', $tahun)->first();

        $pendaftar = $query->latest()->paginate(20)->withQueryString();
        return view('desa.index', compact('pendaftar', 'program', 'jalur', 'periodeAktif'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $pendaftaran = Pendaftaran::with([
            'program', 'jalur', 'identitas.desa', 'identitas.kecamatan',
            'orangtua', 'uploadDokumens.dokumen', 'penilaians.kriteria',
            'rekomendasiDesa',
        ])->whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))
            ->findOrFail($id);

        return view('desa.show', compact('pendaftaran'));
    }

    /**
     * Upload Surat Rekomendasi dan Berita Acara Musyawarah (khusus SDSS).
     */
    public function uploadRekomendasi(UploadRekomendasiRequest $request, RecommendationService $recommendationService, $id)
    {
        $user = auth()->user();
        $pendaftaran = Pendaftaran::whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))->findOrFail($id);

        $validated = $request->validated();

        try {
            $recommendationService->uploadRekomendasi($pendaftaran, $user->desa_id, $user->id, $request->allFiles(), $validated['catatan'] ?? null);
            return redirect()->back()->with('success', 'Surat Rekomendasi dan Berita Acara berhasil diunggah. Pendaftaran telah diteruskan ke Kecamatan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // === Penilaian & Ranking (Khusus SDSS) ===

    public function hitungPenilaian(HitungPenilaianRequest $request, PenilaianService $penilaianService, \App\Services\RankingService $rankingService)
    {
        $user = auth()->user();
        
        // 1. Hitung Penilaian Otomatis
        $resPenilaian = $penilaianService->hitungSemuaPendaftar($request->jalur_id, $request->periode_id, $user->desa_id);
        if (!$resPenilaian['status']) {
            return redirect()->back()->with('error', $resPenilaian['message']);
        }

        // 2. Generate Ranking Desa Otomatis
        $resRanking = $rankingService->generateRankingPerDesa($request->jalur_id, $request->periode_id, $user->desa_id);
        if (!$resRanking['status']) {
            return redirect()->back()->with('error', $resRanking['message']);
        }

        return redirect()->back()->with('success', 'Penilaian dan Perangkingan Desa berhasil diproses.');
    }

    public function generateRanking(HitungPenilaianRequest $request, \App\Services\RankingService $rankingService)
    {
        $user = auth()->user();

        $result = $rankingService->generateRankingPerDesa($request->jalur_id, $request->periode_id, $user->desa_id);
        return redirect()->back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Tetapkan 1 perwakilan desa (SDSS).
     * Jika pendaftar ini dipilih, pendaftar lain di desa yang sama (jalur & periode sama) akan otomatis gugur.
     */
    public function tetapkanPerwakilan(Request $request, RecommendationService $recommendationService, $id)
    {
        $user = auth()->user();
        $pendaftaran = Pendaftaran::whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))->findOrFail($id);

        try {
            $recommendationService->tetapkanPerwakilan($pendaftaran, $user->desa_id);
            return redirect()->back()->with('success', 'Berhasil menetapkan perwakilan. Pendaftar lain dari desa ini telah otomatis digugurkan. Silakan unggah Surat Rekomendasi dan Berita Acara.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menetapkan perwakilan: ' . $e->getMessage());
        }
    }
}
