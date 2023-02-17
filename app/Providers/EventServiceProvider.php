<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Listeners\LogLastLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use App\Listeners\SendOrderToReseller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    // protected $listen =// array(
    //     "App\\Events\\OrderPlaced" => array(
    //         "App\\Listeners\\SendOrderToReseller"
    //     ),
    //     "Illuminate\\Auth\\Events\\Login" => array("App\\Listeners\\LogLastLogin")
    // )

    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // Listner for order
        OrderPlaced::class => [
            SendOrderToReseller::class
        ],
        Login::class => [
            LogLastLogin::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
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
