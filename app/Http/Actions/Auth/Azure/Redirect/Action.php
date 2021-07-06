<?php

declare(strict_types=1);

namespace App\Http\Actions\Auth\Azure\Redirect;

use Laravel\Socialite\Facades\Socialite;

class Action
{
    public function __invoke(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('azure')->stateless()->redirect()->getTargetUrl()
        ]);
    }
}
