<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Enums;

use App\Models\Enum;
use Tests\Feature\Api\ActionTestCase;

class ShowTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'enums.show';
    }

    public function testUserCanSeeEnum(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->create();

        $this->callAuthorizedRouteAction([], ['enum' => $enum->id])
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['title', 'id', 'values']]);
    }

    public function testUserCanSeeEnumWithValues(): void
    {
        /** @var Enum $enum */
        $enum = Enum::factory()->has(Enum\Value::factory()->count(2))->create();

        $this->callAuthorizedRouteAction([], ['enum' => $enum->id])
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['title', 'id', 'values' => [['id', 'title']]]])
            ->assertJsonCount(2, 'data.values')
        ;
    }
}
