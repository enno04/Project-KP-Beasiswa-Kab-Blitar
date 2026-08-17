<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DokumenPublik extends Model
{
    use SoftDeletes;

    protected $table = 'dokumen_publiks';

    protected $fillable = [
        'uuid',
        'nama',
        'deskripsi',
        'file_path',
        'file_name',
        'format_file',
        'ukuran_file',
        'urutan',
        'status_aktif',
        'created_by',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'ukuran_file' => 'integer',
        'urutan' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (DokumenPublik $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // === Scope ===

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan', 'asc')->orderBy('created_at', 'desc');
    }

    // === Accessor ===

    public function getUkuranFormattedAttribute(): string
    {
        $bytes = $this->ukuran_file;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getExtensionBadgeColorAttribute(): string
    {
        $ext = strtolower($this->format_file);
        return match ($ext) {
            'pdf' => 'bg-red-100 text-red-700 border-red-200',
            'doc', 'docx' => 'bg-blue-100 text-blue-700 border-blue-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
