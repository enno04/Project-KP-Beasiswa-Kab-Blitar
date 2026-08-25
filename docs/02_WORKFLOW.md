# WORKFLOW SISTEM
## Website Beasiswa Blitar Mengabdi

Versi : 2.4

---

# 1. Tujuan

Dokumen ini menjelaskan alur bisnis (Business Workflow) Sistem Beasiswa Blitar Mengabdi mulai dari persiapan periode, pendaftaran, verifikasi, seleksi, penetapan, hingga penerbitan SK.

Dokumen ini menjadi acuan utama dalam pengembangan sistem, penyusunan database, hak akses, dan implementasi setiap modul.

---

# 2. Siklus Periode

Setiap Tahun Anggaran memiliki satu Periode Beasiswa (misal: Periode 2026, 2027, 2028).

Pada setiap periode dapat dibuka satu atau lebih Program Beasiswa (`sdss`, `berdaya_berjaya`, `bantuan_biaya`) yang dikonfigurasi melalui Master Data.

---

# 3. Persiapan Periode

### Pelaksana
Super Admin

### Proses
1. Membuat Periode
2. Mengaktifkan Program Beasiswa
3. Mengatur Jalur Beasiswa
4. Mengatur Jadwal
5. Mengatur Tahapan Seleksi
6. Mengatur Persyaratan
7. Mengatur Dokumen
8. Mengatur Kriteria Penilaian
9. Mengatur Bobot Penilaian
10. Mempublikasikan Periode

---

# 4. Workflow Pendaftaran

### Pelaksana
Masyarakat (Tanpa Login)

### Input
- Periode
- Program Beasiswa
- Jalur Beasiswa

### Form Pendaftaran 5 Step
- **Step 1:** Identitas Pendaftar (Statis)
- **Step 2:** Data Orang Tua / Wali (Statis)
- **Step 3:** Status Ekonomi (Dinamis per Kriteria Master)
- **Step 4:** Data Akademik (Dinamis per Kriteria Master)
- **Step 5:** Upload Dokumen (Dinamis per Dokumen Master)

---

# 5. Review Pendaftaran

Sebelum data dikirim, sistem menampilkan seluruh data yang telah diisi.

Pendaftar wajib menyetujui Kebijakan Privasi dan Pelindungan Data Pribadi.

Setelah tombol Submit ditekan:
- Data & dokumen terkunci.
- Status menjadi **Menunggu Verifikasi**.

---

# 6. Workflow Verifikasi OPD

Dokumen yang diunggah diverifikasi secara paralel oleh OPD berwenang (Disdukcapil, Dinsos, Bagian Kesra, dll).

- Jika ada dokumen **Tidak Valid**, status pendaftaran menjadi **Tidak Lolos Verifikasi**.
- Jika seluruh dokumen wajib **Valid**, status pendaftaran menjadi **Lolos Verifikasi** dan diteruskan ke tahapan seleksi berikutnya.
- Verifikasi berjalan **paralel** antar OPD; setiap OPD hanya melihat dokumen sesuai kewenangannya (mapping dokumen-OPD).

---

# 6.1 Auto-Approve SLA Kecamatan & DPMD (Khusus SDSS)

Jika Admin Kecamatan atau Admin DPMD belum memberikan keputusan dalam **3 hari** setelah berkas dikirim oleh Desa, sistem secara otomatis mengubah status verifikasi pihak tersebut menjadi **Disetujui** dengan catatan:

> *"Disetujui otomatis oleh sistem karena melewati batas waktu (SLA 3 Hari)."*

- Proses ini dapat dijalankan secara manual oleh Super Admin melalui tombol **"Jalankan Auto-Verify SLA"** di dashboard.
- Idealnya dikonfigurasi via **Laravel Scheduler** (cron job) di server produksi.
- Seluruh aksi auto-approve dicatat di **Audit Log** dengan label `Auto-Verifikasi Sistem`.

---

# 7. Workflow Program

## A. Satu Desa Satu Sarjana (SDSS) — Persetujuan Paralel

### Tahapan Diagram

