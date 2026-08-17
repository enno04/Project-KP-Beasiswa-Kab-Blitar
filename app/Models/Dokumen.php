<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Dokumen extends Model
{
    use SoftDeletes;

    protected $table = 'dokumens';

    protected $fillable = [
        'uuid', 'jalur_id', 'nama', 'deskripsi', 'wajib',
        'opd_id', 'format_file', 'max_size_kb', 'urutan',
    ];

    protected $casts = [
        'wajib' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Dokumen $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function jalur()
    {
        return $this->belongsTo(Jalur::class);
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function uploadDokumens()
    {
        return $this->hasMany(UploadDokumen::class);
    }
}
