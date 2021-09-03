<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Users\Create;

use App\Http\Actions\Api\ApiRequest;
use App\Models\User;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="UserCreateRequest", required={"login", "email", "name", "password", "role"},
 *     @OA\Property(property="login", type="string", example="ninja24"),
 *     @OA\Property(property="email", type="string", example="some@domain.com"),
 *     @OA\Property(property="name", type="string", example="Иванов Иван"),
 *     @OA\Property(property="password", type="string", example="qwerty12345"),
 *     @OA\Property(property="role", type="string", enum={"user", "moderator", "admin"}),
 *     @OA\Property(property="photo", type="string", example="some.com/123.jpg"),
 * )
 */
class Request extends ApiRequest
{
    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'unique:users,login'],
            'email' => ['required', 'string', 'unique:users,email'],
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', Rule::in(User::ROLES)],
            'photo' => ['sometimes', 'string'],
        ];
    }

    public function struct(): array
    {
        return [
            'login', 'email', 'name', 'photo', 'password', 'role'
        ];
    }
}
