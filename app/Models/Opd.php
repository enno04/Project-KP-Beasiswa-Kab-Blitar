<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Opd extends Model
{
    use SoftDeletes;

    protected $table = 'opd';

    protected $fillable = [
        'uuid', 'nama_opd', 'singkatan',
    ];

    protected static function booted(): void
    {
        static::creating(function (Opd $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function dokumens()
    {
        return $this->hasMany(Dokumen::class);
    }
}
