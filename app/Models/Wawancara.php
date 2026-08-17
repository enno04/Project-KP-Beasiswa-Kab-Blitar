<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Wawancara extends Model
{
    protected $table = 'wawancaras';

    protected $fillable = [
        'uuid', 'pendaftaran_id', 'user_id', 'nilai',
        'catatan', 'tanggal_wawancara',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'tanggal_wawancara' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Wawancara $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
