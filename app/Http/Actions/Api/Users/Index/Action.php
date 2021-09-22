<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Users\Index;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::where('super', false)
                ->orWhere(function (Builder $query) use ($request) {
                    $query->where('super', true)->where('id', $request->user()->id);
                })->get()
        );
    }
}
