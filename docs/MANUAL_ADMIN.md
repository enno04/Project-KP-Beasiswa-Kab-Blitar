# Manual Book — Panduan Administrator
## Sistem Beasiswa Blitar Mengabdi

Versi : 2.4  
Terakhir Diperbarui : Agustus 2026

> **Akses Login Administrator:** Buka URL **`/mengabdi`** di browser. Tombol login tidak ditampilkan di halaman publik.

---

## Daftar Isi

1. [Daftar Role Administrator](#1-daftar-role-administrator)
2. [Super Admin](#2-super-admin)
3. [Admin Kabupaten](#3-admin-kabupaten)
4. [Admin OPD](#4-admin-opd)
5. [Admin Kecamatan](#5-admin-kecamatan)
6. [Admin DPMD](#6-admin-dpmd)
7. [Admin Desa](#7-admin-desa)
8. [Audit Log System](#8-audit-log-system)
9. [Auto-Verifikasi SLA (3 Hari)](#9-auto-verifikasi-sla-3-hari)
10. [Pipeline Status Pendaftaran](#10-pipeline-status-pendaftaran)
11. [Kredensial Default & Keamanan](#11-kredensial-default--keamanan)

---

## 1. Daftar Role Administrator

Sistem menggunakan autentikasi berbasis role (RBAC). Setiap administrator hanya dapat mengakses menu dan data sesuai kewenangannya.

| Role | URL Dashboard | Lingkup Tugas |
|------|--------------|---------------|
| `super_admin` | `/super-admin/*` | Pengelolaan seluruh sistem |
| `admin_kabupaten` | `/kabupaten/*` | Seleksi, penilaian, dan penetapan akhir |
| `admin_opd` | `/opd/*` | Verifikasi dokumen per OPD |
| `admin_kecamatan` | `/kecamatan/*` | Persetujuan SDSS tingkat kecamatan |
| `admin_dpmd` | `/dpmd/*` | Persetujuan SDSS tingkat kabupaten |
| `admin_desa` | `/desa/*` | Seleksi dan rekomendasi tingkat desa |

---

## 2. Super Admin

Super Admin adalah pengelola utama sistem. Memiliki akses penuh ke seluruh modul konfigurasi, referensi wilayah, monitoring, dan audit log.

### 2.1 Master Referensi

Tersedia di menu **Master > Referensi**:

#### Manajemen User
- Membuat, mengedit, dan menonaktifkan akun administrator.
- Menetapkan role dan wilayah kerja (untuk Admin OPD, Kecamatan, DPMD, dan Desa).
- Password baru dapat di-reset melalui menu ini.
- Seluruh perubahan data user dicatat otomatis di Audit Log (termasuk perubahan password — nilai password disamarkan menjadi `[DIUBAH]`).

#### Referensi OPD
Mengelola daftar OPD yang bertugas melakukan verifikasi dokumen. Setiap OPD dapat dikaitkan dengan jenis dokumen yang menjadi kewenangannya.

#### Referensi Kecamatan & Desa
Mengelola master data wilayah administratif (Kecamatan dan Desa/Kelurahan). Data ini digunakan sebagai referensi pada saat pendaftaran dan penetapan akun Admin Desa/Kecamatan.

---

### 2.2 Master Konfigurasi

Tersedia di menu **Master > Konfigurasi**:

#### Periode
- Membuat Periode Beasiswa (1 periode per tahun anggaran).
- Mengaktifkan periode agar sistem membuka pendaftaran secara publik.
- Satu periode aktif berlaku untuk semua program beasiswa yang dibuka pada tahun tersebut.

#### Program & Jalur
- Mengaktifkan program beasiswa (`sdss`, `berdaya_berjaya`, `bantuan_biaya`) untuk periode tertentu.
- Menambahkan dan mengatur Jalur Pendaftaran per program (misal: Jalur Mahasiswa Baru, Jalur Mahasiswa Lama).

#### Persyaratan & Dokumen
- Mendefinisikan dokumen apa saja yang wajib diunggah pendaftar pada setiap jalur.
- Menetapkan OPD yang berwenang memverifikasi setiap jenis dokumen.

#### Kriteria & Bobot SPK
- Menambahkan kriteria penilaian per jalur (misal: IPK, Penghasilan Orang Tua, dll).
- Menetapkan bobot persentase untuk masing-masing kriteria.
- Total bobot seluruh kriteria dalam satu jalur harus berjumlah 100%.
- Seluruh perubahan bobot dicatat di Audit Log (contoh: `Prestasi Akademik: 30% → 40%`).

---

### 2.3 Dokumen Publik

Mengelola dokumen yang dapat diunduh oleh masyarakat dari halaman publik:
- Mengunggah Juknis / SK / Ebook Panduan / Template Surat.
- Format yang didukung: **PDF, DOC, DOCX** (maksimum 10 MB per file).
- Dokumen yang dipublikasikan akan langsung tampil di halaman `/dokumen-penting`.

---

### 2.4 Monitoring Pendaftaran

Super Admin dapat melihat seluruh data pendaftaran lintas program, jalur, kecamatan, dan desa. Tersedia fitur filter berdasarkan program, jalur, kecamatan, desa, dan status.

---

### 2.5 Import Histori Penerima

Tersedia menu **Import Histori** untuk memasukkan data penerima beasiswa dari sistem atau arsip periode sebelumnya. Mendukung dua format Excel:
- **Format Lama** — untuk data migrasi dari sistem sebelumnya.
- **Format Baru** — untuk data ekspor dari sistem ini.

Data yang diimpor akan digunakan untuk validasi NIK pada pendaftaran SDSS (kunci seumur hidup).

---

### 2.6 Bypass OPD (Fitur Testing)

Tersedia menu khusus untuk memverifikasi dokumen secara massal tanpa melalui proses OPD. **Hanya gunakan fitur ini di lingkungan testing/pengembangan.**

> ⚠️ **Nonaktifkan atau hapus akses menu ini sebelum sistem digunakan secara resmi di lingkungan produksi.**

---

### 2.7 Auto-Verifikasi SLA

Tombol **Jalankan Auto-Verify SLA** tersedia di dashboard Super Admin. Fungsi ini menjalankan command `beasiswa:auto-verify` secara manual. Lihat bagian [Auto-Verifikasi SLA](#9-auto-verifikasi-sla-3-hari) untuk penjelasan lengkap.

---

### 2.8 Audit Log

Tersedia di menu **Audit Log**. Menampilkan seluruh catatan aktivitas sistem dengan fitur:
- **Filter multi-kriteria** — berdasarkan kata kunci, nama pengguna, jenis aktivitas, dan rentang tanggal.
- **JSON Diff Viewer** — klik tombol pada baris log untuk melihat perbandingan data sebelum dan sesudah perubahan secara *side-by-side*.
- Log bersifat *append-only* dan tidak dapat dihapus oleh siapapun.

---

## 3. Admin Kabupaten

Admin Kabupaten merupakan tim seleksi utama yang menangani tahap akhir proses seleksi seluruh program beasiswa.

### 3.1 Dashboard

Menampilkan ringkasan statistik:
- Jumlah pendaftar yang lolos verifikasi OPD.
- Jumlah pendaftar dalam proses penilaian.
- Jumlah pendaftar yang sudah ditetapkan (lulus/tidak lulus).

### 3.2 Program Berdaya Berjaya — Input Nilai Wawancara

1. Buka menu **Berdaya Berjaya**.
2. Pilih jalur (Mahasiswa Baru / Mahasiswa Lama).
3. Pada tabel pendaftar yang berstatus **Menunggu Wawancara**, klik tombol **Input Nilai**.
4. Masukkan nilai wawancara (0–100).
5. Atau gunakan tombol **Gugur Wawancara** jika pendaftar tidak hadir.

### 3.3 Kalkulasi SPK & Peringkatan

1. Setelah nilai wawancara diinput (atau untuk program non-wawancara), klik tombol **Hitung SPK** pada jalur yang bersangkutan.
2. Sistem akan menghitung skor total menggunakan rumus Weighted Sum Model:

   ```
   Nilai Total = Σ (Skor Diperoleh / Skor Maksimum × Bobot Kriteria)
   ```

3. Hasil peringkat tampil otomatis di tabel, diurutkan dari skor tertinggi.
4. Jika ada nilai yang sama, urutan ditentukan berdasarkan waktu pendaftaran paling awal.

### 3.4 Penetapan Penerima & Penerbitan SK

1. Pada tabel peringkat, pilih pendaftar yang akan ditetapkan.
2. Gunakan tombol **Lulus** untuk menetapkan sebagai penerima, atau **Tidak Lulus** untuk menolak.
3. Setelah penetapan selesai, klik tombol **Terbitkan SK** untuk mengubah status dari `lulus` menjadi `sk_terbit`.
4. Status `sk_terbit` akan langsung terlihat oleh pendaftar melalui halaman **Cek Status**.

### 3.5 Export Data

Tersedia tombol **Export Excel** untuk mengunduh data penerima yang sudah ditetapkan dalam format `.xlsx`. Data ini dapat digunakan untuk kebutuhan pelaporan dan arsip administrasi.

### 3.6 Unlock Penilaian Desa (SDSS)

Sebelum Admin Desa dapat melakukan perangkingan, Admin Kabupaten perlu mengaktifkan fitur **Unlock Penilaian Desa** pada panel kontrol program SDSS. Hal ini memastikan tidak ada Admin Desa yang melakukan ranking sebelum waktunya.

---

## 4. Admin OPD

Admin OPD bertugas memverifikasi keaslian dan keabsahan dokumen yang diunggah pendaftar, sesuai dengan kewenangan OPD masing-masing.

### 4.1 Dashboard

Menampilkan statistik:
- Jumlah berkas yang belum diverifikasi.
- Jumlah berkas yang sudah diverifikasi (Valid / Tidak Valid).

### 4.2 Proses Verifikasi Dokumen

1. Buka menu **Verifikasi Dokumen**.
2. Sistem hanya menampilkan dokumen yang menjadi kewenangan OPD yang sedang login (mapping otomatis).
3. Klik tombol **Verifikasi** pada baris dokumen yang ingin diproses.
4. Di dalam modal verifikasi:
   - Lihat pratinjau dokumen yang diunggah pendaftar.
   - Pilih status: **Valid** atau **Tidak Valid**.
   - Isi kolom **Catatan** jika ada keterangan tambahan (wajib diisi jika status Tidak Valid).
5. Klik **Simpan**.

> **Aturan Penting:**  
> - Jika seluruh dokumen wajib dinyatakan **Valid**, pendaftar secara otomatis berstatus **Lolos Verifikasi**.  
> - Jika ada satu saja dokumen yang **Tidak Valid**, pendaftar otomatis berstatus **Tidak Lolos Verifikasi**.  
> - Verifikasi antar OPD berjalan secara paralel dan independen.

### 4.3 Riwayat Verifikasi

Menu ini menampilkan seluruh riwayat verifikasi yang telah dilakukan oleh OPD yang sedang login.

---

## 5. Admin Kecamatan

Admin Kecamatan bertugas memberikan persetujuan terhadap berkas rekomendasi yang dikirimkan oleh Desa dalam program SDSS.

### 5.1 Dashboard

Menampilkan monitoring status musyawarah desa-desa yang berada di wilayah kecamatan yang bersangkutan.

### 5.2 Proses Persetujuan SDSS

1. Buka menu **Program SDSS**.
2. Tabel menampilkan daftar pendaftar SDSS usulan desa-desa di wilayah kecamatan.
3. Klik nama pendaftar untuk membuka halaman detail.
4. Pada halaman detail, periksa:
   - Biodata lengkap pendaftar.
   - Surat Rekomendasi Kades/Lurah (dapat dibuka/diunduh).
   - Berita Acara Musyawarah Desa.
5. Isi formulir persetujuan Kecamatan:
   - Pilih keputusan: **Disetujui** atau **Ditolak**.
   - Isi catatan/alasan (wajib diisi jika Ditolak).
6. Klik **Simpan Keputusan**.

> Proses ini berjalan **paralel** dengan Admin DPMD. Kedua keputusan diperlukan sebelum berkas dapat diteruskan ke Admin Kabupaten.

---

## 6. Admin DPMD

Admin DPMD bertugas memberikan persetujuan tingkat kabupaten terhadap berkas rekomendasi SDSS dari seluruh desa se-wilayah.

### 6.1 Dashboard

Menampilkan:
- Statistik pendaftar SDSS se-wilayah yang sudah diteruskan oleh Desa.
- Monitoring progres verifikasi per kecamatan.
- Daftar pendaftar terbaru yang menunggu verifikasi DPMD.

### 6.2 Proses Persetujuan SDSS

1. Buka menu **Program SDSS**.
2. Tabel menampilkan seluruh pendaftar SDSS yang telah mendapatkan rekomendasi dari Desa, beserta status persetujuan Kecamatan.
3. Klik nama pendaftar untuk membuka halaman detail.
4. Pada halaman detail, periksa:
   - Biodata lengkap pendaftar.
   - Surat Rekomendasi & Berita Acara Musyawarah Desa.
   - Panel ringkasan status persetujuan paralel (Kecamatan & DPMD).
5. Isi formulir persetujuan DPMD:
   - Pilih keputusan: **Disetujui** atau **Ditolak**.
   - Isi catatan jika diperlukan (wajib diisi jika Ditolak).
6. Klik **Simpan Keputusan**.

> Berkas hanya akan diteruskan ke Admin Kabupaten apabila **Kecamatan DAN DPMD keduanya telah memberikan keputusan Disetujui**.

---

## 7. Admin Desa

Admin Desa bertugas melakukan seleksi tingkat desa untuk program SDSS.

### 7.1 Dashboard

Menampilkan ringkasan jumlah pendaftar SDSS yang berasal dari desa yang bersangkutan.

### 7.2 Melihat Daftar & Nilai Pendaftar

1. Buka menu **Daftar Pendaftar SDSS**.
2. Tabel menampilkan seluruh pendaftar dari desa tersebut beserta:
   - Nilai SPK yang dihitung otomatis oleh sistem.
   - Peringkat sementara berdasarkan nilai.

> **Catatan:** Fitur ranking baru aktif setelah Admin Kabupaten membuka kunci penilaian desa.

### 7.3 Penetapan Perwakilan Desa

1. Setelah meninjau nilai dan peringkat, klik tombol **Tetapkan sebagai Perwakilan Desa** pada pendaftar yang dipilih.
2. Sistem akan memberikan konfirmasi: pendaftar yang tidak dipilih akan otomatis berstatus `tidak_lolos_desa`.
3. Penetapan ini bersifat **final** dan hanya dapat dilakukan **1 (satu) kali**.

### 7.4 Unggah Surat Rekomendasi

Setelah penetapan, Admin Desa wajib mengunggah berkas rekomendasi:

1. Klik tombol **Upload Rekomendasi** pada pendaftar yang sudah ditetapkan.
2. Unggah berkas berikut:
   - **Surat Rekomendasi Kades/Lurah** (PDF, maks 5 MB)
   - **Berita Acara Musyawarah Desa** (PDF, maks 5 MB)
3. Klik **Simpan**.

Setelah berkas terunggah, sistem secara otomatis meneruskan berkas secara paralel ke **Dashboard Kecamatan** dan **Dashboard DPMD**.

---

## 8. Audit Log System

Seluruh aktivitas penting dalam sistem dicatat secara otomatis pada tabel Audit Log. Log ini bersifat *append-only* (tidak bisa dihapus) dan transparan.

### Cakupan Pencatatan

| Jenis Aktivitas | Detail yang Dicatat |
|----------------|---------------------|
| Login / Logout | Nama user, IP Address, User Agent, waktu |
| Perubahan User | Sebelum & sesudah data (password disamarkan) |
| Perubahan Kriteria & Bobot | Nilai persentase sebelum & sesudah |
| Perubahan Konfigurasi | Periode, Program, Jalur, Dokumen, Persyaratan |
| Perubahan Referensi | OPD, Kecamatan, Desa |
| Verifikasi Dokumen | Status dokumen sebelum & sesudah |
| Persetujuan Rekomendasi | Keputusan Kecamatan & DPMD |
| Penetapan Penerima | Keputusan akhir per pendaftar |
| Auto-Verifikasi Sistem | Rekomendasi yang diselesaikan otomatis oleh SLA |

### Cara Membaca Audit Log

1. Buka menu **Audit Log** (khusus Super Admin).
2. Gunakan filter untuk mempersempit hasil pencarian:
   - **Kata kunci** — cari berdasarkan nama user atau deskripsi aktivitas.
   - **Pengguna** — filter berdasarkan akun yang melakukan aksi.
   - **Jenis Aktivitas** — filter berdasarkan kategori aktivitas.
   - **Rentang Tanggal** — filter berdasarkan periode waktu.
3. Klik ikon **Detail** pada baris log yang ingin dilihat untuk membuka **JSON Diff Viewer**.
4. Viewer menampilkan data lama (highlight merah) dan data baru (highlight hijau) secara berdampingan.

---

## 9. Auto-Verifikasi SLA (3 Hari)

Sistem memiliki mekanisme otomatis untuk mencegah berkas tertahan terlalu lama di tingkat Kecamatan atau DPMD.

### Cara Kerja

- Jika sebuah Surat Rekomendasi sudah dikirimkan oleh Desa, namun **Kecamatan atau DPMD belum memberikan keputusan dalam waktu 3 hari**, sistem secara otomatis mengubah status menjadi **Disetujui**.
- Catatan otomatis ditambahkan: *"Disetujui otomatis oleh sistem karena melewati batas waktu (SLA 3 Hari)."*
- Seluruh aksi ini dicatat di Audit Log dengan label aktivitas **Auto-Verifikasi Sistem**.
- Jika kedua pihak sudah otomatis disetujui, berkas langsung diteruskan ke tahap **Menunggu Penetapan** (Admin Kabupaten).

### Cara Menjalankan Manual

Super Admin dapat menjalankan proses auto-verify secara manual:
1. Buka Dashboard Super Admin.
2. Klik tombol **Jalankan Auto-Verify SLA**.
3. Sistem akan memproses seluruh rekomendasi yang memenuhi kriteria SLA 3 hari.

> Idealnya, proses ini dikonfigurasi berjalan otomatis menggunakan **Laravel Scheduler** (cron job) pada server produksi.

---

## 10. Pipeline Status Pendaftaran

Berikut seluruh kemungkinan status pendaftaran dalam sistem:

```
[Pendaftar Submit]
       ↓
menunggu_verifikasi
       ↓
sedang_diverifikasi
       ↓
   ┌─────────────────────────────────┐
   │                                 │
tidak_lolos_verifikasi          lolos_verifikasi
   │                                 ↓
[GUGUR]              [SDSS]   proses_seleksi / proses_penilaian
                        ↓                    ↓
               tidak_lolos_desa      [Berdaya Berjaya]
               [GUGUR]           menunggu_wawancara
                        ↓              ↓
               diteruskan_ke_kecamatan  gugur_wawancara / proses_wawancara
                        ↓
         ┌──────────────┴──────────────┐
         ↓                            ↓
 [Verifikasi Kec.]           [Verifikasi DPMD]
         └──────────────┬──────────────┘
                        ↓
              menunggu_penetapan
                        ↓
            ┌───────────┴───────────┐
            ↓                      ↓
          lulus                tidak_lulus
            ↓
         sk_terbit  ← SELESAI (Final)
```

---

## 11. Kredensial Default & Keamanan

### Akun Bawaan (Seeder)

Akun berikut dibuat otomatis saat menjalankan `php artisan migrate:fresh --seed`:

| Role | Username | Password |
|------|----------|----------|
| Super Admin | `superadmin` | `password` |
| Admin Kabupaten | `adminkab` | `password` |
| Admin DPMD | `admindpmd` | `password` |
| Admin OPD (Dispora) | `admin_dispora` | `password` |
| Admin OPD (Disdukcapil) | `admin_disdukcapil` | `password` |
| Admin OPD (Dinsos) | `admin_dinsos` | `password` |
| Admin OPD (Disdik) | `admin_disdik` | `password` |
| Admin OPD (PMD) | `admin_pmd` | `password` |
| Admin OPD (Kesra) | `admin_kesra` | `password` |
| Admin Kecamatan (contoh) | `adminkec_kanigoro` | `password` |
| Admin Desa (contoh) | `admindesa_3505072012` | `password` |

> Daftar lengkap akun Admin Kecamatan dan Admin Desa lainnya dapat dilihat melalui menu **Referensi Wilayah** di dashboard Super Admin, atau langsung pada tabel `referensi_kecamatan` dan `referensi_desa` di database.

### Checklist Keamanan Sebelum Produksi

- [ ] Ubah seluruh password bawaan menjadi password yang kuat dan unik.
- [ ] Set `APP_DEBUG=false` dan `APP_ENV=production` di file `.env`.
- [ ] Nonaktifkan atau hapus akses menu **Bypass OPD** dan **Generator Dummy**.
- [ ] Pastikan `TURNSTILE_SITE_KEY` dan `TURNSTILE_SECRET_KEY` menggunakan kunci produksi yang valid.
- [ ] Konfigurasi Laravel Scheduler (`cron`) di server agar Auto-Verify SLA berjalan otomatis.

---

*Dokumen ini merupakan panduan resmi penggunaan Sistem Beasiswa Blitar Mengabdi untuk seluruh pengguna administrator.*
