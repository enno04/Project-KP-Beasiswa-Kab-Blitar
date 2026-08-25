# 08_DATABASE.md

# Database Design
## Website Beasiswa Blitar Mengabdi

Versi : 2.4

---

# 1. Tujuan

Dokumen ini menjelaskan rancangan basis data (Database Design) Website Beasiswa Blitar Mengabdi.

Database dirancang agar mampu mendukung Multi Periode, Multi Program, Multi Jalur, Persetujuan Paralel (Kecamatan + DPMD), SPK Weighted Sum Model, dan Audit Log.

---

# 2. Arsitektur Database

Database dibagi menjadi lima kelompok utama:
1. Master Referensi (`roles`, `users`, `opd`, `kecamatan`, `desa`)
2. Master Konfigurasi (`periode`, `programs`, `jalurs`, `tahapans`, `persyaratans`, `dokumens`, `kelompok_kriterias`, `kriterias`, `pilihan_kriterias`, `bobot_penilaians`, `custom_fields`)
3. Transaksi (`pendaftarans`, `pendaftaran_identitas`, `pendaftaran_orangtuas`, `jawaban_kriterias`, `custom_field_answers`, `upload_dokumens`, `verifikasi_dokumens`, `penilaians`, `wawancaras`, `rekomendasi_desas`, `penetapans`)
4. Output & Documentation (`draft_sks`, `penerima_beasiswas`, `histori_penerimas`, `dokumen_publiks`)
5. Sistem (`audit_logs`, `web_settings`)

---

# 3. Detail Tabel Transaksi Khusus

## `rekomendasi_desas`

Digunakan khusus Program SDSS untuk menyimpan Surat Rekomendasi Kades/Lurah & Berita Acara Musyawarah Desa beserta status persetujuan paralel dari Kecamatan dan DPMD.

```sql
CREATE TABLE rekomendasi_desas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE,
    pendaftaran_id BIGINT UNSIGNED NOT NULL,
    desa_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL, -- Admin Desa yang mengunggah
    surat_rekomendasi_path VARCHAR(255) NULL,
    berita_acara_path VARCHAR(255) NULL,
    catatan TEXT NULL,
    tanggal_rekomendasi DATETIME NULL,
    
    -- Status Verifikasi Kecamatan
    status_kecamatan VARCHAR(50) DEFAULT 'belum_diverifikasi', -- belum_diverifikasi, disetujui, ditolak
    catatan_kecamatan TEXT NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    
    -- Status Verifikasi DPMD (Baru v2.3)
    status_dpmd VARCHAR(50) DEFAULT 'belum_diverifikasi', -- belum_diverifikasi, disetujui, ditolak
    catatan_dpmd TEXT NULL,
    dpmd_verified_by BIGINT UNSIGNED NULL,
    dpmd_verified_at TIMESTAMP NULL,
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftarans(id) ON DELETE CASCADE
);
```

## `pendaftarans` (Status Enum)

```sql
ALTER TABLE pendaftarans MODIFY COLUMN status ENUM(
    'draft',
    'menunggu_verifikasi',
    'sedang_diverifikasi',
    'lolos_verifikasi',
    'tidak_lolos_verifikasi',
    'tidak_lolos_desa',
    'diteruskan_ke_kecamatan',
    'ditolak_kecamatan',
    'ditolak_dpmd',
    'proses_seleksi',
    'menunggu_wawancara',
    'proses_wawancara',
    'gugur_wawancara',
    'menunggu_penilaian',
    'proses_penilaian',
    'menunggu_penetapan',
    'lulus',
    'tidak_lulus',
    'sk_terbit'
) DEFAULT 'menunggu_verifikasi';
```

---

# 4. Relationship Utama

- `Pendaftaran` `hasOne` `PendaftaranIdentitas`
- `Pendaftaran` `hasOne` `PendaftaranOrangtua`
- `Pendaftaran` `hasMany` `JawabanKriteria`
- `Pendaftaran` `hasMany` `CustomFieldAnswer`
- `Pendaftaran` `hasMany` `UploadDokumen`
- `Pendaftaran` `hasMany` `Penilaian`
- `Pendaftaran` `hasOne` `RekomendasiDesa` (SDSS)
- `RekomendasiDesa` `belongsTo` `User` (as `kecamatanVerifier`, `dpmdVerifier`)
- `Pendaftaran` `hasOne` `PenetapanModel`
- `Pendaftaran` `hasOne` `PenerimaBeasiswa`
- `Jalur` `hasMany` `CustomField`
- `CustomField` `hasMany` `CustomFieldAnswer`

---

# 5. Indexing Database

Index wajib ada pada field: `nik`, `periode_id`, `program_id`, `jalur_id`, `desa_id`, `kecamatan_id`, `status`, `created_at`.