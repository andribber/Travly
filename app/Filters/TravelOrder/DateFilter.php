<?php

namespace App\Filters\TravelOrder;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class DateFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if (isset($value)) {
            $date = Carbon::parse($value)->startOfDay();

            return $query->whereDate($property, '>=', $date);
        }
    }
}
