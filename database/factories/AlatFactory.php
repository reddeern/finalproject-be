<?php

namespace Database\Factories;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlatFactory extends Factory
{
    protected $model = Alat::class;

    public function definition(): array
    {
        return [
            'alat_kategori_id' => Kategori::factory(),
            'alat_nama' => $this->faker->word(),
            'alat_deskripsi' => $this->faker->sentence(),
            'alat_hargaperhari' => $this->faker->numberBetween(50000, 500000),
            'alat_stok' => $this->faker->numberBetween(1, 50),
        ];
    }
}
