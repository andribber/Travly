<?php

namespace App\Http\Requests\TravelOrder;

use App\Enums\Travel\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Status::validUpdateStatus())],
        ];
    }
}
