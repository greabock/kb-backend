<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Update;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'values' => 'sometimes|array',
            'values.*.id' => 'sometimes|uuid',
            'values.*.title' => 'sometimes|string|max:255',
        ];
    }
}
