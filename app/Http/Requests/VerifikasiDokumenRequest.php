<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifikasiDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hasil' => 'required|in:valid,tidak_valid',
            'catatan' => 'required_if:hasil,tidak_valid|nullable|string',
        ];
    }
}
