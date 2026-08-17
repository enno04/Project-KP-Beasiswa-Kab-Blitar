<?php

namespace Database\Factories;

use App\Models\Kecamatan;
use Illuminate\Database\Eloquent\Factories\Factory;

class KecamatanFactory extends Factory
{
    protected $model = Kecamatan::class;

    public function definition(): array
    {
        return [
            'kode_kecamatan' => fake()->unique()->numerify('35.05.##'),
            'nama_kecamatan' => fake()->city(),
        ];
    }
}
