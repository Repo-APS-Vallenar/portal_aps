<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use App\Listeners\LogFailedLogin;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Failed::class => [
            LogFailedLogin::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        //
    }
}
