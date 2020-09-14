<?php

namespace App\Events;

use App\Models\UserToken;

class CheckUserTokenEvent extends Event
{
    public $user_token;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserToken $user_token)
    {
        $this->user_token = $user_token;
    }
}
