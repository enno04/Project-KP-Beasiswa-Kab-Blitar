<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreKecamatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_kecamatan' => 'required|string|max:20|unique:kecamatan',
            'nama_kecamatan' => 'required|string|max:255',
        ];
    }
}
