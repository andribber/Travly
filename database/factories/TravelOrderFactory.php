<?php

namespace Database\Factories;

use App\Enums\Travel\Status;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TravelOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'destiny' => $this->faker->locale(),
            'departure_date' => now()->addDays(5),
            'return_date' => now()->addDays(10),
            'status' => Status::REQUESTED,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Status::CANCELLED,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Status::CANCELLED,
        ]);
    }
}
