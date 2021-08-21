<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Index;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(): AnonymousResourceCollection
    {
        return SectionResource::collection(
            Section::with('fields')->orderBy('sort_index')->get(),
        );
    }
}
