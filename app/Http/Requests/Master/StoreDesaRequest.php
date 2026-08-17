<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreDesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'kode_desa' => 'required|string|max:20|unique:desa',
            'nama_desa' => 'required|string|max:255',
        ];
    }
}
