<?php

declare(strict_types=1);

namespace App\Http\Resources\Enum;

use App\Models\Enum\Value;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="EnumValueResource",
 *    @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *    @OA\Property(property="title", type="string", example="Пример названия значения перечисления"),
 * )
 *
 * @property Value $resource
 */
class ValueResource extends JsonResource
{
    public function __construct(Value $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
        ];
    }
}
