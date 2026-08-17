<?php

namespace App\Http\Controllers;

use App\Http\Requests\HitungPenilaianRequest;
use App\Http\Requests\PenetapanRequest;
use App\Http\Requests\SimpanWawancaraRequest;
use App\Models\AuditLog;
use App\Models\Pendaftaran;
use App\Models\Program;
use App\Models\Jalur;
use App\Models\Periode;
use App\Services\PenilaianService;
use App\Services\RankingService;
use App\Services\PenetapanService;
use Illuminate\Http\Request;

class KabupatenController extends Controller
{
    /**
     * Status-status yang relevan untuk kabupaten (sudah melewati verifikasi kecamatan).
     */
    private const KABUPATEN_STATUSES = [
        'lolos_verifikasi',
        'proses_penilaian',
        'menunggu_penetapan',
        'lulus',
        'tidak_lulus',
        'gugur_wawancara',
    ];

    public function dashboard()
    {
        $periodeAktif = Periode::aktif()->first();

        // Hanya tampilkan data yang sudah masuk ke level kabupaten
        $baseQuery = Pendaftaran::whereIn('status', self::KABUPATEN_STATUSES);

        if ($periodeAktif) {
            $baseQuery->where('periode_id', $periodeAktif->id);
        }

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'lolos_verifikasi' => (clone $baseQuery)->where('status', 'lolos_verifikasi')->count(),
            'menunggu_penetapan' => (clone $baseQuery)->where('status', 'menunggu_penetapan')->count(),
            'lulus' => (clone $baseQuery)->where('status', 'lulus')->count(),
        ];

        $aktivitasTerbaru = Pendaftaran::with(['program', 'jalur', 'identitas'])
            ->whereIn('status', self::KABUPATEN_STATUSES)
            ->latest()->take(10)->get();

