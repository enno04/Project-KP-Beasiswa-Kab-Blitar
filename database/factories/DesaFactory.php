<?php

namespace Database\Factories;

use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Database\Eloquent\Factories\Factory;

class DesaFactory extends Factory
{
    protected $model = Desa::class;

    public function definition(): array
    {
        return [
            'kecamatan_id' => Kecamatan::factory(),
            'kode_desa' => fake()->unique()->numerify('35.05.##.20##'),
            'nama_desa' => fake()->streetName(),
        ];
    }
}
