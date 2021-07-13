<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Create;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Greabock\Populator\Populator;

class Action
{
    public function __invoke(Request $request, Populator $populator)
    {
        /** @var Section $section */
        $section = $populator->populate(Section::class, $request->getStruct());

        $populator->flush();

        return new SectionResource($section);
    }
}
