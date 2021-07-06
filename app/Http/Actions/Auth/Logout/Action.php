<?php

declare(strict_types=1);

namespace App\Http\Actions\Auth\Logout;

use Illuminate\Support\Facades\Auth;

class Action
{
    public function __invoke()
    {
        Auth::guard('web')->logout();
    }
}
