<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanKriteria extends Model
{
    protected $table = 'jawaban_kriterias';

    protected $fillable = [
        'pendaftaran_id', 'kriteria_id', 'pilihan_kriteria_id', 'nilai_input',
    ];

    protected $casts = [
        'nilai_input' => 'decimal:2',
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

    // === Helper ===

    /**
     * Ambil skor dari jawaban ini.
     * Jika tipe pilihan: skor dari PilihanKriteria.
     * Jika tipe angka: nilai_input langsung.
     */
    public function getSkor(): float
    {
        if ($this->pilihan_kriteria_id) {
            return $this->pilihanKriteria?->skor ?? 0;
        }

        return $this->nilai_input ?? 0;
    }
}
