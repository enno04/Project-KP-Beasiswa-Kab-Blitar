<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UploadDokumen extends Model
{
    protected $table = 'upload_dokumens';

    protected $fillable = [
        'uuid', 'pendaftaran_id', 'dokumen_id', 'nama_file',
        'file_path', 'versi', 'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (UploadDokumen $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class);
    }

    public function verifikasis()
    {
        return $this->hasMany(VerifikasiDokumen::class);
    }

    public function verifikasiTerakhir()
    {
        return $this->hasOne(VerifikasiDokumen::class)->latestOfMany();
    }

    // === Accessor ===

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'belum_diverifikasi' => 'Belum Diverifikasi',
            'valid' => 'Valid',
            'tidak_valid' => 'Tidak Valid',
            'tidak_wajib' => 'Tidak Wajib',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'valid' => 'green',
            'tidak_valid' => 'red',
            'belum_diverifikasi' => 'yellow',
            'tidak_wajib' => 'gray',
            default => 'gray',
        };
    }
}
