<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Show;

use App\Http\Resources\SectionResource;
use App\Models\Section;

class Action
{
    public function __invoke(Section $section): SectionResource
    {
        $section->load('fields');

        return new SectionResource($section);
    }
}
