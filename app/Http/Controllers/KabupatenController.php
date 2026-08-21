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

    public function riwayatPenetapan(Request $request)
    {
        $query = Pendaftaran::with(['identitas.desa.kecamatan', 'program', 'jalur'])
            ->where('status', 'lulus');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_pendaftaran', 'like', '%' . $request->search . '%')
                  ->orWhereHas('identitas', function($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->search . '%')
                         ->orWhere('nik', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->program_id) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->kecamatan_id) {
            $query->whereHas('identitas', fn($q) => $q->where('kecamatan_id', $request->kecamatan_id));
        }

        if ($request->desa_id) {
            $query->whereHas('identitas', fn($q) => $q->where('desa_id', $request->desa_id));
        }

        $riwayatPenetapan = $query->latest('updated_at')->paginate(20)->withQueryString();

        $programs = \App\Models\Program::aktif()->get();
        $kecamatans = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();

        return view('kabupaten.riwayat_penetapan', compact('riwayatPenetapan', 'programs', 'kecamatans'));
    }

    public function exportRiwayat(Request $request)
    {
        $fileNameParts = ['Riwayat_Penetapan'];

        if ($request->program_id) {
            $program = \App\Models\Program::find($request->program_id);
            if ($program) {
                $fileNameParts[] = \Illuminate\Support\Str::slug($program->nama, '_');
            }
        } else {
            $fileNameParts[] = 'Semua_Program';
        }

        if ($request->kecamatan_id) {
            $kecamatan = \App\Models\Kecamatan::find($request->kecamatan_id);
            if ($kecamatan) {
                $fileNameParts[] = 'Kec_' . \Illuminate\Support\Str::slug($kecamatan->nama_kecamatan, '_');
            }
        }

        if ($request->desa_id) {
            $desa = \App\Models\Desa::find($request->desa_id);
            if ($desa) {
                $fileNameParts[] = 'Desa_' . \Illuminate\Support\Str::slug($desa->nama_desa, '_');
            }
        }

        $fileNameParts[] = date('Ymd');
        $fileName = implode('_', $fileNameParts) . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\RiwayatPenetapanExport($request), $fileName);
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

        $adaBelumDinilai = false;
        if ($jalur && $periodeId) {
            $adaBelumDinilai = Pendaftaran::where('jalur_id', $jalur->id)
                ->where('periode_id', $periodeId)
                ->where('status', 'lolos_verifikasi')
                ->whereNull('ranking')
                ->exists();
        }

        return view('kabupaten.index', compact('pendaftar', 'program', 'jalur', 'periodeAktif', 'periodeList', 'periodeId', 'isSdss', 'isBerdayaBerjaya', 'adaBelumDinilai'));
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
        // Deteksi pendaftar baru yang akan dihitung nilainya (rankingnya masih null)
        $jalur = Jalur::with('program')->findOrFail($request->jalur_id);
        $isBerdayaBerjaya = ($jalur->program->kode === 'berdaya_berjaya' || $jalur->program->slug === 'berdaya-berjaya');
        
        $pendaftarBaruIds = [];
        if ($isBerdayaBerjaya) {
            $pendaftarBaruIds = Pendaftaran::where('jalur_id', $jalur->id)
                ->where('periode_id', $request->periode_id)
                ->where('status', 'lolos_verifikasi')
                ->whereNull('ranking')
                ->pluck('id')
                ->toArray();
        }

        $penilaianResult = $penilaianService->hitungSemuaPendaftar($request->jalur_id, $request->periode_id);
        if (!$penilaianResult['status']) {
            return redirect()->back()->with('error', $penilaianResult['message']);
        }

        // Step 1.5: Otomatis batalkan wawancara pendaftar lama jika nilainya lebih rendah dari pendaftar baru
        if ($isBerdayaBerjaya && !empty($pendaftarBaruIds)) {
            // Karena PenilaianService sudah menghitung nilai pendaftar baru, kita ambil nilai terbesarnya
            $maxNilaiBaru = Pendaftaran::whereIn('id', $pendaftarBaruIds)->max('total_nilai');

            if ($maxNilaiBaru > 0) {
                // Cari pendaftar lama (yang bukan pendaftar baru) yang sudah punya nilai wawancara
                // tetapi total_nilainya lebih kecil dari pendaftar baru tertinggi
                $pendaftarLamaAffected = Pendaftaran::where('jalur_id', $jalur->id)
                    ->where('periode_id', $request->periode_id)
                    ->whereNotIn('id', $pendaftarBaruIds)
                    ->where('total_nilai', '<', $maxNilaiBaru)
                    ->whereNotNull('nilai_wawancara')
                    ->get();

                foreach ($pendaftarLamaAffected as $p) {
                    $p->nilai_wawancara = null;
                    // status jangan diubah ke lolos_verifikasi agar tetap diranking oleh RankingService
                    // (RankingService mengecek status proses_penilaian & menunggu_penetapan)
                    if ($p->status === 'gugur_wawancara') {
                        $p->status = 'menunggu_penetapan'; // Kembalikan ke state normal sebelum digugurkan
                    }
                    $p->save();

                    \App\Models\AuditLog::catat(
                        'Reset Wawancara Otomatis', 
                        "Hasil wawancara dibatalkan otomatis karena terdapat pendaftar baru dengan skor lebih tinggi ({$maxNilaiBaru}).", 
                        Pendaftaran::class, 
                        $p->id
                    );
                }
            }
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
        $catatanInfo = $request->catatan ? " | Catatan: " . $request->catatan : "";

        if ($request->action_type === 'gugurkan') {
            $pendaftaran->status = 'gugur_wawancara';
            $pendaftaran->nilai_wawancara = 0;
            $pendaftaran->save();
            AuditLog::catat('Gugur Wawancara', "Pendaftar ditandai tidak lolos / gugur wawancara." . $catatanInfo, Pendaftaran::class, $pendaftaran->id);
            return redirect()->back()->with('success', 'Pendaftar berhasil digugurkan dari tahap wawancara.');
        }

        if ($request->action_type === 'lolos') {
            $pendaftaran->status = 'lolos_verifikasi';
            $pendaftaran->nilai_wawancara = 100; // Memberi nilai default penuh jika lolos
            $pendaftaran->save();
            AuditLog::catat('Lolos Wawancara', "Pendaftar berhasil lolos tahap wawancara." . $catatanInfo, Pendaftaran::class, $pendaftaran->id);
            return redirect()->back()->with('success', 'Pendaftar berhasil diloloskan dari tahap wawancara.');
        }

        if ($request->action_type === 'batalkan') {
            $pendaftaran->status = 'lolos_verifikasi';
            $pendaftaran->nilai_wawancara = null;
            $pendaftaran->save();
            AuditLog::catat('Reset Wawancara', "Pilihan wawancara dibatalkan." . $catatanInfo, Pendaftaran::class, $pendaftaran->id);
            return redirect()->back()->with('success', 'Status wawancara berhasil di-reset.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }

    // === Penetapan per baris (inline) ===

    public function tetapkanSatu(Request $request, $id, PenetapanService $penetapanService)
    {
        $keputusan = $request->keputusan;
        if (!in_array($keputusan, ['lulus', 'tidak_lulus', 'batal'])) {
            return redirect()->back()->with('error', 'Keputusan tidak valid.');
        }

        if ($keputusan === 'batal') {
            $pendaftaran = Pendaftaran::findOrFail($id);
            // Hanya bisa dibatalkan jika statusnya saat ini lulus atau tidak lulus
            if (in_array($pendaftaran->status, ['lulus', 'tidak_lulus'])) {
                // Hapus penerima beasiswa jika ada
                \App\Models\PenerimaBeasiswa::where('pendaftaran_id', $pendaftaran->id)->delete();
                // Hapus record penetapan terakhir
                \App\Models\PenetapanModel::where('pendaftaran_id', $pendaftaran->id)->delete();
                
                $pendaftaran->status = 'menunggu_penetapan';
                $pendaftaran->save();

                \App\Models\AuditLog::catat(
                    'Batal Penetapan',
                    "Penetapan beasiswa (SK) dibatalkan secara manual.",
                    Pendaftaran::class,
                    $pendaftaran->id
                );

                return redirect()->back()->with('success', 'Penetapan beasiswa berhasil dibatalkan.');
            }
            return redirect()->back()->with('error', 'Status pendaftar ini belum ditetapkan.');
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


    public function rekapData(Request $request)
    {
        $query = \App\Models\Pendaftaran::with(['identitas.desa.kecamatan', 'program', 'jalur', 'rekomendasiDesa']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_pendaftaran', 'like', '%' . $request->search . '%')
                  ->orWhereHas('identitas', function($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->program_id) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->kecamatan_id) {
            $query->whereHas('identitas', fn($q) => $q->where('kecamatan_id', $request->kecamatan_id));
        }

        if ($request->desa_id) {
            $query->whereHas('identitas', fn($q) => $q->where('desa_id', $request->desa_id));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->verifikasi_tipe) {
            if ($request->verifikasi_tipe === 'sistem') {
                $query->whereHas('rekomendasiDesa', function($q) {
                    $q->whereNull('dpmd_verified_by')->whereNull('kecamatan_verified_by');
                });
            } elseif ($request->verifikasi_tipe === 'manual') {
                $query->whereHas('rekomendasiDesa', function($q) {
                    $q->where(function($q2) {
                        $q2->whereNotNull('dpmd_verified_by')->orWhereNotNull('kecamatan_verified_by');
                    });
                });
            }
        }

        $pendaftar = $query->latest()->paginate(20)->withQueryString();
        
        $programs = \App\Models\Program::orderBy('urutan')->get();
        $kecamatanList = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        $desaList = \App\Models\Desa::orderBy('nama_desa')->get();

        return view('kabupaten.rekap_data', compact('pendaftar', 'programs', 'kecamatanList', 'desaList'));
    }
}
