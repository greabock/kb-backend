<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\MassUpdate;

use App\Http\Actions\Api\ApiRequest;

class Request extends ApiRequest
{
    public function rules(): array
    {
        return [
            '*.id' => ['required', 'uuid', 'exists:sections,id'],
            '*.sort_index' => ['required', 'distinct'],
        ];
    }

    public function struct(): array
    {
        return [['id', 'sort_index']];
    }
}
