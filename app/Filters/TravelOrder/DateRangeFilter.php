<?php

namespace App\Filters\TravelOrder;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class DateRangeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $property = array_key_first($value);

        if (is_array($value) && isset($value[$property]['start_date']) && isset($value[$property]['end_date'])) {
            $startDate = Carbon::parse($value[$property]['start_date'])->startOfDay();
            $endDate = Carbon::parse($value[$property]['end_date'])->endOfDay();


            return $query->whereDate($property, '>=', $startDate)->whereDate($property, '<=', $endDate);
        }

        return $query;
    }
}
