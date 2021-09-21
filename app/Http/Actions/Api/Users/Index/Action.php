<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Users\Index;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::where('super', false)
                ->where('id', '!=', $request->user()->id)
                ->get()
        );
    }
}