```
Pendaftar
   ↓
Verifikasi OPD
   ↓
Penilaian Otomatis Sistem & Perangkingan Desa
   ↓
Admin Desa Menetapkan 1 Calon Perwakilan Desa
   ↓
Desa Upload Surat Rekomendasi & Berita Acara Musyawarah
   ↓
┌─────────────────────────┴─────────────────────────┐
▼                                                   ▼
Verifikasi & Persetujuan Kecamatan         Verifikasi & Persetujuan DPMD
└─────────────────────────┬─────────────────────────┘
                          ↓
              Kedua Pihak Menyetujui?
               ├── YA  ──► Penetapan Kabupaten (Draft SK) ──► SK Terbit (Final)
               └── TIDAK ─► Ditolak Kecamatan / Ditolak DPMD
```

### Keterangan Workflow SDSS
- **Penilaian & Ranking Desa:** Nilai dihitung otomatis oleh sistem (Weighted Sum). Dashboard Desa menampilkan peringkat seluruh pendaftar di desanya.
- **Penetapan Perwakilan Desa:** Admin Desa menetapkan 1 (satu) calon perwakilan desa. Pendaftar lain di desa tersebut otomatis gugur (`tidak_lolos_desa`).
- **Upload Berkas Rekomendasi:** Admin Desa mengunggah Surat Rekomendasi Kades/Lurah & Berita Acara Musyawarah Desa.
- **Persetujuan Paralel (Kecamatan + DPMD):** Berkas rekomendasi masuk **secara paralel** ke Dashboard Admin Kecamatan DAN Dashboard Admin DPMD.
- **Persyaratan Kemajuan:** Pendaftaran **hanya diteruskan ke Kabupaten (`menunggu_penetapan`)** apabila **Kecamatan DAN DPMD keduanya telah memberikan persetujuan (`status_kecamatan = disetujui` AND `status_dpmd = disetujui`)**.
- Jika ditolak salah satu pihak, status menjadi `ditolak_kecamatan` atau `ditolak_dpmd`.

---

## B. Berdaya Berjaya

### Tahapan
Pendaftar ──► Verifikasi OPD ──► Penilaian SPK & Tes Wawancara (Admin Kab.) ──► Pemeringkatan ──► Penetapan Kabupaten ──► SK Terbit (Final)

---

## C. Bantuan Biaya Pendidikan (BBP)

### Tahapan
Pendaftar ──► Verifikasi OPD ──► Penilaian SPK ──► Pemeringkatan ──► Penetapan Kabupaten ──► SK Terbit (Final)

---

# 8. Workflow Penilaian SPK

Penilaian dilakukan otomatis oleh sistem menggunakan **Model Penilaian Berbobot (Weighted Sum Model)**:

$$\text{Nilai Total} = \sum \left( \frac{\text{Skor Diperoleh}}{\text{Skor Max}} \times \text{Bobot Kriteria} \right)$$

Tie-breaker untuk total nilai sama: prioritas waktu pendaftaran paling awal (`created_at` ASC).

---

# 9. Workflow Penetapan (Tahap Final)

Penetapan dilakukan oleh Admin Kabupaten. Keputusan Penetapan bersifat final:
- **Lulus — SK Terbit** (Proses administrasi final selesai).
- **Tidak Lulus**.

---

# 10. Output Administrasi

Setelah Penetapan, sistem menghasilkan:
- Draft SK Bupati
- Daftar Penerima Resmi
- Rekapitulasi Penerima per Program & Wilayah

---

# 11. Monitoring & Audit Log

Super Admin & Admin Kabupaten dapat memonitor seluruh proses secara real-time. Entire system activities are recorded in Audit Log.

---

# 12. Pipeline Status Pendaftaran

1. `draft`
2. `menunggu_verifikasi`
3. `sedang_diverifikasi`
4. `lolos_verifikasi` (OPD)
5. `tidak_lolos_verifikasi` (OPD)
6. `tidak_lolos_desa` (Khusus SDSS)
7. `diteruskan_ke_kecamatan` (Khusus SDSS — Menunggu Verif Kec. & DPMD)
8. `ditolak_kecamatan` (Khusus SDSS)
9. `ditolak_dpmd` (Khusus SDSS)
10. `proses_seleksi`
11. `menunggu_wawancara`
12. `proses_wawancara`
13. `gugur_wawancara`
14. `menunggu_penilaian`
15. `proses_penilaian`
16. `menunggu_penetapan`
17. `lulus` / `sk_terbit` (Final — Selesai)
18. `tidak_lulus`

---

# 13. Pengecekan Status Publik

Masyarakat mengakses menu **Cek Status & Cetak Bukti** tanpa login dengan menginputkan:
- **NIK (16 Digit)**
- **Tahun Pendaftaran**

Akses login Admin terselubung di route `/mengabdi`.