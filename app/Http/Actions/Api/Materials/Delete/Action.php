<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Delete;

use App\Models\Material;
use App\Models\Section;
use Illuminate\Http\Response;

class Action
{
    public function __invoke(Section $section, $material): Response
    {
        /** @var Material $material */
        $material = ($section->class_name)::findOrFail($material);
        $material->delete();

        return response()->noContent();
    }
}
