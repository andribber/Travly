<?php

namespace Database\Seeders;

use App\Enums\Travel\Status;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class TravelOrderSeeder extends Seeder
{
    use WithFaker;

    public function run(): void
    {
        User::each(function (User $user) {
            TravelOrder::factory()->for($user)->createMany([
                ['status' => Status::CANCELLED],
                ['status' => Status::APPROVED],
                ['status' => Status::REQUESTED],
            ]);
        });
    }
}
