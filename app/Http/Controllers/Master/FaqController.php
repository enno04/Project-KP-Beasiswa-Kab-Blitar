<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('jawaban', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_aktif', true);
            } elseif ($request->status === '0') {
                $query->where('is_aktif', false);
            }
        }

        $faqs = $query->ordered()->paginate(15)->withQueryString();
        $maxUrutan = Faq::max('urutan') ?? 0;

        return view('super-admin.faq.index', compact('faqs', 'maxUrutan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string|max:1000',
            'jawaban' => 'required|string',
            'urutan' => 'required|integer|min:0',
            'is_aktif' => 'nullable|boolean',
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'jawaban.required' => 'Jawaban wajib diisi.',
            'urutan.required' => 'Urutan wajib diisi.',
        ]);

        $faq = Faq::create([
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'urutan' => $request->urutan ?? 0,
            'is_aktif' => $request->has('is_aktif') ? (bool) $request->is_aktif : true,
            'created_by' => auth()->id(),
        ]);

        AuditLog::catat(
            'Tambah FAQ',
            "Menambahkan FAQ: {$faq->pertanyaan}",
            Faq::class,
            $faq->id
        );

        return redirect()->route('super-admin.master.faq.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'pertanyaan' => 'required|string|max:1000',
            'jawaban' => 'required|string',
            'urutan' => 'required|integer|min:0',
            'is_aktif' => 'nullable|boolean',
        ], [
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'jawaban.required' => 'Jawaban wajib diisi.',
            'urutan.required' => 'Urutan wajib diisi.',
        ]);

        $faq->update([
            'pertanyaan' => $request->pertanyaan,
            'jawaban' => $request->jawaban,
            'urutan' => $request->urutan ?? 0,
            'is_aktif' => $request->has('is_aktif') ? (bool) $request->is_aktif : false,
        ]);

        AuditLog::catat(
            'Ubah FAQ',
            "Mengubah FAQ: {$faq->pertanyaan}",
            Faq::class,
            $faq->id
        );

        return redirect()->route('super-admin.master.faq.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $pertanyaan = $faq->pertanyaan;
        $faq->delete();

        AuditLog::catat(
            'Hapus FAQ',
            "Menghapus FAQ: {$pertanyaan}",
            Faq::class,
            $id
        );

        return redirect()->route('super-admin.master.faq.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->is_aktif = !$faq->is_aktif;
        $faq->save();

        $status = $faq->is_aktif ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::catat(
            'Ubah Status FAQ',
            "Mengubah status FAQ {$faq->pertanyaan} menjadi {$status}",
            Faq::class,
            $faq->id
        );

        return response()->json([
            'success' => true,
            'message' => "FAQ berhasil $status.",
            'new_status' => $faq->is_aktif
        ]);
    }
}
