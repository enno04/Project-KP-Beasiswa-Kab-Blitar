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

        // --- Data untuk Fitur Alert Pintar ---
        $butuhPenilaian = (clone $pendaftaranQuery)
            ->where('status', 'lolos_verifikasi')
            ->where('total_nilai', '<=', 0)
            ->count();

        $menungguPenetapan = (clone $pendaftaranQuery)
            ->where('status', 'lolos_verifikasi')
            ->where('ranking', 1)
            ->where('total_nilai', '>', 0)
            ->doesntHave('rekomendasiDesa')
            ->first();

        // --- Data untuk Hitung Mundur Periode ---
        $periodeAktif = \App\Models\Periode::aktif()->first();

        $stats = [
            'total' => (clone $pendaftaranQuery)->count(),
            'menunggu_verifikasi' => (clone $pendaftaranQuery)->where('status', 'menunggu_verifikasi')->count(),
            'lolos_verifikasi' => (clone $pendaftaranQuery)->where('status', 'lolos_verifikasi')->count(),
            'sudah_dinilai' => (clone $pendaftaranQuery)->where('total_nilai', '>', 0)->count(),
            'lulus' => (clone $pendaftaranQuery)->where('status', 'lulus')->count(),
        ];

        $rekomendasiDesa = \App\Models\RekomendasiDesa::where('desa_id', $desaId)->count();

        // 1. Pendaftar Baru & Sedang Diproses
        $pendaftarBaru = (clone $pendaftaranQuery)
            ->whereIn('status', ['draft', 'menunggu_verifikasi', 'sedang_diverifikasi'])
            ->latest()
            ->paginate(5, ['*'], 'page_baru');

        // 2. Siap Dinilai / Dirangking (Lolos Verifikasi)
        $pendaftarProses = (clone $pendaftaranQuery)
            ->where('status', 'lolos_verifikasi')
            ->orderByRaw('ranking IS NULL, ranking ASC')
            ->orderBy('total_nilai', 'desc')
            ->latest()
            ->paginate(5, ['*'], 'page_proses');

        // 3. Selesai (Diteruskan ke Kecamatan, Lulus, dll)
        $pendaftarSelesai = (clone $pendaftaranQuery)
            ->whereIn('status', ['diteruskan_ke_kecamatan', 'menunggu_penetapan', 'lulus'])
            ->latest()
            ->paginate(5, ['*'], 'page_selesai');

        // 4. Ditolak / Gugur
        $pendaftarGagal = (clone $pendaftaranQuery)
            ->whereIn('status', ['tidak_lolos_verifikasi', 'tidak_lolos_desa', 'ditolak_kecamatan', 'ditolak_dpmd', 'tidak_lulus'])
            ->latest()
            ->paginate(5, ['*'], 'page_gagal');

        // Cek status rekomendasi desa
        $rekomendasiDesa = \App\Models\RekomendasiDesa::where('desa_id', $desaId)->latest()->first();

        return view('desa.dashboard', compact('stats', 'pendaftarBaru', 'pendaftarProses', 'pendaftarSelesai', 'pendaftarGagal', 'rekomendasiDesa', 'butuhPenilaian', 'menungguPenetapan', 'periodeAktif'));
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
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_pendaftaran', 'like', '%' . $request->search . '%')
                  ->orWhereHas('identitas', function($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->search . '%')
                         ->orWhere('nik', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $periodeAktif = \App\Models\Periode::where('tahun', $tahun)->first();

        // Urutkan
        if ($request->sort === 'terbaru') {
            $query->latest();
        } elseif ($request->sort === 'nilai_tertinggi') {
            $query->orderBy('total_nilai', 'desc')->latest();
        } elseif ($request->sort === 'ranking') {
            $query->orderByRaw('ranking IS NULL, ranking ASC')->latest();
        } else {
            // Default
            $query->orderByRaw('ranking IS NULL, ranking ASC')
                  ->orderBy('total_nilai', 'desc')
                  ->latest();
        }

        $sudahDitetapkan = false;
        $belumDinilai = false;
        $sudahDinilai = false;
        if ($program->isSdss() && $periodeAktif) {
            $sudahDitetapkan = \App\Models\RekomendasiDesa::where('desa_id', $user->desa_id)
                ->whereHas('pendaftaran', function($q) use ($program, $periodeAktif) {
                    $q->where('program_id', $program->id)
                      ->where('periode_id', $periodeAktif->id);
                })->exists();

            $belumDinilai = Pendaftaran::where('jalur_id', $jalur->id ?? 0)
                ->where('periode_id', $periodeAktif->id)
                ->where('status', 'lolos_verifikasi')
                ->where(function($q) {
                    $q->whereNull('total_nilai')->orWhere('total_nilai', '<=', 0);
                })
                ->whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))
                ->exists();
                
            $sudahDinilai = Pendaftaran::where('jalur_id', $jalur->id ?? 0)
                ->where('periode_id', $periodeAktif->id)
                ->where('status', 'lolos_verifikasi')
                ->where('total_nilai', '>', 0)
                ->whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))
                ->exists();
        }

        $pendaftar = $query->paginate(20)->withQueryString();

        return view('desa.index', compact('program', 'jalur', 'pendaftar', 'tahun', 'periodeAktif', 'sudahDitetapkan', 'belumDinilai', 'sudahDinilai'));
    }

    public function exportData(Request $request, $programSlug, $jalurSlug = null)
    {
        $user = auth()->user();
        $program = Program::with('jalurs')->where('slug', $programSlug)->firstOrFail();
        
        $jalur = null;
        if ($jalurSlug) {
            $jalur = Jalur::where('program_id', $program->id)->where('slug', $jalurSlug)->firstOrFail();
        } elseif ($program->jalurs->count() === 1) {
            $jalur = $program->jalurs->first();
        }

        $query = Pendaftaran::with(['identitas.desa', 'identitas.kecamatan', 'jalur'])
            ->whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))
            ->where('program_id', $program->id);

        if ($jalur) $query->where('jalur_id', $jalur->id);
        if ($request->tahun) $query->where('tahun', $request->tahun);
        
        if ($request->export_type === 'lolos_saja') {
            $query->where(function($q) {
                $q->whereIn('status', ['diteruskan_ke_kecamatan', 'menunggu_penetapan', 'lulus'])
                  ->orWhereHas('rekomendasiDesa');
            });
        } elseif ($request->export_type === 'tidak_lolos') {
            $query->where('status', 'tidak_lolos_desa');
        } elseif ($request->export_type === 'sudah_dinilai') {
            $query->where('total_nilai', '>', 0);
        } elseif ($request->export_type === 'belum_dinilai') {
            $query->where(function($q) {
                $q->whereNull('total_nilai')->orWhere('total_nilai', '<=', 0);
            });
        } elseif ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_pendaftaran', 'like', '%' . $request->search . '%')
                  ->orWhereHas('identitas', function($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->search . '%')
                         ->orWhere('nik', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Urutkan
        if ($request->sort === 'terbaru') {
            $query->latest();
        } elseif ($request->sort === 'nilai_tertinggi') {
            $query->orderBy('total_nilai', 'desc')->latest();
        } elseif ($request->sort === 'ranking') {
            $query->orderByRaw('ranking IS NULL, ranking ASC')->latest();
        } else {
            $query->orderByRaw('ranking IS NULL, ranking ASC')->orderBy('total_nilai', 'desc')->latest();
        }

        $pendaftarans = $query->get();
        
        if ($pendaftarans->isEmpty()) {
            $pesanError = 'Tidak ada data pendaftar yang dapat diexport dengan kriteria tersebut.';
            if ($request->export_type === 'lolos_saja') {
                $pesanError = 'Belum ada pendaftar yang ditetapkan lolos/diteruskan ke kecamatan.';
            } elseif ($request->export_type === 'tidak_lolos') {
                $pesanError = 'Belum ada pendaftar yang digugurkan / tidak dipilih desa.';
            } elseif ($request->export_type === 'sudah_dinilai') {
                $pesanError = 'Belum ada pendaftar yang selesai dinilai.';
            } elseif ($request->export_type === 'belum_dinilai') {
                $pesanError = 'Semua pendaftar sudah dinilai.';
            }
            return redirect()->back()->with('error', $pesanError);
        }

        $jenisExport = 'Semua_Pendaftar';
        if ($request->export_type === 'lolos_saja') {
            $jenisExport = 'Lolos';
        } elseif ($request->export_type === 'tidak_lolos') {
            $jenisExport = 'Tidak_Dipilih_Desa';
        } elseif ($request->export_type === 'sudah_dinilai') {
            $jenisExport = 'Sudah_Dinilai';
        } elseif ($request->export_type === 'belum_dinilai') {
            $jenisExport = 'Belum_Dinilai';
        }

        // Membuat singkatan untuk nama program (Contoh: "Satu Desa Satu Sarjana" menjadi "SDSS")
        $programSingkatan = '';
        foreach (explode(' ', $program->nama) as $kata) {
            $programSingkatan .= strtoupper(substr($kata, 0, 1));
        }

        $fileNameParts = ['Data_Pendaftar', $jenisExport, $programSingkatan];
        
        if ($jalur) {
            // Mengkapitalkan awal kata jalur dan mengubah spasi jadi underscore
            $jalurNama = ucwords(strtolower($jalur->nama));
            $fileNameParts[] = str_replace(' ', '_', $jalurNama);
        }
        
        $fileNameParts[] = date('Ymd');
        $fileName = implode('_', $fileNameParts) . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\DataPendaftarExport($pendaftarans), $fileName);
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
            return redirect()->back()->with('success', 'Surat Rekomendasi dan Berita Acara berhasil diunggah. Pendaftaran telah diteruskan ke Kecamatan & DPMD.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // === Penilaian & Ranking (Khusus SDSS) ===

    public function hitungPenilaian(HitungPenilaianRequest $request, PenilaianService $penilaianService, \App\Services\RankingService $rankingService)
    {
        $user = auth()->user();

        $jalur = \App\Models\Jalur::with(['program', 'program.periode'])->find($request->jalur_id);
        $tutup = null;
        if ($jalur && $jalur->program) {
            $tutup = $jalur->program->tanggal_tutup ?? ($jalur->program->periode->tanggal_selesai ?? null);
        }

        if ($jalur && $jalur->program->kunci_hitung_nilai && $tutup && \Carbon\Carbon::now()->startOfDay()->lte(\Carbon\Carbon::parse($tutup)->endOfDay())) {
            return redirect()->back()->with('error', 'Penilaian untuk program ini sedang dikunci. Penilaian baru bisa dilakukan setelah pendaftaran ditutup.');
        }

        // Validasi: Cek apakah masih ada pendaftar yang BELUM diverifikasi OPD
        $belumSelesaiVerifikasi = \App\Models\Pendaftaran::where('jalur_id', $request->jalur_id)
            ->where('periode_id', $request->periode_id)
            ->whereIn('status', ['menunggu_verifikasi', 'sedang_diverifikasi'])
            ->whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))
            ->exists();

        if ($belumSelesaiVerifikasi) {
            return redirect()->back()->with('error', 'Terdapat pendaftar dari desa Anda yang belum selesai diverifikasi oleh pihak OPD (Dispora). Penilaian dan Perangkingan hanya bisa dilakukan setelah semua berkas pendaftar berstatus Lolos atau Ditolak Verifikasi.');
        }

        // Validasi: Cek apakah masih ada pendaftar yang belum dinilai
        $belumDinilai = Pendaftaran::where('jalur_id', $request->jalur_id)
            ->where('periode_id', $request->periode_id)
            ->where('status', 'lolos_verifikasi')
            ->where(function($q) {
                $q->whereNull('total_nilai')->orWhere('total_nilai', '<=', 0);
            })
            ->whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))
            ->exists();

        // Cek jika perwakilan sudah ditetapkan
        $sudahDitetapkan = \App\Models\RekomendasiDesa::where('desa_id', $user->desa_id)
            ->whereHas('pendaftaran', function($q) use ($request) {
                $q->where('jalur_id', $request->jalur_id)
                  ->where('periode_id', $request->periode_id);
            })->exists();

        if ($sudahDitetapkan) {
            return redirect()->back()->with('error', 'Perwakilan desa telah ditetapkan. Anda tidak dapat menghitung ulang nilai dan merubah ranking pendaftar.');
        }

        $adaPendaftar = Pendaftaran::where('jalur_id', $request->jalur_id)
            ->where('periode_id', $request->periode_id)
            ->where('status', 'lolos_verifikasi')
            ->whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))
            ->exists();

        if ($adaPendaftar && !$belumDinilai) {
            return redirect()->back()->with('error', 'Semua pendaftar lolos verifikasi dari desa Anda sudah dinilai. Tidak perlu melakukan penilaian ulang.');
        }
        
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

        $jalur = \App\Models\Jalur::with(['program', 'program.periode'])->find($request->jalur_id);
        $tutup = null;
        if ($jalur && $jalur->program) {
            $tutup = $jalur->program->tanggal_tutup ?? ($jalur->program->periode->tanggal_selesai ?? null);
        }

        if ($jalur && $jalur->program->kunci_hitung_nilai && $tutup && \Carbon\Carbon::now()->startOfDay()->lte(\Carbon\Carbon::parse($tutup)->endOfDay())) {
            return redirect()->back()->with('error', 'Perangkingan untuk program ini sedang dikunci. Perangkingan baru bisa dilakukan setelah pendaftaran ditutup.');
        }

        // Validasi: Cek apakah masih ada pendaftar yang BELUM diverifikasi OPD
        $belumSelesaiVerifikasi = \App\Models\Pendaftaran::where('jalur_id', $request->jalur_id)
            ->where('periode_id', $request->periode_id)
            ->whereIn('status', ['menunggu_verifikasi', 'sedang_diverifikasi'])
            ->whereHas('identitas', fn($q) => $q->where('desa_id', $user->desa_id))
            ->exists();

        if ($belumSelesaiVerifikasi) {
            return redirect()->back()->with('error', 'Terdapat pendaftar dari desa Anda yang belum selesai diverifikasi oleh pihak OPD (Dispora). Perangkingan hanya bisa dilakukan setelah semua berkas pendaftar berstatus Lolos atau Ditolak Verifikasi.');
        }

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
            return redirect()->back()
                ->with('success', 'Berhasil menetapkan perwakilan. Pendaftar lain dari desa ini telah otomatis digugurkan. Silakan Export data atau unggah Surat Rekomendasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menetapkan perwakilan: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat Keputusan / Penetapan Desa
     */
    public function riwayat(Request $request)
    {
        $user = auth()->user();
        
        $query = \App\Models\RekomendasiDesa::with(['pendaftaran.identitas', 'kecamatanVerifier'])
            ->where('desa_id', $user->desa_id);

        if ($request->search) {
            $query->whereHas('pendaftaran', function($q) use ($request) {
                $q->where('nomor_pendaftaran', 'like', '%' . $request->search . '%')
                  ->orWhereHas('identitas', function($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->status) {
            $query->where('status_kecamatan', $request->status);
        }

        if ($request->status_dpmd) {
            $query->where('status_dpmd', $request->status_dpmd);
        }

        if ($request->sort === 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $riwayat = $query->paginate(20)->withQueryString();

        return view('desa.riwayat', compact('riwayat'));
    }
}
