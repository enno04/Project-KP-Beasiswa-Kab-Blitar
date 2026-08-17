<?php

namespace Database\Seeders;

use App\Models\DokumenPublik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenPublikSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure storage directory exists
        if (!Storage::disk('public')->exists('dokumen_publik')) {
            Storage::disk('public')->makeDirectory('dokumen_publik');
        }

        // Truncate table to re-seed cleanly
        DokumenPublik::truncate();

        $sourceDir = database_path('seeders/data/dokumen penting');

        $dokumenList = [
            [
                'file_name' => 'Surat Permohonan Beasiswa Bantuan Biaya Pendidikan.docx',
                'nama' => 'Surat Permohonan Beasiswa Bantuan Biaya Pendidikan',
                'deskripsi' => 'Template format Surat Permohonan resmi untuk Program Beasiswa Bantuan Biaya Pendidikan (BBP).',
                'urutan' => 1,
            ],
            [
                'file_name' => 'Surat Permohonan Beasiswa Berdaya Berjaya.docx',
                'nama' => 'Surat Permohonan Beasiswa Berdaya Berjaya',
                'deskripsi' => 'Template format Surat Permohonan resmi untuk Program Beasiswa Berdaya Berjaya.',
                'urutan' => 2,
            ],
            [
                'file_name' => 'Surat Permohonan Beasiswa Satu Desa Satu Sarjana.docx',
                'nama' => 'Surat Permohonan Beasiswa Satu Desa Satu Sarjana',
                'deskripsi' => 'Template format Surat Permohonan resmi untuk Program Beasiswa Satu Desa Satu Sarjana (SDSS).',
                'urutan' => 3,
            ],
            [
                'file_name' => 'Surat Pernyataan Beasiswa Blitar Mengabdi.docx',
                'nama' => 'Surat Pernyataan Beasiswa Blitar Mengabdi',
                'deskripsi' => 'Template format Surat Pernyataan Keabsahan Data dan Kesanggupan Pemohon Beasiswa Blitar Mengabdi.',
                'urutan' => 4,
            ],
            [
                'file_name' => 'Surat Pertanggungjawaban Mutlak Beasiswa Blitar Mengabdi.docx',
                'nama' => 'Surat Pertanggungjawaban Mutlak Beasiswa Blitar Mengabdi',
                'deskripsi' => 'Template format Surat Pertanggungjawaban Mutlak (SPTJM) Kebenaran Dokumen Pemohon Beasiswa.',
                'urutan' => 5,
            ],
        ];

        foreach ($dokumenList as $item) {
            $fullSourcePath = $sourceDir . DIRECTORY_SEPARATOR . $item['file_name'];
            
            if (File::exists($fullSourcePath)) {
                $fileContent = File::get($fullSourcePath);
                $fileSize = File::size($fullSourcePath);
            } else {
                $fileContent = "Mock content for " . $item['file_name'];
                $fileSize = strlen($fileContent);
            }

            $storedFilename = Str::uuid() . '.docx';
            $storagePath = 'dokumen_publik/' . $storedFilename;

            // Put file in public disk storage
            Storage::disk('public')->put($storagePath, $fileContent);

            DokumenPublik::create([
                'uuid' => (string) Str::uuid(),
                'nama' => $item['nama'],
                'deskripsi' => $item['deskripsi'],
                'file_path' => $storagePath,
                'file_name' => $item['file_name'],
                'format_file' => 'docx',
                'ukuran_file' => $fileSize,
                'urutan' => $item['urutan'],
                'status_aktif' => true,
            ]);
        }
    }
}
