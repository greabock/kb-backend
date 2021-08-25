<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Users\Index;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::whereNotIn('role', [User::ROLE_ADMIN])->get()
        );
    }
}
