<?php

declare(strict_types=1);

namespace App\Http\Resources\Section;

use App\Models\Section\Field;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="SectionFieldResource",
 *    @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *    @OA\Property(property="title", type="string", example="Название"),
 *    @OA\Property(property="description", type="string", example="Это пример описания поля..."),
 *    @OA\Property(property="sort_index", type="integer"),
 *    @OA\Property(property="required", type="boolean"),
 *    @OA\Property(property="is_present_in_card", type="boolean"),
 *    @OA\Property(property="is_filterable", type="boolean"),
 *    @OA\Property(property="filter_sort_index", type="integer"),
 *    @OA\Property(property="type", oneOf={
 *        @OA\Schema(ref="#components/schemas/TypeString"),
 *        @OA\Schema(ref="#components/schemas/TypeInteger"),
 *        @OA\Schema(ref="#components/schemas/TypeText"),
 *        @OA\Schema(ref="#components/schemas/TypeWiki"),
 *        @OA\Schema(ref="#components/schemas/TypeFloat"),
 *        @OA\Schema(ref="#components/schemas/TypeBoolean"),
 *        @OA\Schema(ref="#components/schemas/TypeEnum"),
 *        @OA\Schema(ref="#components/schemas/TypeFile"),
 *        @OA\Schema(ref="#components/schemas/TypeList"),
 *    }),
 * )
 * @property Field $resource
 */
class FieldResource extends JsonResource
{
    public function __construct(Field $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'sort_index' => $this->resource->sort_index,
            'required' => $this->resource->required,
            'is_present_in_card' => $this->resource->is_present_in_card,
            'filter_sort_index' => $this->resource->filter_sort_index,
            'type' => $this->resource->type,
        ];
    }
}
