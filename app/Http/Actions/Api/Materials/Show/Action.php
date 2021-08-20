<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Show;

use App\Models\Section;
use App\Models\Material;
use App\Http\Resources\MaterialResource;
use App\Http\Actions\Api\Materials\Update\Request;
use Illuminate\Http\JsonResponse;

class Action
{
    public function __invoke(Section $section, Request $request): JsonResponse
    {
        $section->load('fields');

        /** @var Material $material */
        $material = ($section->class_name)::with(
            $section->getRelationFields()->pluck('id')->toArray()
        )->findOrFail($request->material);

        return (new MaterialResource($material))->response()->setStatusCode(200);
    }
}
