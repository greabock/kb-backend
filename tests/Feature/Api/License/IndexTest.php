<?php

declare(strict_types=1);

namespace Tests\Feature\Api\License;

use Tests\Feature\Api\ActionTestCase;

class IndexTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'license.index';
    }

    public function testReturnState()
    {
        $path = storage_path('license.key');

        file_put_contents($path, '2L9UKVXLNCj4uxQzwxvFzw==VVdiaVJqS2N0T3pmTkwrek45QzEvZVBtZVMveGZJVCtvckUvZEZZYUVYY08vT0FlQVZkbTBuYzBmaWlTbkJIQw==');

        $this->callAuthorizedRouteAction()
            ->assertOk()
            ->assertJsonPath('expires_at', '2021-12-31')
            ->assertJsonPath('key', 'test');
    }
}
