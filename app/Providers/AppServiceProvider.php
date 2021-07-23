<?php

namespace App\Providers;

use App\Models\Section;
use App\Observers\SectionObserver;
use App\Observers\SectionFieldObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(MaterialServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Section::observe(SectionObserver::class);
        Section\Field::observe(SectionFieldObserver::class);
    }
}
