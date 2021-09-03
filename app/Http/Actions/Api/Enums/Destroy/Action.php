<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Destroy;

use App\Models\Enum;
use Illuminate\Http\Response;

class Action
{
    public function __invoke(Enum $enum): Response
    {
        $enum->delete();

        return response()->noContent();
    }
}
