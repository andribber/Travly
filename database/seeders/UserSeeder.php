<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create();

        User::factory()->createMany([
            [
                'name' => $faker->name,
                'email' => 'a@travly.com',
            ],
            [
                'name' => $faker->name,
                'email' => 'b@travly.com',
            ],
        ]);
    }
}
