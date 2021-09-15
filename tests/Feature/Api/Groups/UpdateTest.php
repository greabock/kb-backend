<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Groups;

use App\Models\User;
use App\Models\User\Group;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class UpdateTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'groups.update';
    }

    public function testAdminCanUpdateUserGroup()
    {
        $user = User::factory()->create();
        $group = Group::make(['name' => 'test']);
        $group->id = Uuid::uuid4()->toString();
        $group->save();
        $group->users()->attach($user);

        $this->callAuthorizedByAdminRouteAction([
            'name' => 'test2',
            'users' => [],
        ], ['group' => $group->id]);


        $this->assertDatabaseMissing('group_user', ['user_id' => $user->id, 'group_id' => $group->id]);
        $this->assertDatabaseHas('groups', ['name' => 'test2']);
    }
}
