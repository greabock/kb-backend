<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Auth;

use Tests\Feature\Api\ActionTestCase;

class MeTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'auth.me';
    }

    public function testGuestCantGetInfo(): void
    {
        $this->assertRouteContainsMiddleware('auth:sanctum');
    }

    public function testAuthenticatedUserCanGetInfo(): void
    {
        $this->callAuthorizedRouteAction()
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'avatar', 'role', 'login', 'email']]);
    }
}
