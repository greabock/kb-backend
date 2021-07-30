<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Events\SectionUpdated;
use App\Jobs\CreateMaterialDocument;
use App\Jobs\CreateSectionIndex;
use App\Jobs\DropSectionIndex;
use App\Jobs\ReindexMaterials;
use App\Jobs\UpdateSectionIndex;
use App\Models\Section;
use Bus;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Bus\PendingChain;

class UpdateIndexOnSectionUpdated
{
    public function __construct(
        private Dispatcher $jobs,
    )
    {
    }

    public function handle(SectionUpdated $event)
    {
        if (!empty($event->updatedFields()) || !empty($event->removedFields())) {

            // TODO: как чейнить без фафсада?
            Bus::chain([
                new DropSectionIndex($event->sectionId),
                new CreateSectionIndex($event->sectionId),
                new ReindexMaterials($event->sectionId),
            ])->dispatch();

            return;
        }

        $this->jobs->dispatch(new UpdateSectionIndex(
            $event->sectionId,
            array_map(static fn($field) => $field['id'], $event->createdFields()),
        ));
    }
}
