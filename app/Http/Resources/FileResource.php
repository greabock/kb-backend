<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="FileResource",
 *    @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *    @OA\Property(property="url", type="string", example="http://api.knowledge.msharks.ru/api/files/430e758e-453f-4184-9459-7bd82da0cba4"),
 *    @OA\Property(property="name", type="string", example="some.jpg"),
 *    @OA\Property(property="size", type="integer", example="1024"),
 * )
 * @property File $resource
 */
class FileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'url' => $this->resource->url,
            'name' => $this->resource->name,
            'size' => $this->resource->size,
            'signed' => \URL::signedRoute('files.download', [$this->resource->id])
        ];
    }
}
