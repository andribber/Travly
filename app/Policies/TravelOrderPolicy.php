<?php

namespace App\Policies;

use App\Models\TravelOrder;
use App\Models\User;

class TravelOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TravelOrder $travel): bool
    {
        return $travel->user()->is($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TravelOrder $travel): bool
    {
        return !$travel->user()->is($user);
    }
}
