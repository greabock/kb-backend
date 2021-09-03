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

        $this->callAuthorizedRouteAction([], ['enum' => $enum->id])
            ->assertStatus(204);

        $this->assertDatabaseMissing('enums', ['id' => $enum->id, 'deleted_at' => null]);
    }
}
