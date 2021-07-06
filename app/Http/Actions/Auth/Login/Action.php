<?php

declare(strict_types=1);

namespace App\Http\Actions\Auth\Login;

use Illuminate\Contracts\Auth\Guard;

class Action
{
    public function __invoke(Request $request, Guard $guard)
    {
        if ($guard->attempt($request->only(['login', 'password']), true)) {
            return response()->noContent();
        }

        return response()->json([
            'errors' => [
                'login' => [
                    'Неверное имя пользователя или пароль',
                ],
            ],
        ], 401);
    }
}
