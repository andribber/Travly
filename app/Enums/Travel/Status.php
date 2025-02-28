<?php

namespace App\Enums\Travel;

use ArchTech\Enums\Values;

enum Status: string
{
    use Values;
    case REQUESTED = 'REQUESTED';
    case APPROVED = 'APPROVED';
    case CANCELLED = 'CANCELLED';

    public static function validUpdateStatus(): array
    {
        return [self::APPROVED->value, self::CANCELLED->value];
    }
}
