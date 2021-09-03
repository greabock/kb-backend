<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Create;

use App\Events\MaterialCreated;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use App\Models\Section;
use Greabock\Populator\Populator;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class Action
{
    public function __invoke(
        Section $section,
        Request $request,
        Populator $populator,
        Dispatcher $dispatcher
    ): Response|JsonResponse
    {
        if ($section->indexing) {
            return response()->json(['message' => 'Section is busy'], 403);
        }

        /** @var Material $material */
        $material = $populator->populate($section->class_name, $request->getStruct());

        $populator->flush();

        $dispatcher->dispatch(new MaterialCreated($section->class_name, $material->id));

        return (new MaterialResource($material))->response()->setStatusCode(201);
    }
}
