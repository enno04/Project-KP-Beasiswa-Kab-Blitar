<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pendaftaran extends Model
{
    protected $table = 'pendaftarans';

    protected $fillable = [
        'uuid', 'nomor_pendaftaran', 'periode_id', 'program_id', 'jalur_id',
        'tahun', 'total_nilai', 'ranking', 'status', 'created_by',
    ];

    protected $casts = [
        'total_nilai' => 'decimal:4',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pendaftaran $model) {
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

    public function jalur()
    {
        return $this->belongsTo(Jalur::class);
    }

    public function identitas()
    {
        return $this->hasOne(PendaftaranIdentitas::class);
    }

    public function desa()
    {
        return $this->hasOneThrough(
            Desa::class,
            PendaftaranIdentitas::class,
            'pendaftaran_id', // Foreign key on PendaftaranIdentitas table
            'id', // Foreign key on Desa table
            'id', // Local key on Pendaftaran table
            'desa_id' // Local key on PendaftaranIdentitas table
        );
    }

    public function kecamatan()
    {
        return $this->hasOneThrough(
            Kecamatan::class,
            PendaftaranIdentitas::class,
            'pendaftaran_id', // Foreign key on PendaftaranIdentitas table
            'id', // Foreign key on Kecamatan table
            'id', // Local key on Pendaftaran table
            'kecamatan_id' // Local key on PendaftaranIdentitas table
        );
    }

    public function orangtua()
    {
        return $this->hasOne(PendaftaranOrangtua::class);
    }

    public function jawabanKriterias()
    {
        return $this->hasMany(JawabanKriteria::class);
    }

    public function uploadDokumens()
    {
        return $this->hasMany(UploadDokumen::class);
    }

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function wawancaras()
    {
        return $this->hasMany(Wawancara::class);
    }

    public function rekomendasiDesa()
    {
        return $this->hasOne(RekomendasiDesa::class);
    }

    public function penetapan()
    {
        return $this->hasOne(PenetapanModel::class);
    }

    public function penerimaBeasiswa()
    {
        return $this->hasOne(PenerimaBeasiswa::class);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // === Accessor ===

    public function getNamaLengkapAttribute(): string
    {
        return $this->identitas?->nama_lengkap ?? '-';
    }

    public function getNikAttribute(): string
    {
        return $this->identitas?->nik ?? '-';
    }

    public function getAsalPerguruanTinggiAttribute(): string
    {
        return $this->identitas?->asal_perguruan_tinggi ?? '-';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'diteruskan_ke_kecamatan' && $this->rekomendasiDesa) {
            $kec = $this->rekomendasiDesa->status_kecamatan;
            $dpmd = $this->rekomendasiDesa->status_dpmd;
            
            if ($kec === 'disetujui' && $dpmd !== 'disetujui') {
                return 'Menunggu Verif DPMD';
            } elseif ($kec !== 'disetujui' && $dpmd === 'disetujui') {
                return 'Menunggu Verif Kecamatan';
            }
        }

        if ($this->status === 'lolos_verifikasi' && $this->total_nilai > 0) {
            return 'Selesai Dinilai Desa';
        }

        return match ($this->status) {
            'draft' => 'Draft',
            'menunggu_verifikasi' => 'Diajukan ke OPD',
            'sedang_diverifikasi' => 'Sedang Diverifikasi OPD',
            'lolos_verifikasi' => 'Lolos Verifikasi OPD',
            'tidak_lolos_verifikasi' => 'Gugur Verifikasi OPD',
            'tidak_lolos_desa' => 'Tidak Dipilih Desa',
            'diteruskan_ke_kecamatan' => 'Menunggu Verif Kec. & DPMD',
            'ditolak_kecamatan' => 'Ditolak Kecamatan',
            'ditolak_dpmd' => 'Ditolak DPMD',
            'proses_seleksi' => 'Proses Seleksi',
            'menunggu_wawancara' => 'Menunggu Wawancara',
            'proses_wawancara' => 'Proses Wawancara',
            'gugur_wawancara' => 'Gugur Wawancara',
            'menunggu_penilaian' => 'Menunggu Penilaian',
            'proses_penilaian' => 'Proses Penilaian',
            'menunggu_penetapan' => 'Menunggu Penetapan Kab. Blitar',
            'lulus', 'sk_terbit' => 'Lulus — SK Terbit',
            'tidak_lulus' => 'Tidak Lulus',
            default => str_replace('_', ' ', $this->status),
        };
    }

    public function getDetailProgresAttribute(): string
    {
        if (in_array($this->status, ['menunggu_verifikasi', 'sedang_diverifikasi'])) {
            $pendingOpds = [];
            if ($this->relationLoaded('uploadDokumens')) {
                foreach ($this->uploadDokumens as $upload) {
                    if ($upload->status === 'belum_diverifikasi' && $upload->dokumen && $upload->dokumen->opd) {
                        $opdName = $upload->dokumen->opd->singkatan ?? $upload->dokumen->opd->nama_opd;
                        if (!in_array($opdName, $pendingOpds)) {
                            $pendingOpds[] = $opdName;
                        }
                    }
                }
            }
            if (!empty($pendingOpds)) {
                return 'Terkendala di OPD: ' . implode(', ', $pendingOpds);
            }
            return 'Menunggu Verifikasi OPD';
        }

        if ($this->status === 'lolos_verifikasi') {
            if ($this->program && $this->program->kode === 'sdss') {
                $desa = $this->desa->nama_desa ?? 'Tidak diketahui';
                return 'Menunggu Pemilihan Desa (' . $desa . ')';
            }
            return 'Menunggu Admin Kabupaten (Proses Seleksi)';
        }

        if ($this->status === 'diteruskan_ke_kecamatan') {
            $kecamatan = $this->kecamatan->nama_kecamatan ?? 'Tidak diketahui';
            $rekomendasi = $this->rekomendasiDesa;
            $statusParts = [];
            if ($rekomendasi) {
                $statusParts[] = 'Kec: ' . ($rekomendasi->status_kecamatan === 'disetujui' ? '✓' : ($rekomendasi->status_kecamatan === 'ditolak' ? '✗' : '⏳'));
                $statusParts[] = 'DPMD: ' . ($rekomendasi->status_dpmd === 'disetujui' ? '✓' : ($rekomendasi->status_dpmd === 'ditolak' ? '✗' : '⏳'));
            }
            return 'Menunggu Verifikasi Kecamatan & DPMD (' . $kecamatan . ')' . (!empty($statusParts) ? ' [' . implode(', ', $statusParts) . ']' : '');
        }

        if (in_array($this->status, ['proses_seleksi', 'menunggu_wawancara', 'proses_wawancara', 'menunggu_penilaian', 'proses_penilaian', 'menunggu_penetapan'])) {
            return 'Di tangan Admin Kabupaten';
        }

        if ($this->status === 'tidak_lolos_desa') {
            return 'Berhenti: Tidak lolos pemilihan Desa';
        }

        if ($this->status === 'tidak_lolos_verifikasi') {
            return 'Berhenti: Dokumen ditolak OPD';
        }
        
        if ($this->status === 'ditolak_kecamatan') {
            return 'Berhenti: Ditolak oleh Kecamatan';
        }

        if ($this->status === 'ditolak_dpmd') {
            return 'Berhenti: Ditolak oleh DPMD';
        }

        if (in_array($this->status, ['lulus', 'sk_terbit'])) {
            return 'Selesai: Ditetapkan sebagai Penerima (SK Terbit)';
        }
        
        if ($this->status === 'tidak_lulus') {
            return 'Berhenti: Tidak lulus perangkingan / kuota penuh';
        }

        return 'Pemohon menyusun berkas';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->status === 'lolos_verifikasi' && $this->total_nilai > 0) {
            return 'bg-emerald-100 text-emerald-700'; // Warna khusus untuk yang sudah dinilai desa
        }

        return match ($this->status) {
            'lulus', 'sk_terbit' => 'bg-green-100 text-green-700',
            'tidak_lulus', 'tidak_lolos_verifikasi', 'ditolak_kecamatan', 'ditolak_dpmd', 'gugur_wawancara' => 'bg-red-100 text-red-700',
            'tidak_lolos_desa' => 'bg-gray-200 text-gray-600',
            'menunggu_verifikasi', 'menunggu_penilaian', 'menunggu_penetapan', 'menunggu_wawancara' => 'bg-yellow-100 text-yellow-700',
            'sedang_diverifikasi', 'proses_seleksi', 'proses_wawancara', 'proses_penilaian', 'diteruskan_ke_kecamatan' => 'bg-blue-100 text-blue-700',
            'lolos_verifikasi' => 'bg-teal-100 text-teal-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    // === Helper ===

    /**
     * Generate nomor pendaftaran otomatis.
     * Format: BM-{KODE_PROGRAM}-{KODE_JALUR}-{TAHUN}-{SEQUENCE}
     */
    public static function generateNomorPendaftaran(string $kodeProgram, string $kodeJalur, int $tahun): string
    {
        $prefix = 'BM-' . strtoupper($kodeProgram) . '-' . strtoupper($kodeJalur) . '-' . $tahun . '-';
        $lastNomor = static::where('nomor_pendaftaran', 'like', $prefix . '%')
            ->orderBy('nomor_pendaftaran', 'desc')
            ->value('nomor_pendaftaran');

        if ($lastNomor) {
            $lastSequence = (int) substr($lastNomor, strlen($prefix));
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    public function customFieldAnswers()
    {
        return $this->hasMany(CustomFieldAnswer::class);
    }

    /**
     * Scope untuk mengambil pendaftaran yang verifikasinya belum dikunci bagi OPD.
     */
    public function scopeOpdActive($query)
    {
        return $query->where(function($q) {
            $q->where(function($q2) {
                // Untuk SDSS: OPD aktif selama belum ditetapkan desa (masih menunggu/sedang/lolos/tidak lolos verifikasi OPD)
                $q2->whereHas('program', fn($p) => $p->where('nama', 'like', '%satu desa%'))
                   ->whereIn('pendaftarans.status', ['menunggu_verifikasi', 'sedang_diverifikasi', 'lolos_verifikasi', 'tidak_lolos_verifikasi']);
            })
            ->orWhere(function($q2) {
                // Untuk Non-SDSS: OPD aktif selama belum penetapan akhir Kab. Blitar
                $q2->whereHas('program', fn($p) => $p->where('nama', 'not like', '%satu desa%'))
                   ->whereNotIn('pendaftarans.status', ['menunggu_penetapan', 'lulus', 'sk_terbit', 'ditolak']);
            });
        });
    }

    /**
     * Mengecek apakah verifikasi pendaftaran masih bisa diubah oleh OPD.
     */
    public function isOpdActive(): bool
    {
        $isSDSS = str_contains(strtolower($this->program->nama ?? ''), 'satu desa');
        if ($isSDSS) {
            return in_array($this->status, ['menunggu_verifikasi', 'sedang_diverifikasi', 'lolos_verifikasi', 'tidak_lolos_verifikasi']);
        }
        return !in_array($this->status, ['menunggu_penetapan', 'lulus', 'sk_terbit', 'ditolak']);
    }
}
