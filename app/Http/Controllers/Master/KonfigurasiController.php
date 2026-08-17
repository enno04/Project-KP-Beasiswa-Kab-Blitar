<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Periode;
use App\Models\Program;
use App\Models\Jalur;
use App\Models\Dokumen;
use App\Models\Opd;
use Illuminate\Http\Request;

class KonfigurasiController extends Controller
{
    // === Periode ===
    public function periodeIndex()
    {
        $periode = Periode::search(['tahun', 'nama', 'status'], request('search'))
                          ->sort(request('sort', 'tahun'), request('dir', 'desc'))
                          ->paginate(15)
                          ->withQueryString();
        return view('super-admin.periode.index', compact('periode'));
    }

    public function periodeStore(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2020|max:2040|unique:periode',
            'nama' => 'required|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai',
        ]);
        $p = Periode::create($request->only(['tahun', 'nama', 'tanggal_mulai', 'tanggal_selesai', 'status']));
        AuditLog::catat('Tambah Periode', "Periode: {$p->nama}", Periode::class, $p->id, null, $p->only(['tahun', 'nama', 'status', 'tanggal_mulai', 'tanggal_selesai']));
        return redirect()->route('super-admin.master.periode.index')->with('success', 'Periode berhasil ditambahkan.');
    }

    public function periodeUpdate(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|integer|unique:periode,tahun,' . $id,
            'nama' => 'required|string|max:255',
            'status' => 'required|in:draft,aktif,selesai',
        ]);
        $p = Periode::findOrFail($id);
        $dataLama = $p->only(['tahun', 'nama', 'status', 'tanggal_mulai', 'tanggal_selesai']);
        $p->update($request->only(['tahun', 'nama', 'tanggal_mulai', 'tanggal_selesai', 'status']));
        AuditLog::catat('Ubah Periode', "Periode: {$p->nama}", Periode::class, $p->id, $dataLama, $p->only(['tahun', 'nama', 'status', 'tanggal_mulai', 'tanggal_selesai']));
        return redirect()->route('super-admin.master.periode.index')->with('success', 'Periode berhasil diperbarui.');
    }

    public function periodeDestroy($id)
    {
        $p = Periode::findOrFail($id);
        AuditLog::catat('Hapus Periode', "Periode: {$p->nama}", Periode::class, $p->id, $p->only(['tahun', 'nama', 'status']), null);
        $p->delete();
        return redirect()->route('super-admin.master.periode.index')->with('success', 'Periode berhasil dihapus.');
    }

    // === Program ===
    public function programIndex()
    {
        $programs = Program::with(['periode', 'jalurs'])
                        ->search(['nama', 'kode'], request('search'))
                        ->sort(request('sort', 'created_at'), request('dir', 'desc'))
                        ->paginate(15)
                        ->withQueryString();
                        
        $periodeList = Periode::orderBy('tahun', 'desc')->get();
        return view('super-admin.program.index', compact('programs', 'periodeList'));
    }

    public function programStore(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode,id',
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50',
            'aktif' => 'required|boolean',
        ]);
        $prog = Program::create($request->only(['periode_id', 'nama', 'kode', 'deskripsi', 'tanggal_buka', 'tanggal_tutup', 'aktif']));
        AuditLog::catat('Tambah Program', "Program: {$prog->nama}", Program::class, $prog->id, null, $prog->only(['nama', 'kode', 'aktif', 'tanggal_buka', 'tanggal_tutup']));
        return redirect()->route('super-admin.master.program.index')->with('success', 'Program berhasil ditambahkan.');
    }

    public function programUpdate(Request $request, $id)
    {
        $prog = Program::findOrFail($id);
        $dataLama = $prog->only(['nama', 'kode', 'aktif', 'tanggal_buka', 'tanggal_tutup']);
        $prog->update($request->only(['nama', 'kode', 'deskripsi', 'tanggal_buka', 'tanggal_tutup', 'aktif']));
        AuditLog::catat('Ubah Program', "Program: {$prog->nama}", Program::class, $prog->id, $dataLama, $prog->only(['nama', 'kode', 'aktif', 'tanggal_buka', 'tanggal_tutup']));
        return redirect()->route('super-admin.master.program.index')->with('success', 'Program berhasil diperbarui.');
    }

    public function programDestroy($id)
    {
        $prog = Program::findOrFail($id);
        AuditLog::catat('Hapus Program', "Program: {$prog->nama}", Program::class, $prog->id, $prog->only(['nama', 'kode', 'aktif']), null);
        $prog->delete();
        return redirect()->route('super-admin.master.program.index')->with('success', 'Program berhasil dihapus.');
    }

    // === Jalur ===
    public function jalurIndex($programId)
    {
        $program = Program::with('jalurs')->findOrFail($programId);
        return view('super-admin.jalur.index', compact('program'));
    }

    public function jalurStore(Request $request, $programId)
    {
        $request->validate(['nama' => 'required|string|max:255', 'kode' => 'required|string|max:50', 'urutan' => 'required|integer']);
        $jalur = Jalur::create(array_merge($request->only(['nama', 'kode', 'deskripsi', 'aktif', 'urutan']), ['program_id' => $programId]));
        AuditLog::catat('Tambah Jalur', "Jalur: {$jalur->nama}", Jalur::class, $jalur->id, null, $jalur->only(['nama', 'kode', 'aktif', 'urutan']));
        return redirect()->route('super-admin.master.jalur.index', $programId)->with('success', 'Jalur berhasil ditambahkan.');
    }

    public function jalurUpdate(Request $request, $programId, $id)
    {
        $jalur = Jalur::findOrFail($id);
        $dataLama = $jalur->only(['nama', 'kode', 'aktif', 'urutan']);
        $jalur->update($request->only(['nama', 'kode', 'deskripsi', 'aktif', 'urutan']));
        AuditLog::catat('Ubah Jalur', "Jalur: {$jalur->nama}", Jalur::class, $jalur->id, $dataLama, $jalur->only(['nama', 'kode', 'aktif', 'urutan']));
        return redirect()->route('super-admin.master.jalur.index', $programId)->with('success', 'Jalur berhasil diperbarui.');
    }

    public function jalurDestroy($programId, $id)
    {
        $jalur = Jalur::findOrFail($id);
        AuditLog::catat('Hapus Jalur', "Jalur: {$jalur->nama}", Jalur::class, $jalur->id, $jalur->only(['nama', 'kode', 'aktif']), null);
        $jalur->delete();
        return redirect()->route('super-admin.master.jalur.index', $programId)->with('success', 'Jalur berhasil dihapus.');
    }

    // === Dokumen per Jalur ===
    public function dokumenIndex($jalurId)
    {
        $jalur = Jalur::with(['dokumens.opd', 'program'])->findOrFail($jalurId);
        $opdList = Opd::orderBy('nama_opd')->get();
        return view('super-admin.dokumen.index', compact('jalur', 'opdList'));
    }

    public function dokumenStore(Request $request, $jalurId)
    {
        $request->validate(['nama' => 'required|string|max:255', 'wajib' => 'required|boolean', 'urutan' => 'required|integer']);
        $dok = Dokumen::create(array_merge($request->only(['nama', 'deskripsi', 'wajib', 'opd_id', 'urutan']), ['jalur_id' => $jalurId]));
        AuditLog::catat('Tambah Dokumen', "Dokumen: {$dok->nama}", Dokumen::class, $dok->id, null, $dok->only(['nama', 'wajib', 'urutan']));
        return redirect()->route('super-admin.master.dokumen.index', $jalurId)->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function dokumenUpdate(Request $request, $jalurId, $id)
    {
        $dok = Dokumen::findOrFail($id);
        $dataLama = $dok->only(['nama', 'wajib', 'urutan', 'opd_id']);
        $dok->update($request->only(['nama', 'deskripsi', 'wajib', 'opd_id', 'urutan']));
        AuditLog::catat('Ubah Dokumen', "Dokumen: {$dok->nama}", Dokumen::class, $dok->id, $dataLama, $dok->only(['nama', 'wajib', 'urutan', 'opd_id']));
        return redirect()->route('super-admin.master.dokumen.index', $jalurId)->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function dokumenDestroy($jalurId, $id)
    {
        $dok = Dokumen::findOrFail($id);
        AuditLog::catat('Hapus Dokumen', "Dokumen: {$dok->nama}", Dokumen::class, $dok->id, $dok->only(['nama', 'wajib', 'urutan']), null);
        $dok->delete();
        return redirect()->route('super-admin.master.dokumen.index', $jalurId)->with('success', 'Dokumen berhasil dihapus.');
    }
}
