<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Auth\Azure;

use Tests\Feature\Api\ActionTestCase;

class RedirectTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'auth.azure.redirect';
    }

    public function testRedirectReturnValidUrl(): void
    {
        $this->callRouteAction()
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['url']]);
    }
}
