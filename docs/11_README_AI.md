# README_AI.md

# AI Development Guide
## Website Beasiswa Blitar Mengabdi

Versi : 2.4

---

# 1. Tujuan

Dokumen ini menjadi pedoman bagi AI Assistant (ChatGPT, GitHub Copilot, Claude, Cursor AI, Gemini Antigravity, dan AI lainnya) agar memahami arsitektur, aturan, dan standar pengembangan Website Beasiswa Blitar Mengabdi.

---

# 2. Teknologi & Arsitektur

- **Backend:** Laravel 12 (PHP 8.2+) — Thin Controller + Dedicated Service Layer (`App\Services\*`).
- **Frontend:** Blade Templates + Tailwind CSS v4 + Alpine.js + Lucide Icons.
- **Database:** MySQL.
- **Auth:** Custom Multi-Role Auth via route terselubung `/mengabdi`.

---

# 3. Urutan Dokumen Acuan

1. `01_PROJECT_OVERVIEW.md`
2. `02_WORKFLOW.md`
3. `03_BUSINESS_RULE.md`
4. `04_ROLE_PERMISSION.md`
5. `05_HALAMAN.md`
6. `06_MENU.md`
7. `07_MASTER_DATA.md`
8. `08_DATABASE.md`
9. `09_PENILAIAN.md`
10. `10_DESIGN.md`
11. `ROADMAP.md`
12. `MANUAL_USER.md`
13. `MANUAL_ADMIN.md`

---

# 4. Aturan Penting Workflow & Business Rule (v2.4)

1. **SDSS Parallel Approval Workflow:**
   - Desa menetapkan 1 calon & unggah Surat Rekomendasi + Berita Acara Musyawarah.
   - Pendaftaran masuk **secara paralel** ke Kecamatan DAN DPMD.
   - Pendaftaran **baru maju ke Kabupaten (`menunggu_penetapan`)** apabila **Kecamatan DAN DPMD KEDUANYA memberikan persetujuan (`status_kecamatan = disetujui` AND `status_dpmd = disetujui`)**.
   - Diproses via `RecommendationService::cekPersetujuanParalel()`.

2. **Auto-Approve SLA 3 Hari:**
   - Jika Kecamatan atau DPMD tidak memberi keputusan dalam 3 hari, sistem otomatis menyetujui via command `beasiswa:auto-verify`.
   - Dapat dijalankan manual oleh Super Admin dari dashboard.

3. **Kunci Penilaian Desa (SDSS):**
   - Flag `kunci_hitung_nilai` pada tabel `programs` mengontrol apakah Admin Desa dapat menghitung nilai dan menetapkan perwakilan.
   - Diaktifkan/dinonaktifkan oleh Admin Kabupaten.

4. **Perhitungan SPK:**
   - Dikelola oleh `PenilaianService` menggunakan metode **Model Penilaian Berbobot (Weighted Sum Model)**: $((Skor / Max) \times Bobot)$.
   - Ranking dikelola oleh `RankingService`.
   - Tidak menggunakan TOPSIS atau algoritma lain.

5. **Custom Fields:**
   - Pertanyaan tambahan per Jalur, dikonfigurasi oleh Super Admin.
   - Dapat ditempatkan di tahap Ekonomi atau Akademik.
   - Jawaban disimpan di tabel `custom_field_answers`.

6. **Keamanan Login Administrator:**
   - Tombol login **dihapus dari navbar publik**.
   - Access URL: **`/mengabdi`**.

7. **Daftar Role (7 Roles):**
   - `super_admin`, `admin_kabupaten`, `admin_opd`, `admin_kecamatan`, `admin_dpmd`, `admin_desa`, dan Publik/Guest.

8. **Service Layer (`App\Services\`):**
   - `RegistrationService` — logika pendaftaran & submit
   - `VerifikasiService` — logika verifikasi dokumen OPD
   - `PenilaianService` — kalkulasi SPK Weighted Sum
   - `RankingService` — perangkingan & pengurutan
   - `RecommendationService` — workflow rekomendasi desa & paralel approval
   - `PenetapanService` — penetapan penerima & penerbitan SK
   - `PasswordService` — manajemen password admin