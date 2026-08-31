<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePendaftaranRequest;
use App\Models\Desa;
use App\Models\Jalur;
use App\Models\Kecamatan;
use App\Models\Pendaftaran;
use App\Models\Periode;
use App\Models\Program;
use App\Models\HistoriPenerima;
use App\Services\RegistrationService;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PendaftaranController extends Controller
{
    /**
     * Halaman pilih program beasiswa (publik).
     */
    public function index()
    {
        $periodeAktif = Periode::aktif()->first();
        $programs = $periodeAktif
            ? Program::where('periode_id', $periodeAktif->id)->aktif()
                ->with(['jalurs', 'jalurs.dokumens' => fn($q) => $q->where('wajib', true)->orderBy('urutan')])
                ->orderBy('urutan')->get()
            : collect();

        return view('pendaftaran.index', compact('programs', 'periodeAktif'));
    }

    /**
     * Form pendaftaran multi-step (wizard).
     */
    public function create($programSlug, $jalurSlug)
    {
        $program = Program::where('slug', $programSlug)->firstOrFail();
        $jalur = Jalur::where('program_id', $program->id)->where('slug', $jalurSlug)->firstOrFail();
        $periode = Periode::aktif()->first();

        if (!$periode || $program->status_pendaftaran !== 'Sedang Dibuka') {
            return redirect()->route('pendaftaran.index')
                ->with('error', 'Pendaftaran untuk program ini belum/sudah ditutup.');
        }

        // Data dinamis dari Master Data
        $persyaratans = $jalur->persyaratans()->where('aktif', true)->orderBy('urutan')->get();
        $dokumens = $jalur->dokumens()->orderBy('urutan')->get();
        $kelompokKriterias = $jalur->kelompokKriterias()->with('kriterias.pilihans')->orderBy('urutan')->get();
        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();
        $customFields = $jalur->customFields()->aktif()->orderBy('penempatan')->orderBy('urutan')->get();

        return view('pendaftaran.create', compact(
            'program', 'jalur', 'periode', 'persyaratans', 'dokumens',
            'kelompokKriterias', 'kecamatanList', 'customFields'
        ));
    }

    /**
     * Simpan pendaftaran — data dinormalisasi ke 4+ tabel.
     */
    public function store(StorePendaftaranRequest $request, RegistrationService $registrationService)
    {
        $program = Program::where('slug', $request->program_slug)->firstOrFail();
        $jalur = Jalur::where('program_id', $program->id)->where('slug', $request->jalur_slug)->firstOrFail();
        $periode = Periode::aktif()->firstOrFail();

        $validated = $request->validated();

        try {
            $pendaftaran = $registrationService->register($validated, $request->allFiles(), $program, $jalur, $periode);

            return redirect()->route('pendaftaran.bukti', $pendaftaran->id)
                ->with('success', 'Pendaftaran berhasil! Nomor: ' . $pendaftaran->nomor_pendaftaran);

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function buktiPendaftaran($id)
    {
        $pendaftaran = Pendaftaran::with(['program', 'jalur', 'periode', 'identitas.desa', 'identitas.kecamatan', 'uploadDokumens.dokumen'])->findOrFail($id);
        return view('pendaftaran.bukti', compact('pendaftaran'));
    }

    // === API ===
    public function getDesaByKecamatan($kecamatanId)
    {
        return response()->json(Desa::where('kecamatan_id', $kecamatanId)->orderBy('nama_desa')->get(['id', 'nama_desa']));
    }

    public function cekNik(\Illuminate\Http\Request $request, RegistrationService $registrationService)
    {
        $request->validate([
            'nik' => 'required|string|size:16',
            'program_slug' => 'required|string',
        ]);

        $program = Program::where('slug', $request->program_slug)->first();
        $periode = Periode::aktif()->first();

        if (!$program || !$periode) {
            return response()->json([
                'available' => false,
                'message' => 'Program atau periode aktif tidak ditemukan.'
            ], 400);
        }

        try {
            $registrationService->validateNikAvailability($request->nik, $program, $periode);
            return response()->json([
                'available' => true,
                'message' => 'NIK dapat digunakan untuk mendaftar.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'available' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function downloadSertifikat($id)
    {
        $pendaftaran = Pendaftaran::with(['program', 'periode'])->findOrFail($id);

        if (!in_array($pendaftaran->status, ['lulus', 'sk_terbit'])) {
            abort(403, 'Surat Keterangan hanya tersedia bagi pendaftar yang Lolos/Ditetapkan.');
        }

        $validationUrl = route('pendaftaran.validasi.sertifikat', $pendaftaran->nomor_pendaftaran);
        
        // Generate QR code in SVG format (base64) to avoid ext-imagick dependency
        $qrCode = base64_encode(QrCode::format('svg')->size(150)->generate($validationUrl));

        $pdf = Pdf::loadView('pendaftaran.sertifikat_pdf', compact('pendaftaran', 'qrCode'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Bukti_Lulus_Beasiswa_' . $pendaftaran->nomor_pendaftaran . '.pdf');
    }

    public function validasiSertifikat($nomor_pendaftaran)
    {
        $pendaftaran = Pendaftaran::with(['program', 'periode'])
            ->where('nomor_pendaftaran', $nomor_pendaftaran)
            ->firstOrFail();

        return view('pendaftaran.validasi_sertifikat', compact('pendaftaran'));
    }

    public function downloadSertifikatHistori($id)
    {
        $pendaftaran = HistoriPenerima::findOrFail($id);

        $validationUrl = route('pendaftaran.validasi.histori', $pendaftaran->nomor_pendaftaran);
        
        $qrCode = base64_encode(QrCode::format('svg')->size(150)->generate($validationUrl));

        $pdf = Pdf::loadView('pendaftaran.sertifikat_pdf', compact('pendaftaran', 'qrCode'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Bukti_Lulus_Beasiswa_' . $pendaftaran->nomor_pendaftaran . '.pdf');
    }

    public function validasiSertifikatHistori($nomor_pendaftaran)
    {
        $pendaftaran = HistoriPenerima::where('nomor_pendaftaran', $nomor_pendaftaran)
            ->firstOrFail();

        return view('pendaftaran.validasi_sertifikat', compact('pendaftaran'));
    }
}
