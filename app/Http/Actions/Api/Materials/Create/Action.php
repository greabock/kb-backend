<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Create;

use App\Models\Section;
use Greabock\Populator\Populator;
use Illuminate\Http\JsonResponse;

class Action
{
    public function __invoke(Section $section, Request $request, Populator $populator): JsonResponse
    {
        $material = $populator->populate($section->class_name, $request->getStruct());

        $populator->flush();

        return response()->json([
            'data' => $material->toArray()
        ])->setStatusCode(201);
    }
}
