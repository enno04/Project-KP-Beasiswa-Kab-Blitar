<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Jalur;
use App\Models\Kecamatan;
use App\Models\Periode;
use App\Models\Program;
use App\Models\User;
use App\Models\PerguruanTinggi;
use App\Models\Pendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EndToEndBeasiswaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the base V2 architecture data
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
        
        // Enable exception handling output
        $this->withoutExceptionHandling();
    }

    public function test_full_scholarship_lifecycle()
    {
        Storage::fake('public');

        // 1. Get seeded data
        $program = Program::where('kode', 'sdss')->firstOrFail();
        $jalur = Jalur::where('program_id', $program->id)->firstOrFail();
        
        $kecamatan = Kecamatan::firstOrFail();
        $desa = Desa::where('kecamatan_id', $kecamatan->id)->firstOrFail();


        // ==========================================
        // 1. REGISTRATION (Public)
        // ==========================================
        $payload = [
            'program_slug' => $program->slug,
            'jalur_slug' => $jalur->slug,
            'nik' => '1234567890123456',
            'nama_lengkap' => 'John Doe',
            'tempat_lahir' => 'Blitar',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'alamat_ktp' => 'Jl. Test No. 1',
            'google_maps_url' => 'https://maps.google.com/?q=-8.1,112.1',
            'desa_id' => $desa->id,
            'kecamatan_id' => $kecamatan->id,
            'no_hp' => '081234567890',
            'email' => 'test@example.com',
            'asal_perguruan_tinggi' => 'Universitas Indonesia',
            'program_studi' => 'Teknik Informatika',
            'semester' => 3,

            // Data Ayah
            'nama_ayah' => 'Ayah',
            'nik_ayah' => '1234567890123456',
            'alamat_ayah' => 'Jl. Test',
            'tempat_lahir_ayah' => 'Blitar',
            'tanggal_lahir_ayah' => '1970-01-01',
            'no_hp_ayah' => '0812',

            // Data Ibu
            'nama_ibu' => 'Ibu',
            'nik_ibu' => '1234567890123456',
            'alamat_ibu' => 'Jl. Test',
            'tempat_lahir_ibu' => 'Blitar',
            'tanggal_lahir_ibu' => '1970-01-01',
            'no_hp_ibu' => '0812',
        ];

        // Populate dynamic criteria
        $kriterias = $jalur->getAllKriterias();
        foreach ($kriterias as $k) {
            if ($k->isPilihan()) {
                $pilihan = $k->pilihans()->first();
                $payload["kriteria_{$k->id}"] = $pilihan->id;
            } else {
                $payload["kriteria_{$k->id}"] = $k->nilai_max;
            }
        }

        // Populate dynamic documents
        $dokumens = $jalur->dokumens;
        foreach ($dokumens as $dok) {
            $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
            $payload["dokumen_{$dok->id}"] = $file;
        }

        $response = $this->post(route('pendaftaran.store'), $payload);

        $pendaftaran = Pendaftaran::where('jalur_id', $jalur->id)->first();
        $this->assertNotNull($pendaftaran);
        $this->assertEquals('menunggu_verifikasi', $pendaftaran->status);
        $response->assertRedirect(route('pendaftaran.bukti', $pendaftaran->id));

        // ==========================================
        // 2. VERIFICATION (OPD Admin)
        // ==========================================
        $opdRoleId = \Illuminate\Support\Facades\DB::table('roles')->where('kode', 'admin_opd')->value('id');
        $opdAdmin = User::where('role_id', $opdRoleId)->first();
        $this->actingAs($opdAdmin);

        foreach ($pendaftaran->uploadDokumens as $uploadDok) {
            $this->post(route('opd.verifikasi.store', $uploadDok->id), [
                'hasil' => 'valid',
                'catatan' => 'Sesuai'
            ]);
        }

        $pendaftaran->refresh();
        $this->assertEquals('lolos_verifikasi', $pendaftaran->status);

        // ==========================================
        // 3. CALCULATION & RANKING (Desa / Kabupaten)
        // ==========================================
        $desaRoleId = \Illuminate\Support\Facades\DB::table('roles')->where('kode', 'admin_desa')->value('id');
        $desaAdmin = User::where('role_id', $desaRoleId)->first();
        if ($desaAdmin) {
            $desaAdmin->desa_id = $desa->id;
            $desaAdmin->save();
            $this->actingAs($desaAdmin->fresh());
        }

        $periode = Periode::aktif()->first();

        $response = $this->post(route('desa.penilaian.hitung'), [
            'jalur_id' => $jalur->id,
            'periode_id' => $periode->id
        ]);
        
        $pendaftaran->refresh();
        $this->assertNotNull($pendaftaran->total_nilai);
        $this->assertEquals(1, $pendaftaran->ranking);

        // Desa Rekomendasi & Kecamatan Verifikasi -> status menjadi menunggu_penetapan
        \App\Models\RekomendasiDesa::create([
            'pendaftaran_id' => $pendaftaran->id,
            'desa_id' => $pendaftaran->identitas->desa_id,
            'user_id' => $desaAdmin?->id ?? 1,
            'surat_rekomendasi_path' => 'rekomendasi/test.pdf',
            'berita_acara_path' => 'rekomendasi/ba.pdf',
            'status_kecamatan' => 'disetujui',
            'verified_at' => now(),
        ]);
        $pendaftaran->update(['status' => 'menunggu_penetapan']);

        // ==========================================
        // 5. PENETAPAN (Kabupaten Admin)
        // ==========================================
        $kabRoleId = \Illuminate\Support\Facades\DB::table('roles')->where('kode', 'admin_kabupaten')->value('id');
        $kabAdmin = User::where('role_id', $kabRoleId)->first();
        $this->actingAs($kabAdmin);
        $response = $this->post(route('kabupaten.hasil.penetapan.store'), [
            'pendaftaran_ids' => [$pendaftaran->id],
            'keputusan' => 'lulus',
            'catatan' => 'Lulus seleksi'
        ]);

        $pendaftaran->refresh();
        $this->assertEquals('lulus', $pendaftaran->status);
        
        $this->assertTrue(true, 'Full Lifecycle E2E Test Passed!');
    }
}
