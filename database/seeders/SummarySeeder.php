<?php

namespace Database\Seeders;

use App\Models\Summary;
use Illuminate\Database\Seeder;

class SummarySeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < 50; $i++) {
            Summary::create([
                'phone' => fake()->phoneNumber(),
                'email' => fake()->email(),
                'notes' => fake()->paragraph(),
            ]);
        }
    }
}