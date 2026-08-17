<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8',
            'opd_id' => 'nullable|exists:opd,id',
            'kecamatan_id' => 'nullable|exists:kecamatan,id',
            'desa_id' => 'nullable|exists:desa,id',
        ];
    }
}
