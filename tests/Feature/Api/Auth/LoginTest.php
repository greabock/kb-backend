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
            ->assertJsonValidationErrors(['email', 'password']);
    }


    public function testRequestFailsWithWrongPasswordAndLogin(): void
    {
        $this->callRouteAction([
            'email' => 'wrong_email',
            'password' => 'wrong_password',
        ])->assertStatus(401);
    }

    public function testUserCanTakeTokenWithValidLoginAndPassword(): void
    {
        $user = User::factory()->create();

        $this->callRouteAction([
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['token']]);
    }
}
