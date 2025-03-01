<?php

namespace App\Listeners\Travel;

use App\Enums\Travel\Status;
use App\Events\Travel\Creating;

class FillStatus
{
    public function handle(Creating $event): void
    {
        $event->travelOrder->status = Status::REQUESTED;
    }
}
