<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Search\Filter;

use Exception;
use App\Models\Material;
use App\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class Action
{
    /**
     * @throws Exception
     */
    public function __invoke(Section $section, Request $request): LengthAwarePaginator
    {
        /** @var Section\Field[] $filterableFields */
        $filterableFields = $section->fields->filter(fn(Section\Field $field) => $field->is_filterable);

        /** @var Material $materialClass */
        $materialClass = $section->class_name;

        $query = $materialClass::search($request->get('search'));

        foreach ($filterableFields as $filterableField) {
            if ($request->has($filterableField->id)) {
                $filterableField->applyFilter($query, $request->all());
            }
        }

        return $query->paginate(15);
    }
}
