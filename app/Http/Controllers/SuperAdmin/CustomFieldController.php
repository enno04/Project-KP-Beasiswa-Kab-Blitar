<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Jalur;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    public function index(Jalur $jalur)
    {
        $customFields = $jalur->customFields()->orderBy('penempatan')->orderBy('urutan')->get();
        return view('super-admin.program.custom-fields', compact('jalur', 'customFields'));
    }

    public function store(Request $request, Jalur $jalur)
    {
        $request->validate([
            'nama_field' => 'required|string|max:255',
            'tipe_field' => 'required|in:text,textarea,number,date,select,rupiah',
            'options' => 'nullable|string', // We'll parse this to JSON, e.g. comma separated
            'penempatan' => 'required|in:identitas_diri,orang_tua,akademik,tambahan',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'urutan' => 'integer|min:1',
        ]);

        $options = null;
        if ($request->tipe_field === 'select' && $request->options) {
            $optionsArray = array_map('trim', explode(',', $request->options));
            $options = $optionsArray; // Eloquent will cast this to JSON automatically because of $casts
        }

        $jalur->customFields()->create([
            'nama_field' => $request->nama_field,
            'tipe_field' => $request->tipe_field,
            'options' => $options,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active'),
            'penempatan' => $request->penempatan,
            'urutan' => $request->urutan ?? 1,
        ]);

        return back()->with('success', 'Formulir berhasil ditambahkan.');
    }

    public function update(Request $request, CustomField $customField)
    {
        $request->validate([
            'nama_field' => 'required|string|max:255',
            'tipe_field' => 'required|in:text,textarea,number,date,select,rupiah',
            'options' => 'nullable|string',
            'penempatan' => 'required|in:identitas_diri,orang_tua,akademik,tambahan',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'urutan' => 'integer|min:1',
        ]);

        $options = null;
        if ($request->tipe_field === 'select' && $request->options) {
            $optionsArray = array_map('trim', explode(',', $request->options));
            $options = $optionsArray;
        }

        $customField->update([
            'nama_field' => $request->nama_field,
            'tipe_field' => $request->tipe_field,
            'options' => $options,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active'),
            'penempatan' => $request->penempatan,
            'urutan' => $request->urutan ?? 1,
        ]);

        return back()->with('success', 'Formulir berhasil diperbarui.');
    }

    public function destroy(CustomField $customField)
    {
        // Soft delete
        $customField->delete();
        return back()->with('success', 'Formulir berhasil dihapus.');
    }
}
