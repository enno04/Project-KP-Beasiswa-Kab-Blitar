<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRekomendasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'surat_rekomendasi' => 'required|file|mimes:pdf|max:2048',
            'berita_acara' => 'required|file|mimes:pdf|max:2048',
            'catatan' => 'nullable|string',
        ];
    }
}
