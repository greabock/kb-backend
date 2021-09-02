<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Search\Filter;

use Illuminate\Pagination\LengthAwarePaginator;
use Validator;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Services\Search\Search;
use App\Validation\Rules\FieldType;
use App\Http\Resources\SearchResultResource;

class Action
{
    public function __invoke(Request $request, Section $section, Search $search): SearchResultResource
    {
        // TODO: вынести валидацию
        Validator::validate($request->all(), array_merge(
            [
                'search' => 'nullable|string',
                'sort' => 'sometimes|array:field,direction',
                'sort.field' => 'in:created_at,name',
                'sort.direction' => 'in:asc,desc',
                'extensions' => 'sometimes|array',
                'extensions.*' => 'string',
                'materials' => 'sometimes|boolean',
            ],
            $this->buildFilterRules($section)
        ));


        /** @var Section\Field\Collection $fields */
        $fields = $section->fields;

        $index = $section->id . '_write';

        $materials = ($request->get('materials', false) || empty($request->get('extensions', []))) ? $search->searchMaterials(
            $request->get('search') ?? '',
            $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
            $request->get('filter', []),
            $fields,
            $index,
        ) : collect();


        $files = (!$request->get('materials', false) || !empty($request->get('extensions', []))) && $this->filtersIsEmpty($request->get('filter', [])) ? $search->searchFiles(
            $request->get('search') ?? '',
            $request->get('extensions', []),
            $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
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
                $request->get('per_page', 15),
                $request->get('page', 1),
            ),
        );
    }

    private function buildFilterRules(Section $section): array
    {
        $rules = [];

        foreach ($section->fields as $field) {
            foreach (FieldType::filterRules($field->base_type, $field->id) as $key => $rule) {
                $rules['filter.' . $key . '.*'] = $rule;
            }
        }

        return $rules;
    }

    private function filtersIsEmpty(array $filters): bool
    {
        if (empty($filters)) {
            return true;
        }

        foreach ($filters as $filter) {
            if (!empty($filter)) {
                return false;
            }
        }

        return true;
    }
}
