<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Destroy;

use App\Events\SectionDeleted;
use App\Models\Section;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Response;

class Action
{
    public function __invoke(Section $section, Dispatcher $events): Response
    {
        $section->delete();

        $events->dispatch(new SectionDeleted($section->id));

        return response()->noContent();
    }
}
