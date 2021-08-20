<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\MaterialClassManager;
use Illuminate\Support\ServiceProvider;
use Str;

class MaterialServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MaterialClassManager::class, function () {
            return new MaterialClassManager($this->app['cache']->driver('redis'));
        });
    }

    public function boot()
    {
        spl_autoload_register(function ($className) {
            if (preg_match('#^Section[0-9a-f]{32}$#', $className) && $this->app->has(MaterialClassManager::class)) {
                $this->app[MaterialClassManager::class]($className);
            }
        });
    }
}
