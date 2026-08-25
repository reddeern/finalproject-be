<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use App\Models\PelangganData;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PelangganDataFactory extends Factory
{
    protected $model = PelangganData::class;

    public function definition(): array
    {
        // Use fake file path for tests, or create if needed
        $filename = 'pelanggan_data/' . $this->faker->uuid() . '.jpg';
        
        // Try to create fake file if Storage is using fake driver
        try {
            if (!Storage::disk('public')->exists('pelanggan_data')) {
                Storage::disk('public')->makeDirectory('pelanggan_data');
            }
            $file = UploadedFile::fake()->image('document.jpg', 200, 200);
            Storage::disk('public')->putFileAs('pelanggan_data', $file, basename($filename));
        } catch (\Exception $e) {
            // If not in test environment, just use fake path
        }

        return [
            'pelanggan_data_pelanggan_id' => Pelanggan::factory(),
            'pelanggan_data_jenis' => $this->faker->randomElement(['KTP', 'SIM']),
            'pelanggan_data_file' => $filename,
        ];
    }
}
