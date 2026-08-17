<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VerifikasiDokumen extends Model
{
    protected $table = 'verifikasi_dokumens';

    protected $fillable = [
        'uuid', 'upload_dokumen_id', 'user_id', 'hasil',
        'catatan', 'tanggal_verifikasi',
    ];

    protected $casts = [
        'tanggal_verifikasi' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (VerifikasiDokumen $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function uploadDokumen()
    {
        return $this->belongsTo(UploadDokumen::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
