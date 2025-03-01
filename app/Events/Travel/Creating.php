<?php

namespace App\Events\Travel;

use App\Models\TravelOrder;

class Creating
{
    public function __construct(public TravelOrder $travelOrder)
    {
    }
}
