<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Opd;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Inisialisasi Role System
        $roles = [
            'super_admin' => Role::firstOrCreate(
                ['kode' => 'super_admin'],
                ['uuid' => (string) Str::uuid(), 'nama' => 'Super Admin', 'deskripsi' => 'Pengelola Utama Sistem']
            ),
            'admin_kabupaten' => Role::firstOrCreate(
                ['kode' => 'admin_kabupaten'],
                ['uuid' => (string) Str::uuid(), 'nama' => 'Admin Kabupaten', 'deskripsi' => 'Tim Seleksi Kabupaten']
            ),
            'admin_opd' => Role::firstOrCreate(
                ['kode' => 'admin_opd'],
                ['uuid' => (string) Str::uuid(), 'nama' => 'Admin OPD', 'deskripsi' => 'Verifikator Dokumen OPD']
            ),
            'admin_kecamatan' => Role::firstOrCreate(
                ['kode' => 'admin_kecamatan'],
                ['uuid' => (string) Str::uuid(), 'nama' => 'Admin Kecamatan', 'deskripsi' => 'Verifikator Tingkat Kecamatan']
            ),
            'admin_desa' => Role::firstOrCreate(
                ['kode' => 'admin_desa'],
                ['uuid' => (string) Str::uuid(), 'nama' => 'Admin Desa', 'deskripsi' => 'Verifikator / Usulan Tingkat Desa']
            ),
            'admin_dpmd' => Role::firstOrCreate(
                ['kode' => 'admin_dpmd'],
                ['uuid' => (string) Str::uuid(), 'nama' => 'Admin DPMD', 'deskripsi' => 'Verifikator Tingkat DPMD (SDSS)']
            ),
        ];

        // 2. Super Admin User
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Super Admin',
                'password' => $password,
                'role_id' => $roles['super_admin']->id,
                'status' => true,
            ]
        );

        // 3. Admin Kabupaten User
        User::updateOrCreate(
            ['username' => 'adminkab'],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Admin Kabupaten Blitar',
                'password' => $password,
                'role_id' => $roles['admin_kabupaten']->id,
                'status' => true,
            ]
        );

        // 3b. Admin DPMD User
        User::updateOrCreate(
            ['username' => 'admindpmd'],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Admin DPMD Kab. Blitar',
                'password' => $password,
                'role_id' => $roles['admin_dpmd']->id,
                'status' => true,
            ]
        );

        // 4. Admin OPD Users (6 OPDs)
        $opds = Opd::all();
        foreach ($opds as $opd) {
            $username = 'admin_' . strtolower($opd->singkatan ?: Str::slug($opd->nama_opd));
            User::updateOrCreate(
                ['username' => $username],
                [
                    'uuid' => (string) Str::uuid(),
                    'nama' => 'Admin ' . $opd->nama_opd,
                    'password' => $password,
                    'role_id' => $roles['admin_opd']->id,
                    'opd_id' => $opd->id,
                    'status' => true,
                ]
            );
        }

        // 5. Admin Kecamatan Users (22 Kecamatan)
        $kecamatans = Kecamatan::all();
        foreach ($kecamatans as $kec) {
            $username = 'adminkec_' . Str::slug($kec->nama_kecamatan);
            User::updateOrCreate(
                ['username' => $username],
                [
                    'uuid' => (string) Str::uuid(),
                    'nama' => 'Admin ' . $kec->nama_kecamatan,
                    'password' => $password,
                    'role_id' => $roles['admin_kecamatan']->id,
                    'kecamatan_id' => $kec->id,
                    'status' => true,
                ]
            );
        }

        // 6. Admin Desa Users (248 Desa)
        $desas = Desa::with('kecamatan')->get();
        foreach ($desas as $desa) {
            $username = 'admindesa_' . $desa->kode_desa;
            User::updateOrCreate(
                ['username' => $username],
                [
                    'uuid' => (string) Str::uuid(),
                    'nama' => 'Admin ' . $desa->nama_desa,
                    'password' => $password,
                    'role_id' => $roles['admin_desa']->id,
                    'kecamatan_id' => $desa->kecamatan_id,
                    'desa_id' => $desa->id,
                    'status' => true,
                ]
            );
        }
    }
}
