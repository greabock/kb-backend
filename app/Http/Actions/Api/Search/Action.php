<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Search;

use App\Models\Section;
use App\Services\Search\Search;
use App\Http\Resources\SearchResultResource;
use Illuminate\Pagination\LengthAwarePaginator;

class Action
{
    public function __invoke(Request $request, Search $search): SearchResultResource
    {
        /** @var Section\Collection<int, Section> $sections */
        $sections = Section::with('fields')->get();
        $fields = $sections->fields();
        $index = $sections->index('_write');

        $files = (!$request->get('materials', false) || !empty($request->get('extensions', []))) ? $search->searchFiles(
            $request->get('search'),
            $request->get('extensions', []),
            $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
            $fields,
            $index,
        ) : collect();

        $materials = ($request->get('materials', false) || empty($request->get('extensions', []))) ? $search->searchMaterials(
            $request->get('search'),
            $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
            [],
            $fields,
            $index,
        ) : collect();

        $results = collect()->concat($materials)->concat($files);
        $page = $results
            ->forPage($request->get('page', 1), $request->get('per_page', 15))
            ->groupBy(fn(array $item) => array_key_exists('file', $item) ? 'files' : 'materials');

        $page->put('materials', $page->get('materials', []));
        $page->put('files', $page->get('files', []));

        return new SearchResultResource(
            new LengthAwarePaginator(
                $page,
                $results->count(),
                $request->get('per_page', 1),
                $request->get('page', 1),
            ),
        );
    }
}
