# Beasiswa Blitar Mengabdi

Sistem informasi manajemen beasiswa berbasis web untuk mengelola seluruh siklus program beasiswa daerah — mulai dari pendaftaran publik tanpa akun, verifikasi berjenjang, penilaian berbasis bobot (Weighted Sum Model), hingga penerbitan Surat Keputusan (SK).

---

## Tentang Proyek

Aplikasi ini dirancang untuk menyederhanakan dan mendigitalisasi proses seleksi beasiswa yang melibatkan banyak pihak secara bertahap. Setiap tahapan proses tercatat secara transparan melalui sistem audit log yang menyimpan perubahan data secara *before-after*.

---

## Fitur Utama

- Pendaftaran publik 5 langkah tanpa perlu membuat akun, identifikasi via NIK.
- Pembatasan ketat 1 NIK hanya bisa mendaftar 1 kali per program dalam 1 periode aktif.
- Verifikasi dokumen berjenjang (OPD → Desa → Kecamatan → Kabupaten).
- Alur persetujuan paralel untuk program SDSS — Kecamatan dan DPMD memproses secara bersamaan.
- **Auto-Verifikasi SLA (3 Hari)** — Jika Kecamatan atau DPMD tidak merespons dalam 3 hari kerja, sistem secara otomatis menyetujui rekomendasi dan meneruskan pendaftar ke tahap berikutnya. Seluruh aksi tercatat di audit log sebagai aktivitas sistem.
- Mesin penilaian otomatis berbasis Weighted Sum Model (WSM) dengan bobot kriteria yang dapat dikonfigurasi per jalur.
- Peringkatan otomatis pendaftar per jalur, termasuk peringkatan per desa khusus untuk program SDSS.
- Kunci penilaian desa — Admin Desa hanya dapat melakukan ranking setelah fitur ini diaktifkan oleh Admin Kabupaten.
- Audit log lengkap dengan rekaman perubahan data *before/after* dalam format JSON untuk setiap aktivitas penting.
- Custom field dinamis per jalur pendaftaran yang dapat dikonfigurasi tanpa perubahan kode.
- Generate draft Surat Keputusan (SK) untuk penerima yang telah ditetapkan.
- Export data penerima ke format Excel untuk kebutuhan pelaporan dan arsip.
- Import histori penerima dari file Excel format lama (migrasi data dari sistem sebelumnya).
- Portal unduh dokumen publik (juknis, template surat, panduan) tanpa login.
- Portal Cek Status & Cetak Bukti pendaftaran via NIK untuk pendaftar.

---

## Teknologi

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel (PHP 8.2+) |
| Frontend | Blade + Alpine.js + Lucide Icons |
| CSS | Tailwind CSS v4 |
| Build Tool | Vite |
| Database | MySQL |
| CAPTCHA | Cloudflare Turnstile |
| Autentikasi | Multi-role session auth dengan URL login tersembunyi |

---

## Peran Pengguna

| Role | Tugas |
|------|-------|
| `super_admin` | Manajemen pengguna, konfigurasi program & kriteria, master data wilayah, dan review audit log |
| `admin_kabupaten` | Verifikasi akhir, input nilai wawancara, peringkatan, dan penerbitan SK |
| `admin_opd` | Verifikasi teknis dokumen sesuai instansi yang ditugaskan |
| `admin_kecamatan` | Peninjauan dan persetujuan rekomendasi desa di wilayahnya |
| `admin_dpmd` | Verifikasi dan persetujuan rekomendasi desa tingkat kabupaten |
| `admin_desa` | Verifikasi faktual, penetapan perwakilan desa, dan unggah surat rekomendasi |
| Publik | Pendaftaran, unduh dokumen, dan cek status via NIK |

> URL halaman login tidak ditampilkan di halaman publik secara sengaja.

---

## Alur Seleksi Program SDSS (Paralel)

Setelah Admin Desa mengunggah Surat Rekomendasi, proses dilanjutkan secara paralel ke Kecamatan dan DPMD sebelum naik ke tahap penetapan:

```
[Pendaftar] ──► [Verifikasi OPD] ──► [Desa: Penilaian, Ranking & Rekomendasi]
                                                   │
                                     ┌─────────────┴─────────────┐
                                     ▼                           ▼
                             [Verifikasi Kec.]          [Verifikasi DPMD]
                                     │                           │
                                     └─────────────┬─────────────┘
                                                   ▼
                                         (Kedua Pihak Menyetujui?)
                                                   │
                                                   ▼
                                          [Admin Kabupaten]
                                         (Penetapan & SK Terbit)
```

---

## Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0

---

## Instalasi

```bash
git clone <repository-url>
cd beasiswa-blitar

composer install
npm install

cp .env.example .env
php artisan key:generate

# Sesuaikan kredensial database di file .env, kemudian:
php artisan migrate:fresh --seed
php artisan storage:link

# Jalankan server pengembangan
composer run dev
```

> Untuk pengembangan lokal, *dummy key* Cloudflare Turnstile yang ada di `.env.example` sudah cukup — CAPTCHA akan selalu lolos tanpa memerlukan domain terdaftar.

---

## Deployment

Pastikan variabel berikut sudah dikonfigurasi dengan benar di server produksi:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com
LOG_LEVEL=error
TURNSTILE_SITE_KEY=<site-key-produksi>
TURNSTILE_SECRET_KEY=<secret-key-produksi>
```

Setelah *pull* pembaruan kode:

```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan migrate --force
php artisan optimize
```

---

## Akun Bawaan (Seeder)

Akun berikut dibuat otomatis oleh database seeder. **Semua password wajib diganti sebelum aplikasi digunakan secara resmi.**

| Role | Username | Password |
|------|----------|----------|
| Super Admin | `superadmin` | `password` |
| Admin Kabupaten | `adminkab` | `password` |
| Admin DPMD | `admindpmd` | `password` |
| Admin OPD (contoh) | `admin_dispora` | `password` |
| Admin Kecamatan (contoh) | `adminkec_kanigoro` | `password` |
| Admin Desa (contoh) | `admindesa_3505072012` | `password` |

Daftar lengkap akun Desa dan Kecamatan dapat dilihat melalui menu **Referensi Wilayah** di dashboard Super Admin, atau langsung pada tabel `referensi_kecamatan` dan `referensi_desa` di database.

---

## Lisensi

Hak Cipta © 2026 Pemerintah Kabupaten Blitar.
