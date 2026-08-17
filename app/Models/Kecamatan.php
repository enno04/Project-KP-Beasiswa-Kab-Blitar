<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Kecamatan extends Model
{
    use SoftDeletes;

    protected $table = 'kecamatan';

    protected $fillable = [
        'uuid', 'kode_kecamatan', 'nama_kecamatan',
    ];

    protected static function booted(): void
    {
        static::creating(function (Kecamatan $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function desa()
    {
        return $this->hasMany(Desa::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
