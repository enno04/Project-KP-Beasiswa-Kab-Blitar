<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomField extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jalur_id',
        'nama_field',
        'tipe_field',
        'options',
        'is_required',
        'is_active',
        'penempatan',
        'urutan',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function jalur()
    {
        return $this->belongsTo(Jalur::class);
    }

    public function answers()
    {
        return $this->hasMany(CustomFieldAnswer::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
