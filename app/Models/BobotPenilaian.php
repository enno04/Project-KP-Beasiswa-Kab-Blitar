<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BobotPenilaian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'jalur_id', 'kelompok_kriteria_id', 'bobot_persen',
    ];

    protected $casts = [
        'bobot_persen' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (BobotPenilaian $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function jalur()
    {
        return $this->belongsTo(Jalur::class);
    }

    public function kelompokKriteria()
    {
        return $this->belongsTo(KelompokKriteria::class);
    }
}
