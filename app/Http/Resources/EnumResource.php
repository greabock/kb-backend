<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Enum\ValueResource;
use App\Models\Enum;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;


/**
 * @OA\Schema(schema="EnumResource",
 *    @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *    @OA\Property(property="title", type="string", example="Страны"),
 * )
 *
 * @OA\Schema(schema="EnumWithValuesResource",
 *    @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *    @OA\Property(property="title", type="string", example="Страны"),
 *    @OA\Property(property="values", type="array",
 *      @OA\Items(ref="#/components/schemas/EnumValueResource")
 *    )
 * )
 *
 * @property Enum $resource
 */
class EnumResource extends JsonResource
{
    public function __construct(Enum $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'values' => ValueResource::collection($this->whenLoaded('values')),
        ];
    }
}

