<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelOrderResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'destination'=> $this->destination,
            'departure_date'=> $this->departure_date->toIso8601String(),
            'return_date'=> $this->return_date?->toIso8601String(),
            'status'=> $this->status,
            'orderer_name' => $this->user->name,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
