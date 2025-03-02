<?php

namespace App\Enums\Travel;

use ArchTech\Enums\Values;

enum Status: string
{
    use Values;
    case APPROVED = 'approved';
    case CANCELLED = 'cancelled';
    case REQUESTED = 'requested';

    public static function validUpdateStatus(): array
    {
        return [self::APPROVED->value, self::CANCELLED->value];
    }
}
