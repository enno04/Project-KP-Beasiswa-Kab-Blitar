# PROJECT_OVERVIEW.md

# Website Beasiswa Blitar Mengabdi

Versi : 2.4
Status : Final
Platform : Website
Backend : Laravel 12
Frontend : Blade + Tailwind CSS + Alpine.js
Database : MySQL

---

# 1. Latar Belakang

Program Beasiswa Blitar Mengabdi merupakan program Pemerintah Kabupaten Blitar yang bertujuan meningkatkan kualitas sumber daya manusia melalui pemberian bantuan biaya pendidikan kepada pemuda Kabupaten Blitar.

Sistem ini dibangun untuk mendukung seluruh proses pelaksanaan Beasiswa Blitar Mengabdi secara digital, mulai dari pendaftaran, verifikasi, seleksi, penilaian, penetapan penerima, hingga pelaporan dan monitoring.

Website ini menjadi satu-satunya sistem resmi yang digunakan dalam pelaksanaan Program Beasiswa Blitar Mengabdi.

---

# 2. Tujuan Sistem

Sistem dibangun untuk:

- Mempermudah proses pendaftaran Beasiswa secara online.
- Mempermudah proses verifikasi oleh seluruh instansi terkait (OPD, Desa, Kecamatan, & DPMD).
- Mengotomatisasi proses penilaian berdasarkan kriteria yang telah ditetapkan.
- Membantu proses penetapan penerima Beasiswa.
- Membantu penyusunan dokumen administrasi dan penerbitan SK.
- Menyediakan monitoring seluruh proses secara transparan dan akuntabel.

---

# 3. Ruang Lingkup

Sistem mencakup seluruh proses berikut.

- Pengaturan Periode
- Pengaturan Program Beasiswa
- Pengaturan Jalur Beasiswa
- Pengaturan Master Data (Kriteria, Bobot, Dokumen, Custom Fields)
- Pendaftaran Online (5-Step Tanpa Login)
- Verifikasi Dokumen OPD
- Verifikasi & Penetapan 1 Calon Desa (SDSS)
- Verifikasi & Persetujuan Paralel Kecamatan & DPMD (SDSS)
- Auto-Approve SLA 3 Hari (Kecamatan & DPMD)
- Penilaian SPK (Weighted Sum Model)
- Wawancara (Berdaya Berjaya)
- Penetapan Kabupaten & Penerbitan SK (Tahap Final)
- Modul Dokumen Publik & Ebook Juknis
- Manajemen Pengaturan Website (WebSetting)
- Histori Penerima & Import Data Legacy
- Monitoring & Audit Log

---

# 4. Program Beasiswa

Sistem mendukung tiga Program Beasiswa.

## A. Satu Desa Satu Sarjana (SDSS)

Memiliki satu jalur:

- Jalur Reguler

Tahapan mencakup verifikasi berkas OPD, penetapan 1 calon desa & unggah rekomendasi, **persetujuan paralel oleh Kecamatan & DPMD**, hingga penetapan SK Bupati.

---

## B. Berdaya Berjaya

Memiliki dua jalur:

- Jalur Mahasiswa Baru (Semester 1)
- Jalur Mahasiswa Lama (Diatas Semester 1)

Tahapan mencakup verifikasi berkas OPD, penilaian SPK, Tes Wawancara oleh Admin Kabupaten, hingga penetapan SK.

---

## C. Bantuan Biaya Pendidikan (BBP)

Memiliki dua jalur:

- Jalur Prestasi
- Jalur Kurang Mampu

setiap Program dan Jalur dapat memiliki:

- Persyaratan berbeda
- Dokumen berbeda
- Bobot Penilaian berbeda
- Nilai setiap kriteria berbeda
- Tahapan seleksi berbeda

Seluruh konfigurasi tersebut dikelola melalui Master Data.

---

# 5. Konsep Pendaftaran & Pengecekan Status

Pendaftaran dilakukan tanpa Login.

Calon pendaftar cukup memilih:

- Periode
- Program Beasiswa
- Jalur

Kemudian mengisi Form Pendaftaran 5 Step.

Untuk mengecek status pendaftaran dan mencetak bukti pendaftaran, pendaftar mengakses menu **Cek Status & Cetak Bukti** dengan menginputkan:

- **NIK (Nomor Induk Kependudukan 16 Digit)**
- **Tahun Pendaftaran** (Pilihan dropdown dinamis)

Sistem menampilkan visual stepper alur kemajuan pendaftaran 5 tahap serta tombol cetak bukti tanda pendaftaran.

Akses login Administrator dilakukan melalui route terselubung `/mengabdi` (tanpa tombol di halaman publik).

---

# 6. Tahapan Form Pendaftaran

Seluruh Program menggunakan lima tahapan pendaftaran.

## Step 1

Identitas Pendaftar (Status: Tetap)

---

## Step 2

Data Orang Tua / Wali (Status: Tetap)

---

## Step 3

Status Ekonomi (Status: Dinamis mengikuti Master Data)

---

## Step 4

Data Akademik (Status: Dinamis mengikuti Master Data)

---

## Step 5

Upload Dokumen (Status: Dinamis mengikuti Master Data)

---

# 7. Konsep Master Data

Sistem menggunakan dua kelompok Master Data agar konfigurasi lebih terstruktur.

