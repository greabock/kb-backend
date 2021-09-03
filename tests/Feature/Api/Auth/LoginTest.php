<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Tests\Feature\Api\ActionTestCase;

class LoginTest extends ActionTestCase
{
    public function getRouteName(): string
    {
        return 'auth.login';
    }

    public function testFieldsLoginAndPasswordAreRequired(): void
    {
        $this->callRouteAction([])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['login', 'password']);
    }


    public function testRequestFailsWithWrongPasswordAndLogin(): void
    {
        $this->callRouteAction([
            'login' => 'wrong_login',
            'password' => 'wrong_password',
        ])->assertStatus(401);
    }

    public function testUserCanTakeTokenWithValidLoginAndPassword(): void
    {
        $user = User::factory()->create();

        $this->callRouteAction([
            'login' => $user->login,
            'password' => 'password',
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['token']]);
    }
}
