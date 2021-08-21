<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Section;
use App\Models\Section\Field;
use Illuminate\Support\Collection;

class SectionUpdated
{

    public function __construct(
        public string $sectionId,
        public Section $oldState,
        public Section $newState
    )
    {
        assert($this->sectionId === $this->oldState->id);
        assert($this->sectionId === $this->newState->id);
        assert($this->oldState !== $this->newState);
        assert($this->oldState->relationLoaded('fields'));
        assert($this->newState->relationLoaded('fields'));
    }

    public function updatedFields(): Collection
    {
        return $this->newState->fields->map(fn(Field $newField) => [
            $newField,
            $this->oldState->fields->where($newField->getKeyName(), $newField->getKey())->first()
        ])->filter(fn(array $fields) => (bool)$fields[1]?->isSameType($fields[0]));
    }

    public function removedFields(): Field\Collection
    {
        return $this->oldState->fields->filter(function (Field $field) {
            return !$this->newState->fields->where($field->getKeyName(), $field->getKey())->first();
        });
    }

    public function createdFields(): Field\Collection
    {
        return $this->newState->fields->filter(function (Field $field) {
            return !$this->oldState->fields->where($field->getKeyName(), $field->getKey())->first();
        });
    }
}
