<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['kode' => 'super_admin'],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Super Admin',
                'deskripsi' => 'Pengelola utama sistem beasiswa',
            ]
        );

        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'uuid' => (string) Str::uuid(),
                'nama' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $role->id,
                'status' => true,
            ]
        );
    }
}
