<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\MassUpdate;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        foreach ($request->getStruct() as $struct) {
            $sections[] = tap(
                Section::findOrFail($struct['id']),
                static fn(Section $section) => $section->fill($struct)->save()
            );
        }

        return SectionResource::collection($sections ?? []);
    }
}
