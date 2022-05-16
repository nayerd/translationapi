<?php

namespace App\Providers;

use App\Models\BasketDocument;
use App\Models\BasketTargetLanguage;
use App\Observers\BasketDocumentObserver;
use App\Observers\BasketTargetLanguageObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        if (config('translationapi.enable_observers') == true) {
            BasketDocument::observe(BasketDocumentObserver::class);
            BasketTargetLanguage::observe(BasketTargetLanguageObserver::class);
        }
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
