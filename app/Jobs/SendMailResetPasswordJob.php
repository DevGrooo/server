<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Mails\Mail;

class SendMailResetPasswordJob extends Job
{
    protected $user;
    protected $new_password;
    protected $locale;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, String $new_password)
    {
        $this->user = $user;
        $this->new_password = $new_password;
        $this->locale = app('translator')->getLocale();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            'user' => $this->user,
            'new_password' => $this->new_password,
        ];
        
        Mail::send($this->user->email, $this->user->fullname, trans('mail.title_reset_password', [], $this->locale), 'emails.user_reset_password', $data);
    }
}
