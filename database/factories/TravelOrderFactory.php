<?php

namespace Database\Factories;

use App\Enums\Travel\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'destination' => $this->faker->locale(),
            'departure_date' => now()->addDays(5),
            'return_date' => now()->addDays(10),
            'status' => Status::REQUESTED,
        ];
    }
}
