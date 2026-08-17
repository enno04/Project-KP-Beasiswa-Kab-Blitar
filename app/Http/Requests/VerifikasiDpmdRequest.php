<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifikasiDpmdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keputusan' => 'required|in:disetujui,ditolak',
            'catatan_dpmd' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'keputusan.required' => 'Keputusan verifikasi harus dipilih.',
            'keputusan.in' => 'Keputusan harus berupa disetujui atau ditolak.',
        ];
    }
}
