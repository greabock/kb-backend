<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Create;

use App\Models\Section;
use App\Events\SectionCreated;
use Greabock\Populator\Populator;
use App\Http\Resources\SectionResource;
use Illuminate\Contracts\Events\Dispatcher;

class Action
{
    public function __invoke(Request $request, Populator $populator, Dispatcher $events)
    {
        /** @var Section $section */
        $section = $populator->populate(Section::class, $request->getStruct());
        $section->indexing = true;
        $populator->flush();

        $events->dispatch(new SectionCreated($section->id));

        return new SectionResource($section);
    }
}
