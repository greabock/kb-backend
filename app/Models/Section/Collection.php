<?php

declare(strict_types=1);

namespace App\Models\Section;

use App\Models\Section;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class Collection extends EloquentCollection
{
    public function fields(): Field\Collection
    {
        return $this->reduce(
            fn(Field\Collection $carry, Section $section) => $carry->push(...$section->fields),
            new Field\Collection()
        );
    }

    public function index(string $suffix): string
    {
        return $this->map(fn(Section $section) => $section->id . $suffix)->join(',');
    }
}
