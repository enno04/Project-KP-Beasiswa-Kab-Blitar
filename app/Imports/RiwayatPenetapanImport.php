<?php

namespace App\Imports;

use App\Models\HistoriPenerima;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RiwayatPenetapanImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($rows) {
            $headerRowIndex = -1;
            $colMap = [
                'nomor_pendaftaran' => -1,
                'nama_lengkap' => -1,
                'asal_perguruan_tinggi' => -1,
                'nik' => -1,
                'program_beasiswa' => -1,
                'jalur_beasiswa' => -1,
                'waktu_penetapan' => -1,
                'ipk_nilai' => -1,
                'asal_sekolah' => -1,
                'kecamatan' => -1,
                'desa' => -1,
            ];

            // 1. Cari baris mana yang merupakan Header
            foreach ($rows as $index => $row) {
                foreach ($row as $colIndex => $cell) {
                    if (empty($cell)) continue;
                    $val = strtolower(trim($cell));
                    if (Str::contains($val, ['nomor pendaftaran', 'nama lengkap', 'nik'])) {
                        $headerRowIndex = $index;
                        break 2;
                    }
                }
            }

            if ($headerRowIndex === -1) {
                $headerRowIndex = -1;
            } else {
                // 2. Petakan indeks kolom berdasarkan header
                $headerRow = $rows[$headerRowIndex];
                foreach ($headerRow as $colIndex => $cell) {
                    if (empty($cell)) continue;
                    $val = strtolower(trim($cell));
                    
                    if (Str::contains($val, ['nomor pendaftaran'])) $colMap['nomor_pendaftaran'] = $colIndex;
                    elseif (Str::contains($val, ['nama lengkap'])) $colMap['nama_lengkap'] = $colIndex;
                    elseif (Str::contains($val, ['asal perguruan tinggi', 'asal pt'])) $colMap['asal_perguruan_tinggi'] = $colIndex;
                    elseif (Str::contains($val, ['nik'])) $colMap['nik'] = $colIndex;
                    elseif (Str::contains($val, ['program beasiswa'])) $colMap['program_beasiswa'] = $colIndex;
                    elseif (Str::contains($val, ['jalur beasiswa'])) $colMap['jalur_beasiswa'] = $colIndex;
                    elseif (Str::contains($val, ['waktu penetapan'])) $colMap['waktu_penetapan'] = $colIndex;
                    elseif (Str::contains($val, ['ipk', 'nilai'])) $colMap['ipk_nilai'] = $colIndex;
                    elseif (Str::contains($val, ['asal sekolah'])) $colMap['asal_sekolah'] = $colIndex;
                    elseif (Str::contains($val, ['kecamatan'])) $colMap['kecamatan'] = $colIndex;
                    elseif (Str::contains($val, ['desa', 'kelurahan'])) $colMap['desa'] = $colIndex;
                }
                \Log::info('colMap debug: ' . json_encode($colMap));
            }

            // 3. Proses baris data (setelah header)
            $startRow = $headerRowIndex + 1;
            foreach ($rows as $index => $row) {
                if ($index < $startRow) continue;

                if ($headerRowIndex === -1) {
                    $nama = $row[1] ?? null; // B
                    $pt = $row[2] ?? null; // C (Asal PT)
                    $nik = $row[3] ?? null; // D
                    $program = $row[4] ?? null; // E
                    $jalur = $row[5] ?? null; // F
                    $waktu = $row[10] ?? null; // K
                } else {
                    $nama = $colMap['nama_lengkap'] !== -1 ? ($row[$colMap['nama_lengkap']] ?? null) : null;
                    $pt = $colMap['asal_perguruan_tinggi'] !== -1 ? ($row[$colMap['asal_perguruan_tinggi']] ?? null) : null;
                    $nik = $colMap['nik'] !== -1 ? ($row[$colMap['nik']] ?? null) : null;
                    $program = $colMap['program_beasiswa'] !== -1 ? ($row[$colMap['program_beasiswa']] ?? null) : null;
                    $jalur = $colMap['jalur_beasiswa'] !== -1 ? ($row[$colMap['jalur_beasiswa']] ?? null) : null;
                    $waktu = $colMap['waktu_penetapan'] !== -1 ? ($row[$colMap['waktu_penetapan']] ?? null) : null;
                    $nomorPendaftaran = $colMap['nomor_pendaftaran'] !== -1 ? ($row[$colMap['nomor_pendaftaran']] ?? null) : null;
                    $ipkNilai = $colMap['ipk_nilai'] !== -1 ? ($row[$colMap['ipk_nilai']] ?? null) : null;
                    $asalSekolah = $colMap['asal_sekolah'] !== -1 ? ($row[$colMap['asal_sekolah']] ?? null) : null;
                    $kecamatan = $colMap['kecamatan'] !== -1 ? ($row[$colMap['kecamatan']] ?? null) : null;
                    $desa = $colMap['desa'] !== -1 ? ($row[$colMap['desa']] ?? null) : null;
                }

                // Skip jika nama kosong
                if (empty($nama)) continue;

                // Bersihkan single quote dari NIK jika ada (walau sudah difix, untuk jaga-jaga kalau user import file lama)
                if ($nik) {
                    $nik = str_replace("'", "", $nik);
                    $nik = trim($nik);
                }

                // Gabungkan Jenis Beasiswa
                $jenisBeasiswa = trim(($program ?? '') . ' - ' . ($jalur ?? ''), ' -');
                if (empty($jenisBeasiswa)) {
                    $jenisBeasiswa = '-';
                }

                // Ekstrak Tahun dari Waktu Penetapan (Format d/m/Y H:i:s atau Serial Excel)
                $tahun = date('Y'); // default
                if (!empty($waktu) && $waktu !== '-') {
                    if (is_numeric($waktu)) {
                        // Excel Date Serial Number
                        $unix_date = ($waktu - 25569) * 86400;
                        $tahun = gmdate("Y", $unix_date);
                    } else {
                        // String date like "d/m/Y H:i:s"
                        $parts = explode('/', $waktu);
                        if (count($parts) >= 3) {
                            $yearPart = explode(' ', $parts[2])[0];
                            if (strlen($yearPart) == 4) {
                                $tahun = $yearPart;
                            }
                        }
                    }
                }

                // Cek duplikasi di database
                if ($nik) {
                    $exists = HistoriPenerima::where('nik', $nik)
                        ->where('tahun', $tahun)
                        ->exists();
                        
                    if ($exists) {
                        throw new \Exception("Data Duplikat! Ditemukan data penerima dengan NIK {$nik} pada tahun {$tahun} yang sudah pernah di-import sebelumnya. Proses dibatalkan untuk mencegah data ganda.");
                    }
                }

                HistoriPenerima::create([
                    'tahun' => $tahun,
                    'nik' => $nik ?? '-',
                    'nama_lengkap' => $nama,
                    'asal_perguruan_tinggi' => $pt ?? '-',
                    'jenis_beasiswa' => $jenisBeasiswa,
                    'nomor_pendaftaran' => $nomorPendaftaran,
                    'jalur_beasiswa' => $jalur,
                    'ipk_nilai' => $ipkNilai,
                    'asal_sekolah' => $asalSekolah,
                    'kecamatan' => $kecamatan,
                    'desa' => $desa,
                    'waktu_penetapan' => $waktu,
                    'sumber_data' => 'sistem',
                ]);
            }
        });
    }
}
