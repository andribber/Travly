<?php

namespace App\Http\Requests\TravelOrder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'departure_date' => [
                'required',
                'date_format:Y-m-d H:i:s',
                'after:now',
                Rule::when($this->filled('return_date'), 'before:return_date'),
            ],
            'return_date' => ['nullable', 'date_format:Y-m-d H:i:s', 'after:departure_date'],
            'destination' => ['required', 'string', 'max:64'],
        ];
    }
}
