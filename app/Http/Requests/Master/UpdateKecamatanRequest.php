<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKecamatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'kode_kecamatan' => 'required|string|max:20|unique:kecamatan,kode_kecamatan,' . $id,
            'nama_kecamatan' => 'required|string|max:255',
        ];
    }
}
