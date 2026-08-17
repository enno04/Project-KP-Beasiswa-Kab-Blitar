<?php

namespace Tests\Feature;

use App\Models\DokumenPublik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumenPublikTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
        Storage::fake('public');
    }

    public function test_super_admin_can_view_dokumen_publik_index(): void
    {
        $superAdmin = User::whereHas('role', fn($q) => $q->where('kode', 'super_admin'))->first();

        $response = $this->actingAs($superAdmin)->get(route('super-admin.master.dokumen-publik.index'));
        $response->assertStatus(200);
        $response->assertSee('Dokumen Publik');
    }

    public function test_super_admin_can_upload_dokumen_publik(): void
    {
        $superAdmin = User::whereHas('role', fn($q) => $q->where('kode', 'super_admin'))->first();
        $file = UploadedFile::fake()->create('panduan_beasiswa.pdf', 500, 'application/pdf');

        $response = $this->actingAs($superAdmin)->post(route('super-admin.master.dokumen-publik.store'), [
            'nama' => 'Ebook Panduan Pendaftaran Test',
            'deskripsi' => 'Deskripsi panduan pendaftaran.',
            'file' => $file,
            'urutan' => 1,
            'status_aktif' => 1,
        ]);

        $response->assertRedirect(route('super-admin.master.dokumen-publik.index'));
        $this->assertDatabaseHas('dokumen_publiks', [
            'nama' => 'Ebook Panduan Pendaftaran Test',
            'format_file' => 'pdf',
            'status_aktif' => 1,
        ]);
    }

    public function test_public_user_can_download_active_dokumen_publik(): void
    {
        $doc = DokumenPublik::first();
        $this->assertNotNull($doc);

        Storage::disk('public')->put($doc->file_path, 'sample content');

        $response = $this->get(route('dokumen.publik.download', $doc->id));
        $response->assertStatus(200);
    }
}
