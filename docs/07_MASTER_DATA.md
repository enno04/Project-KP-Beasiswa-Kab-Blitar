# 07_MASTER_DATA.md

# Master Data
## Website Beasiswa Blitar Mengabdi

Versi : 2.4

---

# 1. Tujuan

Dokumen ini menjelaskan seluruh Master Data yang digunakan pada Website Beasiswa Blitar Mengabdi.

Master Data berfungsi sebagai pusat konfigurasi sistem sehingga perubahan kebijakan tidak memerlukan perubahan source code.

Seluruh Master Data hanya dapat dikelola oleh **Super Admin**.

---

# 2. Konsep Master Data

Master Data dibagi menjadi dua kelompok utama:
1. Master Referensi
2. Master Konfigurasi

---

# 3. Master Referensi

Master Referensi adalah data yang relatif jarang berubah dan digunakan sebagai acuan oleh seluruh sistem.

## 3.1 Role

Digunakan untuk menentukan hak akses pengguna (RBAC).

Daftar Role:
- `super_admin`: Super Admin (Pengelola Utama System)
- `admin_kabupaten`: Admin Kabupaten (Tim Seleksi & SK)
- `admin_opd`: Admin OPD (Verifikator Teknis Dokumen)
- `admin_kecamatan`: Admin Kecamatan (Verifikator Wilayah SDSS)
- `admin_dpmd`: Admin DPMD (Verifikator Kabupaten SDSS) — Baru!
- `admin_desa`: Admin Desa (Verifikator Faktual & Rekomendasi SDSS)

---

## 3.2 User

Data seluruh pengguna yang dapat login ke sistem.

Meliputi:
- Nama
- Username
- Password (Hashed)
- Role ID
- OPD ID (jika Admin OPD)
- Kecamatan ID (jika Admin Kecamatan / Desa)
- Desa ID (jika Admin Desa)
- Status Aktif (Boolean)

---

## 3.3 OPD

Daftar Organisasi Perangkat Daerah yang terlibat dalam proses verifikasi berkas (Disdukcapil, Dinas Sosial, Dinas Pendidikan/Kesra, dll).

---

## 3.4 Kecamatan

Master Kecamatan di Kabupaten Blitar (22 Kecamatan).

---

## 3.5 Desa / Kelurahan

Master Desa/Kelurahan (248 Desa/Kelurahan).

---

> **Catatan:** Tabel `perguruan_tinggi` telah dihapus dari sistem. Data perguruan tinggi sekarang diisi sebagai teks bebas oleh pendaftar pada field `asal_perguruan_tinggi` di tabel `pendaftaran_identitas`.

---

# 4. Master Konfigurasi

Master Konfigurasi merupakan data yang dapat berubah pada setiap Periode Beasiswa:

- Periode
- Program Beasiswa (dilengkapi flag `kunci_hitung_nilai` untuk mengontrol akses penilaian desa)
- Jalur
- Tahapan
- Persyaratan
- Dokumen (beserta mapping OPD verifikator)
- Kelompok Kriteria
- Kriteria
- Pilihan Kriteria
- Bobot Penilaian
- **Custom Fields** — pertanyaan tambahan (tipe: text, textarea, select, radio, checkbox) yang dapat dikonfigurasi per Jalur dan ditempatkan di tahap Ekonomi atau Akademik.

---

# 5. Pengaturan Website (WebSetting)

Konfigurasi konten statis halaman publik yang dapat diubah oleh Super Admin tanpa mengubah kode, seperti:
- Teks hero banner
- Nomor WhatsApp contact person
- Informasi program publik

Disimpan dalam tabel `web_settings` dengan format key-value.

Seluruh Master Konfigurasi dikelola oleh Super Admin.