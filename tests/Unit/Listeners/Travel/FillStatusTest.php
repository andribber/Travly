<?php

namespace Tests\Unit\Listeners\Travel;

use App\Enums\Travel\Status;
use App\Events\Travel\Creating;
use App\Listeners\Travel\FillStatus;
use App\Models\TravelOrder;
use Tests\TestCase;

class FillStatusTest extends TestCase
{
    public function test_if_fill_status(): void
    {
        $travelOrder = $this->partialMock(TravelOrder::class);
        $travelOrder->status = null;

        $creating = new Creating($travelOrder);

        new FillStatus()->handle($creating);

        $this->assertEquals(Status::REQUESTED, $travelOrder->status);
    }
}
