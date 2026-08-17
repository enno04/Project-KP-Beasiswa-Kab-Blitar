<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opds = [
            [
                'nama_opd' => 'Bagian Kesejahteraan Rakyat',
                'singkatan' => 'KESRA',
            ],
            [
                'nama_opd' => 'Dinas Kepemudaan dan Olahraga',
                'singkatan' => 'DISPORA',
            ],
            [
                'nama_opd' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'singkatan' => 'DISDUKCAPIL',
            ],
            [
                'nama_opd' => 'Dinas Pemberdayaan Masyarakat dan Desa',
                'singkatan' => 'PMD',
            ],
            [
                'nama_opd' => 'Dinas Pendidikan',
                'singkatan' => 'DISDIK',
            ],
            [
                'nama_opd' => 'Dinas Sosial',
                'singkatan' => 'DINSOS',
            ],
        ];

        foreach ($opds as $opd) {
            Opd::firstOrCreate(
                ['nama_opd' => $opd['nama_opd']],
                [
                    'uuid' => (string) Str::uuid(),
                    'singkatan' => $opd['singkatan'],
                ]
            );
        }
    }
}
