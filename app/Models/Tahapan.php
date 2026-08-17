<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tahapan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'jalur_id', 'nama', 'kode', 'urutan', 'deskripsi',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tahapan $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function jalur()
    {
        return $this->belongsTo(Jalur::class);
    }
}
