<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Program;
use App\Models\Jalur;

class StorePendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nik' => 'required|string|size:16',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat_ktp' => 'required|string',
            'google_maps_url' => 'required|string|max:255',
            'desa_id' => 'required|exists:desa,id',
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'asal_perguruan_tinggi' => 'required|string|max:255',
            'program_studi' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:14',

            // Data Ayah
            'nama_ayah' => 'required|string|max:255',
            'nik_ayah' => 'required|string|size:16',
            'alamat_ayah' => 'required|string',
            'tempat_lahir_ayah' => 'required|string|max:255',
            'tanggal_lahir_ayah' => 'required|date',
            'no_hp_ayah' => 'required|string|max:20',

            // Data Ibu
            'nama_ibu' => 'required|string|max:255',
            'nik_ibu' => 'required|string|size:16',
            'alamat_ibu' => 'required|string',
            'tempat_lahir_ibu' => 'required|string|max:255',
            'tanggal_lahir_ibu' => 'required|date',
            'no_hp_ibu' => 'required|string|max:20',

            // Data Wali
            'nama_wali' => 'nullable|string|max:255',
            'nik_wali' => 'nullable|string|size:16',
            'alamat_wali' => 'nullable|string',
            'tempat_lahir_wali' => 'nullable|string|max:255',
            'tanggal_lahir_wali' => 'nullable|date',
            'no_hp_wali' => 'nullable|string|max:20',
        ];

        // Validasi Dinamis
        $program = Program::where('slug', $this->program_slug)->first();
        if ($program) {
            $jalur = Jalur::where('program_id', $program->id)->where('slug', $this->jalur_slug)->first();
            if ($jalur) {
                // Kriteria
                $kriterias = $jalur->getAllKriterias();
                foreach ($kriterias as $k) {
                    if ($k->isPilihan()) {
                        $rules["kriteria_{$k->id}"] = 'required|exists:pilihan_kriterias,id';
                    } else {
                        $rules["kriteria_{$k->id}"] = "required|numeric|min:{$k->nilai_min}|max:{$k->nilai_max}";
                    }
                }
                
                // Dokumen
                $dokumens = $jalur->dokumens;
                foreach ($dokumens as $dok) {
                    $rules["dokumen_{$dok->id}"] = ($dok->wajib ? 'required' : 'nullable') . '|file|mimes:' . $dok->format_file . '|max:' . $dok->max_size_kb;
                }

                // Custom Fields
                $customFields = $jalur->customFields()->aktif()->get();
                foreach ($customFields as $field) {
                    $rules["custom_fields.{$field->id}"] = $field->is_required ? 'required' : 'nullable';
                }
            }
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $nik = $this->input('nik');
            if (!$nik) return;

            $program = Program::where('slug', $this->program_slug)->first();
            $periode = \App\Models\Periode::aktif()->first();

            if (!$program || !$periode) return;

            try {
                app(\App\Services\RegistrationService::class)->validateNikAvailability($nik, $program, $periode);
            } catch (\Exception $e) {
                $validator->errors()->add('nik', $e->getMessage());
            }
        });
    }
}
