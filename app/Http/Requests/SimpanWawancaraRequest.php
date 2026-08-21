<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanWawancaraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action_type' => 'required|in:lolos,gugurkan,batalkan',
            'catatan' => 'nullable|string|max:500',
        ];
    }
}
