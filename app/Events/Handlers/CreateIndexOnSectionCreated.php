<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Jobs\DropSectionIndex;
use App\Jobs\ReindexMaterials;
use App\Jobs\SectionIndexLock;
use App\Jobs\SectionIndexUnlock;
use Bus;
use Exception;
use App\Events\SectionCreated;
use App\Jobs\CreateSectionIndex;
use Illuminate\Contracts\Bus\Dispatcher;

class CreateIndexOnSectionCreated
{
    public function __construct(private Dispatcher $jobs)
    {
    }

    /**
     * @throws Exception
     */
    public function handle(SectionCreated $event): void
    {
        Bus::chain([
            new SectionIndexLock($event->sectionId),
            new CreateSectionIndex($event->sectionId),
            new SectionIndexUnlock($event->sectionId),
        ])->dispatch();
    }
}
