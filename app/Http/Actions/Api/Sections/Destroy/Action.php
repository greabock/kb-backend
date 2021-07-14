<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Destroy;

use App\Models\Section;

class Action
{
    public function __invoke(Section $section)
    {
        $section->delete();
        return response()->noContent();
    }
}
