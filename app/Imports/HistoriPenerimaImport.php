<?php

namespace App\Imports;

use App\Models\HistoriPenerima;
use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HistoriPenerimaImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($rows) {
            $headerRowIndex = -1;
            $colMap = [
                'tahun' => -1,
                'nik' => -1,
                'nama' => -1,
                'jk' => -1,
                'pt' => -1,
                'jenis_beasiswa' => -1,
            ];

            // 1. Cari baris mana yang merupakan Header
            foreach ($rows as $index => $row) {
                foreach ($row as $colIndex => $cell) {
                    if (empty($cell)) continue;
                    $val = strtolower(trim($cell));
                    
                    // Deteksi jika user salah upload file format Admin Kab
                    if (Str::contains($val, ['nomor pendaftaran', 'ranking', 'waktu penetapan'])) {
                        throw new \Exception('Salah format file! Anda mengunggah file format Admin Kabupaten. Silakan gunakan tombol "Import (Admin Kab)".');
                    }
                    
                    if (Str::contains($val, ['nama', 'nik', 'tahun'])) {
                        $headerRowIndex = $index;
                        break 2;
                    }
                }
            }

            if ($headerRowIndex === -1) {
                $headerRowIndex = -1; // kita mula dari 0 nanti
            } else {
                // 2. Petakan indeks kolom berdasarkan header
                $headerRow = $rows[$headerRowIndex];
                foreach ($headerRow as $colIndex => $cell) {
                    if (empty($cell)) continue;
                    $val = strtolower(trim($cell));
                    
                    if (Str::contains($val, ['tahun'])) $colMap['tahun'] = $colIndex;
                    elseif (Str::contains($val, ['nik', 'no ktp'])) $colMap['nik'] = $colIndex;
                    elseif (Str::contains($val, ['nama'])) $colMap['nama'] = $colIndex;
                    elseif (Str::contains($val, ['jenis kelamin', 'kelamin', 'l/p'])) $colMap['jk'] = $colIndex;
                    elseif (Str::contains($val, ['perguruan tinggi', 'kampus', 'universitas', 'pt', 'asal'])) $colMap['pt'] = $colIndex;
                    elseif (Str::contains($val, ['jenis beasiswa', 'beasiswa'])) $colMap['jenis_beasiswa'] = $colIndex;
                }
            }

            // 3. Proses baris data (setelah header)
            $startRow = $headerRowIndex + 1;
            foreach ($rows as $index => $row) {
                if ($index < $startRow) continue;

                // Jika header tidak ketemu, cek jumlah kolom atau posisi
                if ($headerRowIndex === -1) {
                    $tahun = $row[0] ?? null;
                    $nik = $row[1] ?? null;
                    $nama = $row[2] ?? null;

                    // Cek apakah 5 kolom (Tahun, NIK, Nama, Asal PT, Jenis Beasiswa) atau 6 kolom (termasuk JK)
                    if (count($row) <= 5 || (isset($row[4]) && empty($row[5]))) {
                        $jk = null;
                        $pt = $row[3] ?? null;
                        $jenisBeasiswa = $row[4] ?? null;
                    } else {
                        $jk = $row[3] ?? null;
                        $pt = $row[4] ?? null;
                        $jenisBeasiswa = $row[5] ?? null;
                    }
                } else {
                    $tahun = $colMap['tahun'] !== -1 ? ($row[$colMap['tahun']] ?? null) : null;
                    $nik = $colMap['nik'] !== -1 ? ($row[$colMap['nik']] ?? null) : null;
                    $nama = $colMap['nama'] !== -1 ? ($row[$colMap['nama']] ?? null) : null;
                    $jk = $colMap['jk'] !== -1 ? ($row[$colMap['jk']] ?? null) : null;
                    $pt = $colMap['pt'] !== -1 ? ($row[$colMap['pt']] ?? null) : null;
                    $jenisBeasiswa = $colMap['jenis_beasiswa'] !== -1 ? ($row[$colMap['jenis_beasiswa']] ?? null) : null;
                }

                // Skip jika nama kosong
                if (empty($nama)) continue;

                $tahunVal = $tahun ?? '-';
                
                // Cek duplikasi di database
                if ($nik) {
                    $nik = str_replace("'", "", trim($nik));
                    $exists = HistoriPenerima::where('nik', $nik)
                        ->where('tahun', $tahunVal)
                        ->exists();
                        
                    if ($exists) {
                        throw new \Exception("Data Duplikat! Ditemukan data penerima dengan NIK {$nik} pada tahun {$tahunVal} yang sudah pernah di-import sebelumnya. Proses dibatalkan.");
                    }
                }

                HistoriPenerima::create([
                    'tahun' => $tahunVal,
                    'nik' => $nik,
                    'nama_lengkap' => $nama,
                    'asal_perguruan_tinggi' => $pt ?? '-',
                    'jenis_beasiswa' => $jenisBeasiswa ?? '-',
                    'sumber_data' => 'dinas',
                ]);
            }
        });
    }
}
