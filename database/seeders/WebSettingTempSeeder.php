<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WebSetting;

class WebSettingTempSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'key' => 'address',
                'value' => 'Jl. Raya Sawahan Pojok, Kec. Garum, Blitar',
                'type' => 'text',
                'label' => 'Alamat Sekretariat',
                'description' => 'Alamat lengkap kantor sekretariat.'
            ],
            [
                'key' => 'operational_hours',
                'value' => 'Sen — Jum, 08:00 — 16:00 WIB',
                'type' => 'text',
                'label' => 'Jam Operasional',
                'description' => 'Contoh: Sen — Jum, 08:00 — 16:00 WIB'
            ],
            [
                'key' => 'maps_link',
                'value' => 'https://maps.app.goo.gl/Wgz7JqscQjiqs348A',
                'type' => 'text',
                'label' => 'Link Google Maps',
                'description' => 'URL Google Maps kantor sekretariat.'
            ],
            [
                'key' => 'instagram_link',
                'value' => '',
                'type' => 'text',
                'label' => 'Link Instagram',
                'description' => 'URL profil Instagram resmi (kosongkan jika tidak ada).'
            ],
            [
                'key' => 'website_link',
                'value' => '',
                'type' => 'text',
                'label' => 'Link Website Resmi',
                'description' => 'URL website dinas/instansi (kosongkan jika tidak ada).'
            ],
            [
                'key' => 'announcement_active',
                'value' => '0',
                'type' => 'boolean',
                'label' => 'Aktifkan Pengumuman Global',
                'description' => 'Centang untuk mengaktifkan banner teks berjalan di halaman depan.'
            ],
            [
                'key' => 'announcement_text',
                'value' => 'Pendaftaran Beasiswa Mahasiswa Berprestasi Kabupaten Blitar Resmi Dibuka!',
                'type' => 'text',
                'label' => 'Teks Pengumuman Global',
                'description' => 'Isi pesan pengumuman penting.'
            ]
        ];

        foreach($data as $item) {
            WebSetting::firstOrCreate(['key' => $item['key']], $item);
        }
    }
}
