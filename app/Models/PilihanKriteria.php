<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PilihanKriteria extends Model
{
    use SoftDeletes;

    protected $table = 'pilihan_kriterias';

    protected $fillable = [
        'uuid', 'kriteria_id', 'label', 'skor', 'urutan',
    ];

    protected $casts = [
        'skor' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (PilihanKriteria $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
