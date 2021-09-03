<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Files\Update;

use App\Http\Actions\Api\ApiRequest;

class Request extends ApiRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function struct(): ?array
    {
        return ['name'];
    }
}
