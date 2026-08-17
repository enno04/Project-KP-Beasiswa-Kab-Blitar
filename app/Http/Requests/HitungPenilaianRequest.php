<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HitungPenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jalur_id' => 'required|exists:jalurs,id',
            'periode_id' => 'required|exists:periode,id',
        ];
    }
}
