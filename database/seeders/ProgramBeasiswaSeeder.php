<?php

namespace Database\Seeders;

use App\Models\Jalur;
use App\Models\Periode;
use App\Models\Program;
use App\Models\Tahapan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramBeasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Periode Aktif 2026
        $periode = Periode::firstOrCreate(
            ['tahun' => 2026],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Periode 2026',
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-12-31',
                'status' => 'aktif',
                'keterangan' => 'Periode Aktif Beasiswa Blitar Mengabdi Tahun 2026',
            ]
        );

        // Ensure status is active if already existing
        $periode->update(['status' => 'aktif']);

        // 2. Program 1: Satu Desa Satu Sarjana (SDSS)
        $sdss = Program::firstOrCreate(
            ['kode' => 'sdss', 'periode_id' => $periode->id],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Satu Desa Satu Sarjana (SDSS)',
                'slug' => 'sdss',
                'deskripsi' => 'Program Beasiswa Satu Desa Satu Sarjana Kabupaten Blitar',
                'tanggal_buka' => '2026-03-01',
                'tanggal_tutup' => '2026-12-31',
                'aktif' => true,
                'urutan' => 1,
            ]
        );

        // Jalur SDSS: Reguler
        $sdssReguler = Jalur::firstOrCreate(
            ['kode' => 'reguler', 'program_id' => $sdss->id],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Reguler',
                'slug' => 'reguler',
                'deskripsi' => 'Jalur Reguler SDSS per Desa/Kelurahan',
                'aktif' => true,
                'urutan' => 1,
            ]
        );
        $this->seedTahapan($sdssReguler->id, [
            'verifikasi_opd' => 'Verifikasi OPD',
            'penilaian_otomatis' => 'Penilaian Otomatis',
            'ranking_desa' => 'Perangkingan Desa',
            'penetapan_desa' => 'Penetapan Desa',
            'upload_rekomendasi' => 'Upload Surat Rekomendasi',
            'verifikasi_kecamatan' => 'Verifikasi Kecamatan',
            'penetapan_kabupaten' => 'Penetapan Kabupaten',
            'output' => 'Output Administrasi',
            'sk' => 'Penerbitan SK',
        ]);

        // 3. Program 2: Berdaya Berjaya
        $berdaya = Program::firstOrCreate(
            ['kode' => 'berdaya_berjaya', 'periode_id' => $periode->id],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Berdaya Berjaya',
                'slug' => 'berdaya-berjaya',
                'deskripsi' => 'Program Beasiswa Berdaya Berjaya',
                'tanggal_buka' => '2026-03-01',
                'tanggal_tutup' => '2026-12-31',
                'aktif' => true,
                'urutan' => 2,
            ]
        );

        // Jalur Berdaya Berjaya: Mahasiswa Baru & Mahasiswa Lama
        $berdayaBaru = Jalur::firstOrCreate(
            ['kode' => 'mahasiswa_baru', 'program_id' => $berdaya->id],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Mahasiswa Baru (Semester 1)',
                'slug' => 'mahasiswa-baru',
                'deskripsi' => 'Jalur untuk Mahasiswa Baru (Semester 1)',
                'aktif' => true,
                'urutan' => 1,
            ]
        );
        $this->seedTahapan($berdayaBaru->id, [
            'verifikasi_opd' => 'Verifikasi OPD',
            'wawancara' => 'Wawancara',
            'penilaian' => 'Penilaian',
            'ranking' => 'Perangkingan',
            'penetapan' => 'Penetapan',
            'output' => 'Output Administrasi',
            'sk' => 'Penerbitan SK',
        ]);

        $berdayaLama = Jalur::firstOrCreate(
            ['kode' => 'mahasiswa_lama', 'program_id' => $berdaya->id],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Mahasiswa Lama (Diatas Semester 1)',
                'slug' => 'mahasiswa-lama',
                'deskripsi' => 'Jalur untuk Mahasiswa Lama (Diatas Semester 1)',
                'aktif' => true,
                'urutan' => 2,
            ]
        );
        $this->seedTahapan($berdayaLama->id, [
            'verifikasi_opd' => 'Verifikasi OPD',
            'wawancara' => 'Wawancara',
            'penilaian' => 'Penilaian',
            'ranking' => 'Perangkingan',
            'penetapan' => 'Penetapan',
            'output' => 'Output Administrasi',
            'sk' => 'Penerbitan SK',
        ]);

        // 4. Program 3: Bantuan Biaya Pendidikan
        $bbp = Program::firstOrCreate(
            ['kode' => 'bbp', 'periode_id' => $periode->id],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Bantuan Biaya Pendidikan',
                'slug' => 'bbp',
                'deskripsi' => 'Program Bantuan Biaya Pendidikan Bagi Mahasiswa',
                'tanggal_buka' => '2026-03-01',
                'tanggal_tutup' => '2026-12-31',
                'aktif' => true,
                'urutan' => 3,
            ]
        );

        // Jalur BBP: Prestasi & Kurang Mampu
        $bbpPrestasi = Jalur::firstOrCreate(
            ['kode' => 'prestasi', 'program_id' => $bbp->id],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Prestasi',
                'slug' => 'prestasi',
                'deskripsi' => 'Jalur BBP Prestasi',
                'aktif' => true,
                'urutan' => 1,
            ]
        );
        $this->seedTahapan($bbpPrestasi->id, [
            'verifikasi_opd' => 'Verifikasi OPD',
            'penilaian' => 'Penilaian',
            'ranking' => 'Perangkingan',
            'penetapan' => 'Penetapan',
            'output' => 'Output Administrasi',
            'sk' => 'Penerbitan SK',
        ]);

        $bbpKm = Jalur::firstOrCreate(
            ['kode' => 'kurang_mampu', 'program_id' => $bbp->id],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Kurang Mampu',
                'slug' => 'kurang-mampu',
                'deskripsi' => 'Jalur BBP Kurang Mampu',
                'aktif' => true,
                'urutan' => 2,
            ]
        );
        $this->seedTahapan($bbpKm->id, [
            'verifikasi_opd' => 'Verifikasi OPD',
            'penilaian' => 'Penilaian',
            'ranking' => 'Perangkingan',
            'penetapan' => 'Penetapan',
            'output' => 'Output Administrasi',
            'sk' => 'Penerbitan SK',
        ]);
    }

    private function seedTahapan(int $jalurId, array $tahapans): void
    {
        $urutan = 1;
        foreach ($tahapans as $kode => $nama) {
            Tahapan::firstOrCreate(
                ['jalur_id' => $jalurId, 'kode' => $kode],
                [
                    'uuid' => (string) Str::uuid(),
                    'nama' => $nama,
                    'urutan' => $urutan++,
                ]
            );
        }
    }
}
