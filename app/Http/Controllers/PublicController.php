<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\PendaftaranIdentitas;
use App\Models\Periode;
use App\Models\Program;
use App\Models\DokumenPublik;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    public function home()
    {
        $periodeAktif = Periode::aktif()->first();
        $programs = $periodeAktif
            ? Program::where('periode_id', $periodeAktif->id)
                ->aktif()
                ->with('jalurs')
                ->withCount(['pendaftarans' => function ($q) {
                    $q->where('status', '!=', 'draft');
                }])
                ->orderBy('urutan')
                ->get()
            : collect();
        $dokumenPubliks = DokumenPublik::aktif()->ordered()->get();
        $totalPendaftar = 0;
        $totalLulus = 0;

        if ($periodeAktif) {
            $totalPendaftar = Pendaftaran::where('periode_id', $periodeAktif->id)
                ->where('status', '!=', 'draft')
                ->count();
            $totalLulus = Pendaftaran::where('periode_id', $periodeAktif->id)
                ->where('status', 'lulus')
                ->count();
        }

        return view('public.home', compact('programs', 'periodeAktif', 'dokumenPubliks', 'totalPendaftar', 'totalLulus'));
    }

    public function informasi()
    {
        $periodeAktif = Periode::aktif()->first();
        $programs = $periodeAktif
            ? Program::where('periode_id', $periodeAktif->id)->aktif()->with('jalurs.persyaratans')->orderBy('urutan')->get()
            : collect();
        $dokumenPubliks = DokumenPublik::aktif()->ordered()->get();

        return view('public.informasi_umum', compact('programs', 'periodeAktif', 'dokumenPubliks'));
    }

    public function faq()
    {
        try {
            $faqs = Faq::aktif()->ordered()->get();
        } catch (\Exception $e) {
            $faqs = collect();
        }
        return view('public.faq', compact('faqs'));
    }

    public function kriteriaPersyaratan()
    {
        $periodeAktif = Periode::aktif()->first();
        $programs = $periodeAktif
            ? Program::where('periode_id', $periodeAktif->id)->aktif()
                ->with(['jalurs.persyaratans', 'jalurs.dokumens', 'jalurs.kelompokKriterias.kriterias.pilihans', 'jalurs.kelompokKriterias.bobotPenilaian'])
                ->orderBy('urutan')->get()
            : collect();
        $dokumenPubliks = DokumenPublik::aktif()->ordered()->get();

        return view('public.kriteria_persyaratan', compact('programs', 'periodeAktif', 'dokumenPubliks'));
    }

    public function downloadDokumenPublik($id)
    {
        $dokumen = DokumenPublik::findOrFail($id);

        if (!$dokumen->status_aktif && !auth()->check()) {
            abort(404, 'Dokumen tidak ditemukan atau tidak aktif.');
        }

        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            abort(404, 'File dokumen tidak ditemukan pada server.');
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->file_name);
    }

    public function seleksiPenetapan()
    {
        $periodeAktif = Periode::aktif()->first();
        $programs = $periodeAktif
            ? Program::where('periode_id', $periodeAktif->id)->aktif()->with('jalurs')->orderBy('urutan')->get()
            : collect();

        $penerima = [];
        if ($periodeAktif) {
            foreach ($programs as $prog) {
                foreach ($prog->jalurs as $jalur) {
                    $penerima[$jalur->id] = Pendaftaran::with('identitas')
                        ->where('jalur_id', $jalur->id)
                        ->where('periode_id', $periodeAktif->id)
                        ->where('status', 'lulus')
                        ->orderBy('ranking')->get();
                }
            }
        }

        return view('public.seleksi_penetapan', compact('programs', 'periodeAktif', 'penerima'));
    }

    private function getTahunOptions(): array
    {
        $periodeYears = Periode::pluck('tahun')->filter()->unique();
        $pendaftaranYears = Pendaftaran::pluck('tahun')->filter()->unique();
        $currentYear = (int) date('Y');

        $years = collect([$currentYear])
            ->merge($periodeYears)
            ->merge($pendaftaranYears)
            ->map(fn($y) => (string) $y)
            ->unique()
            ->sortDesc();

        return $years->mapWithKeys(fn($y) => [$y => 'Tahun ' . $y])->toArray();
    }

    public function cekStatus()
    {
        $tahunOptions = $this->getTahunOptions();
        $tahunAktif = (string) (Periode::aktif()->value('tahun') ?? date('Y'));

        return view('public.cek_status', compact('tahunOptions', 'tahunAktif'));
    }

    public function cekStatusProses(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16',
            'tahun' => 'required|numeric|digits:4',
        ]);

        $tahunOptions = $this->getTahunOptions();
        $tahunAktif = (string) (Periode::aktif()->value('tahun') ?? date('Y'));

        $pendaftarans = Pendaftaran::with(['program', 'jalur', 'periode', 'identitas', 'uploadDokumens.dokumen'])
            ->where('tahun', $request->tahun)
            ->whereHas('identitas', fn($q) => $q->where('nik', $request->nik))
            ->latest()->get();

        $historiPenerimas = \App\Models\HistoriPenerima::where('tahun', $request->tahun)
            ->where('nik', $request->nik)
            ->latest()->get();

        return view('public.cek_status', compact('pendaftarans', 'historiPenerimas', 'tahunOptions', 'tahunAktif'));
    }
}
