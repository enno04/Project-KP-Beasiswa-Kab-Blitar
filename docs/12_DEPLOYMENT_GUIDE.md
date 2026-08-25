# 12_DEPLOYMENT_GUIDE.md

# Panduan Deployment & Auto-Update
## Sistem Beasiswa Blitar Mengabdi

Versi : 2.4  
Terakhir Diperbarui : Agustus 2026

---

## Daftar Isi

1. [Persyaratan Server](#1-persyaratan-server)
2. [Checklist Sebelum Deploy Pertama Kali](#2-checklist-sebelum-deploy-pertama-kali)
3. [Langkah Deploy Pertama Kali ke Server](#3-langkah-deploy-pertama-kali-ke-server)
4. [Setup Auto-Update: Push GitHub → Server Otomatis Update](#4-setup-auto-update-push-github--server-otomatis-update)
5. [Alur Kerja Sehari-Hari (Update Kode)](#5-alur-kerja-sehari-hari-update-kode)
6. [Perintah Darurat & Pemecahan Masalah](#6-perintah-darurat--pemecahan-masalah)

---

## 1. Persyaratan Server

Pastikan server sudah terpasang semua kebutuhan berikut sebelum memulai:

| Komponen | Versi Minimum | Keterangan |
|----------|-------------|------------|
| PHP | 8.2+ | Wajib. Cek: `php -v` |
| Composer | 2.x | Wajib. Cek: `composer -V` |
| Node.js | 18.x+ | Untuk build aset frontend |
| NPM | 9.x+ | Ikut bersama Node.js |
| MySQL / MariaDB | 8.0+ / 10.4+ | Database utama |
| Git | 2.x+ | Untuk koneksi ke GitHub |
| Web Server | Nginx / Apache | Disarankan Nginx |

> **Ekstensi PHP yang wajib aktif:** `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd` atau `imagick`, `zip`, `tokenizer`, `xml`, `bcmath`, `intl`.

---

## 2. Checklist Sebelum Deploy Pertama Kali

Selesaikan seluruh poin berikut sebelum sistem digunakan secara resmi.

### A. Pembersihan File & Folder Development ⚠️

> Ini adalah langkah yang **sering terlewat**. File dan folder di bawah ini hanya boleh ada di komputer lokal (development) — **jangan sampai ikut ke GitHub atau ke server produksi**.

**Folder `scratch/`** — berisi skrip-skrip sementara yang dibuat selama pengembangan (PHP, Python, txt). Tidak ada fungsinya di server.

```bash
# Hapus folder scratch dari project (jalankan di root project)
rm -rf scratch/
```

Atau hapus manual via File Explorer jika di Windows.

Selain itu, pastikan `scratch/` sudah masuk ke `.gitignore` (sudah ditambahkan):

```
# Di file .gitignore
/scratch
```

**File lain yang tidak boleh di-push ke GitHub:**

| File / Folder | Keterangan | Status .gitignore |
|---|---|---|
| `.env` | Berisi password & secret key | ✅ Sudah dikecualikan |
| `node_modules/` | Dependensi frontend (diinstall ulang di server) | ✅ Sudah dikecualikan |
| `vendor/` | Dependensi PHP (diinstall ulang di server) | ✅ Sudah dikecualikan |
| `public/build/` | Hasil build frontend (dibangun ulang di server) | ✅ Sudah dikecualikan |
| `storage/logs/` | File log (dibuat otomatis oleh Laravel) | ✅ Sudah dikecualikan |
| `scratch/` | Skrip-skrip sementara development | ✅ Baru ditambahkan |

Setelah memastikan seluruh file di atas bersih, lanjutkan ke checklist berikutnya.

---

### B. Konfigurasi `.env`

- [ ] Salin file `.env.example` menjadi `.env`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_URL` sesuai domain/IP server (contoh: `http://192.168.1.100` atau `https://beasiswa.blitarkab.go.id`)
- [ ] Isi konfigurasi database (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- [ ] Generate APP_KEY: `php artisan key:generate`
- [ ] Set `TURNSTILE_SITE_KEY` dan `TURNSTILE_SECRET_KEY` dengan kunci produksi (bukan kunci test)
- [ ] Set konfigurasi email jika diperlukan untuk notifikasi

### C. Keamanan Akun

- [ ] Ubah password seluruh akun administrator bawaan (seeder) — jangan biarkan menggunakan `password`
- [ ] Pastikan tidak ada akun test yang tidak sengaja aktif

### D. File & Folder Permission

- [ ] Folder `storage/` wajib dapat ditulis oleh web server: `chmod -R 775 storage`
- [ ] Folder `bootstrap/cache/` wajib dapat ditulis: `chmod -R 775 bootstrap/cache`
- [ ] Owner folder disesuaikan: `chown -R www-data:www-data /path/ke/proyek` (untuk Nginx/Apache di Linux)

### E. Fitur Khusus Testing

- [ ] Pastikan akses menu **Bypass OPD** dan **Data Dummy** di dashboard Super Admin **tidak dapat diakses** oleh pengguna selain Super Admin. Jika perlu, nonaktifkan route tersebut di `routes/super-admin.php`.

### F. Konfigurasi Web Server (Nginx)

Contoh konfigurasi Nginx untuk Laravel:

```nginx
server {
    listen 80;
    server_name beasiswa.blitarkab.go.id;  # ganti sesuai domain/IP

    root /var/www/beasiswa/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    client_max_body_size 20M;  # sesuaikan batas upload dokumen
}
```

---

## 3. Langkah Deploy Pertama Kali ke Server

Jalankan langkah-langkah berikut secara berurutan melalui terminal server (SSH).

### Langkah 1 — Clone Repository dari GitHub

```bash
cd /var/www
git clone https://github.com/NAMA_USER/NAMA_REPO.git beasiswa
cd beasiswa
```

> Ganti `NAMA_USER/NAMA_REPO` dengan URL repository GitHub Anda.

### Langkah 2 — Buat dan Isi File `.env`

```bash
cp .env.example .env
nano .env
# Isi seluruh konfigurasi sesuai kebutuhan, lalu simpan
```

### Langkah 3 — Install Dependensi PHP

```bash
composer install --optimize-autoloader --no-dev
```

### Langkah 4 — Generate Application Key

```bash
php artisan key:generate
```

### Langkah 5 — Build Aset Frontend

```bash
npm install
npm run build
```

### Langkah 6 — Jalankan Migrasi & Seeder

```bash
php artisan migrate --force
php artisan db:seed --force
```

> Perintah `db:seed` akan membuat akun administrator bawaan. **Segera ubah passwordnya setelah ini.**

### Langkah 7 — Atur Permission Folder

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /var/www/beasiswa
```

### Langkah 8 — Optimasi Aplikasi

```bash
php artisan optimize
php artisan storage:link
```

### Langkah 9 — Konfigurasi Cron Job (Auto-Verify SLA)

Tambahkan baris berikut ke crontab server (`crontab -e`) agar Auto-Verify SLA berjalan otomatis setiap hari:

```cron
* * * * * www-data php /var/www/beasiswa/artisan schedule:run >> /dev/null 2>&1
```

Ini akan menjalankan Laravel Scheduler setiap menit, dan scheduler akan memproses job yang sudah terjadwal (termasuk `beasiswa:auto-verify`).

### Langkah 10 — Restart Web Server

```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

Sistem sekarang sudah aktif dan dapat diakses melalui browser.

---

## 4. Setup Auto-Update: Push GitHub → Server Otomatis Update

Tujuan: setiap kali Anda melakukan `git push` dari komputer lokal, server akan otomatis mengambil kode terbaru dan menjalankan perintah update yang diperlukan.

Ada dua cara yang bisa digunakan:

---

### Cara A — GitHub Actions + SSH (Direkomendasikan jika server punya akses internet)

Cara ini menggunakan fitur **GitHub Actions** untuk menghubungkan GitHub ke server via SSH setiap kali ada push ke branch `main`.

#### Langkah 1: Buat SSH Key di Server

```bash
ssh-keygen -t ed25519 -C "github-deploy" -f ~/.ssh/github_deploy
```

Dua file akan dibuat:
- `~/.ssh/github_deploy` → Private Key (disimpan di GitHub sebagai Secret)
- `~/.ssh/github_deploy.pub` → Public Key (ditambahkan ke server)

#### Langkah 2: Tambahkan Public Key ke Server

```bash
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

#### Langkah 3: Tambahkan Private Key ke GitHub Secrets

1. Buka repositori di GitHub → **Settings** → **Secrets and variables** → **Actions**
2. Klik **New repository secret**, tambahkan:
   - `SSH_PRIVATE_KEY` → isi dengan isi file `~/.ssh/github_deploy` (private key)
   - `SSH_HOST` → IP atau hostname server (contoh: `192.168.1.100`)
   - `SSH_USER` → nama user SSH (contoh: `ubuntu` atau `www-data`)
   - `SSH_PATH` → path aplikasi di server (contoh: `/var/www/beasiswa`)

#### Langkah 4: Buat File GitHub Actions Workflow

Buat file `.github/workflows/deploy.yml` di dalam repositori:

```yaml
name: Deploy to Server

on:
  push:
    branches:
      - main  # ganti jika branch utama Anda berbeda (misal: master)

jobs:
  deploy:
    name: Deploy via SSH
    runs-on: ubuntu-latest

    steps:
      - name: Deploy ke Server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SSH_HOST }}
          username: ${{ secrets.SSH_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd ${{ secrets.SSH_PATH }}
            git pull origin main
            composer install --optimize-autoloader --no-dev
            npm install
            npm run build
            php artisan migrate --force
            php artisan optimize:clear
            php artisan optimize
            echo "Deploy selesai: $(date)"
```

#### Langkah 5: Commit dan Push File Workflow

```bash
git add .github/workflows/deploy.yml
git commit -m "Tambahkan GitHub Actions auto-deploy"
git push origin main
```

Setelah ini, setiap push ke branch `main` akan otomatis memicu proses deploy ke server.

---

### Cara B — Git Webhook (Untuk Server Intranet tanpa akses internet ke GitHub)

Jika server berada di jaringan intranet (tidak dapat dijangkau dari GitHub), gunakan pendekatan ini: **server secara berkala mengecek GitHub dan pull jika ada perubahan**.

#### Buat Script Deploy di Server

Buat file `/var/www/beasiswa/deploy.sh`:

```bash
#!/bin/bash
cd /var/www/beasiswa

echo "=== [$(date)] Memulai auto-deploy ==="

# Ambil perubahan terbaru dari GitHub
git fetch origin main
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/main)

if [ "$LOCAL" != "$REMOTE" ]; then
    echo "Ditemukan update baru. Menjalankan deploy..."
    git pull origin main
    composer install --optimize-autoloader --no-dev
    npm install
    npm run build
    php artisan migrate --force
    php artisan optimize:clear
    php artisan optimize
    sudo systemctl reload nginx
    echo "Deploy selesai."
else
    echo "Tidak ada perubahan."
fi
```

```bash
chmod +x /var/www/beasiswa/deploy.sh
```

#### Jadwalkan Script via Cron

```bash
crontab -e
```

Tambahkan baris berikut (cek setiap 5 menit):

```cron
*/5 * * * * /var/www/beasiswa/deploy.sh >> /var/log/beasiswa-deploy.log 2>&1
```

---

## 5. Alur Kerja Sehari-Hari (Update Kode)

Setelah setup selesai, alur kerja harian untuk memperbarui aplikasi menjadi sangat sederhana:

```
[Komputer Lokal]
       │
       │  1. Edit kode di VS Code / IDE
       │
       ▼
  git add .
  git commit -m "Keterangan perubahan"
  git push origin main
       │
       │  2. GitHub menerima push
       │
       ▼
[GitHub Actions / Cron Script]
       │
       │  3. Server otomatis:
       │     - git pull
       │     - composer install
       │     - npm run build
       │     - php artisan migrate
       │     - php artisan optimize
       │
       ▼
[Server Produksi Terupdate]
       │
       │  4. Website langsung mencerminkan perubahan terbaru
       ▼
     SELESAI
```

> **Tips:** Tambahkan komentar yang jelas di setiap commit agar riwayat perubahan mudah dilacak, contoh: `git commit -m "Fix: perbaiki tampilan tabel verifikasi OPD"`.

---

## 6. Perintah Darurat & Pemecahan Masalah

### Rollback ke Versi Sebelumnya

Jika terjadi masalah setelah update:

```bash
cd /var/www/beasiswa
git log --oneline -10   # lihat daftar commit
git revert HEAD         # batalkan commit terakhir
# atau
git reset --hard <commit-hash>  # kembali ke commit tertentu
php artisan optimize:clear && php artisan optimize
```

### Error 500 Setelah Deploy

```bash
# Cek log error
tail -n 50 storage/logs/laravel.log

# Bersihkan semua cache
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Build ulang cache
php artisan optimize
```

### Storage/Upload Tidak Bisa Diakses

```bash
php artisan storage:link
chmod -R 775 storage
chown -R www-data:www-data storage
```

### Database Migration Gagal

```bash
# Cek status migration
php artisan migrate:status

# Jalankan hanya migration yang belum berjalan
php artisan migrate --force
```

### Cek Status Cron Job (Auto-Verify SLA)

```bash
# Lihat log auto-deploy (jika menggunakan Cara B)
tail -f /var/log/beasiswa-deploy.log

# Jalankan schedule secara manual untuk testing
php artisan schedule:run
php artisan beasiswa:auto-verify
```

### Maintenance Mode (Sementara Menonaktifkan Website)

```bash
# Aktifkan maintenance mode
php artisan down --message="Sistem sedang dalam pemeliharaan. Mohon tunggu sebentar."

# Lakukan update/perbaikan...

# Nonaktifkan maintenance mode
php artisan up
```

---

> Dokumen ini mencakup dua skenario deployment: server dengan akses internet (GitHub Actions) dan server intranet (Cron Script). Pilih salah satu yang sesuai dengan kondisi infrastruktur Anda.

---

## 7. Masalah CSS & JS Tidak Terbaca di Server (Wajib Dibaca!) ⚠️

Ini adalah masalah yang **hampir pasti terjadi** jika Anda lupa menanganinya. Website akan tampil **polos putih tanpa styling** sama sekali — semua tampilan hilang, tidak ada warna, tidak ada layout.

### Mengapa ini terjadi?

Proyek ini menggunakan **Vite** untuk memproses CSS (Tailwind) dan JavaScript. Hasil kompilasinya disimpan di folder `public/build/`. Folder ini **sengaja dikecualikan dari GitHub** (ada di `.gitignore`):

```
/public/build   ← tidak ikut ke GitHub
```

Artinya: saat server melakukan `git pull`, folder `public/build/` **tidak akan ada**. Semua layout (`app.blade.php`, `admin.blade.php`, `guest.blade.php`, `public.blade.php`) menggunakan `@vite(...)`:

```html
<!-- resources/views/components/layouts/app.blade.php -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

Jika folder build tidak ada → direktif `@vite` tidak bisa menemukan file → **seluruh CSS dan JS tidak terbaca**.

### Solusinya

**Wajib jalankan `npm run build` di server setiap kali deploy.** Perintah ini sudah tercantum di seluruh script deploy di dokumen ini (langkah 5 deploy pertama, GitHub Actions, maupun Cron Script).

Verifikasi build berhasil dengan mengecek apakah folder dan file berikut ada di server:

```
public/
└── build/
    ├── manifest.json     ← file ini yang dibaca Laravel untuk load aset
    └── assets/
        ├── app-XXXXXXXX.css
        └── app-XXXXXXXX.js
```

Jika folder `public/build/` tidak ada atau kosong → build belum berjalan. Jalankan ulang:

```bash
cd /var/www/beasiswa
npm install
npm run build
```

### Masalah Tambahan: APP_URL Salah

Jika CSS sudah di-build tapi masih tidak terbaca, kemungkinan `APP_URL` di `.env` tidak sesuai dengan URL yang diakses. Laravel menggunakan `APP_URL` untuk membentuk URL aset.

```bash
# Contoh jika diakses via IP:
APP_URL=http://192.168.1.100

# Contoh jika diakses via domain:
APP_URL=https://beasiswa.blitarkab.go.id
```

Setelah ubah `APP_URL`, wajib jalankan:

```bash
php artisan optimize:clear
php artisan optimize
```

### Checklist Diagnosis Cepat

Jika website tampil polos setelah deploy, jalankan urutan ini:

```bash
# 1. Cek apakah folder build ada
ls -la public/build/

# 2. Jika tidak ada, build ulang
npm install && npm run build

# 3. Bersihkan semua cache Laravel
php artisan optimize:clear

# 4. Rebuild cache
php artisan optimize

# 5. Pastikan storage link ada
php artisan storage:link

# 6. Cek log jika masih error
tail -n 30 storage/logs/laravel.log
```
