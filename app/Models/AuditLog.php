<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'uuid', 'user_id', 'aktivitas', 'model_type', 'model_id',
        'deskripsi', 'data_lama', 'data_baru', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AuditLog $model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    // === Relasi ===

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relation to audited model.
     */
    public function model()
    {
        return $this->morphTo();
    }

    // === Helper ===

    /**
     * Catat aktivitas ke Audit Log.
     */
    public static function catat(
        string $aktivitas,
        ?string $deskripsi = null,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null,
    ): self {
        return static::create([
            'user_id' => auth()->id(),
            'aktivitas' => $aktivitas,
            'deskripsi' => $deskripsi,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
