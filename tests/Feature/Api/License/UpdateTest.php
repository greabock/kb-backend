<?php

declare(strict_types=1);

namespace Tests\Feature\Api\License;

use Tests\Feature\Api\ActionTestCase;

class UpdateTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'license.update';
    }

    public function testUpdateState()
    {
        $path = storage_path('license.key');

        $this->callAuthorizedRouteAction([
            'key' => $key = 'TVZRSv+U534IRXwQKcD1og==Nmw2QkZiZnJ1VHpEY2xwcmI5ZnM3ZThXNU81VjhUVm1haDBwcDk2Q2NXdCtFOFdSZHIzSTAzUUgrK3hSVEV6bA=='
        ])->assertOk();

        $this->assertEquals($key, file_get_contents($path));
    }
}
