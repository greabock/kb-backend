<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Delete;

use App\Events\SectionDeleted;
use App\Models\Section;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Action
{
    public function __invoke(Section $section, Dispatcher $events, Request $request): Response
    {
        $section->delete();
        $events->dispatch(new SectionDeleted($section->id));
        return response()->noContent();
    }
}
