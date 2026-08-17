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
11. `CHANGELOG.md`

---

# 4. Aturan Penting Workflow & Business Rule (v2.3)

1. **SDSS Parallel Approval Workflow:**
   - Desa menetapkan 1 calon & unggah Surat Rekomendasi + Berita Acara Musyawarah.
   - Pendaftaran masuk **secara paralel** ke Kecamatan DAN DPMD.
   - Pendaftaran **baru maju ke Kabupaten (`menunggu_penetapan`)** apabila **Kecamatan DAN DPMD KEDUANYA memberikan persetujuan (`status_kecamatan = disetujui` AND `status_dpmd = disetujui`)**.
   - Diproses via `RecommendationService::cekPersetujuanParalel()`.

2. **Perhitungan SPK:**
   - Dikelola oleh `PenilaianService` menggunakan metode **Model Penilaian Berbobot (Weighted Sum Model)**: $((Skor / Max) \times Bobot)$.
   - Tidak menggunakan TOPSIS atau algoritma lain.

3. **Keamanan Login Administrator:**
   - Tombol login **dihapus dari navbar publik**.
   - Access URL: **`/mengabdi`**.

4. **Daftar Role (7 Roles):**
   - `super_admin`, `admin_kabupaten`, `admin_opd`, `admin_kecamatan`, `admin_dpmd`, `admin_desa`, dan Publik/Guest.