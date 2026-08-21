<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Periode extends Model
{
    use SoftDeletes;

    protected $table = 'periode';

    protected $fillable = [
        'uuid', 'tahun', 'nama', 'tanggal_mulai', 'tanggal_selesai',
        'status', 'keterangan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'kunci_hitung_nilai' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Periode $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    // === Scope ===

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // === Accessor ===

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'aktif' => 'Aktif',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'yellow',
            'aktif' => 'green',
            'selesai' => 'gray',
            default => 'gray',
        };
    }
}
