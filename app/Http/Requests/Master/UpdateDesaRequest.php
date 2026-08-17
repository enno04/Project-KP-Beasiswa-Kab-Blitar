<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'kode_desa' => 'required|string|max:20|unique:desa,kode_desa,' . $id,
            'nama_desa' => 'required|string|max:255',
        ];
    }
}
