<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Show;

use App\Http\Resources\EnumResource;
use App\Models\Enum;

class Action
{
    public function __invoke($enum)
    {
        return new EnumResource(Enum::withTrashed()->with('values')->findOrFail($enum));
    }
}
