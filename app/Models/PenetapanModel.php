<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PenetapanModel extends Model
{
    protected $table = 'penetapans';

    protected $fillable = [
        'uuid', 'pendaftaran_id', 'user_id', 'keputusan',
        'catatan', 'tanggal_penetapan',
    ];

    protected $casts = [
        'tanggal_penetapan' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (PenetapanModel $model) {
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
