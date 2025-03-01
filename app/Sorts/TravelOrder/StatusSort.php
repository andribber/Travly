<?php

namespace App\Sorts\TravelOrder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;

class StatusSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query->addSelect([DB::raw("FIELD(status, 'Requested', 'Approved', 'Cancelled') AS status_order")])
            ->orderBy('status', $descending ? 'desc' : 'asc');
    }
}
