<?php

declare(strict_types=1);

namespace App\Events\Handlers;

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
        $this->jobs->dispatch(
            (new CreateSectionIndex($event->sectionId)),
        );
    }
}
