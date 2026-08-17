<?php

namespace Database\Seeders;

use App\Models\BobotPenilaian;
use App\Models\Dokumen;
use App\Models\Jalur;
use App\Models\KelompokKriteria;
use App\Models\Kriteria;
use App\Models\Opd;
use App\Models\PilihanKriteria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KriteriaDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID OPD Verifikator
        $disdukcapilId = Opd::where('singkatan', 'DISDUKCAPIL')->value('id');
        $dinsosId = Opd::where('singkatan', 'DINSOS')->value('id');
        $disdikId = Opd::where('singkatan', 'DISDIK')->value('id');
        $pmdId = Opd::where('singkatan', 'PMD')->value('id');
        $kesraId = Opd::where('singkatan', 'KESRA')->value('id');
        $disporaId = Opd::where('singkatan', 'DISPORA')->value('id');

        $jalurs = Jalur::all();

        foreach ($jalurs as $jalur) {
            $this->seedKriteriaDanDokumenForJalur($jalur, $disdukcapilId, $dinsosId, $disdikId, $pmdId, $kesraId, $disporaId);
        }
    }

    private function seedKriteriaDanDokumenForJalur(
        Jalur $jalur,
        ?int $disdukcapilId,
        ?int $dinsosId,
        ?int $disdikId,
        ?int $pmdId,
        ?int $kesraId,
        ?int $disporaId
    ): void {
        // === 1. KELOMPOK KRITERIA, KRITERIA, OPSI, & BOBOT ===

        // A. Kelompok 1: Status Ekonomi (Bobot: 40%)
        $kelEkonomi = KelompokKriteria::firstOrCreate(
            ['jalur_id' => $jalur->id, 'kode' => 'ekonomi'],
            ['uuid' => (string) Str::uuid(), 'nama' => 'Status Ekonomi', 'urutan' => 1]
        );
        BobotPenilaian::updateOrCreate(
            ['jalur_id' => $jalur->id, 'kelompok_kriteria_id' => $kelEkonomi->id],
            ['uuid' => (string) Str::uuid(), 'bobot_persen' => 40.00]
        );

        $kriteriaDtsen = Kriteria::firstOrCreate(
            ['kelompok_kriteria_id' => $kelEkonomi->id, 'kode' => 'dtsen'],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'DTSEN',
                'tipe_input' => 'pilihan',
                'nilai_min' => 0,
                'nilai_max' => 100,
                'urutan' => 1,
            ]
        );
        $dtsenPilihans = [
            ['label' => 'Desil 1', 'skor' => 100.00],
            ['label' => 'Desil 2', 'skor' => 90.00],
            ['label' => 'Desil 3', 'skor' => 80.00],
            ['label' => 'Desil 4', 'skor' => 70.00],
            ['label' => 'Desil 5', 'skor' => 60.00],
            ['label' => 'Desil 6-10', 'skor' => 25.00],
        ];
        foreach ($dtsenPilihans as $idx => $p) {
            PilihanKriteria::firstOrCreate(
                ['kriteria_id' => $kriteriaDtsen->id, 'label' => $p['label']],
                ['uuid' => (string) Str::uuid(), 'skor' => $p['skor'], 'urutan' => $idx + 1]
            );
        }

        // B. Kelompok 2: Sertifikat Prestasi (Akademik/Non Akademik) (Bobot: 25%)
        $kelPrestasi = KelompokKriteria::firstOrCreate(
            ['jalur_id' => $jalur->id, 'kode' => 'sertifikatPrestasi'],
            ['uuid' => (string) Str::uuid(), 'nama' => 'Sertifikat Prestasi (Akademik/Non Akademik)', 'urutan' => 2]
        );
        BobotPenilaian::updateOrCreate(
            ['jalur_id' => $jalur->id, 'kelompok_kriteria_id' => $kelPrestasi->id],
            ['uuid' => (string) Str::uuid(), 'bobot_persen' => 25.00]
        );

        $kriteriaPrestasi = Kriteria::firstOrCreate(
            ['kelompok_kriteria_id' => $kelPrestasi->id, 'kode' => 'peringkatPrestasi'],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Peringkat Prestasi',
                'tipe_input' => 'pilihan',
                'nilai_min' => 0,
                'nilai_max' => 100,
                'urutan' => 1,
            ]
        );
        $prestasiPilihans = [
            ['label' => 'Tingkat Nasional / Internasional Peringkat 1', 'skor' => 100.00],
            ['label' => 'Tingkat Nasional / Internasional Peringkat 2', 'skor' => 95.00],
            ['label' => 'Tingkat Nasional / Internasional Peringkat 3', 'skor' => 90.00],
            ['label' => 'Tingkat Nasional / Internasional Peringkat 4', 'skor' => 85.00],
            ['label' => 'Tingkat Nasional / Internasional Peringkat 5', 'skor' => 80.00],
            ['label' => 'Tingkat Nasional / Internasional Peringkat 6', 'skor' => 75.00],
            ['label' => 'Tingkat Provinsi Peringkat 1', 'skor' => 70.00],
            ['label' => 'Tingkat Provinsi Peringkat 2', 'skor' => 65.00],
            ['label' => 'Tingkat Provinsi Peringkat 3', 'skor' => 60.00],
            ['label' => 'Tingkat Provinsi Peringkat 4', 'skor' => 55.00],
            ['label' => 'Tingkat Provinsi Peringkat 5', 'skor' => 50.00],
            ['label' => 'Tingkat Provinsi Peringkat 6', 'skor' => 45.00],
            ['label' => 'Tingkat Kabupaten / Kota Peringkat 1', 'skor' => 40.00],
            ['label' => 'Tingkat Kabupaten / Kota Peringkat 2', 'skor' => 35.00],
            ['label' => 'Tingkat Kabupaten / Kota Peringkat 3', 'skor' => 30.00],
            ['label' => 'Tingkat Kabupaten / Kota Peringkat 4', 'skor' => 25.00],
            ['label' => 'Tingkat Kabupaten / Kota Peringkat 5', 'skor' => 20.00],
            ['label' => 'Tingkat Kabupaten / Kota Peringkat 6', 'skor' => 15.00],
        ];
        foreach ($prestasiPilihans as $idx => $p) {
            PilihanKriteria::firstOrCreate(
                ['kriteria_id' => $kriteriaPrestasi->id, 'label' => $p['label']],
                ['uuid' => (string) Str::uuid(), 'skor' => $p['skor'], 'urutan' => $idx + 1]
            );
        }

        // C. Kelompok 3: Nilai Akademik (Rapor) (Bobot: 25%)
        $kelAkademik = KelompokKriteria::firstOrCreate(
            ['jalur_id' => $jalur->id, 'kode' => 'nilaiRapor'],
            ['uuid' => (string) Str::uuid(), 'nama' => 'Nilai Akademik (Rapor)', 'urutan' => 3]
        );
        BobotPenilaian::updateOrCreate(
            ['jalur_id' => $jalur->id, 'kelompok_kriteria_id' => $kelAkademik->id],
            ['uuid' => (string) Str::uuid(), 'bobot_persen' => 25.00]
        );

        Kriteria::firstOrCreate(
            ['kelompok_kriteria_id' => $kelAkademik->id, 'kode' => 'rapor'],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Nilai Rapor',
                'tipe_input' => 'angka',
                'nilai_min' => 0.00,
                'nilai_max' => 100.00,
                'urutan' => 1,
            ]
        );

        // D. Kelompok 4: Status Perguruan Tinggi (Bobot: 10%)
        $kelPT = KelompokKriteria::firstOrCreate(
            ['jalur_id' => $jalur->id, 'kode' => 'perguruanTinggi'],
            ['uuid' => (string) Str::uuid(), 'nama' => 'Status Perguruan Tinggi', 'urutan' => 4]
        );
        BobotPenilaian::updateOrCreate(
            ['jalur_id' => $jalur->id, 'kelompok_kriteria_id' => $kelPT->id],
            ['uuid' => (string) Str::uuid(), 'bobot_persen' => 10.00]
        );

        $kriteriaPT = Kriteria::firstOrCreate(
            ['kelompok_kriteria_id' => $kelPT->id, 'kode' => 'statusPerguruan'],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Status Perguruan Tinggi',
                'tipe_input' => 'pilihan',
                'nilai_min' => 0,
                'nilai_max' => 100,
                'urutan' => 1,
            ]
        );
        $ptPilihans = [
            ['label' => 'Perguruan Tinggi Negeri dan/atau Program Studi Akreditasi A / Unggul', 'skor' => 100.00],
            ['label' => 'Perguruan Tinggi Negeri dan/atau Program Studi Akreditasi B / Baik Sekali', 'skor' => 75.00],
            ['label' => 'Perguruan Tinggi Swasta dan/atau Program Studi Akreditasi A / Unggul', 'skor' => 75.00],
            ['label' => 'Perguruan Tinggi Swasta dan/atau Program Studi Akreditasi B / Baik Sekali', 'skor' => 50.00],
        ];
        foreach ($ptPilihans as $idx => $p) {
            PilihanKriteria::firstOrCreate(
                ['kriteria_id' => $kriteriaPT->id, 'label' => $p['label']],
                ['uuid' => (string) Str::uuid(), 'skor' => $p['skor'], 'urutan' => $idx + 1]
            );
        }

        // === 2. DOKUMEN PENDAFTARAN ===
        $dokumens = [
            ['nama' => 'KTP', 'opd_id' => $disdukcapilId, 'wajib' => true],
            ['nama' => 'Kartu Keluarga', 'opd_id' => $disdukcapilId, 'wajib' => true],
            ['nama' => 'Surat DTSEN', 'opd_id' => $dinsosId, 'wajib' => true],
            ['nama' => 'Pas Foto 3x4', 'opd_id' => $disdukcapilId, 'wajib' => true],
            ['nama' => 'Surat Pernyataan Bermeterai', 'opd_id' => $kesraId, 'wajib' => true],
            ['nama' => 'Surat Keterangan Diterima di Universitas', 'opd_id' => $disdikId, 'wajib' => true],
            ['nama' => 'Sertifikat Akreditasi Perguruan Tinggi', 'opd_id' => $disdikId, 'wajib' => true],
            ['nama' => 'Surat Pengantar dari Kepala Desa / Lurah', 'opd_id' => $pmdId, 'wajib' => true],
            ['nama' => 'Surat Permohonan Beasiswa Satu Desa Satu Sarjana', 'opd_id' => $kesraId, 'wajib' => true],
            ['nama' => 'Surat Pertanggungjawaban Mutlak', 'opd_id' => $kesraId, 'wajib' => true],
        ];

        foreach ($dokumens as $idx => $dok) {
            Dokumen::firstOrCreate(
                ['jalur_id' => $jalur->id, 'nama' => $dok['nama']],
                [
                    'uuid' => (string) Str::uuid(),
                    'wajib' => $dok['wajib'],
                    'opd_id' => $dok['opd_id'],
                    'format_file' => 'pdf,jpg,jpeg,png',
                    'max_size_kb' => 2048,
                    'urutan' => $idx + 1,
                ]
            );
        }
    }
}
