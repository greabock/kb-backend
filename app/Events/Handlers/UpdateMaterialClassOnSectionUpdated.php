<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Events\SectionUpdated;
use App\Jobs\UpdateMaterialClass;
use Illuminate\Contracts\Bus\Dispatcher;

class UpdateMaterialClassOnSectionUpdated
{
    public function __construct(
        private Dispatcher $jobs,
    )
    {
    }

    public function handle(SectionUpdated $event): void
    {
        $this->jobs->dispatch(new UpdateMaterialClass($event->sectionId));
    }
}
