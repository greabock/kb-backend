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
 *    @OA\Property(property="avatar", type="string", example="absolute/path/to/image.jpg", nullable=true),
 *    @OA\Property(property="role", type="string", enum={"admin", "moderator", "user"}),
 *    @OA\Property(property="login", type="string", example="john15"),
 *    @OA\Property(property="email", type="string", example="user@mail.com", nullable=true),
 * )
 * @property User $resource
 */
class UserResource extends JsonResource
{
    public function __construct(User $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'avatar' => $this->resource->photo ? url('storage/' . $this->resource->photo) : null,
            'role' => $this->resource->role,
            'login' => $this->resource->login,
            'email' => $this->resource->email,
        ];
    }
}
