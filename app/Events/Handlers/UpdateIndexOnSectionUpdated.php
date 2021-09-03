<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Events\SectionUpdated;
use App\Jobs\CreateSectionIndex;
use App\Jobs\DropSectionIndex;
use App\Jobs\ReindexMaterials;
use App\Jobs\SectionIndexLock;
use App\Jobs\SectionIndexUnlock;
use Bus;

class UpdateIndexOnSectionUpdated
{
    // TODO: как чейнить без фасада?
    public function handle(SectionUpdated $event)
    {
        Bus::chain([
            new SectionIndexLock($event->sectionId),
            new DropSectionIndex($event->sectionId),
            new CreateSectionIndex($event->sectionId),
            new ReindexMaterials($event->sectionId),
            new SectionIndexUnlock($event->sectionId),
        ])->dispatch();
    }
}
