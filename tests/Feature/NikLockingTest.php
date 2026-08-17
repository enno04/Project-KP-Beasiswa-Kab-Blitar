<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\HistoriPenerima;
use App\Models\Jalur;
use App\Models\Kecamatan;
use App\Models\Pendaftaran;
use App\Models\PendaftaranIdentitas;
use App\Models\Periode;
use App\Models\Program;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class NikLockingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
    }

    public function test_legacy_histori_penerima_nik_is_permanently_locked()
    {
        $service = app(RegistrationService::class);
        $periode = Periode::aktif()->firstOrFail();
        $program = Program::firstOrFail();

        HistoriPenerima::create([
            'tahun' => '2024',
            'nik' => '3505999999999999',
            'nama_lengkap' => 'Penerima Lama',
            'asal_perguruan_tinggi' => 'UGM',
            'jenis_beasiswa' => 'BBP',
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NIK 3505999999999999 telah terdaftar sebagai penerima beasiswa');

        $service->validateNikAvailability('3505999999999999', $program, $periode);
    }

    public function test_system_v2_accepted_penerima_nik_is_permanently_locked()
    {
        $service = app(RegistrationService::class);
        $periode = Periode::aktif()->firstOrFail();
        $program = Program::firstOrFail();
        $jalur = Jalur::firstOrFail();

        $pendaftaran = Pendaftaran::create([
            'nomor_pendaftaran' => 'TEST-001',
            'periode_id' => $periode->id,
            'program_id' => $program->id,
            'jalur_id' => $jalur->id,
            'tahun' => $periode->tahun,
            'status' => 'lulus',
        ]);

        PendaftaranIdentitas::create([
            'pendaftaran_id' => $pendaftaran->id,
            'nik' => '3505888888888888',
            'nama_lengkap' => 'Penerima Baru',
            'tempat_lahir' => 'Blitar',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'alamat_ktp' => 'Alamat',
            'google_maps_url' => 'https://maps.google.com',
            'desa_id' => 1,
            'kecamatan_id' => 1,
            'no_hp' => '081',
            'asal_perguruan_tinggi' => 'UNS',
            'program_studi' => 'Informatika',
            'semester' => 1,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NIK 3505888888888888 telah ditetapkan sebagai penerima beasiswa');

        $service->validateNikAvailability('3505888888888888', $program, $periode);
    }

    public function test_cannot_register_same_program_twice_in_same_periode()
    {
        $service = app(RegistrationService::class);
        $periode = Periode::aktif()->firstOrFail();
        $program = Program::firstOrFail();
        $jalur = Jalur::firstOrFail();

        $pendaftaran = Pendaftaran::create([
            'nomor_pendaftaran' => 'TEST-002',
            'periode_id' => $periode->id,
            'program_id' => $program->id,
            'jalur_id' => $jalur->id,
            'tahun' => $periode->tahun,
            'status' => 'menunggu_verifikasi',
        ]);

        PendaftaranIdentitas::create([
            'pendaftaran_id' => $pendaftaran->id,
            'nik' => '3505777777777777',
            'nama_lengkap' => 'Pendaftar Aktif',
            'tempat_lahir' => 'Blitar',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'alamat_ktp' => 'Alamat',
            'google_maps_url' => 'https://maps.google.com',
            'desa_id' => 1,
            'kecamatan_id' => 1,
            'no_hp' => '081',
            'asal_perguruan_tinggi' => 'UNS',
            'program_studi' => 'Informatika',
            'semester' => 1,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NIK 3505777777777777 sudah mendaftar pada program beasiswa ini');

        $service->validateNikAvailability('3505777777777777', $program, $periode);
    }

    public function test_can_register_different_program_if_previous_registration_was_rejected()
    {
        $service = app(RegistrationService::class);
        $periode = Periode::aktif()->firstOrFail();
        $programs = Program::take(2)->get();
        if ($programs->count() < 2) {
            $this->markTestSkipped('Need at least 2 programs');
        }

        $programA = $programs[0];
        $programB = $programs[1];
        $jalur = Jalur::where('program_id', $programA->id)->first() ?? Jalur::firstOrFail();

        // Pendaftaran Program A yang DITOLAK
        $pendaftaran = Pendaftaran::create([
            'nomor_pendaftaran' => 'TEST-003',
            'periode_id' => $periode->id,
            'program_id' => $programA->id,
            'jalur_id' => $jalur->id,
            'tahun' => $periode->tahun,
            'status' => 'tidak_lolos_verifikasi',
        ]);

        PendaftaranIdentitas::create([
            'pendaftaran_id' => $pendaftaran->id,
            'nik' => '3505666666666666',
            'nama_lengkap' => 'Pendaftar Gugur',
            'tempat_lahir' => 'Blitar',
            'tanggal_lahir' => '2000-01-01',
            'jenis_kelamin' => 'L',
            'alamat_ktp' => 'Alamat',
            'google_maps_url' => 'https://maps.google.com',
            'desa_id' => 1,
            'kecamatan_id' => 1,
            'no_hp' => '081',
            'asal_perguruan_tinggi' => 'UNS',
            'program_studi' => 'Informatika',
            'semester' => 1,
        ]);

        // Harus BISA daftar Program B karena pendaftaran Program A sudah gugur
        $service->validateNikAvailability('3505666666666666', $programB, $periode);
        $this->assertTrue(true, 'Dapat mendaftar program lain setelah gugur!');
    }
}
