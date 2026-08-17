# PENILAIAN.md

# Sistem Penilaian
## Website Beasiswa Blitar Mengabdi

Versi : 2.3

---

# 1. Tujuan

Dokumen ini menjelaskan mekanisme penilaian yang digunakan pada Website Beasiswa Blitar Mengabdi.

Sistem penilaian dirancang agar objektif, transparan, konsisten, fleksibel, dan mudah disesuaikan. Seluruh proses perhitungan dilakukan melalui metode **Model Penilaian Berbobot (Weighted Sum Model)** yang dikelola oleh *Service Layer* (`PenilaianService`) secara otomatis.

---

# 2. Konsep Penilaian

Penilaian menggunakan dua komponen utama:
- Status Ekonomi
- Akademik

Pada Program tertentu dapat ditambahkan:
- Nilai Wawancara (Berdaya Berjaya)

---

# 3. Perhitungan Nilai (Weighted Sum Model)

$$\text{Nilai Variabel} = \left( \frac{\text{Skor Diperoleh}}{\text{Skor Max}} \right) \times \text{Bobot Variabel}$$

$$\text{Total Nilai} = \sum (\text{Nilai Variabel})$$

Tie-breaker: jika total nilai sama, urutan diprioritaskan berdasarkan waktu pendaftaran paling awal (`created_at` ASC).

---

# 4. Penilaian per Program

## 4.1 Satu Desa Satu Sarjana (SDSS)
- Sistem menghitung nilai otomatis & menyusun ranking per Desa.
- Admin Desa melihat ranking di desanya, lalu memilih **1 calon perwakilan desa** & unggah Surat Rekomendasi + Berita Acara.
- Berkas rekomendasi diteruskan untuk **Persetujuan Paralel Kecamatan & DPMD**.
- Jika kedua pihak menyetujui, pendaftaran masuk ke Admin Kabupaten untuk penetapan SK.

## 4.2 Berdaya Berjaya
- Tahapan: Verifikasi OPD ──► Tes Wawancara (Admin Kab. input 0-100 / gugur) ──► Penilaian SPK ──► Pemeringkatan ──► Penetapan SK.

## 4.3 Bantuan Biaya Pendidikan (BBP)
- Tahapan: Verifikasi OPD ──► Penilaian SPK ──► Pemeringkatan ──► Penetapan SK (Tanpa Wawancara).