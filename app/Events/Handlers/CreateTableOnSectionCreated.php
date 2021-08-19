<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Events\SectionCreated;
use App\Models\Section;
use App\Services\TableBuilder;

class CreateTableOnSectionCreated
{
    public function __construct(
        private TableBuilder $builder
    )
    {
    }

    public function handle(SectionCreated $event)
    {
        $section = Section::find($event->sectionId);

        // TODO: в отдельные задачи
        $this->builder->create($section);
        $this->builder->buildColumns($section);
    }
}
