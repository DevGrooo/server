<?php

namespace App\Events;

use App\Models\TradingSession;

class CancelTradingSessionEvent extends Event
{
    public $trading_session;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TradingSession $trading_session)
    {
        $this->trading_session = $trading_session;
    }
}
