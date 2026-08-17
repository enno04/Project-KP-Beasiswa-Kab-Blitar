# 05_HALAMAN.md

# DAFTAR HALAMAN
## Website Beasiswa Blitar Mengabdi

Versi : 2.3

---

# 1. Tujuan

Dokumen ini menjelaskan seluruh halaman yang terdapat pada Website Beasiswa Blitar Mengabdi.

---

# 2. Kelompok Halaman

Sistem dibagi menjadi dua kelompok halaman:
1. Halaman Publik
2. Halaman Administrator

Masyarakat tidak memiliki akun dan tidak melakukan login.

---

# 3. Halaman Publik

## 3.1 Beranda (`/`)
- Hero Banner Slider Foto Kegiatan
- Informasi Program Beasiswa (`sdss`, `berdaya_berjaya`, `bantuan_biaya`)
- Alur Pendaftaran & Timelines
- Section Dokumen Penting & Juknis
- FAQ & Footer Contact Person Official

## 3.2 Informasi Beasiswa (`/informasi-umum`)
- Detail Program & Jalur
- Dasar Hukum & Persyaratan
- Pusat Informasi & Layanan Bantuan (WhatsApp CP Official)

## 3.3 Dokumen Penting (`/dokumen-penting`)
- List Dokumen Publik / Juknis / Ebook Panduan / Format Surat Pernyataan
- Tombol Unduh Berkas (.pdf / .doc / .docx)

## 3.4 Cek Status & Cetak Bukti (`/cek-status`)
- Input NIK (16 Digit) & Select Tahun Pendaftaran
- Visual Stepper Kemajuan Pendaftaran (5-Stage: Pengajuan ──► Verifikasi OPD ──► Desa & Kec./DPMD ──► Penilaian SPK ──► Penetapan SK)
- Banner Detail Posisi Berkas
- Tombol Cetak Tanda Bukti Pendaftaran

## 3.5 Form Pendaftaran (`/pendaftaran`)
- Multi-Step Wizard 5 Stage (Identitas, Orang Tua, Ekonomi, Akademik, Upload Dokumen)
- Review Pendaftaran & Persetujuan Kebijakan Privasi

---

# 4. Halaman Login Administrator (`/mengabdi`)

- URL terselubung: **`http://localhost:8000/mengabdi`** (Tombol login dihapus dari halaman publik).
- Form input Username & Password.
- Digunakan oleh Super Admin, Admin Kabupaten, Admin OPD, Admin Kecamatan, Admin DPMD, dan Admin Desa.

---

# 5. Halaman Super Admin (`/super-admin/*`)

- **Dashboard:** Overview statistik, total user, OPD, kecamatan, desa, & pendaftar.
- **Master Referensi:** Management User, Role, OPD, Kecamatan, Desa.
- **Master Konfigurasi:** Management Periode, Program, Jalur, Tahapan, Persyaratan, Dokumen, Kriteria, Pilihan, Bobot.
- **Dokumen Publik:** Management Ebook Panduan & SK Juknis Publik.
- **Monitoring & Histori:** Monitoring Pendaftaran, Import Histori Legacy, Audit Log.

---

# 6. Halaman Admin Kabupaten (`/kabupaten/*`)

- **Dashboard:** Summary data lolos verifikasi, diproses SPK, dan lulus.
- **Daftar Pendaftar per Program/Jalur:** Tabel pendaftar, Input Nilai Wawancara (Berdaya Berjaya), kalkulasi SPK & Ranking inline, Penetapan inline (`lulus` / `tidak_lulus`).
- **Hasil & Output:** Ranking SPK, Penetapan Final, Penerbitan SK Terbit.

---

# 7. Halaman Admin OPD (`/opd/*`)

- **Dashboard:** Statistik berkas belum diverifikasi, valid, & tidak valid.
- **Verifikasi Dokumen:** Tabel dokumen pendaftar sesuai kewenangan OPD + Modal Verifikasi & Catatan.
- **Riwayat Verifikasi:** History verifikasi dokumen.

---

# 8. Halaman Admin Kecamatan (`/kecamatan/*`)

- **Dashboard:** Monitoring status musyawarah desa di wilayah kecamatan.
- **Program SDSS Index:** Daftar pendaftar SDSS usulan desa.
- **Detail Pendaftar:** Detail pendaftar, Surat Rekomendasi Desa, form verifikasi & persetujuan Kecamatan.

---

# 9. Halaman Admin DPMD (`/dpmd/*`) — Baru!

- **Dashboard:** Statistik pendaftar SDSS se-Kabupaten, monitoring per kecamatan, pendaftar terbaru.
- **Program SDSS Index:** List pendaftar SDSS yang di-forward dari Desa dengan status persetujuan Kecamatan & DPMD.
- **Detail Pendaftar:** Detail pendaftar, Surat Rekomendasi & Berita Acara Musyawarah Desa, Form Verifikasi & Persetujuan DPMD, serta Panel Summary Persetujuan Paralel.

---

# 10. Halaman Admin Desa (`/desa/*`)

- **Dashboard:** Summary pendaftar desa.
- **Daftar Pendaftar SDSS:** Tabel pendaftar, nilai otomatis SPK, perangkingan desa.
- **Penetapan & Upload Rekomendasi:** Tombol tetapkan 1 perwakilan desa + Form Unggah Surat Rekomendasi Kades & Berita Acara Musyawarah.

---

# 11. Prinsip Halaman

- Responsif, Berbasis Role (RBAC), Mengikuti Workflow Paralel, Mengamankan Akses Admin via `/mengabdi`, serta Pencatatan Audit Log pada setiap aksi perubahan data.