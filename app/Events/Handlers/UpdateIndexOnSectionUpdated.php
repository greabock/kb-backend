<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Jobs\SectionIndexLock;
use App\Jobs\SectionIndexUnlock;
use App\Models\Section;
use Bus;
use App\Events\SectionUpdated;
use App\Jobs\CreateSectionIndex;
use App\Jobs\DropSectionIndex;
use App\Jobs\ReindexMaterials;
use App\Jobs\UpdateSectionIndex;

class UpdateIndexOnSectionUpdated
{
    // TODO: как чейнить без фасада?
    public function handle(SectionUpdated $event)
    {
        if (!empty($event->updatedFields()) || !empty($event->removedFields())) {
            Bus::chain([
                new SectionIndexLock($event->sectionId),
                new DropSectionIndex($event->sectionId),
                new CreateSectionIndex($event->sectionId),
                new ReindexMaterials($event->sectionId),
                new SectionIndexUnlock($event->sectionId),
            ])->dispatch();

            return;
        }

        Bus::chain([
            new SectionIndexLock($event->sectionId),
            new UpdateSectionIndex(
                $event->sectionId,
                array_map(static fn($field) => $field['id'], $event->createdFields()),
            ),
            new SectionIndexUnlock($event->sectionId),
        ])->dispatch();
    }
}