## 7.1 Master Referensi

Data yang relatif jarang berubah. Meliputi:

- Kecamatan
- Desa / Kelurahan
- OPD
- User
- Role

> Catatan: Tabel `perguruan_tinggi` telah dihapus. Data perguruan tinggi kini diisi langsung oleh pendaftar sebagai field teks bebas (`asal_perguruan_tinggi`).

---

## 7.2 Master Konfigurasi

Data yang dapat berubah pada setiap Periode. Meliputi:

- Periode
- Program Beasiswa
- Jalur
- Tahapan
- Persyaratan
- Dokumen
- Kelompok Kriteria
- Kriteria
- Pilihan Kriteria
- Bobot Penilaian
- Custom Fields (Pertanyaan Tambahan per Jalur)

Seluruh Master Konfigurasi hanya dapat diubah oleh Super Admin.

---

# 8. Konsep Penilaian

Sistem melakukan penilaian secara otomatis (SPK berbasis Model Penilaian Berbobot / Weighted Sum).

Nilai dihitung berdasarkan konfigurasi Bobot, Kriteria, Pilihan, dan Skor yang telah ditentukan pada Master Konfigurasi.

Khusus program Berdaya Berjaya, Admin Kabupaten dapat menambahkan nilai wawancara (0-100) atau menggugurkan pendaftar yang tidak hadir wawancara.

---

# 9. Konsep Workflow

Workflow setiap Program disusun berdasarkan Tahapan yang dikonfigurasi pada Master Konfigurasi.

SDSS melibatkan:

- Desa (Penilaian otomatis, perangkingan desa, penetapan 1 calon & unggah Surat Rekomendasi + Berita Acara)
- **Kecamatan & DPMD (Persetujuan Paralel — Keduanya wajib menyetujui)**
- Kabupaten (Verifikasi akhir & Penetapan SK)

Sedangkan Program Berdaya Berjaya dan BBP diproses langsung oleh OPD & Kabupaten.

Workflow dijelaskan lebih rinci pada dokumen `02_WORKFLOW.md`.

---

# 10. Konsep Verifikasi

Sebelum seleksi dilakukan, dokumen diverifikasi oleh OPD yang berwenang (misal: Disdukcapil, Dinas Sosial, Bagian Kesra) secara paralel.

Untuk program SDSS, verifikasi rekomendasi desa dilakukan secara **paralel oleh Kecamatan & DPMD**.

---

# 11. Konsep Penetapan (Tahap Final)

Penetapan dilakukan oleh Admin Kabupaten.

Tim Kabupaten menetapkan penerima berdasarkan hasil seleksi dan pemeringkatan SPK.

Setelah SK diterbitkan (`Lulus — SK Terbit`), alur pendaftaran dinyatakan selesai.

---

# 12. Monitoring

Monitoring dapat dilakukan secara real-time berdasarkan Periode, Program, Jalur, maupun Status Pendaftaran oleh Super Admin & Admin Kabupaten. Seluruh perubahan data penting tercatat otomatis pada Audit Log.

---

# 13. Hak Akses

Sistem memiliki 7 kelompok pengguna:

1. Super Admin
2. Admin Kabupaten
3. Admin OPD
4. Admin Kecamatan
5. Admin DPMD (Role Baru)
6. Admin Desa / Kelurahan
7. Masyarakat (Pengguna Publik — tanpa akun)

---

# 14. Informasi Website

Seluruh informasi publik pada Website bersifat statis dan didukung modul Dokumen Publik untuk mengunggah Juknis & Ebook Panduan.

---

# 15. Prinsip Sistem

- Transparan, Akuntabel, Fleksibel, Responsif, Aman, Terintegrasi, Modular, Configuration Driven.

---

# ARSITEKTUR SISTEM

```
MASTER REFERENSI
        │
        ▼
MASTER KONFIGURASI (Periode, Program, Jalur, Kriteria, Bobot, Custom Fields)
        │
        ▼
PENDAFTARAN PUBLIK (5-Step Tanpa Login)
        │
        ▼
VERIFIKASI OPD (Paralel — setiap OPD memverifikasi dokumen kewenangannya)
        │
        ▼
[SDSS]
Desa Tetapkan 1 Perwakilan + Upload Rekomendasi
        │
        ▼
Persetujuan Paralel Kecamatan & DPMD
(Auto-Approve jika melewati SLA 3 Hari)
        │
        ▼
[BBP / Berdaya Berjaya]
PENILAIAN OTOMATIS SPK + WAWANCARA (Berdaya Berjaya)
        │
        ▼
PENETAPAN KABUPATEN
        │
        ▼
SK TERBIT (TAHAP FINAL)
        │
        ▼
MONITORING & AUDIT LOG
```

---

# 17. Dokumen Acuan

Dokumen ini menjadi acuan utama seluruh dokumentasi proyek:
- 02_WORKFLOW.md
- 03_BUSINESS_RULE.md
- 04_ROLE_PERMISSION.md
- 05_HALAMAN.md
- 06_MENU.md
- 07_MASTER_DATA.md
- 08_DATABASE.md
- 09_PENILAIAN.md
- 10_DESIGN.md
- 11_README_AI.md
- ROADMAP.md
- MANUAL_USER.md
- MANUAL_ADMIN.md