<?php

namespace Database\Seeders;

use App\Models\Summary;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SummarySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            Summary::create([
                'phone' => '07' . $faker->numerify('#########'), // Creates a number like '07123456789'
                'email' => $faker->email,
                'notes' => $faker->paragraph(),
            ]);
        }
    }
}