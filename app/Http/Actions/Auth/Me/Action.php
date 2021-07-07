<?php

declare(strict_types=1);

namespace App\Http\Actions\Auth\Me;

use App\Http\Resources\UserResource;

class Action
{
    public function __invoke()
    {
        return new UserResource(request()->user());
    }
}
