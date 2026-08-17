# 🎓 Beasiswa Blitar Mengabdi (V2.4)

Sistem Informasi Manajemen Beasiswa Terpadu Kabupaten Blitar — Platform Pendaftaran Publik, Verifikasi Berjenjang (OPD, Desa, Kecamatan, & DPMD), Pembobotan Kriteria SPK, Audit Log System dengan Traceability State (`data_lama` vs `data_baru`), dan Penetapan SK resmi yang dikelola oleh **Dinas Kepemudaan dan Olahraga (Dispora) Kabupaten Blitar**.

---

## 📋 Deskripsi Project

Aplikasi web modern untuk mengelola seluruh siklus pelaksanaan beasiswa daerah: mulai dari pendaftaran terbuka oleh masyarakat (tanpa login), verifikasi berjenjang oleh OPD / Desa / Kecamatan / DPMD, wawancara seleksi, penilaian otomatis berbasis **Model Penilaian Berbobot (Weighted Sum Model)** oleh Admin Kabupaten, hingga pengawasan perubahan sistem secara menyeluruh melalui **Audit Log System (State Tracking & Multi-Filter)**.

### Tiga Program Beasiswa

| Kode | Nama Program | Keterangan & Alur Seleksi |
|------|-------------|---------------------------|
| `sdss` | **Satu Desa Satu Sarjana (SDSS)** | Melibatkan verifikasi berkas OPD, verifikasi faktual Desa, **persetujuan paralel oleh Kecamatan & DPMD**, hingga penetapan SK Bupati. |
| `berdaya_berjaya` | **Berdaya Berjaya** | Dibuka untuk Mahasiswa Baru & Lama dengan tahapan Tes Wawancara & perhitungan SPK Kumulatif. |
| `bantuan_biaya` | **Bantuan Biaya Pendidikan (BBP)** | Bantuan biaya pendidikan untuk mahasiswa dari keluarga kurang mampu atau berprestasi. |

---

## 🛠️ Technology Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend Framework** | Laravel 12 (PHP 8.2+) |
| **Architecture Pattern** | Thin Controller + Dedicated Service Layer |
| **Frontend UI** | Blade Templates + Alpine.js + Lucide Icons |
| **CSS Framework** | Tailwind CSS v4 (via `@tailwindcss/vite`) |
| **Build Tool** | Vite 7 |
| **Database Engine** | MySQL |
| **Authentication** | Custom Multi-Role Auth (Concealed Login `/mengabdi`) |
| **Audit & State Tracking** | Audit Log Engine (`data_lama` vs `data_baru` JSON diff viewer) |
| **Typography** | Plus Jakarta Sans (Headings) + Inter (Body) |

---

## 🏗️ Service Layer Architecture

Logika bisnis utama dipisahkan ke dalam **Service Classes** modular pada namespace `App\Services`:

- **`RegistrationService`**: Mengelola pendaftaran publik 5-step, pembuatan nomor pendaftaran unik `BM-YYYY-XXXXXX`, dan pembatasan NIK 1-pendaftar-1-program seumur hidup (SDSS).
- **`VerifikasiService`**: Mengelola alur verifikasi berkas oleh OPD, rekomendasi Desa/Kelurahan, dan validasi berkas.
- **`PenilaianService`**: Menghitung skor SPK menggunakan rumus terbobot kriteria $((Skor / Max) \times Bobot)$.
- **`RankingService`**: Mengurutkan pemeringkatan pendaftar secara otomatis berdasarkan total skor kumulatif (per desa untuk SDSS).
- **`PenetapanService`**: Mengelola penetapan kelulusan pendaftar dan penerbitan Draft SK Bupati.
- **`RecommendationService`**: Menangani pengunggahan Surat Rekomendasi Kades/Lurah serta mekanisme **Persetujuan Paralel** (Kecamatan & DPMD).

---

## 👥 User Roles & Hak Akses

| Role | Namespace / Dashboard | Hak Akses & Tugas |
|------|----------------------|-------------------|
| `super_admin` | `/super-admin/*` | Manajemen pengguna, referensi wilayah (OPD/Kecamatan/Desa), konfigurasi periode & program, kriteria SPK, dokumen publik, serta audit log komprehensif (pencatatan data lama vs baru, audit login/logout, dan filter log). |
| `admin_kabupaten` | `/kabupaten/*` | Verifikasi akhir, input nilai wawancara (Berdaya Berjaya), kalkulasi SPK, pemeringkatan, export data, dan penerbitan SK Terbit. |
| `admin_opd` | `/opd/*` | Verifikasi teknis dokumen pendaftaran sesuai instansi terkait. |
| `admin_kecamatan` | `/kecamatan/*` | Peninjauan & persetujuan rekomendasi desa di wilayah kecamatan (SDSS). |
| `admin_dpmd` | `/dpmd/*` | Verifikasi & persetujuan rekomendasi desa tingkat kabupaten (SDSS). |
| `admin_desa` | `/desa/*` | Verifikasi faktual domisili pendaftar, penetapan 1 perwakilan desa, dan pengungahan Surat Rekomendasi Kades/Lurah. |
| **Publik (Guest)** | `/` | Pendaftaran 5-step tanpa akun, unduh dokumen publik/juknis, dan portal Cek Status & Cetak Bukti via NIK. |

