<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Section\Field;

class SectionFieldObserver
{
    public function updating(Field $field)
    {
        if ($field->wasChanged(['type'])) {
            $field->drop();
        }
    }

    public function created(Field $field)
    {
        $field->build();
    }

    public function updated(Field $field)
    {
        $field->build();
    }

    public function deleting(Field $field): bool
    {
        $field->drop();

        return true;
    }
}
