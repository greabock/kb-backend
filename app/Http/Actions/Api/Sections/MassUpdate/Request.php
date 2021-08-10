<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\MassUpdate;

use App\Http\Actions\Api\ApiRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="SectionMassUpdateRequest", type="array",
 *     @OA\Items(type="object", required={"id", "role"},
 *       @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *       @OA\Property(property="sort_index", type="integer"),
 *       @OA\Property(property="is_dictionary", type="boolean"),
 *       @OA\Property(property="is_navigation", type="boolean"),
 *    )
 * )
 */
class Request extends ApiRequest
{
    public function rules(): array
    {
        return [
            '*.id' => ['required', 'uuid', 'exists:sections,id'],
            '*.sort_index' => ['sometimes', 'distinct'],
            '*.is_dictionary' => ['sometimes', 'boolean'],
            '*.is_navigation' => ['sometimes', 'boolean'],
        ];
    }

    public function struct(): array
    {
        return [['id', 'sort_index', 'is_dictionary', 'is_navigation']];
    }
}
