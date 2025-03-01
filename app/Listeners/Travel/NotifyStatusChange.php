<?php

namespace App\Listeners\Travel;

use App\Events\Travel\Updated;
use App\Notifications\StatusChange;

class NotifyStatusChange
{
    public function handle(Updated $event): void
    {
        $travelOrder = $event->travelOrder;

        $travelOrder->user->notify(new StatusChange($travelOrder));
    }
}
