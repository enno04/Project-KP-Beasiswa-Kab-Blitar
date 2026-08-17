<?php

namespace Tests\Feature;

use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Periode::create([
            'nama' => 'Periode 2026',
            'tahun' => 2026,
            'tanggal_mulai' => now()->subDay(),
            'tanggal_selesai' => now()->addMonth(),
            'status' => 'aktif',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
