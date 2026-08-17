<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Program extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'periode_id', 'nama', 'kode', 'slug', 'deskripsi',
        'tanggal_buka', 'tanggal_tutup', 'aktif', 'urutan',
    ];

    protected $casts = [
        'tanggal_buka' => 'date',
        'tanggal_tutup' => 'date',
        'aktif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Program $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
            $model->slug = $model->slug ?: Str::slug($model->nama);
        });
    }

    // === Relasi ===

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function jalurs()
    {
        return $this->hasMany(Jalur::class)->orderBy('urutan');
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function draftSks()
    {
        return $this->hasMany(DraftSk::class);
    }

    // === Scope ===

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function scopeForPeriode($query, int $periodeId)
    {
        return $query->where('periode_id', $periodeId);
    }

    // === Accessor ===

    public function getStatusPendaftaranAttribute(): string
    {
        $today = now()->startOfDay();

        if (!$this->tanggal_buka || !$this->tanggal_tutup) {
            return 'Belum Dikonfigurasi';
        }

        if ($today < $this->tanggal_buka) {
            return 'Belum Dibuka';
        } elseif ($today <= $this->tanggal_tutup) {
            return 'Sedang Dibuka';
        }

        return 'Ditutup';
    }

    /**
     * Cek apakah program ini adalah SDSS.
     */
    public function isSdss(): bool
    {
        return $this->kode === 'sdss';
    }

    /**
     * Cek apakah program ini memiliki tahapan wawancara.
     */
    public function hasWawancara(): bool
    {
        return $this->kode === 'berdaya_berjaya';
    }
}
