<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Create;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'sometimes|uuid|unique:enums,id',
            'title' => 'required',
            'values' => 'sometimes|array',
            'values.*.id' => 'sometimes|uuid|unique:enum_values,id',
            'values.*.title' => 'required|string|max:255',
        ];
    }
}
