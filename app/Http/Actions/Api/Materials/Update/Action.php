<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Update;

use App\Events\MaterialUpdated;
use App\Http\Resources\MaterialResource;
use App\Models\Section;
use Greabock\Populator\Populator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;

class Action
{
    public function __invoke(Section $section, Request $request, Populator $populator, Dispatcher $dispatcher): JsonResponse
    {
        // TODO событие обновления материала
        $material = ($section->class_name)::findOrFail($request->material);

        $populator->populate($material, $request->getStruct());

        $populator->flush();

        $dispatcher->dispatch(new MaterialUpdated($section->class_name, $material->id));

        return (new MaterialResource($material))->response()->setStatusCode(200);
    }
}
