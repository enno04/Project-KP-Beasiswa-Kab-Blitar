<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Temukan semua desa yang namanya sama persis dengan nama kecamatannya
        $desas = DB::table('desas')
            ->join('kecamatans', 'desas.kecamatan_id', '=', 'kecamatans.id')
            ->whereRaw('LOWER(desas.nama_desa) = LOWER(kecamatans.nama_kecamatan)')
            ->select('desas.id')
            ->get();

        $duplicateIds = $desas->pluck('id')->toArray();

        if (count($duplicateIds) > 0) {
            // Hapus Admin Desa yang terkait dengan desa duplikat tersebut
            DB::table('users')->whereIn('desa_id', $duplicateIds)->delete();
            
            // Hapus desa duplikat tersebut
            DB::table('desas')->whereIn('id', $duplicateIds)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Proses ini tidak bisa di-reverse dengan mudah tanpa seeder, jadi dibiarkan kosong.
    }
};
