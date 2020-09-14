<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\CheckUserTokenEvent::class => [
            \App\Listeners\UpdateExpiredUserToken::class
        ],
        \App\Events\PerformTradingOrderEvent::class => [
            \App\Listeners\PerformTradingOrderListener::class
        ],
        \App\Events\EndTradingSessionEvent::class => [
            \App\Listeners\EndTradingSessionListener::class
        ],
        \App\Events\CancelTradingSessionEvent::class => [
            \App\Listeners\CancelTradingSessionListener::class
        ],
        \App\Events\PerformCashinEvent::class => [
            \App\Listeners\PerformCashinListener::class
        ],
        \App\Events\ActiveInvestorEvent::class => [
            \App\Listeners\ActiveInvestorListener::class
        ],
    ];
}
