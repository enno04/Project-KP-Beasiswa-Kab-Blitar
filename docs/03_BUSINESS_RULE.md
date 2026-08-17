# 03_BUSINESS_RULE.md

# BUSINESS RULE
## Sistem Beasiswa Blitar Mengabdi

Versi : 2.3

---

# 1. Tujuan

Dokumen ini berisi seluruh aturan bisnis (Business Rule) yang menjadi dasar pengembangan Sistem Beasiswa Blitar Mengabdi.

Seluruh modul sistem, database, workflow, hak akses, dan proses seleksi **wajib mengikuti aturan yang tercantum pada dokumen ini**.

---

# 2. Periode

- Satu tahun anggaran hanya memiliki satu Periode Beasiswa (dibuat oleh Super Admin).
- Seluruh proses pendaftaran menggunakan Periode yang aktif.

---

# 3. Program Beasiswa

Program yang didukung sistem:
- **Satu Desa Satu Sarjana (SDSS)** — Jalur Reguler
- **Berdaya Berjaya** — Jalur Mahasiswa Baru & Mahasiswa Lama
- **Bantuan Biaya Pendidikan (BBP)** — Jalur Prestasi & Jalur Kurang Mampu

---

# 4. Pendaftaran & Keamanan Akses

## 4.1 Akses Publik & Login
- Masyarakat **tidak perlu login** untuk melakukan pendaftaran.
- Demi keamanan sistem, tombol "Masuk" / "Login" **dihapus dari halaman publik**.
- Administrator mengakses halaman login melalui URL terselubung **`/mengabdi`**.

## 4.2 Form Pendaftaran 5 Step
1. **Identitas** (Statis)
2. **Data Orang Tua** (Statis)
3. **Status Ekonomi** (Dinamis per Kriteria Master)
4. **Akademik** (Dinamis per Kriteria Master)
5. **Dokumen** (Dinamis per Persyaratan Master)

---

# 5. Review & Persetujuan Data

Sebelum Submit, pendaftar wajib melakukan pengecekan data dan menyetujui Kebijakan Privasi serta Pernyataan Keabsahan Data.

Setelah tombol Submit ditekan:
- Data & dokumen terkunci permanen.
- Tidak dapat diubah kembali oleh pendaftar.

---

# 6. Verifikasi Dokumen OPD

- Setiap dokumen memiliki mapping OPD Verifikator.
- OPD memverifikasi dokumen secara paralel.
- Jika ada dokumen wajib **Tidak Valid**, pendaftar otomatis gugur (**Tidak Lolos Verifikasi**).
- Jika seluruh dokumen wajib **Valid**, pendaftar berstatus **Lolos Verifikasi**.

---

# 7. Workflow SDSS & Persetujuan Paralel

Khusus Program SDSS:
1. **Penetapan Desa:** Desa melihat nilai SPK & ranking pendaftar di desanya, lalu menetapkan **1 (satu) calon perwakilan desa**. Pendaftar lain di desa tersebut otomatis gugur (`tidak_lolos_desa`).
2. **Upload Rekomendasi:** Admin Desa wajib mengunggah Surat Rekomendasi Kades/Lurah & Berita Acara Musyawarah Desa.
3. **Persetujuan Paralel:** Berkas rekomendasi diteruskan secara paralel ke **Dashboard Kecamatan** DAN **Dashboard DPMD**.
4. **Aturan Kelulusan Berkas:** Pendaftaran **baru dapat diteruskan ke Admin Kabupaten (`menunggu_penetapan`)** apabila **Kecamatan DAN DPMD KEDUANYA memberikan keputusan `disetujui`**.
5. Jika ditolak salah satu pihak, berkas berstatus `ditolak_kecamatan` atau `ditolak_dpmd`.

---

# 8. Wawancara (Berdaya Berjaya)

- Nilai wawancara (0-100) diinput oleh Admin Kabupaten pada dashboard Berdaya Berjaya.
- Admin Kabupaten berhak menggugurkan pendaftar yang tidak hadir wawancara (`gugur_wawancara`).

---

# 9. Penilaian SPK & Perangkingan

- Penilaian dihitung otomatis oleh sistem menggunakan **Weighted Sum Model** $((Skor / Max) \times Bobot)$.
- Tie-breaker nilai sama: waktu pendaftaran paling awal (`created_at` ASC).

---

# 10. Penetapan & SK Bupati (Tahap Final)

- Penetapan dilakukan oleh Admin Kabupaten berdasarkan hasil perangkingan SPK / usulan SDSS.
- Setelah SK diterbitkan (`Lulus — SK Terbit`), alur pendaftaran dinyatakan **Selesai (Final)**.

---

# 11. Riwayat Penerima Beasiswa

- Pendaftar yang telah ditetapkan sebagai penerima beasiswa pada tahun/periode sebelumnya **tidak dapat mendaftar kembali** pada program SDSS (penguncian NIK 16 digit seumur hidup).

---

# 12. Hak Akses & Konfigurasi

- Seluruh Master Konfigurasi (Periode, Program, Jalur, Kriteria, Bobot, Dokumen, Persyaratan) **hanya dapat dikelola oleh Super Admin**.
- Role lain hanya menjalankan tugas sesuai kewenangannya (RBAC).
- Seluruh perubahan data penting wajib dicatat dalam **Audit Log**.