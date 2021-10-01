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
        $key = @file_get_contents(storage_path('license.key'));

        [$vi, $data] = explode('==', $key);

        $data = json_decode(
            openssl_decrypt(
                base64_decode($data),
                'AES-192-CBC',
                env('DOXCASE_CLIENT', ''),
                0,
                base64_decode($vi),
            ), true, 512, JSON_THROW_ON_ERROR);

        $date = Carbon::createFromFormat($data['expires_at'], 'Y-m-d')->endOfDay();

        if ($date->isPast()) {
            return response('Payment required', 402);
        }

        return $next($request);
    }
}
