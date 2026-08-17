<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'opd_id' => 'nullable|exists:opd,id',
            'kecamatan_id' => 'nullable|exists:kecamatan,id',
            'desa_id' => 'nullable|exists:desa,id',
        ];
    }
}
