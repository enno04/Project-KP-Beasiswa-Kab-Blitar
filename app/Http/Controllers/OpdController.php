<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifikasiDokumenRequest;
use App\Models\Dokumen;
use App\Models\UploadDokumen;
use App\Models\VerifikasiDokumen;
use App\Services\VerifikasiService;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $opdId = $user->opd_id;
        $dokumenIds = Dokumen::where('opd_id', $opdId)->pluck('id');

        $stats = [
            'belum_diverifikasi' => UploadDokumen::whereIn('dokumen_id', $dokumenIds)
                ->where('status', 'belum_diverifikasi')
                ->whereHas('pendaftaran', fn($q) => $q->opdActive())
                ->count(),
            'valid' => UploadDokumen::whereIn('dokumen_id', $dokumenIds)->where('status', 'valid')->count(),
            'tidak_valid' => UploadDokumen::whereIn('dokumen_id', $dokumenIds)->where('status', 'tidak_valid')->count(),
        ];

        $totalDokumen = $stats['belum_diverifikasi'] + $stats['valid'] + $stats['tidak_valid'];
        $persentase = $totalDokumen > 0 ? round((($stats['valid'] + $stats['tidak_valid']) / $totalDokumen) * 100) : 0;

        $aktivitasTerbaru = UploadDokumen::with(['pendaftaran.identitas', 'dokumen'])
            ->whereIn('dokumen_id', $dokumenIds)
            ->where('status', 'belum_diverifikasi')
            ->whereHas('pendaftaran', fn($q) => $q->opdActive())
            ->latest()
            ->take(5)
            ->get();

        $slaWarning = UploadDokumen::whereIn('dokumen_id', $dokumenIds)
            ->where('status', 'belum_diverifikasi')
            ->where('created_at', '<=', now()->subDays(3))
            ->count();

        $breakdownProgram = UploadDokumen::whereIn('upload_dokumens.dokumen_id', $dokumenIds)
            ->where('upload_dokumens.status', 'belum_diverifikasi')
            ->join('pendaftarans', 'upload_dokumens.pendaftaran_id', '=', 'pendaftarans.id')
            ->whereHas('pendaftaran', fn($q) => $q->opdActive())
            ->join('programs', 'pendaftarans.program_id', '=', 'programs.id')
            ->select('programs.nama', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('programs.nama')
            ->get();

        $dokumenDitolak = VerifikasiDokumen::with(['uploadDokumen.dokumen', 'uploadDokumen.pendaftaran.identitas'])
            ->whereHas('uploadDokumen', fn($q) => $q->whereIn('dokumen_id', $dokumenIds))
            ->where('hasil', 'tidak_valid')
            ->latest()
            ->take(5)
            ->get();

        return view('opd.dashboard', compact('stats', 'totalDokumen', 'persentase', 'aktivitasTerbaru', 'slaWarning', 'breakdownProgram', 'dokumenDitolak'));
    }

    public function verifikasiIndex(Request $request, VerifikasiService $verifikasiService)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $statusReq = $request->input('status', 'belum_diverifikasi');
        $statusFilter = $statusReq === 'semua' ? null : $statusReq;

        $dokumen = $verifikasiService->getDokumenByOpd(
            (int) $user->opd_id, 
            $statusFilter, 
            $request->filled('program_id') ? (int) $request->input('program_id') : null,
            $request->filled('kecamatan_id') ? (int) $request->input('kecamatan_id') : null,
            $request->filled('desa_id') ? (int) $request->input('desa_id') : null,
            $request->filled('search') ? (string) $request->input('search') : null
        );
        $programs = \App\Models\Program::all();
        $kecamatans = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        $desas = [];
        
        if ($request->kecamatan_id) {
            $desas = \App\Models\Desa::where('kecamatan_id', $request->kecamatan_id)->orderBy('nama_desa')->get();
        }

        return view('opd.verifikasi.index', compact('dokumen', 'programs', 'kecamatans', 'desas'));
    }

    public function verifikasiShow($id)
    {
        $user = auth()->user();
        $dokumenIds = Dokumen::where('opd_id', $user->opd_id)->pluck('id');

        $upload = UploadDokumen::with(['dokumen', 'pendaftaran.identitas', 'pendaftaran.program', 'verifikasis.user'])
            ->whereIn('dokumen_id', $dokumenIds)->findOrFail($id);

        return view('opd.verifikasi.show', compact('upload'));
    }

    public function verifikasiStore(VerifikasiDokumenRequest $request, $id, VerifikasiService $verifikasiService)
    {
        $result = $verifikasiService->verifikasiDokumen($id, $request->hasil, $request->catatan);

        // Cek apakah semua dokumen wajib sudah valid
        $upload = UploadDokumen::find($id);
        if ($upload && $request->hasil === 'valid') {
            $verifikasiService->cekStatusVerifikasi($upload->pendaftaran);
        }

        $redirectUrl = $request->filled('redirect_to') ? $request->input('redirect_to') : route('opd.verifikasi.index');
        return redirect($redirectUrl)->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function riwayat(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $dokumenIds = Dokumen::where('opd_id', $user->opd_id)->pluck('id');

        $query = VerifikasiDokumen::with(['uploadDokumen.dokumen', 'uploadDokumen.pendaftaran.identitas', 'uploadDokumen.pendaftaran.program', 'user'])
            ->whereHas('uploadDokumen', fn($q) => $q->whereIn('dokumen_id', $dokumenIds));

        // Filter: Status (valid/tidak_valid)
        if ($request->filled('status')) {
            $query->where('hasil', $request->status);
        }

        // Filter: Tanggal Verifikasi
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('created_at', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        // Filter: Program
        if ($request->filled('program_id')) {
            $query->whereHas('uploadDokumen.pendaftaran', fn($q) => $q->where('program_id', $request->program_id));
        }

        // Filter: Pencarian (Nama, NIK, No Pendaftaran)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('uploadDokumen.pendaftaran', function($q) use ($search) {
                $q->where('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhereHas('identitas', function($q2) use ($search) {
                      $q2->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        $riwayat = $query->latest()->paginate(20)->withQueryString();
        $programs = \App\Models\Program::all();

        return view('opd.riwayat', compact('riwayat', 'programs'));
    }
}
