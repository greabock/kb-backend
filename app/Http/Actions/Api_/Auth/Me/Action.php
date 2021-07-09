<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Auth\Me;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class Action
{
    public function __invoke(Request $request): UserResource
    {
        return (new UserResource($request->user()));
    }
}
