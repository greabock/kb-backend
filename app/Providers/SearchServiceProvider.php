<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\Laravel\ServiceProvider;
use Elasticsearch\Client;

class SearchServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->alias('scout_elastic.client', Client::class);
    }
}
