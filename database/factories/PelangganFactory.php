<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PelangganFactory extends Factory
{
    protected $model = Pelanggan::class;

    public function definition(): array
    {
        return [
            'pelanggan_nama' => $this->faker->name(),
            'pelanggan_alamat' => $this->faker->address(),
            'pelanggan_notelp' => $this->faker->numerify('08##########'),
            'pelanggan_email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
