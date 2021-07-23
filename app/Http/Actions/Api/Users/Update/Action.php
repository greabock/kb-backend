<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Users\Update;

use App\Models\User;
use App\Http\Resources\UserResource;

class Action
{
    public function __invoke(Request $request, User $user): UserResource
    {
        $user->fill($request->getStruct());

        return new UserResource($user);
    }
}
