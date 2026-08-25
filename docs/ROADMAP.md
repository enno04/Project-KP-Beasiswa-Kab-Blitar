# ROADMAP — Website Beasiswa Blitar Mengabdi

Dokumen ini memuat peta jalan pengawasan dan pengembangan fitur Sistem Informasi Beasiswa Kabupaten Blitar ke depan.

---

## 📌 Phase 1: Core System & Multi-Role Verification (SELESAI / CURRENT)
- [x] Multi-step dynamic form registration (SDSS, BBP, Berdaya Berjaya).
- [x] Dynamic Master Data management (Program, Jalur, Kriteria, Opsi, OPD, Wilayah).
- [x] **Custom Fields** per Jalur — pertanyaan tambahan dengan penempatan dinamis (Ekonomi / Akademik).
- [x] Multi-level verification & scoring Engine (Verifikasi OPD, Rekomendasi Desa, **Verifikasi Paralel Kecamatan & DPMD**, SPK Weighted Sum).
- [x] **Auto-Approve SLA 3 Hari** — persetujuan otomatis Kecamatan/DPMD jika melewati batas waktu, dapat dijalankan manual oleh Super Admin.
- [x] **Kunci Penilaian Desa** — flag `kunci_hitung_nilai` untuk mengontrol akses penilaian & penetapan perwakilan desa per program.
- [x] Audit Log System Overhaul (State tracking `data_lama` vs `data_baru`, perbandingan perubahan bobot/kriteria, audit login/logout, filter log, & JSON diff viewer modal).
- [x] Fitur Wawancara & Input Nilai Wawancara (0-100) langsung pada Dashboard Admin Kabupaten.
- [x] Real-time tracking status pendaftaran berbasis NIK + Tahun Pendaftaran, Visual 5-Stage Stepper, & cetak bukti pendaftaran.
- [x] Keamanan login admin via route terselubung `/mengabdi` (tombol login publik disembunyikan).
- [x] Finalisasi pipeline pendaftaran pada tahap Penetapan SK (`Lulus — SK Terbit`).
- [x] Standarisasi tabel interaktif (Search, Filter, Sorting, Pagination, Badges, Empty/Loading States).
- [x] **Import Histori Penerima Legacy** — mendukung dua format Excel (Format Lama & Format Baru) untuk migrasi data penerima dari sistem sebelumnya.
- [x] **Modul Dokumen Publik** — unggah & kelola Juknis/Ebook/Template Surat yang dapat diunduh publik.
- [x] **WebSetting** — manajemen konten teks statis halaman publik (hero, kontak) tanpa mengubah kode.
- [x] Manual Book Sistem (Format Markdown: `MANUAL_USER.md` & `MANUAL_ADMIN.md`).

---

## 📌 Phase 2: System Enhancements & Integration (PLANNED)
- [ ] **Integrasi API Dukcapil**: Validasi NIK dan KK otomatis via API Disdukcapil Kabupaten Blitar.
- [ ] **Notifikasi WhatsApp Gateway**: Pengiriman notifikasi otomatis perubahan status pendaftaran dan hasil seleksi ke nomor WhatsApp pendaftar.
- [ ] **Integrasi SIM-KIP / Kemendikbud**: Verifikasi status penerima KIP-Kuliah secara otomatis.

---

## 📌 Phase 3: Analytics & Reporting (FUTURE)
- [ ] **Sistem Laporan Eksekutif**: Ekspor laporan statistik persebaran penerima beasiswa per kecamatan/desa dalam format PDF/Excel dengan grafik interaktif.
- [ ] **Penyaluran & Monitoring Dana Beasiswa**: Pelaporan IPK semesteran penerima beasiswa secara berkala untuk keberlanjutan beasiswa.
