<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Search;

use App\Http\Actions\Api\ApiRequest;

class Request extends ApiRequest
{
    public function rules(): array
    {
        return  [
            'search' => 'nullable|string',
            'sort' => 'sometimes|array:field,direction',
            'sort.field' => 'in:created_at,name',
            'sort.direction' => 'in:asc,desc',
            'extensions' => 'sometimes|array',
            'extensions.*' => 'string',
        ];
    }
}
