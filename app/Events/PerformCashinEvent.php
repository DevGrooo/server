<?php

namespace App\Events;

use App\Models\Cashin;

class PerformCashinEvent extends Event
{
    public $cashin;
    public $locale;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Cashin $cashin)
    {
        $this->cashin = $cashin;
        $this->locale = app('translator')->getLocale();
    }
}
