<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriPenerima extends Model
{
    protected $fillable = [
        'tahun',
        'nik',
        'nama_lengkap',
        'asal_perguruan_tinggi',
        'jenis_beasiswa',
        'sumber_data',
        'nomor_pendaftaran',
        'jalur_beasiswa',
        'ipk_nilai',
        'asal_sekolah',
        'kecamatan',
        'desa',
        'waktu_penetapan',
    ];
}
