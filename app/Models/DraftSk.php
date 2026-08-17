<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DraftSk extends Model
{
    protected $table = 'draft_sks';

    protected $fillable = [
        'uuid', 'periode_id', 'program_id', 'nomor_sk', 'tanggal_sk',
        'konten', 'file_path', 'status', 'created_by',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (DraftSk $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function periode()
    {
        return $this->belongsTo(Periode::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
