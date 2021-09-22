<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Groups;

use App\Models\User;
use Tests\Feature\Api\ActionTestCase;

class CreateTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'groups.create';
    }

    public function testAdminCanCreateGroup(): void
    {
        $this->callAuthorizedByAdminRouteAction(['name' => 'test'])
            ->assertCreated();

        $this->assertDatabaseHas('groups', ['name' => 'test']);
    }

    public function testAdminCanCreateGroupWithUsers(): void
    {
        $user = User::factory()->create();

        $this->callAuthorizedByAdminRouteAction(['name' => 'test', 'users' => [['id' => $user->id]]])
            ->assertCreated();

        $this->assertDatabaseHas('groups', ['name' => 'test']);
        $this->assertDatabaseHas('group_user', ['user_id' => $user->id]);
    }
}
