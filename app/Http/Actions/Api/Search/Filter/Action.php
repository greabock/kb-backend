<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Search\Filter;

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

        $materials = $request->get('materials', false) ? $search->searchMaterials(
            $request->get('search') ?? '',
            $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
            $request->get('filter', []),
            $fields->nonFileFields(),
            $index,
        ) : collect();

        $files = $search->searchFiles(
            $request->get('search') ?? '',
            $request->get('extensions', []),
            $request->get('sort', ['field' => 'created_at', 'direction' => 'desc']),
            $fields->fileFields(),
            $index,
        );

        return new SearchResultResource(compact('materials', 'files'));
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
}
