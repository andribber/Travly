<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;

class UserSeeder
{
    use WithFaker;

    public function run(): void
    {
        User::factory()->createMany([
            [
                'name' => $this->faker->name,
                'email' => 'a@travly.com',
            ],
            [
                'name' => $this->faker->name,
                'email' => 'b@travly.com',
            ],
        ]);
    }
}
