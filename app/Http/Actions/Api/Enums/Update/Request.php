<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Enums\Update;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="EnumUpdateRequest",
 *     @OA\Property(property="title", type="string", example="Страны"),
 *     @OA\Property(property="values", type="array",
 *         @OA\Items(type="object", required={"title"},
 *            @OA\Property(property="id", type="string", example="Канада"),
 *            @OA\Property(property="title", type="string", example="Канада"),
 *         )
 *     ),
 * )
 */
class Request extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'values' => 'sometimes|array',
            'values.*.id' => 'sometimes|uuid',
            'values.*.title' => 'sometimes|string|max:255',
        ];
    }
}
