<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenetapanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pendaftaran_ids' => 'required|array',
            'keputusan' => 'required|in:lulus,tidak_lulus',
            'catatan' => 'nullable|string',
        ];
    }
}
