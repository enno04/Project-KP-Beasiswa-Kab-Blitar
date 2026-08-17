<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Persyaratan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'jalur_id', 'deskripsi', 'urutan', 'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Persyaratan $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function jalur()
    {
        return $this->belongsTo(Jalur::class);
    }
}
