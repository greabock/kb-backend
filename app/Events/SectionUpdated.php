<?php

declare(strict_types=1);

namespace App\Events;

class SectionUpdated
{
    public function __construct(
        public string $sectionId,
        public array $oldState,
        public array $newState
    )
    {
    }

    public function updatedFields(): array
    {
        $newFields = collect($this->newState['fields']);

        return collect($this->oldState['fields'])->filter(function ($field) use ($newFields) {
            $newField = $newFields->where('id', $field['id'])->first();
            return $newField && $field['type'] !== $newField['type'];
        })->toArray();
    }

    public function removedFields(): array
    {
        $newFields = collect($this->newState['fields']);

        return collect($this->oldState['fields'])->filter(function ($field) use ($newFields) {
            return $newFields->where('id', $field['id'])->isEmpty();
        })->toArray();
    }

    public function createdFields(): array
    {
        $oldFields = collect($this->oldState['fields']);

        return collect($this->newState['fields'])->filter(function ($field) use ($oldFields) {
            return $oldFields->where('id', $field['id'])->isEmpty();
        })->toArray();
    }
}
