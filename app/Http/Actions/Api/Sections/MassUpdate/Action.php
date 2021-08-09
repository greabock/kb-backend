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
        foreach ($request->getStruct() as ['id' => $id, 'sort_index' => $sortIndex]) {
            $sections[] = tap(
                Section::findOrFail($id),
                static fn(Section $section) => $section->setAttribute('sort_index', $sortIndex)->save()
            );
        }

        return SectionResource::collection($sections ?? []);
    }
}
