# ROADMAP — Website Beasiswa Blitar Mengabdi

Dokumen ini memuat peta jalan pengawasan dan pengembangan fitur Sistem Informasi Beasiswa Kabupaten Blitar ke depan.

---

## 📌 Phase 1: Core System & Multi-Role Verification (SELESAI / CURRENT)
- [x] Multi-step dynamic form registration (SDSS, BBP, Berdaya Berjaya).
- [x] Dynamic Master Data management (Program, Jalur, Kriteria, Opsi, OPD, Wilayah).
- [x] Multi-level verification & scoring Engine (Verifikasi OPD, Rekomendasi Desa, **Verifikasi Paralel Kecamatan & DPMD**, SPK Weighted Sum).
- [x] Audit Log System Overhaul (State tracking `data_lama` vs `data_baru`, perbandingan perubahan bobot/kriteria, audit login/logout, filter log, & JSON diff viewer modal).
- [x] Fitur Wawancara & Input Nilai Wawancara (0-100) langsung pada Dashboard Admin Kabupaten.
- [x] Real-time tracking status pendaftaran berbasis NIK + Tahun Pendaftaran, Visual 5-Stage Stepper, & cetak bukti pendaftaran (Tombol Tunggal Cek Status & Cetak Bukti).
- [x] Keamanan login admin via route terselubung `/mengabdi` (tombol login publik disembunyikan).
- [x] Finalisasi pipeline pendaftaran pada tahap Penetapan SK (`Lulus — SK Terbit`).
- [x] Standarisasi tabel interaktif (Search, Filter, Sorting, Pagination, Badges, Empty/Loading States).
- [x] Generator otomatis **Manual Book PDF Sistem** 14 BAB via script ReportLab.

---

## 📌 Phase 2: System Enhancements & Integration (PLANNED)
- [ ] **Integrasi API Dukcapil**: Validasi NIK dan KK otomatis via API Disdukcapil Kabupaten Blitar.
- [ ] **Notifikasi WhatsApp Gateway**: Pengiriman notifikasi otomatis perubahan status pendaftaran dan hasil seleksi ke nomor WhatsApp pendaftar.
- [ ] **Integrasi SIM-KIP / Kemendikbud**: Verifikasi status penerima KIP-Kuliah secara otomatis.

---

## 📌 Phase 3: Analytics & Reporting (FUTURE)
- [ ] **Sistem Laporan Eksekutif**: Ekspor laporan statistik persebaran penerima beasiswa per kecamatan/desa dalam format PDF/Excel dengan grafik interaktif.
- [ ] **Penyaluran & Monitoring Dana Beasiswa**: Pelaporan IPK semesteran penerima beasiswa secara berkala untuk keberlanjutan beasiswa.
