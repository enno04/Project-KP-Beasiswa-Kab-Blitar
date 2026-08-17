<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranIdentitas extends Model
{
    protected $table = 'pendaftaran_identitas';

    protected $fillable = [
        'pendaftaran_id', 'nik', 'nama_lengkap', 'tempat_lahir',
        'tanggal_lahir', 'jenis_kelamin', 'alamat_ktp', 'google_maps_url', 'desa_id',
        'kecamatan_id', 'no_hp', 'email',
        'asal_perguruan_tinggi', 'program_studi', 'semester',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // === Relasi ===

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
