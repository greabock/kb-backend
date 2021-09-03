<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Delete;

use App\Models\Material;
use App\Models\Section;
use Illuminate\Http\Response;
use App\Events\MaterialDeleted;
use Illuminate\Events\Dispatcher;

class Action
{
    public function __invoke(Section $section, $material, Dispatcher $events): Response
    {
        /** @var Material $material */
        $material = ($section->class_name)::findOrFail($material);
        $material->delete();

        $events->dispatch(new MaterialDeleted($section->class_name, $material->id));

        return response()->noContent();
    }
}
