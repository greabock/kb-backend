<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\License\Index;

use JsonException;

class Action
{
    /**
     * @throws JsonException
     */
    public function __invoke()
    {
        return [
            'expires_at' => now()->addYear()->format('Y-m-d'),
            'key' => 'hello',
            'current_date' => now()->format('Y-m-d'),
        ];
    }
}
