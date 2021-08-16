<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Users;

use App\Models\User;
use Tests\Feature\Api\ActionTestCase;

class UpdateTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'users.update';
    }

    public function testAdminCanUpdateUser()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this
            ->callAuthorizedByAdminRouteAction($user->toArray(), ['user' => $user->id])
            ->assertOk()
        ;
    }
}
