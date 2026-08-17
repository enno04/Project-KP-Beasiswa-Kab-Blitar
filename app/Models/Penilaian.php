<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    protected $table = 'penilaians';

    protected $fillable = [
        'pendaftaran_id', 'kriteria_id', 'pilihan_kriteria_id',
        'skor', 'nilai_terbobot',
    ];

    protected $casts = [
        'skor' => 'decimal:2',
        'nilai_terbobot' => 'decimal:4',
    ];

    // === Relasi ===

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function pilihanKriteria()
    {
        return $this->belongsTo(PilihanKriteria::class);
    }
}
