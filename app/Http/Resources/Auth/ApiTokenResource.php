<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;

/**
 * @OA\Schema(schema="ApiTokenResource",
 *    @OA\Property(property="token", type="string", example="access_token"),
 * )
 * @property NewAccessToken $resource
 */
class ApiTokenResource extends JsonResource
{
    public function __construct(NewAccessToken $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'token' => $this->resource->accessToken->token,
        ];
    }
}
