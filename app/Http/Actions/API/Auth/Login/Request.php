<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Auth\Login;

use Illuminate\Foundation\Http\FormRequest;


/**
 * @OA\Schema(schema="LoginRequest",
 *    @OA\Property(property="login", type="string", example="user@some.com"),
 *    @OA\Property(property="password", type="string", example="123123"),
 * )
 */
class Request extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => 'required',
            'password' => 'required',
        ];
    }
}
