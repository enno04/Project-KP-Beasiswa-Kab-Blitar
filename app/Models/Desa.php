<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Desa extends Model
{
    use SoftDeletes;

    protected $table = 'desa';

    protected $fillable = [
        'uuid', 'kecamatan_id', 'kode_desa', 'nama_desa',
    ];

    protected static function booted(): void
    {
        static::creating(function (Desa $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
