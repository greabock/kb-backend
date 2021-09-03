<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Events\SectionCreated;
use App\Jobs\UpdateMaterialClass;
use Illuminate\Contracts\Bus\Dispatcher;

class CreateMaterialClassOnSectionCreated
{
    public function __construct(
        private Dispatcher $jobs,
    )
    {
    }

    public function handle(SectionCreated $event): void
    {
        $this->jobs->dispatch(new UpdateMaterialClass($event->sectionId));
    }
}
