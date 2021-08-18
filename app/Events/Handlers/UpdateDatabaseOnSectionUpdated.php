<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Events\SectionUpdated;
use App\Services\TableBuilder;

class UpdateDatabaseOnSectionUpdated
{
    public function __construct(private TableBuilder $builder)
    {
    }

    // TODO: как чейнить без фасада?
    public function handle(SectionUpdated $event)
    {
        foreach ($event->createdFields() as $field) {
            $this->builder->addField($field);
        }

        foreach ($event->removedFields() as $field) {
            $this->builder->dropField($field);
        }
    }
}
