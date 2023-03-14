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
        return response('', 200);
    }
}
