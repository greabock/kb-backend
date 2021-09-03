<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Auth\Azure\Redirect;


use App\Http\Resources\Auth\RedirectResource;
use Laravel\Socialite\Facades\Socialite;

class Action
{
    public function __invoke(): RedirectResource
    {
        return new RedirectResource(Socialite::driver('azure')->stateless()->redirect());
    }
}
