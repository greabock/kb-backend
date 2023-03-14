<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class Paid
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