---

## 📁 Struktur Directory Utama

```
Beasiswa/
├── app/
│   ├── Http/Controllers/
│   │   ├── Master/                    # Controller Master Data (UserController, ReferensiController, KonfigurasiController, KriteriaController, DokumenPublikController)
│   │   ├── SuperAdminController.php   # Dasbor Super Admin & Audit Log
│   │   ├── KabupatenController.php    # Dasbor Admin Kabupaten (Wawancara, Ranking, Penetapan)
│   │   ├── OpdController.php          # Dasbor Verifikasi OPD
│   │   ├── KecamatanController.php    # Dasbor Kecamatan
│   │   ├── DpmdController.php         # Dasbor Admin DPMD (SDSS)
│   │   ├── DesaController.php         # Dasbor Verifikasi Desa & Rekomendasi
│   │   ├── PendaftaranController.php # Form Pendaftaran Publik
│   │   └── PublicController.php       # Halaman Publik, Download Dokumen, Cek Status NIK
│   ├── Models/                        # Eloquent Models (Pendaftaran, RekomendasiDesa, DokumenPublik, AuditLog, Periode, Program, Jalur, Kriteria, dll)
│   └── Services/                      # Layer Logika Bisnis (RegistrationService, PenilaianService, RankingService, RecommendationService, dll)
├── database/
│   ├── migrations/                    # Schema database V2 & DPMD parallel columns
│   └── seeders/                       # Database seeders (User, Referensi, Program, Kriteria, DokumenPublik)
├── resources/
│   ├── css/app.css                    # Tailwind CSS v4 design system tokens
│   └── views/
│       ├── components/                # Komponen Blade & Layouts (admin & public)
│       ├── super-admin/               # Views CRUD Master Data & Monitoring
│       ├── kabupaten/                 # Views Dasbor Kabupaten & SPK
│       ├── opd/                       # Views Verifikasi OPD
│       ├── kecamatan/                 # Views Peninjauan Kecamatan
│       ├── dpmd/                      # Views Verifikasi DPMD (SDSS)
│       ├── desa/                      # Views Verifikasi & Rekomendasi Desa
│       └── public/                    # Landing page, Informasi, Kriteria, Seleksi, Cek Status
└── routes/
    ├── web.php                        # Root router
    ├── public.php                     # Route publik & download
    ├── super-admin.php                # Route Super Admin
    ├── admin-kabupaten.php            # Route Admin Kabupaten
    ├── opd.php                        # Route Admin OPD
    ├── kecamatan.php                  # Route Admin Kecamatan
    ├── dpmd.php                       # Route Admin DPMD
    └── desa.php                       # Route Admin Desa
```

---

## 🔀 Alur Seleksi & Pipeline Status (SDSS Paralel)

Proses administrasi pendaftaran SDSS diteruskan secara paralel ke Kecamatan & DPMD setelah Desa mengunggah Surat Rekomendasi:

```
[Pendaftar] ──► [Verifikasi OPD] ──► [Desa: Penilaian, Ranking & Rekomendasi]
                                                   │
                                     ┌─────────────┴─────────────┐
                                     ▼                           ▼
                             [Verifikasi Kec.]          [Verifikasi DPMD]
                                     │                           │
                                     └─────────────┬─────────────┘
                                                   ▼
                                         (Kedua Pihak Setuju?)
                                                   │
                                                   ▼
                                           [Admin Kabupaten]
                                         (Penetapan SK Terbit)
```

---

## 📄 Modul Dokumen Publik & Manual Book PDF

Super Admin dapat mengelola dokumen publik (**Ebook Panduan, SK Juknis, Template Surat Pernyataan**) melalui menu `/super-admin/master/dokumen-publik`:
- Terpisah penuh dari jenis dokumen persyaratan pendaftaran pemohon.
- Format yang diperbolehkan: **PDF, DOC, DOCX** (Maks 10 MB).
- Tampil secara dinamis di halaman depan publik dan dapat diunduh tanpa login.
- **Manual Book PDF Sistem** lengkap 14 BAB dapat diterbitkan via script ReportLab `build_full_manual_book_pdf.py`.

