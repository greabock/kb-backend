<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Update;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Greabock\Populator\Populator;

class Action
{
    public function __invoke(Section $section, Request $request, Populator $populator): SectionResource
    {
        $populator->populate($section, $request->getStruct());
        $populator->flush();

        return new SectionResource($section);
    }
}
