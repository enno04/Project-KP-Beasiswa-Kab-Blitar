<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DokumenPublik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenPublikController extends Controller
{
    public function index(Request $request)
    {
        $query = DokumenPublik::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('status_aktif', true);
            } elseif ($request->status === '0') {
                $query->where('status_aktif', false);
            }
        }

        $dokumens = $query->ordered()->paginate(15)->withQueryString();

        return view('super-admin.dokumen-publik.index', compact('dokumens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            'urutan' => 'required|integer|min:0',
            'status_aktif' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama dokumen wajib diisi.',
            'file.required' => 'File dokumen wajib diunggah.',
            'file.mimes' => 'Format file yang diperbolehkan hanya PDF, DOC, atau DOCX.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = $file->getClientOriginalName();
        $size = $file->getSize();

        // Store file securely in public disk under dokumen_publik/
        $filename = Str::uuid() . '.' . $extension;
        $path = $file->storeAs('dokumen_publik', $filename, 'public');

        $dokumen = DokumenPublik::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path,
            'file_name' => $originalName,
            'format_file' => $extension,
            'ukuran_file' => $size,
            'urutan' => $request->urutan ?? 0,
            'status_aktif' => $request->has('status_aktif') ? (bool) $request->status_aktif : true,
            'created_by' => auth()->id(),
        ]);

        AuditLog::catat(
            'Tambah Dokumen Publik',
            "Menambahkan dokumen publik: {$dokumen->nama} ({$extension}, {$dokumen->ukuran_formatted})",
            DokumenPublik::class,
            $dokumen->id
        );

        return redirect()->route('super-admin.master.dokumen-publik.index')
            ->with('success', 'Dokumen publik berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $dokumen = DokumenPublik::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'urutan' => 'required|integer|min:0',
            'status_aktif' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama dokumen wajib diisi.',
            'file.mimes' => 'Format file yang diperbolehkan hanya PDF, DOC, atau DOCX.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $data = [
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'urutan' => $request->urutan ?? 0,
            'status_aktif' => $request->has('status_aktif') ? (bool) $request->status_aktif : false,
        ];

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }

            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName();
            $size = $file->getSize();

            $filename = Str::uuid() . '.' . $extension;
            $path = $file->storeAs('dokumen_publik', $filename, 'public');

            $data['file_path'] = $path;
            $data['file_name'] = $originalName;
            $data['format_file'] = $extension;
            $data['ukuran_file'] = $size;
        }

        $dokumen->update($data);

        AuditLog::catat(
            'Ubah Dokumen Publik',
            "Mengubah dokumen publik: {$dokumen->nama}",
            DokumenPublik::class,
            $dokumen->id
        );

        return redirect()->route('super-admin.master.dokumen-publik.index')
            ->with('success', 'Dokumen publik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dokumen = DokumenPublik::findOrFail($id);
        $nama = $dokumen->nama;

        // Note: Soft delete record so audit history remains intact
        $dokumen->delete();

        AuditLog::catat(
            'Hapus Dokumen Publik',
            "Menghapus dokumen publik: {$nama}",
            DokumenPublik::class,
            $id
        );

        return redirect()->route('super-admin.master.dokumen-publik.index')
            ->with('success', 'Dokumen publik berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $dokumen = DokumenPublik::findOrFail($id);
        $dokumen->status_aktif = !$dokumen->status_aktif;
        $dokumen->save();

        $statusText = $dokumen->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::catat(
            'Toggle Status Dokumen Publik',
            "Status dokumen '{$dokumen->nama}' {$statusText}",
            DokumenPublik::class,
            $dokumen->id
        );

        return redirect()->route('super-admin.master.dokumen-publik.index')
            ->with('success', "Dokumen publik berhasil {$statusText}.");
    }
}
