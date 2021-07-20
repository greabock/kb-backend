<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Materials\Index;

use App\Http\Resources\MaterialResource;
use App\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Action
{
    public function __invoke(Section $section): AnonymousResourceCollection
    {
        /** @var LengthAwarePaginator $materials */
        $materials = ($section->class_name)::only($section->cardFields())->paginate();

        return MaterialResource::collection($materials);
    }
}
