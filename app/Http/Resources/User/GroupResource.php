<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Http\Resources\UserResource;
use App\Models\User\Group;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="GroupResource",
 *    @OA\Property(property="id", type="integer", example="123"),
 *    @OA\Property(property="name", type="string", example="Ульянов Владимир Ильич", nullable=true),
 *    @OA\Property(property="users", type="array",
 *      @OA\Items(ref="#/components/schemas/UserResource")
 *    ),
 * )
 * @property Group $resource
 */
class GroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'users' => $this->whenLoaded('users', fn() => UserResource::collection($this->resource->users)),
        ];
    }
}
