<?php

declare(strict_types=1);

namespace App\Http\Actions\Auth\Azure\Redirect;

use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class Action
{
    public function __invoke(): JsonResponse
    {
        $url = Socialite::driver('azure')->stateless()->redirect()->getTargetUrl();

        return response()->json(compact('url'));
    }
}
