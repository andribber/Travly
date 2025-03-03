<?php

namespace App\Http\Queries\TravelOrder;

use App\Http\Filters\TravelOrder\DateFilter;
use App\Http\Filters\TravelOrder\DateRangeFilter;
use App\Http\Sorts\TravelOrder\StatusSort;
use App\Models\TravelOrder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TravelOrderQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(TravelOrder::query()->whereBelongsTo(auth()->user()));

        $this->allowedIncludes(['user']);

        $this->allowedSorts([
            'departure_date',
            AllowedSort::custom('status', new StatusSort()),
        ]);

        $this->allowedFilters([
            AllowedFilter::custom('departure_date', new DateFilter()),
            AllowedFilter::custom('return_date', new DateFilter()),
            AllowedFilter::exact('destination'),
            AllowedFilter::exact('status'),
            AllowedFilter::custom('period', new DateRangeFilter()),
        ]);

        $this->defaultSort('-id');
    }
}
