<?php

namespace App\Exports;

use App\Models\Pendaftaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RiwayatPenetapanExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomValueBinder, WithColumnFormatting
{
    protected $request;

    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Pendaftaran::with(['identitas', 'identitas.desa', 'identitas.kecamatan', 'program', 'jalur'])
            ->where('status', 'lulus');

        if ($this->request->search) {
            $query->where(function($q) {
                $q->where('nomor_pendaftaran', 'like', '%' . $this->request->search . '%')
                  ->orWhereHas('identitas', function($q2) {
                      $q2->where('nama_lengkap', 'like', '%' . $this->request->search . '%')
                         ->orWhere('nik', 'like', '%' . $this->request->search . '%');
                  });
            });
        }

        if ($this->request->program_id) {
            $query->where('program_id', $this->request->program_id);
        }

        if ($this->request->kecamatan_id) {
            $query->whereHas('identitas', fn($q) => $q->where('kecamatan_id', $this->request->kecamatan_id));
        }

        if ($this->request->desa_id) {
            $query->whereHas('identitas', fn($q) => $q->where('desa_id', $this->request->desa_id));
        }

        return $query->latest('updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'Nomor Pendaftaran',
            'Nama Lengkap',
            'Asal Perguruan Tinggi',
            'NIK',
            'Program Beasiswa',
            'Jalur Beasiswa',
            'Total Nilai',
            'Ranking',
            'Asal Desa',
            'Asal Kecamatan',
            'Waktu Penetapan SK',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nomor_pendaftaran,
            $row->identitas->nama_lengkap ?? '-',
            $row->identitas->asal_perguruan_tinggi ?? '-',
            $row->identitas->nik ?? '-', // Hapus petik, format diatur di bindValue
            $row->program->nama ?? '-',
            $row->jalur->nama ?? '-',
            $row->total_nilai ?? '-',
            $row->ranking ?? '-',
            $row->identitas->desa->nama_desa ?? '-',
            $row->identitas->kecamatan->nama_kecamatan ?? '-',
            $row->updated_at ? $row->updated_at->format('d/m/Y H:i:s') : '-',
        ];
    }

    /**
     * Set kolom D (NIK) spesifik sebagai string agar tidak berubah ke scientific notation tanpa menambah simbol petik.
     */
    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'D') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    /**
     * Pastikan format kolom D adalah Teks (Text Format) pada level Spreadsheet.
     * Ini mencegah Excel merubah NIK menjadi scientific notation meskipun user meng-klik atau mengedit sel.
     */
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT, // Mengubah kategori sel menjadi 'Text'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();
        $range = 'A1:' . $highestCol . $highestRow;

        return [
            // Headings menjadi tebal (Bold)
            1 => ['font' => ['bold' => true]],

            // Menambahkan border pada seluruh tabel (pembatas per sel/kolom tipis)
            $range => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    ],
                    // Kotak garis tebal di luar tabel
                    'outline' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['argb' => '00000000'],
                    ],
                ],
            ],
        ];
    }
}
