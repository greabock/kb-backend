<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Update;

use App\Http\Actions\Api\ApiRequest;
use OpenApi\Annotations as OA;
use App\Models\Section;

/**
 * @OA\Schema(schema="MaterialUpdateRequest",
 *     @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *     @OA\Property(property="name", type="string", example="Название материала"),
 *     @OA\AdditionalProperties(type="object", anyOf={
 *        @OA\Schema(type="object",
 *            @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *        ),
 *        @OA\Schema(type="array",
 *            @OA\Items(type="object",
 *              @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426655440000"),
 *            ),
 *        ),
 *        @OA\Schema(oneOf={@OA\Schema(type="string"), @OA\Schema(type="number"), @OA\Schema(type="boolean")}),
 *    }),
 * )
 */
class Request extends ApiRequest
{
    public function rules(): array
    {
        return array_merge(
            ['name' => 'sometimes|string|max:255'],
            $this->resolveSection()->rules(required: false),
        );
    }

    public function struct(): ?array
    {
        return $this->resolveSection()->struct();
    }

    public function resolveSection(): Section
    {
        return $this->section instanceof Section ? $this->section : $this->section = Section::findOrFail($this->section);
    }
}
