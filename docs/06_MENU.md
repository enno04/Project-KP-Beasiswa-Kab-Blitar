# 06_MENU.md

# Struktur Menu
## Website Beasiswa Blitar Mengabdi

Versi : 2.3

---

# 1. Tujuan

Dokumen ini menjelaskan struktur menu pada Website Beasiswa Blitar Mengabdi. Menu disusun berdasarkan modul sistem dan ditampilkan sesuai Role pengguna menggunakan Role Based Access Control (RBAC).

---

# 2. Menu Publik

- **Beranda** (Hero Slider, Program, Alur, FAQ, Kontak)
- **Informasi Beasiswa** (SDSS, Berdaya Berjaya, BBP)
- **Persyaratan & Dokumen Penting** (Ebook Juknis & Template Surat)
- **Cek Status & Cetak Bukti** (Input NIK & Tahun, 5-Stage Stepper Alur)
- **Login Admin** (Akses route terselubung `/mengabdi`, tanpa tombol di navbar publik)

---

# 3. Menu Super Admin

- **Dashboard** (Statistik & Overview System)
- **Master Referensi** (Users, Role, OPD, Kecamatan, Desa)
- **Konfigurasi System** (Periode, Program, Jalur, Tahapan, Persyaratan, Dokumen, Kriteria, Pilihan, Bobot)
- **Dokumen Publik** (Manajemen Ebook & Juknis Publik)
- **Monitoring & Log** (Monitoring Pendaftaran, Histori Penerima, Audit Log)

---

# 4. Menu Admin Kabupaten

- **Dashboard**
- **Program Beasiswa** (SDSS, Berdaya Berjaya, BBP)
- **Evaluasi & SPK** (Input Nilai Wawancara, Kalkulasi SPK & Ranking, Penetapan SK)
- **Hasil & Output** (Draft SK Bupati, Rekapitulasi Penerima, Data Pembayaran)

---

# 5. Menu Admin OPD

- **Dashboard**
- **Verifikasi Dokumen** (Tabel Dokumen Pendaftar sesuai mapping OPD)
- **Riwayat Verifikasi**

---

# 6. Menu Admin Kecamatan (SDSS)

- **Dashboard** (Monitoring status musyawarah desa se-kecamatan)
- **Program SDSS** (Daftar Pendaftar Usulan Desa)
- **Verifikasi Kecamatan** (Verifikasi & Persetujuan Berkas Desa)

---

# 7. Menu Admin DPMD (SDSS) — Baru!

- **Dashboard DPMD** (Statistik pendaftar SDSS se-Kabupaten & monitoring kecamatan)
- **Program SDSS** (Daftar Pendaftar Usulan Desa se-Kabupaten)
- **Verifikasi DPMD** (Verifikasi & Persetujuan Berkas Rekomendasi Desa)

---

# 8. Menu Admin Desa (SDSS)

- **Dashboard**
- **Pendaftar SDSS** (Live Scoring SPK & Perangkingan Desa)
- **Penetapan & Rekomendasi** (Tetapkan 1 Calon Perwakilan & Unggah Surat Rekomendasi + Berita Acara Musyawarah)

---

# 9. Hak Akses Menu Matrix

| Menu | Super Admin | Kabupaten | OPD | Kecamatan | DPMD | Desa |
|------|-------------|-----------|-----|-----------|------|------|
| Dashboard | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ |
| Konfigurasi Master | ✔ | ✖ | ✖ | ✖ | ✖ | ✖ |
| Master Referensi | ✔ | ✖ | ✖ | ✖ | ✖ | ✖ |
| Dokumen Publik | ✔ | ✖ | ✖ | ✖ | ✖ | ✖ |
| Pendaftaran | ✔ | ✔ | View | SDSS | SDSS | SDSS |
| Verifikasi Dokumen OPD | ✔ | View | ✔ | ✖ | ✖ | ✖ |
| Verifikasi Rekomendasi | View | View | ✖ | ✔ SDSS | ✔ SDSS | ✖ |
| Wawancara | ✔ | ✔ | ✖ | ✖ | ✖ | ✖ |
| Penilaian SPK & Ranking | ✔ | ✔ | ✖ | ✖ | ✖ | ✔ SDSS |
| Penetapan 1 Calon | ✖ | ✖ | ✖ | ✖ | ✖ | ✔ SDSS |
| Penetapan SK Final | ✔ | ✔ | ✖ | ✖ | ✖ | ✖ |
| Audit Log | ✔ | ✖ | ✖ | ✖ | ✖ | ✖ |