# DESIGN.md

# UI/UX Design Guideline
## Website Beasiswa Blitar Mengabdi

Versi : 2.3

---

# 1. Tujuan

Dokumen ini menjadi pedoman desain antarmuka (UI) dan pengalaman pengguna (UX) Website Beasiswa Blitar Mengabdi.

---

# 2. Prinsip Desain

- Sederhana, Modern, Responsif, Konsisten, User Friendly, High Contrast Readability, Concealed Security Login.

---

# 3. Teknologi UI

- Frontend: Blade Templates + Tailwind CSS v4 + Alpine.js + Lucide Icons
- Branding: Logo Resmi Pemerintah Kabupaten Blitar (`logo-kab-blitar.png`)
- Custom Header Heros: `header_pendaftaran.png` & `header_cekstatus.png` dengan typography kontras tinggi (`text-white`, `text-amber-400`).

---

# 4. Skema Warna (Design Tokens)

- **Primary:** Navy Pekat (`#2B5C92` — `#0C1446`) — Header / Topbar / Navbar Branding
- **Accent:** Kuning Bumblebee Vibrant (`#FFD800`) — Tombol "Daftar Sekarang" dengan teks Navy Pekat (`#0C1446`) `font-extrabold`
- **Secondary & Background:** Putih & Latar Belakang `background.jpeg` (`bg-fixed`) dengan elemen `card` putih bersih
- **Functional Status:**
  - Success: Hijau Emerald (`#10B981`)
  - Warning: Amber / Kuning (`#F59E0B`)
  - Danger: Merah Rose (`#EF4444`)
  - Neutral: Slate Gray (`#64748B`)

---

# 5. Keamanan Akses & Concealed Login

- Tombol "Masuk" / "Login" **dihapus dari navbar publik** demi keamanan sistem.
- Administrator mengakses login melalui URL **`/mengabdi`**.

---

# 6. Dashboard Berdasarkan Role

- **Super Admin:** Overview statistik, management Master Referensi & Master Konfigurasi, Dokumen Publik, Audit Log.
- **Admin Kabupaten:** Dashboard SPK, Input Wawancara, Pemeringkatan, & Penetapan SK Terbit.
- **Admin OPD:** Tabel Verifikasi Berkas Teknis sesuai kewenangan OPD.
- **Admin Kecamatan:** Monitoring musyawarah desa & verifikasi persetujuan wilayah kecamatan (SDSS).
- **Admin DPMD:** Dashboard statistik SDSS se-Kabupaten, monitoring kecamatan, verifikasi & persetujuan DPMD (SDSS).
- **Admin Desa:** Scoring SPK per desa, penetapan 1 perwakilan, unggah Surat Rekomendasi & Berita Acara.