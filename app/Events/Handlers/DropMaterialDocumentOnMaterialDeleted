<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Events\MaterialCreated;
use App\Jobs\DropMaterialDocument;
use Illuminate\Bus\Dispatcher;

class DropMaterialDocumentOnMaterialDeleted
{
    public function __construct(
        private Dispatcher $jobs,
    )
    {
    }

    public function handle(MaterialCreated $event)
    {
        $this->jobs->dispatch(new DropMaterialDocument(
            $event->materialClass,
            $event->materialId
        ));
    }
}
