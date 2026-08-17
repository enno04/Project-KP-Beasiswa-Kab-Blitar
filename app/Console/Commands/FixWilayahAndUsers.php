<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixWilayahAndUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-wilayah-and-users';
    protected $description = 'Fix duplicated Wilayah data and generate Admin Kecamatan/Desa users for all.';

    public function handle()
    {
        $this->info('Mulai perbaikan data wilayah dan pembuatan user...');
        
        // 1. Pemetaan ID lama (duplikat dengan prefix) ke ID baru (kanonikal)
        // Kecamatan ID 1 (Kec. Kanigoro) -> ID 10 (Kanigoro)
        // Kecamatan ID 2 (Kec. Garum) -> ID 8 (Garum)
        // Kecamatan ID 3 (Kec. Talun) -> ID 20 (Talun)
        $mapKec = [
            1 => 10,
            2 => 8,
            3 => 20,
        ];

        // Desa IDs 1-9 -> cari ID kanonikal berdasarkan nama (buang prefix "Kel. ", "Desa ")
        $mapDesa = [];
        $desasLama = \App\Models\Desa::whereIn('id', [1,2,3,4,5,6,7,8,9])->get();
        foreach ($desasLama as $desaLama) {
            $namaBersih = str_replace(['Kel. ', 'Desa '], '', $desaLama->nama_desa);
            $desaKanonikal = \App\Models\Desa::where('nama_desa', $namaBersih)
                ->where('id', '>', 9)
                ->first();
            
            if ($desaKanonikal) {
                $mapDesa[$desaLama->id] = $desaKanonikal->id;
            }
        }

        // 2. Update Foreign Keys di tabel User dan PendaftaranIdentitas
        foreach ($mapKec as $oldId => $newId) {
            \App\Models\User::where('kecamatan_id', $oldId)->update(['kecamatan_id' => $newId]);
            \App\Models\PendaftaranIdentitas::where('kecamatan_id', $oldId)->update(['kecamatan_id' => $newId]);
            // Hapus kecamatan lama
            \App\Models\Kecamatan::where('id', $oldId)->delete();
        }

        foreach ($mapDesa as $oldId => $newId) {
            \App\Models\User::where('desa_id', $oldId)->update(['desa_id' => $newId]);
            \App\Models\PendaftaranIdentitas::where('desa_id', $oldId)->update(['desa_id' => $newId]);
            // Hapus desa lama
            \App\Models\Desa::where('id', $oldId)->delete();
        }

        $this->info('Data wilayah berhasil dibersihkan.');

        $this->info('Menghapus seluruh User Admin Kecamatan dan Admin Desa sebelumnya...');
        \App\Models\User::whereIn('role_id', [4, 5])->forceDelete();
        $this->info('User lama berhasil dihapus.');

        // 3. Generate Users untuk seluruh Kecamatan
        $kecamatans = \App\Models\Kecamatan::all();
        $countKec = 0;
        foreach ($kecamatans as $kec) {
            $username = 'adminkec_' . strtolower(str_replace(' ', '', $kec->nama_kecamatan));
            // cek username conflict (just in case there are other roles with this username)
            if (\App\Models\User::where('username', $username)->exists()) {
                $username .= '_' . $kec->id;
            }
            \App\Models\User::create([
                'nama' => 'Admin ' . $kec->nama_kecamatan,
                'username' => $username,
                'password' => bcrypt('password123'),
                'role_id' => 4,
                'kecamatan_id' => $kec->id,
                'status' => true,
            ]);
            $countKec++;
        }

        // 4. Generate Users untuk seluruh Desa
        $desas = \App\Models\Desa::with('kecamatan')->get();
        $countDesa = 0;
        foreach ($desas as $desa) {
            $cleanDesa = strtolower(str_replace([' ', '-', '.'], '', $desa->nama_desa));
            $username = 'admindesa_' . $cleanDesa;
            // cek username conflict
            if (\App\Models\User::where('username', $username)->exists()) {
                $username .= '_' . $desa->id;
            }
            \App\Models\User::create([
                'nama' => 'Admin ' . $desa->nama_desa,
                'username' => $username,
                'password' => bcrypt('password123'),
                'role_id' => 5,
                'desa_id' => $desa->id,
                'status' => true,
            ]);
            $countDesa++;
        }

        $this->info("Berhasil membuat $countKec User Kecamatan dan $countDesa User Desa baru.");
        $this->info('Semua password default: password123');
    }
}
