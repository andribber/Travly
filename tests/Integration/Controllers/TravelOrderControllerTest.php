<?php

namespace Tests\Integration\Controllers;

use App\Enums\Travel\Status;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\StatusChange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TravelOrderControllerTest extends TestCase
{
    private readonly User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(now());

        $this->user = User::factory()->create();
    }

    public function test_it_will_list_all_travel_orders(): void
    {
        TravelOrder::factory()->count(10)->for($this->user)->createQuietly();

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index'))
            ->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function test_it_will_show_a_travel_order(): void
    {
        $travelOrder = TravelOrder::factory()->for($this->user)->createQuietly();

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.show', $travelOrder))
            ->assertOk()
            ->assertJsonFragment(['id' => $travelOrder->id]);
    }

    public function test_it_will_create_a_travel_order(): void
    {
        $this->setAuthenticatedUser($this->user)
            ->postJson(route('v1.travel-orders.store', [
                'departure_date' => now()->addDays(15)->format('Y-m-d H:i:s'),
                'return_date' => now()->addDays(25)->format('Y-m-d H:i:s'),
                'destination' => 'RUSSLAND',
            ]))
            ->assertCreated();

        $this->assertDatabaseHas('travel_orders', [
            'destination' => 'RUSSLAND',
            'status' => Status::REQUESTED->value,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_it_will_sort_by_status(): void
    {
        TravelOrder::factory()->for($this->user)->createManyQuietly([
            ['status' => Status::APPROVED->value],
            ['status' => Status::CANCELLED->value],
            ['status' => Status::REQUESTED->value],
            ['status' => Status::REQUESTED->value],
        ]);

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index', ['sort' => 'status']))
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJson([
                'data' => [
                    ['status' => Status::REQUESTED->value],
                    ['status' => Status::REQUESTED->value],
                    ['status' => Status::APPROVED->value],
                    ['status' => Status::CANCELLED->value],
                ],
            ]);
    }

    public function test_it_will_sort_by_departure_date(): void
    {
        TravelOrder::factory()->for($this->user)->createManyQuietly([
            ['id' => 1, 'departure_date' => now()->addDays(10)],
            ['id' => 2, 'departure_date' => now()->addDays(11)],
            ['id' => 3, 'departure_date' => now()->addDays(12)],
            ['id' => 4, 'departure_date' => now()->addDays(13)],
        ]);

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index', ['sort' => 'departure_date']))
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJson([
                'data' => [
                    ['id' => 1],
                    ['id' => 2],
                    ['id' => 3],
                    ['id' => 4],
                ],
            ]);
    }

    public function test_it_will_sort_by_departure_date_desc(): void
    {
        TravelOrder::factory()->for($this->user)->createManyQuietly([
            ['id' => 2, 'departure_date' => now()->addDays(11)],
            ['id' => 3, 'departure_date' => now()->addDays(12)],
            ['id' => 4, 'departure_date' => now()->addDays(13)],
            ['id' => 1, 'departure_date' => now()->addDays(10)],
        ]);

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index', ['sort' => '-departure_date']))
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJson([
                'data' => [
                    ['id' => 4],
                    ['id' => 3],
                    ['id' => 2],
                    ['id' => 1],
                ],
            ]);
    }

    public function test_it_will_filter_by_departure_date(): void
    {
        TravelOrder::factory()->for($this->user)->createManyQuietly([
            ['id' => 2, 'departure_date' => now()->addDays(11)],
            ['id' => 3, 'departure_date' => now()->addDays(12)],
            ['id' => 4, 'departure_date' => now()->addDays(13)],
            ['id' => 1, 'departure_date' => now()->addDays(10)],
        ]);

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index', [
                'filter[departure_date]' => now()->addDays(11)->toDateString(),
            ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['id' => 2],
                ],
            ]);
    }

    public function test_it_will_filter_by_return_date(): void
    {
        TravelOrder::factory()->for($this->user)->createManyQuietly([
            ['id' => 2, 'return_date' => now()->addDays(11)],
            ['id' => 3, 'return_date' => now()->addDays(12)],
            ['id' => 4, 'return_date' => now()->addDays(13)],
            ['id' => 1, 'return_date' => now()->addDays(10)],
        ]);

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index', [
                'filter[return_date]' => now()->addDays(13)->toDateString(),
            ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['id' => 4],
                ],
            ]);
    }

    public function test_it_will_filter_by_departure_date_period(): void
    {
        TravelOrder::factory()->for($this->user)->createManyQuietly([
            ['id' => 2, 'departure_date' => now()->addDays(11)],
            ['id' => 3, 'departure_date' => now()->addDays(12)],
            ['id' => 4, 'departure_date' => now()->addDays(13)],
            ['id' => 1, 'departure_date' => now()->addDays(10)],
        ]);

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index', [
                'filter[period]' => [
                    'departure_date' => [
                        'start_date' => now()->addDays(10)->toDateString(),
                        'end_date' => now()->addDays(12)->toDateString(),
                    ],
                ],
            ]))
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJson([
                'data' => [
                    ['id' => 3],
                    ['id' => 2],
                    ['id' => 1],
                ],
            ]);
    }

    public function test_it_will_filter_by_return_date_period(): void
    {
        TravelOrder::factory()->for($this->user)->createManyQuietly([
            ['id' => 2, 'return_date' => now()->addDays(11)],
            ['id' => 3, 'return_date' => now()->addDays(12)],
            ['id' => 4, 'return_date' => now()->addDays(13)],
            ['id' => 1, 'return_date' => now()->addDays(10)],
        ]);

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index', [
                'filter[period]' => [
                    'return_date' => [
                        'start_date' => now()->addDays(10)->toDateString(),
                        'end_date' => now()->addDays(11)->toDateString(),
                    ],
                ],
            ]))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'data' => [
                    ['id' => 2],
                    ['id' => 1],
                ],
            ]);
    }

    public function test_it_will_filter_by_status(): void
    {
        TravelOrder::factory()->for($this->user)->createManyQuietly([
            ['id' => 1, 'status' => Status::APPROVED->value],
            ['id' => 2, 'status' => Status::CANCELLED->value],
            ['id' => 3, 'status' => Status::REQUESTED->value],
            ['id' => 4, 'status' => Status::REQUESTED->value],
        ]);

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index', [
                'filter[status]' => Status::CANCELLED->value,
            ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['id' => 2, 'status' => Status::CANCELLED->value],
                ],
            ]);
    }

    public function test_it_will_filter_by_destination(): void
    {
        TravelOrder::factory()->for($this->user)->createManyQuietly([
            ['id' => 3, 'destination' => 'portugal'],
            ['id' => 4, 'destination' => 'sweden'],
            ['id' => 1, 'destination' => 'germany'],
            ['id' => 5, 'destination' => 'france'],
            ['id' => 6, 'destination' => 'poland'],
            ['id' => 2, 'destination' => 'germany'],
            ['id' => 7, 'destination' => 'romania'],
        ]);

        $this->setAuthenticatedUser($this->user)
            ->getJson(route('v1.travel-orders.index', [
                'filter[destination]' => 'germany',
            ]))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'data' => [
                    ['id' => 2, 'destination' => 'germany'],
                    ['id' => 1, 'destination' => 'germany'],
                ],
            ]);
    }

    public function test_if_owner_user_cannot_update_travel_order_status(): void
    {
        $travelOrder = TravelOrder::factory()->for($this->user)->createQuietly();

        $this->setAuthenticatedUser($this->user)
            ->putJson(route('v1.travel-orders.update', $travelOrder), ['status' => Status::CANCELLED->value])
            ->assertUnauthorized();
    }

    #[DataProvider('statusProvider')]
    public function test_if_other_user_can_update_travel_order_status(string $status): void
    {
        Notification::fake();

        $travelOrder = TravelOrder::factory()->for($this->user)->createQuietly();
        $anotherUser = User::factory()->createQuietly();

        $this->setAuthenticatedUser($anotherUser)
            ->putJson(route('v1.travel-orders.update', $travelOrder), ['status' => $status])
            ->assertOk();

        $this->assertDatabaseHas('travel_orders', [
            'id' => $travelOrder->id,
            'status' => $status,
        ]);

        Notification::assertSentTo($this->user, StatusChange::class);
    }

    public function test_if_cannot_cancel_travel_order_if_created_date_is_older_than_7_days(): void
    {
        $travelOrder = TravelOrder::factory()->for($this->user)->createQuietly([
            'created_at' => now()->subDays(8),
        ]);
        $anotherUser = User::factory()->createQuietly();

        $this->setAuthenticatedUser($anotherUser)
            ->putJson(route('v1.travel-orders.update', $travelOrder), ['status' => Status::CANCELLED->value])
            ->assertForbidden()
            ->assertJsonFragment(['O pedido de viagem não pode ser cancelado.']);
    }

    public function test_if_cannot_cancel_travel_order_if_departure_date_is_less_than_3_days(): void
    {
        $travelOrder = TravelOrder::factory()->for($this->user)->createQuietly([
            'status' => Status::APPROVED->value,
            'departure_date' => now()->addDays(2),
        ]);
        $anotherUser = User::factory()->createQuietly();

        $this->setAuthenticatedUser($anotherUser)
            ->putJson(route('v1.travel-orders.update', $travelOrder), ['status' => Status::CANCELLED->value])
            ->assertForbidden();
    }

    public function test_if_can_cancel_travel_order_if_departure_date_is_older_than_3_days(): void
    {
        Notification::fake();

        $travelOrder = TravelOrder::factory()->for($this->user)->createQuietly([
            'status' => Status::APPROVED->value,
            'departure_date' => now()->addDays(5),
        ]);
        $anotherUser = User::factory()->createQuietly();

        $this->setAuthenticatedUser($anotherUser)
            ->putJson(route('v1.travel-orders.update', $travelOrder), ['status' => Status::CANCELLED->value])
            ->assertOk();

        $this->assertDatabaseHas('travel_orders', [
            'id' => $travelOrder->id,
            'status' => Status::CANCELLED->value,
        ]);

        Notification::assertSentTo($this->user, StatusChange::class);
    }

    public function test_if_user_cannot_view_travel_orders_from_other_user(): void
    {
        TravelOrder::factory()->count(10)->for($this->user)->createQuietly();
        $otherUser = User::factory()->createQuietly();

        $this->setAuthenticatedUser($otherUser)
            ->getJson(route('v1.travel-orders.index'))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_if_user_cannot_show_travel_orders_from_other_user(): void
    {
        $travelOrder = TravelOrder::factory()->for($this->user)->createQuietly();
        $otherUser = User::factory()->createQuietly();

        $this->setAuthenticatedUser($otherUser)
            ->getJson(route('v1.travel-orders.show', $travelOrder))
            ->assertUnauthorized();
    }

    public static function statusProvider(): array
    {
        return [
            [Status::CANCELLED->value],
            [Status::APPROVED->value],
        ];
    }
}
