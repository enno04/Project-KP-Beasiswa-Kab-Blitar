<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KelompokKriteria extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'jalur_id', 'nama', 'kode', 'urutan',
    ];

    protected static function booted(): void
    {
        static::creating(function (KelompokKriteria $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function jalur()
    {
        return $this->belongsTo(Jalur::class);
    }

    public function kriterias()
    {
        return $this->hasMany(Kriteria::class)->orderBy('urutan');
    }

    public function bobotPenilaian()
    {
        return $this->hasOne(BobotPenilaian::class);
    }

    public function getBobotAttribute(): float
    {
        $sumKriteriaBobot = (float) $this->kriterias->sum('bobot');
        if ($sumKriteriaBobot > 0) {
            return $sumKriteriaBobot;
        }

        $bp = $this->relationLoaded('bobotPenilaian') ? $this->bobotPenilaian : $this->bobotPenilaian()->first();
        if ($bp && $bp->bobot_persen > 0) {
            return (float) $bp->bobot_persen;
        }

        return 0;
    }

    /**
     * Ambil bobot (persen) kelompok ini untuk jalur tertentu.
     */
    public function getBobot(): float
    {
        return $this->bobot;
    }
}
