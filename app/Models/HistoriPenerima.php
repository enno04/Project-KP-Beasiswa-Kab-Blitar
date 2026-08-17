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
    ];
}
