<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'nama', 'kode', 'deskripsi',
    ];

    protected static function booted(): void
    {
        static::creating(function (Role $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // === Helper ===

    public static function getByKode(string $kode): ?self
    {
        return static::where('kode', $kode)->first();
    }
}
