<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use App\Models\Penyewaan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenyewaanFactory extends Factory
{
    protected $model = Penyewaan::class;

    public function definition(): array
    {
        $tglsewa = $this->faker->dateTimeBetween('now', '+30 days');
        $tglkembali = $this->faker->dateTimeBetween($tglsewa, '+60 days');

        return [
            'penyewaan_pelanggan_id' => Pelanggan::factory(),
            'penyewaan_tglsewa' => $tglsewa,
            'penyewaan_tglkembali' => $tglkembali,
            'penyewaan_sttspembayaran' => $this->faker->randomElement(['Lunas', 'Belum Dibayar', 'DP']),
            'penyewaan_sttskembali' => $this->faker->randomElement(['Sudah Kembali', 'Belum Kembali']),
            'penyewaan_totalharga' => $this->faker->numberBetween(100000, 10000000),
        ];
    }
}
