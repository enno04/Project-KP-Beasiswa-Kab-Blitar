<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Kriteria extends Model
{
    use SoftDeletes;

    protected $table = 'kriterias';

    protected $fillable = [
        'uuid', 'kelompok_kriteria_id', 'nama', 'kode',
        'tipe_input', 'nilai_min', 'nilai_max', 'bobot', 'urutan',
    ];

    protected $casts = [
        'nilai_min' => 'decimal:2',
        'nilai_max' => 'decimal:2',
        'bobot' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Kriteria $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function kelompokKriteria()
    {
        return $this->belongsTo(KelompokKriteria::class);
    }

    public function pilihans()
    {
        return $this->hasMany(PilihanKriteria::class)->orderBy('urutan');
    }

    public function jawabanKriterias()
    {
        return $this->hasMany(JawabanKriteria::class);
    }

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }

    // === Helper ===

    public function isPilihan(): bool
    {
        return $this->tipe_input === 'pilihan';
    }

    public function isAngka(): bool
    {
        return $this->tipe_input === 'angka';
    }
}
