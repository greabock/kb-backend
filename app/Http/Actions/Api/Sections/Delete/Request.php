<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Delete;

use App\Http\Actions\Api\ApiRequest;

class Request extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->section->hasAccess($this->user);
    }
}
