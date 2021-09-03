<?php

declare(strict_types=1);

namespace App\Http\Actions\Api\Users\Delete;

use App\Models\User;
use Illuminate\Http\Response;

class Action
{
    public function __invoke(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }
}
