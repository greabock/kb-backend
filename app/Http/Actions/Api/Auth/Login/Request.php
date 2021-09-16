<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Auth\Login;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="LoginRequest",
 *    @OA\Property(property="email", type="string", example="user@some.com"),
 *    @OA\Property(property="password", type="string", example="123123"),
 * )
 */
class Request extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required',
            'password' => 'required',
        ];
    }
}
