<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Users\MassUpdate;


use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        foreach ($request->getStruct() as ['id' => $id, 'role' => $role]) {
            $users[] = tap(
                User::findOrFail($id),
                fn(User $user) => $user->setAttribute('role', $role)->save()
            );
        }

        return UserResource::collection($users ?? []);
    }
}
