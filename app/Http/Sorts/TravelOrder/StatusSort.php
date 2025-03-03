<?php

namespace App\Http\Sorts\TravelOrder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;

class StatusSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query->addSelect('*')
            ->addSelect([DB::raw("FIELD(status, 'requested', 'approved', 'cancelled') AS status_order")])
            ->orderBy('status_order', $descending ? 'desc' : 'asc');
    }
}
