<?php

declare(strict_types=1);

namespace App\Events\Handlers;

use App\Events\MaterialUpdated;
use App\Jobs\CreateMaterialDocument;
use Illuminate\Contracts\Bus\Dispatcher;

class UpdateMaterialDocumentOnMaterialUpdated
{
    public function __construct(private Dispatcher $jobs)
    {
    }

    public function handle(MaterialUpdated $event)
    {
        $this->jobs->dispatch(new CreateMaterialDocument(
            $event->materialClass,
            $event->materialId,
        ));
    }
}
