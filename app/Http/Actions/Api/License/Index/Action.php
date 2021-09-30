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
        $key = @file_get_contents(storage_path('license.key'));

        if (!$key) {
            return [
                'expires_at' => null,
                'current_date' => now()->format('Y-m-d'),
                'key' => null,
            ];
        }

        [$vi, $data] = explode('==', $key);

        $data = json_decode(
            openssl_decrypt(
                base64_decode($data),
                'AES-192-CBC',
                env('DOXCASE_CLIENT', ''),
                0,
                base64_decode($vi),
            ), true, 512, JSON_THROW_ON_ERROR);

        $data['current_date'] = now()->format('Y-m-d');

        return $data;
    }
}
