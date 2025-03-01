<?php

namespace App\Http\Controllers;

use App\Enums\Travel\Status;
use App\Http\Queries\TravelOrder\TravelOrderQuery;
use App\Http\Requests\TravelOrder\StoreRequest;
use App\Http\Requests\TravelOrder\UpdateRequest;
use App\Http\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller;

class TravelController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly Factory $auth)
    {
    }

    public function index(Request $request, TravelOrderQuery $query): JsonResource
    {
        $this->authorize('viewAny', TravelOrder::class);

        return TravelOrderResource::collection(
            $query->with('user')
                ->simplePaginate($request->get('limit', 10))
                ->appends($request->query()),
        );
    }

    public function show(Request $request, TravelOrderQuery $query, TravelOrder $travelOrder): JsonResource
    {
        $this->authorize('view', [TravelOrder::class, $travelOrder]);

        return new TravelOrderResource($query->where('id', $travelOrder->id)->first());

    }

    public function store(StoreRequest $request): JsonResource
    {
        $this->authorize('create', TravelOrder::class);

        return new TravelOrderResource($this->auth->user()->travelOrders()->create($request->validated()));
    }

    public function update(UpdateRequest $request, TravelOrder $travelOrder): JsonResource|JsonResponse
    {
        $this->authorize('update', [TravelOrder::class, $travelOrder]);

        $status = $request->get('status');

        if($status === Status::CANCELLED->value && !$travelOrder->canCancel()) {
            return response()->json('O pedido de viagem não pode ser cancelado.', 403);
        }

        $travelOrder->update($request->validated());

        return new TravelOrderResource($travelOrder);
    }
}
