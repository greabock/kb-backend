<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Events\MaterialCreated;
use App\Jobs\CreateMaterialDocument;
use Illuminate\Bus\Dispatcher;

class CreateMaterialDocumentOnMaterialCreated
{
    public function __construct(
        private Dispatcher $jobs,
    )
    {
    }

    public function handle(MaterialCreated $event)
    {
        $this->jobs->dispatch(new CreateMaterialDocument(
            $event->materialClass,
            $event->materialId
        ));
    }
}
