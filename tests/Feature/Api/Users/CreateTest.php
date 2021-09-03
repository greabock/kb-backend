<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Users;

use Tests\Feature\Api\ActionTestCase;

class CreateTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'users.create';
    }

    public function testAdminCanCreateUser()
    {
        $this->callAuthorizedByAdminRouteAction([
            'login' => 'user',
            'email' => 'user@not.admin',
            'name' => 'user',
            'password' => 'user',
            'role' => 'user',
        ])->assertCreated();
    }
}
