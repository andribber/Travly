<?php

namespace App\Events\Travel;

use App\Models\TravelOrder;

class Updated
{
    public function __construct(public TravelOrder $travelOrder)
    {
    }
}
