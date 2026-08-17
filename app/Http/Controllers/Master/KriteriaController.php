<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\KelompokKriteria;
use App\Models\Kriteria;
use App\Models\PilihanKriteria;
use App\Models\BobotPenilaian;
use App\Models\Jalur;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index($jalurId)
    {
        $jalur = Jalur::with(['kelompokKriterias.kriterias.pilihans', 'bobotPenilaians.kelompokKriteria', 'program'])->findOrFail($jalurId);
        return view('super-admin.kriteria.index', compact('jalur'));
    }

    // === Kelompok Kriteria ===
    public function kelompokStore(Request $request, $jalurId)
    {
        $request->validate(['nama' => 'required|string|max:255', 'kode' => 'required|string|max:50', 'urutan' => 'required|integer']);
        $kelompok = KelompokKriteria::create(array_merge($request->only(['nama', 'kode', 'urutan']), ['jalur_id' => $jalurId]));
        AuditLog::catat('Tambah Kelompok Kriteria', "Kelompok: {$kelompok->nama}", KelompokKriteria::class, $kelompok->id, null, $kelompok->only(['nama', 'kode', 'urutan']));
        return redirect()->back()->with('success', 'Kelompok Kriteria berhasil ditambahkan.');
    }

    public function kelompokDestroy($jalurId, $id)
    {
        $kelompok = KelompokKriteria::findOrFail($id);
        AuditLog::catat('Hapus Kelompok Kriteria', "Kelompok: {$kelompok->nama}", KelompokKriteria::class, $kelompok->id, $kelompok->only(['nama', 'kode', 'urutan']), null);
        $kelompok->delete();
        return redirect()->back()->with('success', 'Kelompok Kriteria berhasil dihapus.');
    }

    public function kelompokUpdate(Request $request, $jalurId, $id)
    {
        $request->validate(['nama' => 'required|string|max:255', 'kode' => 'required|string|max:50', 'urutan' => 'required|integer']);
        $kelompok = KelompokKriteria::findOrFail($id);
        $dataLama = $kelompok->only(['nama', 'kode', 'urutan']);
        $kelompok->update($request->only(['nama', 'kode', 'urutan']));
        AuditLog::catat('Ubah Kelompok Kriteria', "Kelompok: {$kelompok->nama}", KelompokKriteria::class, $kelompok->id, $dataLama, $kelompok->only(['nama', 'kode', 'urutan']));
        return redirect()->back()->with('success', 'Kelompok Kriteria berhasil diperbarui.');
    }

    // === Kriteria ===
    public function kriteriaStore(Request $request, $jalurId)
    {
        $request->validate([
            'kelompok_kriteria_id' => 'required|exists:kelompok_kriterias,id',
            'nama' => 'required|string|max:255', 'kode' => 'required|string|max:50',
            'tipe_input' => 'required|in:pilihan,angka', 'urutan' => 'required|integer',
            'nilai_min' => 'nullable|numeric', 'nilai_max' => 'nullable|numeric',
            'bobot' => 'required|numeric|min:0|max:100',
        ]);
        $kriteria = Kriteria::create($request->only(['kelompok_kriteria_id', 'nama', 'kode', 'tipe_input', 'urutan', 'nilai_min', 'nilai_max', 'bobot']));
        AuditLog::catat('Tambah Kriteria', "Kriteria: {$kriteria->nama} (Bobot: {$kriteria->bobot}%)", Kriteria::class, $kriteria->id, null, $kriteria->only(['nama', 'kode', 'tipe_input', 'bobot', 'nilai_min', 'nilai_max']));

        $this->syncGroupBobot($jalurId, $request->kelompok_kriteria_id);

        return redirect()->back()->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function kriteriaDestroy($jalurId, $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kelompokId = $kriteria->kelompok_kriteria_id;
        AuditLog::catat('Hapus Kriteria', "Kriteria: {$kriteria->nama} (Bobot: {$kriteria->bobot}%)", Kriteria::class, $kriteria->id, $kriteria->only(['nama', 'kode', 'bobot', 'tipe_input']), null);
        $kriteria->delete();

        $this->syncGroupBobot($jalurId, $kelompokId);

        return redirect()->back()->with('success', 'Kriteria berhasil dihapus.');
    }

    public function kriteriaUpdate(Request $request, $jalurId, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255', 'kode' => 'required|string|max:50',
            'tipe_input' => 'required|in:pilihan,angka', 'urutan' => 'required|integer',
            'nilai_min' => 'nullable|numeric', 'nilai_max' => 'nullable|numeric',
            'bobot' => 'required|numeric|min:0|max:100',
            'kelompok_kriteria_id' => 'required|exists:kelompok_kriterias,id',
        ]);
        $kriteria = Kriteria::findOrFail($id);
        $dataLama = $kriteria->only(['nama', 'kode', 'tipe_input', 'bobot', 'nilai_min', 'nilai_max', 'kelompok_kriteria_id']);
        $oldKelompokId = $kriteria->kelompok_kriteria_id;
        $kriteria->update($request->only(['kelompok_kriteria_id', 'nama', 'kode', 'tipe_input', 'urutan', 'nilai_min', 'nilai_max', 'bobot']));
        AuditLog::catat('Ubah Kriteria', "Kriteria: {$kriteria->nama} (Bobot: {$dataLama['bobot']}% → {$kriteria->bobot}%)", Kriteria::class, $kriteria->id, $dataLama, $kriteria->only(['nama', 'kode', 'tipe_input', 'bobot', 'nilai_min', 'nilai_max', 'kelompok_kriteria_id']));

        $this->syncGroupBobot($jalurId, $request->kelompok_kriteria_id);
        if ($oldKelompokId != $request->kelompok_kriteria_id) {
            $this->syncGroupBobot($jalurId, $oldKelompokId);
        }

        return redirect()->back()->with('success', 'Kriteria berhasil diperbarui.');
    }

    private function syncGroupBobot($jalurId, $kelompokId): void
    {
        $kelompok = KelompokKriteria::find($kelompokId);
        if ($kelompok) {
            $sumBobot = (float) $kelompok->kriterias()->sum('bobot');
            BobotPenilaian::updateOrCreate(
                ['jalur_id' => $jalurId, 'kelompok_kriteria_id' => $kelompokId],
                ['bobot_persen' => $sumBobot]
            );
        }
    }

    // === Pilihan Kriteria ===
    public function pilihanStore(Request $request, $jalurId)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriterias,id',
            'label' => 'required|string|max:255', 'skor' => 'required|numeric', 'urutan' => 'required|integer',
        ]);
        $pilihan = PilihanKriteria::create($request->only(['kriteria_id', 'label', 'skor', 'urutan']));
        AuditLog::catat('Tambah Pilihan Kriteria', "Pilihan: {$pilihan->label} (Skor: {$pilihan->skor})", PilihanKriteria::class, $pilihan->id, null, $pilihan->only(['label', 'skor', 'urutan']));
        return redirect()->back()->with('success', 'Pilihan berhasil ditambahkan.');
    }

    public function pilihanDestroy($jalurId, $id)
    {
        $pilihan = PilihanKriteria::findOrFail($id);
        AuditLog::catat('Hapus Pilihan Kriteria', "Pilihan: {$pilihan->label} (Skor: {$pilihan->skor})", PilihanKriteria::class, $pilihan->id, $pilihan->only(['label', 'skor', 'urutan']), null);
        $pilihan->delete();
        return redirect()->back()->with('success', 'Pilihan berhasil dihapus.');
    }

    // === Bobot ===
    public function bobotUpdate(Request $request, $jalurId)
    {
        $request->validate(['bobot' => 'required|array', 'bobot.*' => 'required|numeric|min:0|max:100']);
        $total = array_sum($request->bobot);
        if (abs($total - 100) > 0.01) {
            return redirect()->back()->with('error', "Total bobot harus 100%. Saat ini: {$total}%");
        }

        $allOld = [];
        $allNew = [];
        foreach ($request->bobot as $kelompokId => $bobotPersen) {
            $existing = BobotPenilaian::where('jalur_id', $jalurId)->where('kelompok_kriteria_id', $kelompokId)->first();
            $oldPersen = $existing?->bobot_persen ?? 0;
            $kelompokNama = KelompokKriteria::find($kelompokId)?->nama ?? "ID:{$kelompokId}";

            BobotPenilaian::updateOrCreate(
                ['jalur_id' => $jalurId, 'kelompok_kriteria_id' => $kelompokId],
                ['bobot_persen' => $bobotPersen]
            );

            if (abs($oldPersen - $bobotPersen) > 0.001) {
                $allOld[$kelompokNama] = $oldPersen . '%';
                $allNew[$kelompokNama] = $bobotPersen . '%';
            }
        }

        if (!empty($allOld)) {
            $jalur = Jalur::find($jalurId);
            $desc = collect($allOld)->map(fn($v, $k) => "{$k}: {$v} → {$allNew[$k]}")->implode(', ');
            AuditLog::catat('Ubah Bobot Penilaian', "Jalur: {$jalur?->nama} — {$desc}", Jalur::class, (int) $jalurId, $allOld, $allNew);
        }

        return redirect()->back()->with('success', 'Bobot berhasil diperbarui.');
    }
}
