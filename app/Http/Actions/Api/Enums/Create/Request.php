<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Create;

use App\Http\Actions\Api\ApiRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="EnumCreateRequest", required={"title"},
 *     @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *     @OA\Property(property="title", type="string", example="Страны"),
 *     @OA\Property(property="values", type="array",
 *         @OA\Items(type="object", required={"title"},
 *            @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *            @OA\Property(property="title", type="string", example="Канада"),
 *         )
 *     ),
 * )
 */
class Request extends ApiRequest
{
    public function rules(): array
    {
        return [
            'id' => 'sometimes|uuid|unique:enums,id',
            'title' => 'required',
            'values' => 'sometimes|array',
            'values.*.id' => 'sometimes|uuid|unique:enum_values,id',
            'values.*.title' => 'required|string|max:255',
        ];
    }

    public function struct(): array
    {
        return [
            'id', 'title', 'values' => [['id', 'title']]
        ];
    }
}
