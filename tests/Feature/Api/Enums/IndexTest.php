<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Enums;


use App\Models\Enum;
use Tests\Feature\Api\ActionTestCase;

class IndexTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'enums.index';
    }

    public function testUserCanSeeListOfEnums(): void
    {
        Enum::factory()->count(2)->create();

        $this->callAuthorizedRouteAction()
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [['title', 'id']]])
            ->assertJsonCount(2, 'data');
    }
}
