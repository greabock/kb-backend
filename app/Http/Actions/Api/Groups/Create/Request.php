<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Groups\Create;

use App\Http\Actions\Api\ApiRequest;

/**
 * @OA\Schema(schema="GroupCreateRequest", required={"title"},
 *     @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *     @OA\Property(property="name", type="string", example="Имя группы"),
 *     @OA\Property(property="users", type="array",
 *         @OA\Items(type="object", required={"id"},
 *            @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *         )
 *     ),
 * )
 */
class Request extends ApiRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'users' => ['sometimes'],
            'users.*.id' => ['required', 'exists:users']
        ];
    }

    public function struct(): ?array
    {
        return [
            'name',
            'users' => ['id']
        ];
    }
}
