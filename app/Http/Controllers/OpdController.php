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
            'belum_diverifikasi' => UploadDokumen::whereIn('dokumen_id', $dokumenIds)->where('status', 'belum_diverifikasi')->count(),
            'valid' => UploadDokumen::whereIn('dokumen_id', $dokumenIds)->where('status', 'valid')->count(),
            'tidak_valid' => UploadDokumen::whereIn('dokumen_id', $dokumenIds)->where('status', 'tidak_valid')->count(),
        ];
        return view('opd.dashboard', compact('stats'));
    }

    public function verifikasiIndex(Request $request, VerifikasiService $verifikasiService)
    {
        $dokumen = $verifikasiService->getDokumenByOpd(auth()->user()->opd_id, $request->status);
        return view('opd.verifikasi.index', compact('dokumen'));
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

        return redirect()->route('opd.verifikasi.index')->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function riwayat()
    {
        $user = auth()->user();
        $dokumenIds = Dokumen::where('opd_id', $user->opd_id)->pluck('id');

        $riwayat = VerifikasiDokumen::with(['uploadDokumen.dokumen', 'uploadDokumen.pendaftaran.identitas', 'user'])
            ->whereHas('uploadDokumen', fn($q) => $q->whereIn('dokumen_id', $dokumenIds))
            ->latest()->paginate(20);

        return view('opd.riwayat', compact('riwayat'));
    }
}
