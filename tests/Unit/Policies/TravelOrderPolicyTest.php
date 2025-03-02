<?php

namespace Tests\Unit\Policies;

use App\Models\TravelOrder;
use App\Models\User;
use App\Policies\TravelOrderPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TravelOrderPolicyTest extends TestCase
{
    #[DataProvider('policyDataProvider')]
    public function test_policy(string $method, string $user, string $travelOrder, bool $expected): void
    {
        $policy = new TravelOrderPolicy();

        $userInstance = User::factory()->create();
        $otherUserInstance = User::factory()->create();
        $userTravelOrder = TravelOrder::factory()->for($userInstance)->create();
        $otherUserTravelOrder = TravelOrder::factory()->for($otherUserInstance)->create();

        $users = [
            'user' => $userInstance,
            'otherUser' => $otherUserInstance,
        ];

        $travelOrders = [
            'userTravelOrder' => $userTravelOrder,
            'otherUserTravelOrder' => $otherUserTravelOrder,
        ];

        $this->assertEquals($policy->$method($users[$user], $travelOrders[$travelOrder]), $expected);
    }

    public static function policyDataProvider(): array
    {
        return [
            ['view', 'user', 'userTravelOrder', true],
            ['view', 'user', 'otherUserTravelOrder', false],
            ['update', 'user', 'userTravelOrder', false],
            ['update', 'user', 'otherUserTravelOrder', true],
        ];
    }
}
