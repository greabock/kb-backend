<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Auth\Login;

use App\Http\Resources\Auth\ApiTokenResource;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

class Action
{
    public function __invoke(Request $request, Guard $guard): JsonResponse|ApiTokenResource
    {
        if ($guard->validate($request->only(['login', 'password']))) {

            $user = User::where('login', $request->get('login'))->firstOrFail();

            return new ApiTokenResource($user->createToken('web'));
        }

        $errors = new MessageBag();

        $errors->add('login', 'Неверное имя пользователя или пароль');

        return response()->json(compact('errors'), Response::HTTP_UNAUTHORIZED);
    }
}
