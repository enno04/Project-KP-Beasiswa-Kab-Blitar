<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranOrangtua extends Model
{
    protected $fillable = [
        'pendaftaran_id', 
        'nama_ayah', 'nik_ayah', 'alamat_ayah', 'tempat_lahir_ayah', 'tanggal_lahir_ayah', 'no_hp_ayah',
        'nama_ibu', 'nik_ibu', 'alamat_ibu', 'tempat_lahir_ibu', 'tanggal_lahir_ibu', 'no_hp_ibu',
        'nama_wali', 'nik_wali', 'alamat_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali', 'no_hp_wali',
    ];

    protected $casts = [
        'tanggal_lahir_ayah' => 'date',
        'tanggal_lahir_ibu' => 'date',
        'tanggal_lahir_wali' => 'date',
    ];

    // === Relasi ===

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}
