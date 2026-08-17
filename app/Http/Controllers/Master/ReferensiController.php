<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreDesaRequest;
use App\Http\Requests\Master\StoreKecamatanRequest;
use App\Http\Requests\Master\StoreOpdRequest;
use App\Http\Requests\Master\UpdateDesaRequest;
use App\Http\Requests\Master\UpdateKecamatanRequest;
use App\Http\Requests\Master\UpdateOpdRequest;
use App\Models\AuditLog;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Opd;
use Illuminate\Http\Request;

class ReferensiController extends Controller
{
    // === Kecamatan ===

    public function kecamatanIndex()
    {
        $kecamatan = Kecamatan::withCount('desa')
                        ->search(['nama_kecamatan', 'kode_kecamatan'], request('search'))
                        ->sort(request('sort', 'nama_kecamatan'), request('dir', 'asc'))
                        ->paginate(15)
                        ->withQueryString();
        return view('super-admin.wilayah.kecamatan', compact('kecamatan'));
    }

    public function kecamatanStore(StoreKecamatanRequest $request)
    {
        $kec = Kecamatan::create($request->validated());
        AuditLog::catat('Tambah Kecamatan', "Kecamatan: {$kec->nama_kecamatan}", Kecamatan::class, $kec->id, null, $kec->only(['nama_kecamatan', 'kode_kecamatan']));
        return redirect()->route('super-admin.master.kecamatan.index')->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function kecamatanUpdate(UpdateKecamatanRequest $request, $id)
    {
        $kec = Kecamatan::findOrFail($id);
        $dataLama = $kec->only(['nama_kecamatan', 'kode_kecamatan']);
        $kec->update($request->validated());
        AuditLog::catat('Ubah Kecamatan', "Kecamatan: {$kec->nama_kecamatan}", Kecamatan::class, $kec->id, $dataLama, $kec->only(['nama_kecamatan', 'kode_kecamatan']));
        return redirect()->route('super-admin.master.kecamatan.index')->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function kecamatanDestroy($id)
    {
        $kec = Kecamatan::findOrFail($id);
        AuditLog::catat('Hapus Kecamatan', "Kecamatan: {$kec->nama_kecamatan}", Kecamatan::class, $kec->id, $kec->only(['nama_kecamatan', 'kode_kecamatan']), null);
        $kec->delete();
        return redirect()->route('super-admin.master.kecamatan.index')->with('success', 'Kecamatan berhasil dihapus.');
    }

    // === Desa ===

    public function desaIndex(Request $request)
    {
        $query = Desa::with('kecamatan')
                    ->search(['nama_desa', 'kode_desa'], request('search'));

        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }
        
        $desa = $query->sort(request('sort', 'nama_desa'), request('dir', 'asc'))
                      ->paginate(15)
                      ->withQueryString();
                      
        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();
        return view('super-admin.wilayah.desa', compact('desa', 'kecamatanList'));
    }

    public function desaStore(StoreDesaRequest $request)
    {
        $desa = Desa::create($request->validated());
        AuditLog::catat('Tambah Desa', "Desa: {$desa->nama_desa}", Desa::class, $desa->id, null, $desa->only(['nama_desa', 'kode_desa', 'kecamatan_id']));
        return redirect()->back()->with('success', 'Desa berhasil ditambahkan.');
    }

    public function desaUpdate(UpdateDesaRequest $request, $id)
    {
        $desa = Desa::findOrFail($id);
        $dataLama = $desa->only(['nama_desa', 'kode_desa', 'kecamatan_id']);
        $desa->update($request->validated());
        AuditLog::catat('Ubah Desa', "Desa: {$desa->nama_desa}", Desa::class, $desa->id, $dataLama, $desa->only(['nama_desa', 'kode_desa', 'kecamatan_id']));
        return redirect()->back()->with('success', 'Desa berhasil diperbarui.');
    }

    public function desaDestroy($id)
    {
        $desa = Desa::findOrFail($id);
        AuditLog::catat('Hapus Desa', "Desa: {$desa->nama_desa}", Desa::class, $desa->id, $desa->only(['nama_desa', 'kode_desa', 'kecamatan_id']), null);
        $desa->delete();
        return redirect()->back()->with('success', 'Desa berhasil dihapus.');
    }

    // === OPD ===

    public function opdIndex()
    {
        $opd = Opd::search(['nama_opd', 'singkatan'], request('search'))
                  ->sort(request('sort', 'nama_opd'), request('dir', 'asc'))
                  ->paginate(15)
                  ->withQueryString();
        return view('super-admin.opd.index', compact('opd'));
    }

    public function opdStore(StoreOpdRequest $request)
    {
        $opd = Opd::create($request->validated());
        AuditLog::catat('Tambah OPD', "OPD: {$opd->nama_opd}", Opd::class, $opd->id, null, $opd->only(['nama_opd', 'singkatan']));
        return redirect()->route('super-admin.master.opd.index')->with('success', 'OPD berhasil ditambahkan.');
    }

    public function opdUpdate(UpdateOpdRequest $request, $id)
    {
        $opd = Opd::findOrFail($id);
        $dataLama = $opd->only(['nama_opd', 'singkatan']);
        $opd->update($request->validated());
        AuditLog::catat('Ubah OPD', "OPD: {$opd->nama_opd}", Opd::class, $opd->id, $dataLama, $opd->only(['nama_opd', 'singkatan']));
        return redirect()->route('super-admin.master.opd.index')->with('success', 'OPD berhasil diperbarui.');
    }

    public function opdDestroy($id)
    {
        $opd = Opd::findOrFail($id);
        AuditLog::catat('Hapus OPD', "OPD: {$opd->nama_opd}", Opd::class, $opd->id, $opd->only(['nama_opd', 'singkatan']), null);
        $opd->delete();
        return redirect()->route('super-admin.master.opd.index')->with('success', 'OPD berhasil dihapus.');
    }



    // === API ===

    public function getDesaByKecamatan($kecamatanId)
    {
        return response()->json(Desa::where('kecamatan_id', $kecamatanId)->orderBy('nama_desa')->get(['id', 'nama_desa']));
    }
}
