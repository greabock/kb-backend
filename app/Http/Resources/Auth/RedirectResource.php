<?php

declare(strict_types=1);

namespace App\Http\Resources\Auth;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\RedirectResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="RedirectResource",
 *    @OA\Property(property="url", type="string", example="https://account.microsoft.com"),
 * )
 * @property User $resource
 */
class RedirectResource extends JsonResource
{
    public function __construct(RedirectResponse $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request): array
    {
        return [
            'url' => $this->resource->getTargetUrl(),
        ];
    }
}
