<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Groups;

use App\Models\User\Group;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Api\ActionTestCase;

class DeleteTest extends ActionTestCase
{

    public function getRouteName(): string
    {
        return 'groups.delete';
    }

    public function testAdminCanDeleteGroup()
    {
        $group = Group::make(['name' => 'test']);
        $group->id = Uuid::uuid4()->toString();
        $group->save();

        $this->callAuthorizedByAdminRouteAction([], ['group' => $group->id])
            ->assertNoContent();

        $this->assertDatabaseMissing('groups', ['id' => $group->id, 'deleted_at' => null]);
    }
}
