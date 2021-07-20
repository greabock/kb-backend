<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Show;

use App\Http\Actions\Api\Materials\Update\Request;
use App\Http\Resources\MaterialResource;
use App\Models\Section;
use Illuminate\Http\JsonResponse;

class Action
{
    public function __invoke(Section $section, Request $request): JsonResponse
    {
        $material = ($section->class_name)::findOrFail($request->material);

        return (new MaterialResource($material))->response()->setStatusCode(200);
    }
}
