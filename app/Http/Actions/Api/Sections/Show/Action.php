<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Sections\Show;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class Action
{
    public function __invoke(Section $section, Request $request): SectionResource
    {
        if ($section->hasAccess($request->user())) {

            $section->load('fields');
            $section->load('groups');
            $section->load('users');

            return new SectionResource($section);
        }

        throw new AuthorizationException();
    }
}
