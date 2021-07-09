<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Enums;

use App\Models\User;
use Tests\Feature\Api\ActionTestCase;

class CreateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'enums.create';
    }

    public function testUserCanCreateEnum(): void
    {
        $enumName = 'testEnum';

        $this->assertDatabaseCount('enums', 0);

        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin']);

        $this->callAuthorizedByUserRouteAction($user, ['title' => $enumName])
            ->assertStatus(201);

        $this->assertDatabaseHas('enums', ['title' => $enumName]);
    }

    public function testUserCanCreateEnumWithValues(): void
    {
        $enumName = 'testEnum';

        $valueTitle = 'testValue';

        $this->assertDatabaseCount('enums', 0);
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin']);

        $this->callAuthorizedByUserRouteAction($user, ['title' => $enumName, 'values' => [['title' => $valueTitle]]])
            ->assertStatus(201);

        $this->assertDatabaseHas('enums', ['title' => $enumName]);
        $this->assertDatabaseHas('enum_values', ['title' => $valueTitle]);
    }
}
