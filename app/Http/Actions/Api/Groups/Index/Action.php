<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Groups\Index;

use App\Http\Resources\User\GroupResource;
use App\Models\User\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(): AnonymousResourceCollection
    {
        return GroupResource::collection(Group::with('users')->get());
    }
}
