<?php

declare(strict_types=1);

use App\Models\Material;
use App\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Action
{
    public function __invoke(Section $section, Request $request): LengthAwarePaginator
    {
        $filterableFields = $section->fields->filter(fn(Section\Field $field) => $field->is_filterable);
        /** @var Material $materialClass */
        $materialClass = $section->class_name;

        $query = $materialClass::search($request->get('search'));

        foreach ($filterableFields as $filterableField) {
            if ($request->has($filterableField->id)) {
                $query->where($filterableField->columnName, $request->get($filterableField->id));
            }
        }

        return $query->paginate(15);
    }
}
