<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Show;

use App\Http\Resources\EnumResource;
use App\Models\Enum;

class Action
{
    public function __invoke(Enum $enum)
    {
        $enum->load('values');

        return new EnumResource($enum);
    }
}
