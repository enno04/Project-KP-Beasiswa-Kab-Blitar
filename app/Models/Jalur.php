<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Jalur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'program_id', 'nama', 'kode', 'slug',
        'deskripsi', 'aktif', 'urutan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Jalur $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
            $model->slug = $model->slug ?: Str::slug($model->nama);
        });
    }

    // === Relasi ===

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function tahapans()
    {
        return $this->hasMany(Tahapan::class)->orderBy('urutan');
    }

    public function persyaratans()
    {
        return $this->hasMany(Persyaratan::class)->orderBy('urutan');
    }

    public function dokumens()
    {
        return $this->hasMany(Dokumen::class)->orderBy('urutan');
    }

    public function kelompokKriterias()
    {
        return $this->hasMany(KelompokKriteria::class)->orderBy('urutan');
    }

    public function bobotPenilaians()
    {
        return $this->hasMany(BobotPenilaian::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function customFields()
    {
        return $this->hasMany(CustomField::class)->orderBy('penempatan')->orderBy('urutan');
    }

    // === Scope ===

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    // === Helper ===

    /**
     * Ambil seluruh kriteria yang dimiliki jalur ini (melalui kelompok kriteria).
     */
    public function getAllKriterias()
    {
        return Kriteria::whereIn(
            'kelompok_kriteria_id',
            $this->kelompokKriterias()->pluck('id')
        )->orderBy('urutan')->get();
    }

    /**
     * Cek apakah total bobot sudah 100%.
     */
    public function isBobotValid(): bool
    {
        $totalBobot = $this->bobotPenilaians()->sum('bobot_persen');
        return abs($totalBobot - 100) < 0.01;
    }
}
