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

    /**
     * @return Collection<integer, Field>|Field[]
     */
    public function searchableFields(): Collection
    {
        return $this->filter(fn(Field $field) => in_array(
            $field->base_type['name'],
            FieldType::SEARCHABLE_FIELDS, true
        ));
    }

    public function dateFields(): Collection
    {
        return $this->filter(fn(Field $field) => $field->type['name'] === FieldType::T_DATE);
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
