<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifikasiRekomendasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keputusan' => 'required|in:disetujui,ditolak',
            'catatan_kecamatan' => 'nullable|string|max:1000',
        ];
    }
}
