<?php

namespace App\Filters\TravelOrder;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class DateRangeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if (is_array($value) && isset($value['start_date']) && isset($value['end_date'])) {
            $startDate = Carbon::parse($value['start_date'])->startOfDay();
            $endDate = Carbon::parse($value['end_date'])->endOfDay();

            return $query->whereDate('departure_date', '>=', $startDate)
                ->whereDate('departure_date', '<=', $endDate);
        }
    }
}
