<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\SectionMaterialModelBuilder;
use Illuminate\Support\ServiceProvider;

class MaterialServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SectionMaterialModelBuilder::class, function () {
            return new SectionMaterialModelBuilder($this->app['cache']->driver('redis'));
        });
    }

    public function boot()
    {
        spl_autoload_register(function ($className) {
            if (str_contains($className, 'Section') && $this->app->has(SectionMaterialModelBuilder::class)) {
                $this->app[SectionMaterialModelBuilder::class]($className);
            }
        });
    }
}
