<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Enums;

use App\Models\Enum;
use Tests\Feature\Api\ActionTestCase;

class DestroyTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'enums.destroy';
    }

    public function testUserCanDestroyEnum(): void
    {
        $enum = Enum::factory()->create();

        $this->assertDatabaseCount('enums', 1);

        $this->callAuthorizedRouteAction([], ['enum' => $enum->id])
            ->assertStatus(204);

        $this->assertDatabaseCount('enums', 0);
    }
}
