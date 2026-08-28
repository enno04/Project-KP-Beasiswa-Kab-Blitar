<?php

namespace App\Exports;

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
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class DataPendaftarExport extends StringValueBinder implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomValueBinder, WithColumnFormatting
{
    protected $pendaftarans;

    public function __construct($pendaftarans)
    {
        $this->pendaftarans = $pendaftarans;
    }

    public function collection()
    {
        return $this->pendaftarans;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'NIK',
            'Alamat',
            'Desa / Kelurahan',
            'Kecamatan',
            'Asal Perguruan Tinggi'
        ];
    }

    public function map($pendaftaran): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $identitas = $pendaftaran->identitas;
        
        $alamat = $identitas->alamat_ktp ?? '-';
        if (isset($identitas->rt) && isset($identitas->rw)) {
            $alamat .= ' RT ' . $identitas->rt . ' / RW ' . $identitas->rw;
        }

        return [
            $rowNumber,
            $identitas->nama_lengkap ?? $pendaftaran->nama_lengkap ?? '-',
            $identitas->nik ?? '-', // Tanpa petik satu
            $alamat,
            $identitas->desa->nama_desa ?? '-',
            $identitas->kecamatan->nama_kecamatan ?? '-',
            $identitas->asal_perguruan_tinggi ?? '-'
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'C') { // Kolom C adalah NIK
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'C' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
