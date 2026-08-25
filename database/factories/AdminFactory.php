<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'admin_username' => $this->faker->unique()->userName(),
            'admin_password' => bcrypt('password123'),
        ];
    }
}
