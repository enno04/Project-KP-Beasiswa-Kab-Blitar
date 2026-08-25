# 04_ROLE_PERMISSION.md

# ROLE & PERMISSION
## Website Beasiswa Blitar Mengabdi

Versi : 2.4

---

# 1. Tujuan

Dokumen ini menjelaskan seluruh Role (Hak Akses) yang digunakan pada Sistem Beasiswa Blitar Mengabdi.

Setiap pengguna hanya dapat mengakses menu, data, dan proses sesuai kewenangannya.

Seluruh aktivitas pengguna dicatat pada Audit Log.

---

# 2. Daftar Role

Sistem memiliki 7 Role utama:

1. Super Admin
2. Admin Kabupaten
3. Admin OPD
4. Admin Kecamatan
5. Admin DPMD (Baru!)
6. Admin Desa / Kelurahan
7. Masyarakat (Pengguna Publik — tanpa akun)

---

# 3. Super Admin

Merupakan pengelola utama sistem. Memiliki akses penuh terhadap seluruh modul.

### Hak Akses
- ✔ Kelola Master Referensi (User, Role, OPD, Kecamatan, Desa)
- ✔ Kelola Master Konfigurasi (Periode, Program, Jalur, Tahapan, Persyaratan, Dokumen, Kriteria, Pilihan, Bobot, Custom Fields)
- ✔ Kelola Dokumen Publik & Juknis
- ✔ Kelola Pengaturan Website (WebSetting)
- ✔ Monitoring Seluruh Pendaftaran, Verifikasi, Penilaian, Penetapan, & Audit Log
- ✔ Menjalankan Auto-Verify SLA secara manual

---

# 4. Admin Kabupaten

Merupakan Tim Seleksi Kabupaten.

### Hak Akses
- ✔ Melihat seluruh pendaftaran
- ✔ Melihat hasil verifikasi OPD, Kecamatan, & DPMD
- ✔ Membuka/Mengunci akses penilaian & penetapan desa (SDSS) via flag `kunci_hitung_nilai`
- ✔ Input Nilai Wawancara (Berdaya Berjaya) / Gugurkan Peserta
- ✔ Kalkulasi SPK & Pemeringkatan (Ranking)
- ✔ Penetapan Penerima Beasiswa & Penerbitan SK Terbit (Tahap Final)
- ✔ Generate Output Administrasi (Draft SK, Daftar Penerima, Laporan)

---

# 5. Admin OPD

Merupakan Organisasi Perangkat Daerah yang melakukan verifikasi dokumen sesuai kewenangannya (Disdukcapil, Dinsos, Kesra, dll).

### Hak Akses
- ✔ Memverifikasi teknis dokumen pendaftaran sesuai mapping kewenangan OPD
- ✔ Memberikan catatan verifikasi (Valid / Tidak Valid)

*Admin OPD tidak dapat melihat nilai, ranking, maupun penetapan.*

---

# 6. Admin Kecamatan

Digunakan khusus Program SDSS.

### Hak Akses
- ✔ Melihat pendaftar SDSS dari desa-desa di wilayah kecamatannya
- ✔ Melihat Berita Acara & Surat Rekomendasi Desa
- ✔ Memverifikasi & memberikan keputusan persetujuan administrasi Kecamatan (`disetujui` / `ditolak`)

*Proses verifikasi Kecamatan berjalan secara paralel dengan Admin DPMD.*

---

# 7. Admin DPMD (Dinas Pemberdayaan Masyarakat dan Desa)

Digunakan khusus Program SDSS tingkat Kabupaten.

### Hak Akses
- ✔ Melihat seluruh pendaftar SDSS se-Kabupaten Blitar yang telah di-forward oleh Desa
- ✔ Melihat Berita Acara Musyawarah Desa & Surat Rekomendasi Kades/Lurah
- ✔ Memverifikasi & memberikan keputusan persetujuan DPMD (`disetujui` / `ditolak`)
- ✔ Dashboard monitoring progres verifikasi desa per kecamatan

*Berkas pendaftaran SDSS baru dapat diteruskan ke Admin Kabupaten jika Admin DPMD DAN Admin Kecamatan KEDUANYA memberikan persetujuan.*

---

# 8. Admin Desa / Kelurahan

Digunakan khusus Program SDSS.

### Hak Akses
- ✔ Melihat pendaftar pada Desa/Kelurahannya
- ✔ Melihat nilai otomatis SPK & ranking pendaftar di desanya
- ✔ Menentukan **1 (satu) calon perwakilan desa**
- ✔ Mengunggah Surat Rekomendasi Kades & Berita Acara Musyawarah Desa

---

# 9. Masyarakat (Pengguna Publik)

Masyarakat tidak memiliki akun dan tidak perlu login.

### Hak Akses
- ✔ Akses Informasi Publik, Persyaratan, & Download Dokumen Penting/Juknis
- ✔ Melakukan Pendaftaran 5-Step Tanpa Login
- ✔ Cek Status Pendaftaran & Cetak Bukti Tanda Pendaftaran via NIK 16 Digit + Tahun

*Akses login administrator disembunyikan di route `/mengabdi`.*

---

# 10. Hak Akses Berdasarkan Program

## SDSS (Satu Desa Satu Sarjana)
Role yang terlibat:
- Super Admin
- Admin OPD
- Admin Desa
- Admin Kecamatan
- **Admin DPMD** (Persetujuan Paralel)
- Admin Kabupaten

## Berdaya Berjaya & Bantuan Biaya Pendidikan (BBP)
Role yang terlibat:
- Super Admin
- Admin OPD
- Admin Kabupaten

---

# 11. Hak Akses Berdasarkan Tahapan

| Tahapan | Super Admin | Kabupaten | OPD | Kecamatan | DPMD | Desa |
|---------|------------|-----------|-----|-----------|------|------|
| Konfigurasi | ✔ | ✖ | ✖ | ✖ | ✖ | ✖ |
| Pendaftaran | View | View | View | View SDSS | View SDSS | View SDSS |
| Verifikasi Berkas | View | View | ✔ | ✖ | ✖ | ✖ |
| Rekomendasi Desa | View | View | ✖ | ✖ | ✖ | ✔ SDSS |
| Persetujuan Paralel | View | View | ✖ | ✔ SDSS | ✔ SDSS | ✖ |
| Penilaian SPK | View | View/Input | View | View | View | View SDSS |
| Ranking | ✔ | ✔ | ✖ | ✖ | ✖ | ✔ SDSS |
| Penetapan & SK | ✔ | ✔ | ✖ | ✖ | ✖ | 1 Calon |
| Monitoring & Log | ✔ | View | View | View | View | View |