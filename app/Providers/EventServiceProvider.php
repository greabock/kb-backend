<?php

namespace App\Providers;

use App\Events\Handlers\CreateMaterialClassOnSectionCreated;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            'SocialiteProviders\\Azure\\AzureExtendSocialite@handle',
        ],
        \App\Events\SectionCreated::class => [
            \App\Events\Handlers\CreateTableOnSectionCreated::class,
            \App\Events\Handlers\CreateIndexOnSectionCreated::class,
            \App\Events\Handlers\CreateMaterialClassOnSectionCreated::class,
        ],
        \App\Events\SectionUpdated::class => [
            \App\Events\Handlers\UpdateIndexOnSectionUpdated::class,
            \App\Events\Handlers\UpdateDatabaseOnSectionUpdated::class,
            \App\Events\Handlers\UpdateMaterialClassOnSectionUpdated::class,
        ],
        \App\Events\MaterialCreated::class => [
            \App\Events\Handlers\CreateMaterialDocumentOnMaterialCreated::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
