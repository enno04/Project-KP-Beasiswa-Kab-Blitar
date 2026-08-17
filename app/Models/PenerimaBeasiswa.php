<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PenerimaBeasiswa extends Model
{
    protected $table = 'penerima_beasiswas';

    protected $fillable = [
        'uuid', 'pendaftaran_id', 'periode_id', 'program_id',
        'jalur_id', 'total_nilai', 'ranking', 'status',
    ];

    protected $casts = [
        'total_nilai' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::creating(function (PenerimaBeasiswa $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function jalur()
    {
        return $this->belongsTo(Jalur::class);
    }

    // === Scope ===

    /**
     * Cek apakah NIK sudah pernah menjadi penerima.
     * Sesuai 03_BUSINESS_RULE.md §21
     */
    public static function pernahMenerima(string $nik): bool
    {
        return static::whereHas('pendaftaran.identitas', function ($q) use ($nik) {
            $q->where('nik', $nik);
        })->where('status', 'aktif')->exists();
    }
}
