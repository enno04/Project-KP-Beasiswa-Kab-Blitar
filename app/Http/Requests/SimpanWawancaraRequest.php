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
        // Jika action_type adalah 'gugurkan', tidak perlu validasi nilai wawancara
        if ($this->action_type === 'gugurkan') {
            return [];
        }

        return [
            'nilai_wawancara' => 'required|numeric|min:0|max:100',
        ];
    }
}
