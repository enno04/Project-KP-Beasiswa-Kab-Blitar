<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportHistoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_excel' => 'required|mimes:xlsx,xls,csv',
        ];
    }
}
