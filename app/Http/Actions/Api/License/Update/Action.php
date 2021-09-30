<?php

namespace App\Http\Actions\Api\License\Update;


use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use JsonException;

class Action
{
    /**
     * @throws ValidationException
     * @throws JsonException
     */
    public function __invoke(Request $request)
    {
        [$vi, $data] = explode('==', $key = $request->get('key'));
        $client = env('DOXCASE_CLIENT');

        $data = json_decode(
            openssl_decrypt(
                base64_decode($data),
                'AES-192-CBC',
                $client,
                0,
                base64_decode($vi),
            ), true, 512, JSON_THROW_ON_ERROR);

        if ($data['key'] !== $client) {
            throw ValidationException::withMessages(['key' => ['Invalid key']]);
        }

        file_put_contents(storage_path('license.key'), $key);

        return response('', 200);
    }
}
