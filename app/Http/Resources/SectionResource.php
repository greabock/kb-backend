<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Section\FieldResource;
use App\Http\Resources\User\GroupResource;
use App\Models\Section;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="SectionResource",
 *    @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *    @OA\Property(property="title", type="string", example="Агенты"),
 *    @OA\Property(property="image", type="string", example="absolute://path.to/image.jpg"),
 *    @OA\Property(property="is_dictionary", type="boolean"),
 *    @OA\Property(property="is_navigation", type="boolean"),
 *    @OA\Property(property="sort_index", type="integer"),
 *    @OA\Property(property="config", type="object",
 *       @OA\Property(property="name", type="string", example="Название"),
 *       @OA\Property(property="description", type="string", example="Описание"),
 *    ),
 * )
 *
 * @OA\Schema(schema="SectionWithFieldsResource",
 *    @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *    @OA\Property(property="title", type="string", example="Страны"),
 *    @OA\Property(property="image", type="string", example="absolute://path.to/image.jpg"),
 *    @OA\Property(property="is_dictionary", type="boolean"),
 *    @OA\Property(property="is_navigation", type="boolean"),
 *    @OA\Property(property="sort_index", type="integer"),
 *    @OA\Property(property="config", type="object",
 *       @OA\Property(property="name", type="string", example="Название"),
 *       @OA\Property(property="description", type="string", example="Описание"),
 *    ),
 *    @OA\Property(property="fields", type="array",
 *      @OA\Items(ref="#/components/schemas/SectionFieldResource")
 *    )
 * )
 * @property Section $resource
 */
class SectionResource extends JsonResource
{
    public function __construct(Section $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'image' => $this->resource->image,
            'is_dictionary' => $this->resource->is_dictionary,
            'is_navigation' => $this->resource->is_navigation,
            'sort_index' => $this->resource->sort_index,
            'config' => $this->resource->config,
            'fields' => FieldResource::collection($this->whenLoaded('fields')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'groups' => GroupResource::collection($this->whenLoaded('groups')),
        ];
    }
}
