<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RekomendasiDesa extends Model
{
    protected $table = 'rekomendasi_desas';

    protected $fillable = [
        'uuid', 'pendaftaran_id', 'desa_id', 'user_id',
        'surat_rekomendasi_path', 'berita_acara_path', 'catatan', 'tanggal_rekomendasi',
        'status_kecamatan', 'catatan_kecamatan', 'verified_by', 'verified_at',
        'status_dpmd', 'catatan_dpmd', 'dpmd_verified_by', 'dpmd_verified_at',
    ];

    protected $casts = [
        'tanggal_rekomendasi' => 'datetime',
        'verified_at' => 'datetime',
        'dpmd_verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (RekomendasiDesa $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kecamatanVerifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function dpmdVerifier()
    {
        return $this->belongsTo(User::class, 'dpmd_verified_by');
    }

    // === Helper ===

    /**
     * Cek apakah Kecamatan DAN DPMD sudah menyetujui rekomendasi ini.
     */
    public function isFullyApproved(): bool
    {
        return $this->status_kecamatan === 'disetujui'
            && $this->status_dpmd === 'disetujui';
    }

    /**
     * Cek apakah salah satu pihak menolak.
     */
    public function isRejected(): bool
    {
        return $this->status_kecamatan === 'ditolak'
            || $this->status_dpmd === 'ditolak';
    }
}
