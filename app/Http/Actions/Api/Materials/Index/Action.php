<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Index;

use App\Http\Resources\MaterialResource;
use App\Models\Section;
use App\Services\Search\Search;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(Section $section, Request $request, Search $search): AnonymousResourceCollection
    {
        $res = $search->searchMaterialsByName($section->id . '_write', $request->get('search'));

        $query = ($section->class_name)::whereIn('id', $res)->select(...$section->cardFields());

        return MaterialResource::collection($query->paginate());
    }
}
