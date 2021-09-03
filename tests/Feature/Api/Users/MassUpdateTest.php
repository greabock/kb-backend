<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Users;

use App\Models\User;
use Tests\Feature\Api\ActionTestCase;

class MassUpdateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'users.mass-update';
    }

    public function testAdminCanMassUpdateUsers()
    {
        $users = User::factory()->count(2)->create();

        $res = $this->callAuthorizedByAdminRouteAction([
            [
                'id' => $users[0]->id,
                'role' => User::ROLE_MODERATOR,
            ],
            [
                'id' => $users[1]->id,
                'role' => User::ROLE_MODERATOR,
            ]
        ])
            ->assertOk()
            ->assertJsonPath('data.0.role', User::ROLE_MODERATOR)
            ->assertJsonPath('data.1.role', User::ROLE_MODERATOR);
    }
}
