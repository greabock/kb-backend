<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Search;

use App\Models\Section;
use App\Services\Search\Search;
use App\Http\Resources\SearchResultResource;

class Action
{
    public function __invoke(Request $request, Search $search): SearchResultResource
    {
        /** @var Section\Collection<int, Section> $sections */
        $sections = Section::with('fields')->get();
        $fields = $sections->fields();
        $index = $sections->index('_write');


        return new SearchResultResource([
            'files' => $search->searchFiles(
                $request->get('search'),
                $request->get('extensions', []),
                $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
                $fields->fileFields(),
                $index,
            ),
            'materials' => $request->get('materials', false) ?  $search->searchMaterials(
                $request->get('search'),
                $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
                [],
                $fields->searchableFields(),
                $index,
            ) : collect()
        ]);
    }
}
