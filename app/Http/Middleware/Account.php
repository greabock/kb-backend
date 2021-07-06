<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Account
{
    public function handle(Request $request, Closure $next)
    {
        // TODO: check account available
        // TODO: select account database
        return $next($request);
    }
}
