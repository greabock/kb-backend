<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (auth()->user()->super || in_array(auth('sanctum')->user()->role, $roles, true)) {

            return $next($request);
        }

        return response('Unauthorized', 403);
    }
}
