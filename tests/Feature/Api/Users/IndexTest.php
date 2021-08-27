<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Users;

use App\Models\User;
use Tests\Feature\Api\ActionTestCase;

class IndexTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'users.index';
    }

    public function testAdminIsHidden(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'super' => true]);
        User::factory()->create(['role' => User::ROLE_USER]);
        User::factory()->create(['role' => User::ROLE_MODERATOR]);

        $this->callAuthorizedByUserRouteAction($admin)
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
