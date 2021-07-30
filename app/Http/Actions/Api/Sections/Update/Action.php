<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Update;

use App\Events\SectionUpdated;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use Greabock\Populator\Populator;
use Illuminate\Contracts\Events\Dispatcher;

class Action
{
    public function __invoke(Section $section, Request $request, Populator $populator, Dispatcher $events): SectionResource
    {
        $section->load('fields');
        $old = $section->toArray();

        $populator->populate($section, $request->getStruct());
        $populator->flush();

        $events->dispatch(new SectionUpdated($section->id, $old, $section->toArray()));

        return new SectionResource($section);
    }
}
