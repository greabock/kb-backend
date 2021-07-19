<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Greabock\Populator\Populator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Route;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class ActionTestCase extends TestCase
{
    use DatabaseTransactions;

    private Populator $populator;

    /**
     * Имя маршрута для которого пишутся тесты
     *
     * @return string
     */
    abstract public function getRouteName(): string;

    public function assertRouteContainsMiddleware(...$names): static
    {
        $route = $this->getRouteByName();

        foreach ($names as $name) {
            $this->assertContains(
                $name, $route->middleware(),
                "Route doesn't contain middleware [{$name}]"
            );
        }

        return $this;
    }

    public function assertRouteHasExactMiddleware(...$names): static
    {
        $route = $this->getRouteByName();

        $this->assertRouteContainsMiddleware(...$names);
        $this->assertCount($names, $route->middleware(), 'Route contains not the same amount of middleware.');

        return $this;
    }


    /**
     * @return Route
     */
    private function getRouteByName(): Route
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();

        /** @var Route $route */
        $route = $routes->getByName($this->getRouteName());

        if (!$route) {
            $this->fail("Route with name [{$this->getRouteName()}] not found!");
        }

        return $route;
    }

    /**
     * Выполнение неавторизованного запроса
     *
     * @param array $data Request body
     * @param array $parameters Route parameters
     * @param array $headers Request headers
     *
     * @return TestResponse
     */
    protected function callRouteAction(array $data = [], array $parameters = [], array $headers = []): TestResponse
    {
        $route = $this->getRouteByName();
        $method = $route->methods()[0];
        $url = route($this->getRouteName(), $parameters);

        return $this->json($method, $url, $data, $headers);
    }

    /**
     * Выполнение авторизованного запроса. В момент запроса создается рандомный пользователь
     * и от него выполняется запрос
     *
     * @param array $data Request body
     * @param array $parameters Route parameters
     * @param array $headers Request headers
     * @param array $scopes
     *
     * @return TestResponse
     */
    protected function callAuthorizedRouteAction(array $data = [], array $parameters = [], array $headers = [], array $scopes = []): TestResponse
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user = User::findOrFail($user->getKey());

        return $this->callAuthorizedByUserRouteAction($user, $data, $parameters, $headers, $scopes);
    }

    /**
     * Выполнение запроса от имени переданного пользователя.
     *
     * @param User $user
     * @param array $data
     * @param array $parameters
     * @param array $headers
     * @param array $scopes
     *
     * @return TestResponse
     */
    protected function callAuthorizedByUserRouteAction(User $user, array $data = [], array $parameters = [], array $headers = [], array $scopes = []): TestResponse
    {
        \Auth::login($user);

        return $this->callRouteAction($data, $parameters, $headers);
    }

    public function populator(): Populator
    {
        return $this->populator ?? $this->populator = $this->app[Populator::class];
    }
}
