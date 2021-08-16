<?php

namespace App\Providers;

use App\Models\Section;
use App\Observers\SectionObserver;
use App\Observers\SectionFieldObserver;
use App\Services\FileManager;
use Illuminate\Support\ServiceProvider;
use Storage;
use Vaites\ApacheTika\Client as TikaClient;
use Validator;

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
        $this->app->register(SearchServiceProvider::class);
        $this->app->singleton(FileManager::class, fn() => new FileManager(Storage::disk('local'), $this->app[TikaClient::class]));
        $this->app->singleton(TikaClient::class, fn() => TikaClient::make(config('services.tika.path')));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('index_array', function ($attribute, $value, $parameters, $validator) {
            return (is_array($value) && count(array_filter(array_keys($value), 'is_string')) === 0);
        }, 'Field :attribute should be an index array');
    }
}
