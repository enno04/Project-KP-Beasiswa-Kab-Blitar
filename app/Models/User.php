<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid', 'nama', 'username', 'password', 'role_id',
        'opd_id', 'kecamatan_id', 'desa_id', 'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    // === Helper Role ===

    public function getRoleKode(): string
    {
        return $this->role?->kode ?? '';
    }

    public function isSuperAdmin(): bool
    {
        return $this->getRoleKode() === 'super_admin';
    }

    public function isAdminKabupaten(): bool
    {
        return $this->getRoleKode() === 'admin_kabupaten';
    }

    public function isAdminOpd(): bool
    {
        return $this->getRoleKode() === 'admin_opd';
    }

    public function isAdminKecamatan(): bool
    {
        return $this->getRoleKode() === 'admin_kecamatan';
    }

    public function isAdminDesa(): bool
    {
        return $this->getRoleKode() === 'admin_desa';
    }

    public function isAdminDpmd(): bool
    {
        return $this->getRoleKode() === 'admin_dpmd';
    }

    /**
     * Label role yang mudah dibaca.
     */
    public function getRoleLabelAttribute(): string
    {
        return $this->role?->nama ?? 'Unknown';
    }

    /**
     * Cek apakah user memiliki salah satu role yang diberikan.
     */
    public function hasRole(string ...$kodes): bool
    {
        return in_array($this->getRoleKode(), $kodes);
    }
}
