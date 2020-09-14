<?php

namespace App\Listeners;

use App\Events\CheckUserTokenEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateExpiredUserToken
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CheckUserTokenEvent  $event
     * @return void
     */
    public function handle(CheckUserTokenEvent $event)
    {
        $event->user_token->expired_at = $event->user_token->getExpiredAt(time());
        $event->user_token->save();
    }
}
