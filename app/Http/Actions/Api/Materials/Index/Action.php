<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Index;

use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Action
{
    public function __invoke(Section $section): JsonResponse
    {
        /** @var LengthAwarePaginator $materials */
        $materials = ($section->class_name)::only($section->cardFields())->paginate();

        return response()->json($materials->toArray());
    }
}
