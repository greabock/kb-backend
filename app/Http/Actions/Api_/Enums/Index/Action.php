<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Index;

use App\Http\Resources\EnumResource;
use App\Models\Enum;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(): AnonymousResourceCollection
    {
        return EnumResource::collection(Enum::all());
    }
}
