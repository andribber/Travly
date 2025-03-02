<?php

namespace Tests\Unit\Listeners\Travel;

use App\Events\Travel\Updated;
use App\Listeners\Travel\NotifyStatusChange;
use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\StatusChange;
use Tests\TestCase;

class NotifyStatusChangeTest extends TestCase
{
    public function test_it_will_notify_user(): void
    {
        $travelOrder = $this->partialMock(TravelOrder::class);
        $user = $this->partialMock(User::class);
        $travelOrder->user = $user;

        $event = $this->partialMock(Updated::class);
        $event->travelOrder = $travelOrder;

        $user->shouldReceive('notify')
            ->once()
            ->with($this->isInstanceOf(StatusChange::class));

        $updated = new Updated($travelOrder);

        new NotifyStatusChange()->handle($updated);
    }
}
