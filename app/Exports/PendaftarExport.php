<?php

namespace App\Exports;

use App\Models\Pendaftaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PendaftarExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Pendaftaran::with([
            'identitas.desa',
            'identitas.kecamatan',
            'orangTua',
            'statusEkonomi',
            'akademik',
            'verifikasis',
        ])->orderBy('created_at', 'desc')->get();
    }

    public function title(): string
    {
        return 'Data Pendaftar Beasiswa ' . date('Y');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Pendaftaran',
            'Jenis Beasiswa',
            'Status',
            'NIK',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tempat, Tanggal Lahir',
            'Alamat Domisili',
            'Desa',
            'Kecamatan',
            'No. Telp/WA',
            'Email',
            'Perguruan Tinggi',
            'Fakultas / Jurusan',
            'Semester',
            'IPK',
            'Status PT (Akreditasi)',
            'Prestasi',
            'DTSEN Desil',
            'Pekerjaan Ortu',
            'Penghasilan Ortu',
            'Kepemilikan Rumah',
            'Luas Tempat Tinggal',
            'Rekening Listrik',
            'Tanggungan Keluarga',
            'Kepemilikan Kendaraan',
            'Nama Ayah',
            'Nama Ibu',
            'Total Skor Penilaian',
            'Hasil Verifikasi',
            'Tanggal Daftar',
        ];
    }

    public function map($pendaftaran): array
    {
        static $no = 0;
        $no++;

        $labelDesil = [
            'desil_1' => 'Desil 1: Sangat miskin (miskin ekstrem)',
            'desil_2' => 'Desil 2: Miskin',
            'desil_3' => 'Desil 3: Hampir miskin',
            'desil_4' => 'Desil 4: Rentan miskin',
            'desil_5' => 'Desil 5: Batas bawah kelompok menengah',
            'desil_6_10' => 'Desil 6-10: Kelas menengah ke atas',
        ];

        $labelPekerjaan = [
            'pns_tni_polri' => 'PNS/TNI/Polri/BUMN',
            'pensiunan' => 'Pensiunan',
            'swasta' => 'Karyawan Swasta',
            'buruh' => 'Pekerjaan Tidak Tetap / Buruh',
        ];

        $labelPenghasilan = [
            'diatas_3500' => 'Diatas Rp. 3.500.000',
            '2000_3500' => 'Rp. 2.000.000 - Rp. 3.500.000',
            '1000_2000' => 'Rp. 1.000.000 - Rp. 2.000.000',
            'dibawah_1000' => 'Dibawah Rp. 1.000.000',
        ];

        $labelRumah = [
            'permanen' => 'Rumah Sendiri Permanen',
            'semi_permanen' => 'Rumah Sendiri Semi Permanen',
            'kontrak' => 'Kontrak',
            'menumpang' => 'Menumpang',
        ];

        $labelLuas = [
            'dibawah_21' => 'Dibawah 21 m2',
            '21_45' => '21 - 45 m2',
            'diatas_45' => 'Diatas 45 m2',
        ];

        $labelListrik = [
            'diatas_1300' => 'Diatas 1300 Kwh',
            '900' => '900 Kwh',
            '450' => '450 Kwh',
        ];

        $labelTanggungan = [
            'satu' => '1 Orang',
            'dua' => '2 Orang',
            'tiga' => '3 Orang',
            'lebih_tiga' => 'Lebih dari 3 Orang',
        ];

        $labelKendaraan = [
            'roda_empat' => 'Kendaraan Roda Empat',
            'roda_dua' => 'Kendaraan Roda Dua',
            'sepeda' => 'Sepeda Angin',
            'tidak_punya' => 'Tidak Memiliki Kendaraan',
        ];

        $labelBeasiswa = [
            'sdss' => 'Satu Desa Satu Sarjana',
            'berdaya_berjaya' => 'Berdaya Berjaya',
            'bantuan_biaya' => 'Bantuan Biaya Pendidikan',
        ];

        $labelPrestasi = [
            'nasional_internasional' => 'Nasional / Internasional',
            'provinsi' => 'Tingkat Provinsi',
            'kabupaten_kota' => 'Tingkat Kabupaten / Kota',
            'tidak_ada' => 'Tidak Ada',
        ];

        $labelStatusPT = [
            'ptn_akred_a' => 'PTN Akreditasi A',
            'ptn_akred_b' => 'PTN Akreditasi B',
            'pts_dalam_ab' => 'PTS Dalam Daerah Akreditasi A/B',
            'pts_luar_a' => 'PTS Luar Daerah Akreditasi A',
            'pts_luar_b' => 'PTS Luar Daerah Akreditasi B',
        ];

        $labelStatus = [
            'submitted' => 'Diajukan',
            'verifikasi_desa' => 'Verifikasi Desa',
            'verifikasi_kabupaten' => 'Verifikasi Kabupaten',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
        ];

        // Get latest verification score
        $latestVerif = $pendaftaran->verifikasis->sortByDesc('created_at')->first();

        return [
            $no,
            $pendaftaran->nomor_pendaftaran,
            $labelBeasiswa[$pendaftaran->jenis_beasiswa] ?? $pendaftaran->jenis_beasiswa,
            $labelStatus[$pendaftaran->status] ?? $pendaftaran->status,
            "'" . ($pendaftaran->identitas->nik ?? ''),  // prefix with ' to keep as text in Excel
            $pendaftaran->identitas->nama_lengkap ?? '',
            ($pendaftaran->identitas->jenis_kelamin ?? '') === 'L' ? 'Laki-laki' : 'Perempuan',
            ($pendaftaran->identitas->tempat_lahir ?? '') . ', ' . ($pendaftaran->identitas->tanggal_lahir ?? ''),
            $pendaftaran->identitas->alamat_domisili ?? '',
            $pendaftaran->identitas->desa->nama_desa ?? '',
            $pendaftaran->identitas->kecamatan->nama_kecamatan ?? '',
            $pendaftaran->identitas->no_telp ?? '',
            $pendaftaran->identitas->email ?? '',
            $pendaftaran->identitas->asal_perguruan_tinggi ?? '',
            $pendaftaran->identitas->fakultas_jurusan ?? '',
            $pendaftaran->identitas->semester ?? '',
            $pendaftaran->akademik->ipk ?? '',
            $labelStatusPT[$pendaftaran->akademik->status_pt ?? ''] ?? '',
            $labelPrestasi[$pendaftaran->akademik->prestasi ?? ''] ?? '',
            $labelDesil[$pendaftaran->statusEkonomi->dtsen_desil ?? ''] ?? '',
            $labelPekerjaan[$pendaftaran->statusEkonomi->pekerjaan_ortu ?? ''] ?? '',
            $labelPenghasilan[$pendaftaran->statusEkonomi->penghasilan_ortu ?? ''] ?? '',
            $labelRumah[$pendaftaran->statusEkonomi->kepemilikan_rumah ?? ''] ?? '',
            $labelLuas[$pendaftaran->statusEkonomi->luas_tempat_tinggal ?? ''] ?? '',
            $labelListrik[$pendaftaran->statusEkonomi->rekening_listrik ?? ''] ?? '',
            $labelTanggungan[$pendaftaran->statusEkonomi->tanggungan_keluarga ?? ''] ?? '',
            $labelKendaraan[$pendaftaran->statusEkonomi->kepemilikan_kendaraan ?? ''] ?? '',
            $pendaftaran->orangTua->nama_ayah ?? '',
            $pendaftaran->orangTua->nama_ibu ?? '',
            $latestVerif ? $latestVerif->total_skor : '-',
            $latestVerif ? strtoupper($latestVerif->hasil) : '-',
            $pendaftaran->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        return [
            // Header row styling
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0B5ED7'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            // All cells border
            "A1:{$lastCol}{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }
}
