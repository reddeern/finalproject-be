<?php

namespace Database\Factories;

use App\Models\Alat;
use App\Models\Penyewaan;
use App\Models\PenyewaanDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenyewaanDetailFactory extends Factory
{
    protected $model = PenyewaanDetail::class;

    public function definition(): array
    {
        return [
            'penyewaan_detail_penyewaan_id' => Penyewaan::factory(),
            'penyewaan_detail_alat_id' => Alat::factory(),
            'penyewaan_detail_jumlah' => $this->faker->numberBetween(1, 10),
            'penyewaan_detail_subharga' => $this->faker->numberBetween(50000, 1000000),
        ];
    }
}
