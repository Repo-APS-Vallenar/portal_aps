<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use App\Listeners\LogFailedLogin;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Failed::class => [
            LogFailedLogin::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
