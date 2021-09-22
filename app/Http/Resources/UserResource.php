<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="UserResource",
 *    @OA\Property(property="id", type="integer", example="123"),
 *    @OA\Property(property="name", type="string", example="Ульянов Владимир Ильич", nullable=true),
 *    @OA\Property(property="photo", type="string", example="absolute/path/to/image.jpg", nullable=true),
 *    @OA\Property(property="role", type="string", enum={"admin", "moderator", "user"}),
 *    @OA\Property(property="email", type="string", example="user@mail.com", nullable=true),
 * )
 * @property User $resource
 */
class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'photo' => $this->resource->photo,
            'role' => $this->resource->role,
            'email' => $this->resource->email,
        ];
    }
}
