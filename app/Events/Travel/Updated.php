<?php

namespace App\Events\Travel;

use App\Models\Travel;

class Updated
{
    public function __construct(public Travel $travel)
    {
    }
}
