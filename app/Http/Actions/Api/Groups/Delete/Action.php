<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Groups\Delete;

use App\Models\User\Group;
use Illuminate\Http\Response;

class Action
{
    public function __invoke(Group $group): Response
    {
        $group->delete();

        return response()->noContent();
    }
}
