<?php

declare(strict_types=1);

namespace App\Models\Section\Field;

use App\Models\Section\Field;
use App\Validation\Rules\FieldType;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class Collection extends EloquentCollection
{
    public function fileFields(): self
    {
        return $this->filter(fn(Field $field) => $field->base_type['name'] === FieldType::T_FILE);
    }

    public function nonFileFields(): self
    {
        return $this->filter(fn(Field $field) => $field->base_type['name'] !== FieldType::T_FILE);
    }

    public function presentInCard(): self
    {
        return $this->filter(fn(Field $f) => $f->is_present_in_card);
    }

    public function relations(): self
    {
        return $this->filter(fn(Field $f) => $f->isRelationField());
    }

    public function plain(): self
    {
        return $this->filter(fn(Field $f) => $f->isPlainField());
    }
}