        return view('kabupaten.dashboard', compact('stats', 'aktivitasTerbaru', 'periodeAktif'));
    }

    /**
     * Daftar pendaftar per program/jalur — dengan fitur penilaian, ranking & penetapan inline.
     */
    public function index(Request $request, $programSlug, $jalurSlug = null)
    {
        $program = Program::with('jalurs')->where('slug', $programSlug)->firstOrFail();
        $periodeAktif = Periode::aktif()->first();
        
        $jalur = null;
        if ($jalurSlug) {
            $jalur = Jalur::where('program_id', $program->id)->where('slug', $jalurSlug)->firstOrFail();
        } elseif ($program->jalurs->count() === 1) {
            $jalur = $program->jalurs->first();
        }

        $query = Pendaftaran::with(['identitas.desa', 'identitas.kecamatan', 'jalur'])
            ->where('program_id', $program->id)
            ->whereIn('status', self::KABUPATEN_STATUSES);

        if ($jalur) $query->where('jalur_id', $jalur->id);
        
        // Default ke periode aktif
        $periodeId = $request->periode_id ?? ($periodeAktif ? $periodeAktif->id : null);
        if ($periodeId) $query->where('periode_id', $periodeId);

        if ($request->status) $query->where('status', $request->status);

        // Urutkan: yang sudah ada ranking di atas, lalu by total_nilai desc
        $pendaftar = $query->orderByRaw('CASE WHEN ranking IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('ranking', 'asc')
            ->orderBy('total_nilai', 'desc')
            ->paginate(50)->withQueryString();

        $periodeList = Periode::orderBy('tahun', 'desc')->get();
        $isSdss = $program->isSdss();
        $isBerdayaBerjaya = ($program->kode === 'berdaya_berjaya' || $program->slug === 'berdaya-berjaya');

        return view('kabupaten.index', compact('pendaftar', 'program', 'jalur', 'periodeAktif', 'periodeList', 'periodeId', 'isSdss', 'isBerdayaBerjaya'));
    }

    public function show($id)
    {
        $pendaftaran = Pendaftaran::with([
            'program', 'jalur', 'identitas.desa', 'identitas.kecamatan',
            'orangtua', 'jawabanKriterias.kriteria.kelompokKriteria', 'jawabanKriterias.pilihanKriteria',
            'uploadDokumens.dokumen', 'penilaians.kriteria', 'wawancaras', 'penetapan',
        ])->findOrFail($id);
        return view('kabupaten.show', compact('pendaftaran'));
    }

    // === Penilaian & Ranking (Konsolidasi 1 tombol) ===

    public function hitungDanRanking(HitungPenilaianRequest $request, PenilaianService $penilaianService, RankingService $rankingService)
    {
        // Step 1: Hitung penilaian
        $penilaianResult = $penilaianService->hitungSemuaPendaftar($request->jalur_id, $request->periode_id);
        if (!$penilaianResult['status']) {
            return redirect()->back()->with('error', $penilaianResult['message']);
        }

        // Step 2: Generate ranking
        $jalur = Jalur::with('program')->findOrFail($request->jalur_id);
        $rankingResult = $jalur->program->isSdss()
            ? $rankingService->generateRankingPerDesa($request->jalur_id, $request->periode_id)
            : $rankingService->generateRanking($request->jalur_id, $request->periode_id);

        if (!$rankingResult['status']) {
            return redirect()->back()->with('error', $rankingResult['message']);
        }

        return redirect()->back()->with('success', 'Penilaian & ranking berhasil dihitung. Pendaftar siap untuk ditetapkan.');
    }

    // === Legacy routes (agar backward compatible) ===
    public function hitungPenilaian(HitungPenilaianRequest $request, PenilaianService $penilaianService)
    {
        $result = $penilaianService->hitungSemuaPendaftar($request->jalur_id, $request->periode_id);
        return redirect()->back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function generateRanking(HitungPenilaianRequest $request, RankingService $rankingService)
    {
        $jalur = Jalur::with('program')->findOrFail($request->jalur_id);
        $result = $jalur->program->isSdss()
            ? $rankingService->generateRankingPerDesa($request->jalur_id, $request->periode_id)
            : $rankingService->generateRanking($request->jalur_id, $request->periode_id);
        return redirect()->back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    // === Wawancara (Khusus Berdaya Berjaya) ===
    public function simpanWawancara(SimpanWawancaraRequest $request, $id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        if ($request->action_type === 'gugurkan') {
            $pendaftaran->status = 'gugur_wawancara';
            $pendaftaran->nilai_wawancara = 0;
            $pendaftaran->save();
            AuditLog::catat('Gugur Wawancara', "Pendaftar ditandai tidak hadir / gugur wawancara.", Pendaftaran::class, $pendaftaran->id);
            return redirect()->back()->with('success', 'Pendaftar berhasil digugurkan dari tahap wawancara.');
        }

        $pendaftaran->nilai_wawancara = $request->nilai_wawancara;
        if ($pendaftaran->status === 'gugur_wawancara') {
            $pendaftaran->status = 'lolos_verifikasi';
        }
        $pendaftaran->save();
        
        AuditLog::catat('Input Wawancara', "Nilai Wawancara {$request->nilai_wawancara} disimpan.", Pendaftaran::class, $pendaftaran->id);
        return redirect()->back()->with('success', 'Nilai wawancara berhasil disimpan.');
    }

    // === Penetapan per baris (inline) ===

    public function tetapkanSatu(Request $request, $id, PenetapanService $penetapanService)
    {
        $keputusan = $request->keputusan;
        if (!in_array($keputusan, ['lulus', 'tidak_lulus'])) {
            return redirect()->back()->with('error', 'Keputusan tidak valid.');
        }

        $result = $penetapanService->tetapkan([$id], $keputusan, $request->catatan);
        return redirect()->back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    // === Legacy Hasil Ranking & Penetapan (tetap ada) ===

    public function hasilRanking(Request $request)
    {
        $programs = Program::with('jalurs')->get();
        $periodeList = Periode::orderBy('tahun', 'desc')->get();
        $pendaftars = collect();

        if ($request->filled(['jalur_id', 'periode_id'])) {
            $pendaftars = Pendaftaran::with(['identitas.desa', 'identitas.kecamatan', 'jalur'])
                ->where('jalur_id', $request->jalur_id)
                ->where('periode_id', $request->periode_id)
                ->whereIn('status', [
                    'lolos_verifikasi',
                    'gugur_wawancara',
                    'proses_penilaian',
                    'menunggu_penetapan', 
                    'lulus', 
                    'tidak_lulus'
                ])
                ->orderBy('total_nilai', 'desc')->get();
        }
        return view('kabupaten.hasil.ranking', compact('programs', 'periodeList', 'pendaftars'));
    }

    public function penetapan(Request $request)
    {
        $programs = Program::with('jalurs')->get();
        $periodeList = Periode::orderBy('tahun', 'desc')->get();
        $pendaftars = collect();

        if ($request->filled(['jalur_id', 'periode_id'])) {
            $pendaftars = Pendaftaran::with(['identitas.desa', 'identitas.kecamatan', 'jalur', 'program'])
                ->where('jalur_id', $request->jalur_id)
                ->where('periode_id', $request->periode_id)
                ->whereIn('status', ['menunggu_penetapan', 'lulus', 'tidak_lulus'])
                ->orderBy('ranking', 'asc')->get();
        }
        return view('kabupaten.hasil.penetapan', compact('programs', 'periodeList', 'pendaftars'));
    }

    public function penetapanStore(PenetapanRequest $request, PenetapanService $penetapanService)
    {
        $result = $penetapanService->tetapkan($request->pendaftaran_ids, $request->keputusan, $request->catatan);
        return redirect()->back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }
}

