<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Material;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="PageOfMaterialResources",
 *    @OA\Property(property="data", type="array",
 *      @OA\Items(ref="#/components/schemas/MaterialDetailedResource"),
 *    ),
 *    @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
 * )
 *
 * @OA\Schema(schema="MaterialResource",
 *    @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *    @OA\Property(property="name", type="string", example="Пример названия материала"),
 * )
 *
 * @OA\Schema(schema="MaterialDetailedResource",
 *    @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *    @OA\Property(property="name", type="string", example="Канада"),
 *    @OA\AdditionalProperties(type="object", anyOf={
 *        @OA\Schema(ref="#/components/schemas/EnumValueResource"),
 *        @OA\Schema(ref="#/components/schemas/MaterialResource"),
 *        @OA\Schema(oneOf={@OA\Schema(type="string"), @OA\Schema(type="number"), @OA\Schema(type="boolean")}),
 *        @OA\Schema(type="array",
 *            @OA\Items(oneOf={
 *              @OA\Schema(ref="#/components/schemas/EnumValueResource"),
 *              @OA\Schema(ref="#/components/schemas/MaterialResource"),
 *            }),
 *        ),
 *    }),
 * )
 *
 * @property Material $resource
 */
class MaterialResource extends JsonResource
{
}
