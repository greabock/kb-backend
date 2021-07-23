<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Users\Create;

use App\Http\Resources\UserResource;
use App\Models\User;
use Greabock\Populator\Populator;

class Action
{
    public function __invoke(Request $request, Populator $populator): UserResource
    {
        /** @var User $user */
        $user = $populator->populate(User::class, $request->getStruct());
        $populator->flush();

        return new UserResource($user);
    }
}
