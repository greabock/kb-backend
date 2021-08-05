<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Users\MassUpdate;

use App\Http\Actions\Api\ApiRequest;
use App\Models\User;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;


/**
 * @OA\Schema(schema="UserMassUpdateRequest", type="array",
 *     @OA\Items(type="object", required={"id", "role"},
 *       @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *       @OA\Property(property="role", type="string", enum={"user", "moderator", "admin"}),
 *    )
 * )
 */
class Request extends ApiRequest
{
    public function rules(): array
    {
        return [
            '*.id' => ['required', 'integer', 'exists:users,id'],
            '*.role' => ['required', Rule::in(User::ROLES)],
        ];
    }

    public function struct(): array
    {
        return [['id', 'role']];
    }
}
