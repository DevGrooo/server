<?php

namespace App\Events;

use App\Models\Investor;

class ActiveInvestorEvent extends Event
{
    public $investor;
    public $locale;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Investor $investor)
    {
        $this->investor = $investor;
        $this->locale = app('translator')->getLocale();
    }
}