---

## 🛡️ Modul Audit Log System & State Traceability

Sistem dilengkapi dengan audit log server-side yang *append-only* dan transparan untuk mencatat setiap perubahan data penting dan aktivitas administrator secara *real-time*:

- **State Tracking (`data_lama` vs `data_baru`)**: Menyimpan *snapshot* kondisi data sebelum (*before*) dan sesudah (*after*) perubahan dalam bentuk objek JSON pada tabel `audit_logs`.
- **Cakupan Pengawasan Master Data & Konfigurasi**:
  - **Kriteria & Bobot SPK**: Setiap penambahan/perubahan kriteria serta pengubahan persentase bobot penilaian SPK recorded secara otomatis per kelompok kriteria (contoh: `Prestasi Akademik: 30% → 40%`).
  - **Konfigurasi Seleksi**: Perubahan data Periode, Program, Jalur Pendaftaran, dan Dokumen Persyaratan per jalur.
  - **Referensi Wilayah & Instansi**: Perubahan master Kecamatan, Desa, dan OPD.
  - **Manajemen User**: Perekaman pembuatan akun user, pembaruan role/wilayah, penghapusan, dan toggle status aktif/nonaktif (nilai password terlindungi dengan mask `[DIUBAH]`).
- **Audit Autentikasi**: Pencatatan otomatis aktivitas `Login` dan `Logout` pengguna beserta alamat `IP Address` dan `User Agent`.
- **Fitur Dashboard Audit Log (`/super-admin/audit-log`)**:
  - **Multi-Filter & Pencarian**: Filter log berdasarkan kata kunci pencarian, pengguna, jenis aktivitas, serta rentang tanggal.
  - **JSON Diff Viewer Modal**: Pop-up interaktif berbasis Alpine.js untuk membandingkan `Data Lama` (highlight merah) dan `Data Baru` (highlight hijau) secara *side-by-side*.

---

## 🔐 Akun Demo & Akses Login (Seeder)

> [!NOTE]
> Untuk alasan keamanan, tombol **Masuk / Login** dihapus dari halaman publik. Administrator harus mengakses URL login terselubung secara manual di: **`http://localhost:8000/mengabdi`**.

| Role | Username | Password | Keterangan |
|------|----------|----------|------------|
| Super Admin | `superadmin` | `password` | Akses penuh master data & audit log |
| Admin Kabupaten | `adminkab` | `password` | Dasbor utama kabupaten |
| Admin DPMD | `admindpmd` | `password` | Verifikasi DPMD (SDSS) |
| Admin OPD | `admin_dispora` | `password` | Verifikasi dokumen Dispora |
| | `admin_disdukcapil` | `password` | Verifikasi KTP, KK, & Pas Foto |
| | `admin_dinsos` | `password` | Verifikasi Surat Miskin / DTSEN |
| | `admin_disdik` | `password` | Verifikasi Keterangan Diterima Universitas |
| | `admin_pmd` | `password` | Verifikasi Keterangan Desa / PMD |
| | `admin_kesra` | `password` | Verifikasi Surat Pernyataan Mutlak |
| Admin Kecamatan (Contoh) | `adminkec_kanigoro` | `password` | Verifikasi & persetujuan tingkat kecamatan |
| Admin Desa (Contoh) | `admindesa_3505072012` | `password` | Verifikasi faktual, input nilai, & upload rekomendasi |

> [!TIP]
> **Cara Mencari Akun Desa/Kecamatan Lain:**  
> Daftar lengkap nama kecamatan dan 10-digit kode desa dapat Anda lihat dengan *login* sebagai **Super Admin** (`superadmin`), lalu membuka menu **Referensi Wilayah**. Anda juga bisa mengeceknya secara langsung melalui *database* pada tabel `referensi_kecamatan` dan `referensi_desa`.

---

## ⚡ Instalasi & Setup Dev Environment

```bash
# 1. Clone repository
git clone <repository-url>
cd Beasiswa

# 2. Setup env & install dependencies
composer setup

# 3. Jalankan server lokal & asset bundler
composer dev
# atau jalankan secara terpisah:
# php artisan serve
# npm run dev
```

---

## 🧪 Running Automated Tests

```bash
# Jalankan seluruh suite pengujian otomatis
php artisan test

# Jalankan pengujian spesifik
php artisan test --filter EndToEndBeasiswaTest
php artisan test --filter DokumenPublikTest
php artisan test --filter NikLockingTest
```

---

## 📜 Lisensi

Hak Cipta © 2026 **Pemerintah Kabupaten Blitar — Dinas Kepemudaan dan Olahraga (Dispora)**.
Framework Laravel dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).
